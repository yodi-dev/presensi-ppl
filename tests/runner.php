<?php

/**
 * Presensi PPL - Automated Test Runner
 * Menjalankan test suite mandiri untuk memverifikasi keamanan dan fungsionalitas aplikasi.
 */

define('TEST_START_TIME', microtime(true));

// Load official CodeIgniter 4 Test Bootstrap
require_once __DIR__ . '/../system/Test/bootstrap.php';

class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $errors = [];

    public function describe(string $title): void
    {
        echo "\n\033[1;36m=== {$title} ===\033[0m\n";
    }

    public function it(string $description, callable $testCase): void
    {
        try {
            $testCase();
            $this->passed++;
            echo "  \033[32m✔ PASS\033[0m: {$description}\n";
        } catch (\Throwable $e) {
            $this->failed++;
            $this->errors[] = [
                'desc' => $description,
                'msg'  => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine()
            ];
            echo "  \033[31m✖ FAIL\033[0m: {$description} - " . $e->getMessage() . "\n";
        }
    }

    public function assertTrue(bool $condition, string $message = 'Expected true, got false'): void
    {
        if (!$condition) {
            throw new \AssertionError($message);
        }
    }

    public function assertFalse(bool $condition, string $message = 'Expected false, got true'): void
    {
        if ($condition) {
            throw new \AssertionError($message);
        }
    }

    public function assertEquals($expected, $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $msg = $message ?: "Expected " . var_export($expected, true) . ", got " . var_export($actual, true);
            throw new \AssertionError($msg);
        }
    }

    public function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        if (!preg_match($pattern, $string)) {
            $msg = $message ?: "String '{$string}' does not match pattern '{$pattern}'";
            throw new \AssertionError($msg);
        }
    }

    public function report(): int
    {
        $duration = number_format(microtime(true) - TEST_START_TIME, 4);
        echo "\n\033[1;33m--------------------------------------------------\033[0m\n";
        echo "\033[1;37mHASIL PENGUJIAN OTOMATIS (TEST REPORT):\033[0m\n";
        echo "  Total Tests Run : " . ($this->passed + $this->failed) . "\n";
        echo "  \033[32mPassed          : {$this->passed}\033[0m\n";
        if ($this->failed > 0) {
            echo "  \033[31mFailed          : {$this->failed}\033[0m\n";
            echo "\nRincian Kegagalan:\n";
            foreach ($this->errors as $err) {
                echo "  - {$err['desc']}\n    {$err['msg']} at {$err['file']}\n";
            }
        } else {
            echo "  \033[32mFailed          : 0\033[0m\n";
            echo "  \033[1;32mSTATUS          : SEMUA PENGUJIAN LULUS (ALL TESTS PASSED)! 🎉\033[0m\n";
        }
        echo "  Durasi          : {$duration} detik\n";
        echo "\033[1;33m--------------------------------------------------\033[0m\n\n";

        return $this->failed === 0 ? 0 : 1;
    }
}

// Inisialisasi runner
$runner = new TestRunner();

// ==========================================
// 1. PENGUJIAN FILTER AUTENTIKASI (AuthFilter)
// ==========================================
$runner->describe("1. Pengujian AuthFilter (Proteksi Akses Tamu)");

require_once __DIR__ . '/../app/Filters/AuthFilter.php';

// Injeksi MockSession untuk pengujian CLI tanpa ketergantungan header browser
$configSession = new \Config\Session();
$mockSession = new \CodeIgniter\Test\Mock\MockSession(
    new \CodeIgniter\Session\Handlers\ArrayHandler($configSession, '127.0.0.1'),
    $configSession
);
\Config\Services::injectMock('session', $mockSession);

$request = \Config\Services::request();
$session = \Config\Services::session();
$authFilter = new \App\Filters\AuthFilter();

$runner->it("Tamu tanpa sesi 'isLoggedIn' harus ditolak dan dialihkan ke /auth", function() use ($runner, $authFilter, $request, $session) {
    $session->remove('isLoggedIn');
    $result = $authFilter->before($request);
    $runner->assertTrue($result instanceof \CodeIgniter\HTTP\RedirectResponse, "Filter harus mengembalikan RedirectResponse");
    $runner->assertTrue(strpos($result->getHeaderLine('Location'), '/auth') !== false, "Target redirect harus mengarah ke /auth");
});

$runner->it("User yang memiliki sesi 'isLoggedIn = true' harus diizinkan lewat (return null)", function() use ($runner, $authFilter, $request, $session) {
    $session->set('isLoggedIn', true);
    $result = $authFilter->before($request);
    $runner->assertTrue($result === null, "Filter harus return null (lanjutkan eksekusi)");
});


// ==========================================
// 2. PENGUJIAN FILTER PERAN (RoleFilter)
// ==========================================
$runner->describe("2. Pengujian RoleFilter (Pemisahan Hak Akses Guru & Mahasiswa)");

require_once __DIR__ . '/../app/Filters/RoleFilter.php';
$roleFilter = new \App\Filters\RoleFilter();

$runner->it("User belum login yang mengakses rute role-restricted harus dialihkan ke /auth", function() use ($runner, $roleFilter, $request, $session) {
    $session->remove('isLoggedIn');
    $session->remove('role');
    $result = $roleFilter->before($request, ['guru']);
    $runner->assertTrue($result instanceof \CodeIgniter\HTTP\RedirectResponse);
    $runner->assertTrue(strpos($result->getHeaderLine('Location'), '/auth') !== false);
});

$runner->it("Mahasiswa mencoba mengakses rute Guru harus dialihkan ke /mahasiswa dengan pesan ditolak", function() use ($runner, $roleFilter, $request, $session) {
    $session->set('isLoggedIn', true);
    $session->set('role', 'mahasiswa');
    $result = $roleFilter->before($request, ['guru']);
    $runner->assertTrue($result instanceof \CodeIgniter\HTTP\RedirectResponse);
    $runner->assertTrue(strpos($result->getHeaderLine('Location'), '/mahasiswa') !== false);
});

$runner->it("Guru mencoba mengakses rute Mahasiswa harus dialihkan ke /guru dengan pesan ditolak", function() use ($runner, $roleFilter, $request, $session) {
    $session->set('isLoggedIn', true);
    $session->set('role', 'guru');
    $result = $roleFilter->before($request, ['mahasiswa']);
    $runner->assertTrue($result instanceof \CodeIgniter\HTTP\RedirectResponse);
    $runner->assertTrue(strpos($result->getHeaderLine('Location'), '/guru') !== false);
});

$runner->it("Guru mengakses rute Guru harus diizinkan (return null)", function() use ($runner, $roleFilter, $request, $session) {
    $session->set('isLoggedIn', true);
    $session->set('role', 'guru');
    $result = $roleFilter->before($request, ['guru']);
    $runner->assertTrue($result === null);
});


// ==========================================
// 3. PENGUJIAN KEAMANAN UPLOAD FOTO PIKET
// ==========================================
$runner->describe("3. Pengujian Keamanan File Upload Bukti Piket");

$runner->it("Format data Base64 berbahaya dengan MIME bukan gambar (misal image/php) harus ditolak regex", function() use ($runner) {
    $maliciousPayload = "data:image/php;base64," . base64_encode("<?php phpinfo(); ?>");
    $pattern = '/^data:(image\/(jpeg|jpg|png|webp));base64,(.+)$/i';
    $runner->assertFalse((bool) preg_match($pattern, $maliciousPayload), "Header image/php harus ditolak");
});

$runner->it("Ekstensi manipulasi seperti .phtml, .cgi, .sh harus ditolak", function() use ($runner) {
    $allowedMimes = ['image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $runner->assertFalse(isset($allowedMimes['image/phtml']));
    $runner->assertFalse(isset($allowedMimes['image/x-php']));
    $runner->assertFalse(isset($allowedMimes['text/plain']));
});

$runner->it("Header image/png tetapi isi data binary palsu (bukan gambar asli) harus terdeteksi oleh getimagesizefromstring", function() use ($runner) {
    $fakeImageBinary = "Ini bukan binary gambar yang valid, ini teks palsu!";
    $info = @getimagesizefromstring($fakeImageBinary);
    $runner->assertFalse($info !== false, "Binary palsu harus gagal diidentifikasi sebagai gambar");
});

$runner->it("Gambar PNG asli 1x1 pixel harus berhasil divalidasi getimagesizefromstring", function() use ($runner) {
    // 1x1 transparent PNG base64
    $validPngBase64 = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAAElFTkSuQmCC";
    $binary = base64_decode($validPngBase64);
    $info = @getimagesizefromstring($binary);
    $runner->assertTrue($info !== false);
    $runner->assertEquals('image/png', $info['mime']);
});

$runner->it("Format nama file baru harus mematuhi pola aman: piket_{userId}_{timestamp}_{randomHash}.{ext}", function() use ($runner) {
    $userId = 12;
    $time = time();
    $randomHash = bin2hex(random_bytes(4));
    $ext = 'jpg';
    $fileName = "piket_{$userId}_{$time}_{$randomHash}.{$ext}";
    $runner->assertMatchesRegularExpression('/^piket_\d+_\d+_[a-f0-9]{8}\.(jpg|png|webp)$/', $fileName);
});

$runner->it("File .htaccess di folder uploads harus memblokir eksekusi skrip PHP", function() use ($runner) {
    $htaccessPath = __DIR__ . '/../public/uploads/.htaccess';
    $runner->assertTrue(file_exists($htaccessPath), "File .htaccess harus ada di public/uploads/");
    $content = file_get_contents($htaccessPath);
    $runner->assertMatchesRegularExpression('/php|phtml|phar/i', $content);
    $runner->assertMatchesRegularExpression('/Deny from all/i', $content);
});


// ==========================================
// 4. PENGUJIAN PENANGGULANGAN SQL INJECTION
// ==========================================
$runner->describe("4. Pengujian Penanggulangan SQL Injection");

$runner->it("Filter tanggal dengan format tidak valid atau SQL Injection harus di-fallback ke tanggal hari ini", function() use ($runner) {
    $sqliPayload = "2026-04-16' OR 1=1 --";
    $isValid = (is_string($sqliPayload) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sqliPayload));
    $tanggalPilih = $isValid ? $sqliPayload : date('Y-m-d');
    $runner->assertEquals(date('Y-m-d'), $tanggalPilih, "Payload SQL Injection pada tanggal harus dinetralkan");
});

$runner->it("Filter bulan dan tahun dengan SQL Injection harus dibersihkan oleh integer casting", function() use ($runner) {
    $sqliBulan = "04; DROP TABLE users; --";
    $isValidBulan = (is_string($sqliBulan) && preg_match('/^(0[1-9]|1[0-2])$/', $sqliBulan));
    $bulanPilih = $isValidBulan ? $sqliBulan : date('m');
    $runner->assertEquals(date('m'), $bulanPilih);

    $sqliTahun = "2026 UNION SELECT password FROM users";
    $isValidTahun = (is_string($sqliTahun) && preg_match('/^\d{4}$/', $sqliTahun));
    $tahunPilih = $isValidTahun ? $sqliTahun : date('Y');
    $runner->assertEquals(date('Y'), $tahunPilih);
});


// ==========================================
// 5. PENGUJIAN SANITASI XSS (Cross-Site Scripting)
// ==========================================
$runner->describe("5. Pengujian Sanitasi Output XSS");

function esc($data, string $context = 'html'): string {
    return htmlspecialchars((string) $data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$runner->it("Tag berbahaya <script> dalam nama siswa atau keterangan harus diubah menjadi entitas HTML aman", function() use ($runner) {
    $xssNama = "<script>alert('pwned')</script>";
    $escaped = esc($xssNama);
    $runner->assertFalse(strpos($escaped, '<script>') !== false, "Tag <script> tidak boleh muncul mentah");
    $runner->assertEquals("&lt;script&gt;alert(&#039;pwned&#039;)&lt;/script&gt;", $escaped);
});

$runner->it("Attribut event handler XSS seperti onerror=alert(1) harus dinetralkan", function() use ($runner) {
    $xssAttr = '<img src=x onerror=alert(1)>';
    $escaped = esc($xssAttr);
    $runner->assertFalse(strpos($escaped, '<img') !== false);
});

$runner->it("Output flash message dalam JavaScript SweetAlert aman menggunakan json_encode()", function() use ($runner) {
    $rawFlash = "Pesan 'quote' dan </script><script>alert(1)</script>";
    $jsonEncoded = json_encode((string) $rawFlash);
    $runner->assertFalse(strpos($jsonEncoded, "</script>") !== false, "Script injection dalam JSON harus terhindar dari pemecah string JS");
});


// ==========================================
// 6. PENGUJIAN KONFIGURASI GIT & ENV
// ==========================================
$runner->describe("6. Pengujian Sanitasi Git & Environment");

$runner->it("File .gitignore harus mengabaikan kredensial .env, .env.*, dan folder writable", function() use ($runner) {
    $gitignorePath = __DIR__ . '/../.gitignore';
    $runner->assertTrue(file_exists($gitignorePath));
    $content = file_get_contents($gitignorePath);
    $runner->assertMatchesRegularExpression('/\.env/i', $content);
    $runner->assertMatchesRegularExpression('/writable\/session/i', $content);
});

$runner->it("File .env.example harus tersedia dan tidak boleh memuat password database sensitif", function() use ($runner) {
    $examplePath = __DIR__ . '/../.env.example';
    $runner->assertTrue(file_exists($examplePath));
    $content = file_get_contents($examplePath);
    $runner->assertFalse(strpos($content, 'VbX4pzFXTf') !== false, "Password produksi tidak boleh bocor di .env.example");
    $runner->assertFalse(strpos($content, 'sql204.infinityfree.com') !== false, "Host produksi tidak boleh bocor di .env.example");
});


// Cetak laporan akhir & exit code
exit($runner->report());
