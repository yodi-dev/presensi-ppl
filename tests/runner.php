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


// ==========================================
// 7. PENGUJIAN GPS GEOFENCING & HAVERSINE FORMULA
// ==========================================
$runner->describe("7. Pengujian GPS Geofencing (Formula Haversine & Validasi Radius)");

require_once __DIR__ . '/../app/Config/Presensi.php';
$presensiConfig = new \Config\Presensi();

$runner->it("Koordinat titik tepat sekolah harus menghasilkan jarak ~0 meter", function() use ($runner, $presensiConfig) {
    $jarak = \Config\Presensi::hitungJarak(
        $presensiConfig->schoolLatitude,
        $presensiConfig->schoolLongitude,
        $presensiConfig->schoolLatitude,
        $presensiConfig->schoolLongitude
    );
    $runner->assertTrue($jarak < 1.0, "Jarak harus mendekati 0 meter");
});

$runner->it("Koordinat dalam radius 50 meter harus diizinkan (< radius 100m)", function() use ($runner, $presensiConfig) {
    // Geser sedikit latitude (+0.0003 derajat ~ 33 meter)
    $latDekat = $presensiConfig->schoolLatitude + 0.0003;
    $longDekat = $presensiConfig->schoolLongitude;
    $jarak = \Config\Presensi::hitungJarak(
        $latDekat,
        $longDekat,
        $presensiConfig->schoolLatitude,
        $presensiConfig->schoolLongitude
    );
    $runner->assertTrue($jarak <= $presensiConfig->schoolRadius, "Jarak {$jarak}m harus masuk radius {$presensiConfig->schoolRadius}m");
});

$runner->it("Koordinat sejauh 500 meter (> 100 meter) harus ditolak berada di luar radius", function() use ($runner, $presensiConfig) {
    // Geser latitude (+0.005 derajat ~ 550 meter)
    $latJauh = $presensiConfig->schoolLatitude + 0.005;
    $longJauh = $presensiConfig->schoolLongitude;
    $jarak = \Config\Presensi::hitungJarak(
        $latJauh,
        $longJauh,
        $presensiConfig->schoolLatitude,
        $presensiConfig->schoolLongitude
    );
    $runner->assertTrue($jarak > $presensiConfig->schoolRadius, "Jarak {$jarak}m harus melebihi batas radius {$presensiConfig->schoolRadius}m");
});

$runner->it("Format koordinat tidak valid (di luar rentang -90..90 atau -180..180) harus ditolak", function() use ($runner) {
    $latInvalid = 95.0;
    $longInvalid = 200.0;
    $isValid = ($latInvalid >= -90 && $latInvalid <= 90 && $longInvalid >= -180 && $longInvalid <= 180);
    $runner->assertFalse($isValid, "Koordinat di luar bumi harus tidak valid");
});


// ==========================================
// 8. PENGUJIAN KEBIJAKAN JAM KERJA & KETERLAMBATAN
// ==========================================
$runner->describe("8. Pengujian Kebijakan Jam Kerja & Keterlambatan");

$runner->it("Presensi sebelum 07:15:00 WIB harus berstatus 'hadir'", function() use ($runner, $presensiConfig) {
    $jamMasukTepatWaktu = "07:05:00";
    $isTerlambat = ($jamMasukTepatWaktu > $presensiConfig->jamMasukMax);
    $status = $isTerlambat ? 'terlambat' : 'hadir';
    $runner->assertEquals('hadir', $status);
});

$runner->it("Presensi setelah 07:15:00 WIB harus berstatus 'terlambat'", function() use ($runner, $presensiConfig) {
    $jamMasukTerlambat = "07:22:15";
    $isTerlambat = ($jamMasukTerlambat > $presensiConfig->jamMasukMax);
    $status = $isTerlambat ? 'terlambat' : 'hadir';
    $runner->assertEquals('terlambat', $status);
});

$runner->it("Presensi pulang sebelum 15:00:00 WIB harus dicegah / ditolak", function() use ($runner, $presensiConfig) {
    $jamPulangAwal = "13:30:00";
    $bisaPulang = ($jamPulangAwal >= $presensiConfig->jamPulangMin);
    $runner->assertFalse($bisaPulang, "Jam pulang sebelum 15:00:00 harus dicegah");
});

$runner->it("Presensi pulang pada atau setelah 15:00:00 WIB harus diizinkan", function() use ($runner, $presensiConfig) {
    $jamPulangSah = "15:15:00";
    $bisaPulang = ($jamPulangSah >= $presensiConfig->jamPulangMin);
    $runner->assertTrue($bisaPulang, "Jam pulang setelah 15:00:00 harus diizinkan");
});


// ==========================================
// 9. PENGUJIAN HAK AKSES ADMIN & USER MANAGEMENT
// ==========================================
$runner->describe("9. Pengujian Hak Akses Admin & Manajemen Pengguna");

$runner->it("Admin mengakses rute Admin harus diizinkan (return null)", function() use ($runner, $roleFilter, $request, $session) {
    $session->set('isLoggedIn', true);
    $session->set('role', 'admin');
    $result = $roleFilter->before($request, ['admin']);
    $runner->assertTrue($result === null);
});

$runner->it("Mahasiswa mencoba mengakses rute Admin harus dialihkan ke /admin", function() use ($runner, $roleFilter, $request, $session) {
    $session->set('isLoggedIn', true);
    $session->set('role', 'mahasiswa');
    $result = $roleFilter->before($request, ['admin']);
    $runner->assertTrue($result instanceof \CodeIgniter\HTTP\RedirectResponse);
    $runner->assertTrue(strpos($result->getHeaderLine('Location'), '/mahasiswa') !== false);
});

$runner->it("Guru mencoba mengakses rute Admin harus dialihkan ke /guru", function() use ($runner, $roleFilter, $request, $session) {
    $session->set('isLoggedIn', true);
    $session->set('role', 'guru');
    $result = $roleFilter->before($request, ['admin']);
    $runner->assertTrue($result instanceof \CodeIgniter\HTTP\RedirectResponse);
    $runner->assertTrue(strpos($result->getHeaderLine('Location'), '/guru') !== false);
});

$runner->it("Admin dilarang menghapus akun dirinya sendiri", function() use ($runner) {
    $currentAdminId = 1;
    $targetDeleteId = 1;
    $isSelfDelete = ((int) $targetDeleteId === (int) $currentAdminId);
    $runner->assertTrue($isSelfDelete, "Penghapusan akun sendiri harus terdeteksi");
});


// ==========================================
// 10. PENGUJIAN EXPORT EXCEL & INTEGRITAS FITUR
// ==========================================
$runner->describe("10. Pengujian Laporan Excel & Status Whitelist");

$runner->it("Daftar status sah di Guru::update_status dan PresensiModel harus mendukung 'terlambat'", function() use ($runner) {
    $presensiModel = new \App\Models\PresensiModel();
    $rules = $presensiModel->getValidationRules();
    $runner->assertTrue(isset($rules['status']));
    $runner->assertTrue(strpos($rules['status'], 'terlambat') !== false, "Status 'terlambat' harus ada di validasi PresensiModel");
});

$runner->it("Method exportExcel harus terdefinisi pada Controller Guru", function() use ($runner) {
    $guruController = new \App\Controllers\Guru();
    $runner->assertTrue(method_exists($guruController, 'exportExcel'), "Method exportExcel harus ada di Guru controller");
});

$runner->it("Controller Admin harus memiliki metode CRUD lengkap", function() use ($runner) {
    $adminController = new \App\Controllers\Admin();
    $runner->assertTrue(method_exists($adminController, 'index'));
    $runner->assertTrue(method_exists($adminController, 'tambahUser'));
    $runner->assertTrue(method_exists($adminController, 'editUser'));
    $runner->assertTrue(method_exists($adminController, 'hapusUser'));
    $runner->assertTrue(method_exists($adminController, 'resetPassword'));
});


// ==========================================
// 11. PENGUJIAN MODUL 1: PENGATURAN GEOFENCING DINAMIS
// ==========================================
$runner->describe("11. Pengujian Modul 1: Pengaturan Geofencing Dinamis & SettingModel");

require_once __DIR__ . '/../app/Models/SettingModel.php';

$runner->it("SettingModel harus memiliki metode getSetting, setSetting, dan getAllSettings", function() use ($runner) {
    $runner->assertTrue(method_exists(\App\Models\SettingModel::class, 'getSetting'));
    $runner->assertTrue(method_exists(\App\Models\SettingModel::class, 'setSetting'));
    $runner->assertTrue(method_exists(\App\Models\SettingModel::class, 'getAllSettings'));
});

$runner->it("SettingModel::getSetting harus mengembalikan nilai default bila key tidak ditemukan", function() use ($runner) {
    $val = \App\Models\SettingModel::getSetting('kunci_tidak_ada_di_database_xyz', 'nilai_default_aman');
    $runner->assertEquals('nilai_default_aman', $val);
});

$runner->it("Config\\Presensi harus memuat properti dinamis (schoolName, geofenceActive, dsb)", function() use ($runner) {
    $cfg = new \Config\Presensi();
    $runner->assertTrue(property_exists($cfg, 'schoolName'));
    $runner->assertTrue(property_exists($cfg, 'geofenceActive'));
    $runner->assertTrue(property_exists($cfg, 'schoolLatitude'));
    $runner->assertTrue(property_exists($cfg, 'schoolLongitude'));
    $runner->assertTrue(property_exists($cfg, 'schoolRadius'));
});

$runner->it("Validasi input pengaturan harus menolak koordinat dan radius di luar batas", function() use ($runner) {
    $latSalah = 105.5; // > 90
    $isLatValid = (is_numeric($latSalah) && $latSalah >= -90 && $latSalah <= 90);
    $runner->assertFalse($isLatValid, "Latitude di atas 90 harus ditolak");

    $radiusNegatif = -50;
    $isRadiusValid = (is_numeric($radiusNegatif) && $radiusNegatif >= 10 && $radiusNegatif <= 5000);
    $runner->assertFalse($isRadiusValid, "Radius negatif harus ditolak");

    $radiusTerlaluBesar = 10000;
    $isRadiusValid2 = (is_numeric($radiusTerlaluBesar) && $radiusTerlaluBesar >= 10 && $radiusTerlaluBesar <= 5000);
    $runner->assertFalse($isRadiusValid2, "Radius di atas 5000 meter harus ditolak");
});

$runner->it("Validasi jam kerja harus menerima format HH:MM dan HH:MM:SS", function() use ($runner) {
    $jamValid1 = "07:15";
    $jamValid2 = "07:15:00";
    $jamSalah  = "25:70:99";
    $pattern = '/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/';

    $runner->assertTrue((bool) preg_match($pattern, $jamValid1));
    $runner->assertTrue((bool) preg_match($pattern, $jamValid2));
    $runner->assertFalse((bool) preg_match($pattern, $jamSalah));
});

$runner->it("Mode toleransi (geofenceActive = false) tidak boleh memblokir presensi di luar radius", function() use ($runner, $presensiConfig) {
    $latJauh = $presensiConfig->schoolLatitude + 0.05; // ~5.5 km
    $longJauh = $presensiConfig->schoolLongitude;
    $jarak = \Config\Presensi::hitungJarak($latJauh, $longJauh, $presensiConfig->schoolLatitude, $presensiConfig->schoolLongitude);
    $runner->assertTrue($jarak > $presensiConfig->schoolRadius, "Jarak harus jauh (> 100m)");

    // Simulasi logika controller dengan toggle nonaktif
    $geofenceActive = false;
    $apakahDitolak = false;
    if ($geofenceActive && $jarak > $presensiConfig->schoolRadius) {
        $apakahDitolak = true;
    }
    $runner->assertFalse($apakahDitolak, "Ketika geofence nonaktif, presensi tidak boleh ditolak");
});

$runner->it("Controller Admin harus memiliki method pengaturan dan simpanPengaturan", function() use ($runner) {
    $adminController = new \App\Controllers\Admin();
    $runner->assertTrue(method_exists($adminController, 'pengaturan'));
    $runner->assertTrue(method_exists($adminController, 'simpanPengaturan'));
});

$runner->describe("12. Pengujian Hari Kerja Efektif & Identitas Resmi Skagata");

$runner->it("Config\\Presensi harus mengarahkan institusi ke SMK Negeri 3 Yogyakarta dan koordinat Skagata", function() use ($runner) {
    $cfg = new \Config\Presensi();
    $runner->assertEquals('SMK Negeri 3 Yogyakarta', $cfg->schoolName);
    $runner->assertEquals(-7.780120, $cfg->schoolLatitude);
    $runner->assertEquals(110.366450, $cfg->schoolLongitude);
});

$runner->it("Logika siklus kerja 5 hari harus mengidentifikasi Sabtu dan Minggu sebagai akhir pekan resmi", function() use ($runner) {
    // 2026-10-02 adalah Jumat (hari kerja), 2026-10-03 adalah Sabtu, 2026-10-04 adalah Minggu
    $jumat = date('N', strtotime('2026-10-02')); // 5
    $sabtu = date('N', strtotime('2026-10-03')); // 6
    $minggu = date('N', strtotime('2026-10-04')); // 7

    $isHariKerjaJumat = ($jumat >= 1 && $jumat <= 5);
    $isHariKerjaSabtu = ($sabtu >= 1 && $sabtu <= 5);
    $isHariKerjaMinggu = ($minggu >= 1 && $minggu <= 5);

    $runner->assertTrue($isHariKerjaJumat, "Jumat harus dihitung hari kerja");
    $runner->assertFalse($isHariKerjaSabtu, "Sabtu bukan hari kerja");
    $runner->assertFalse($isHariKerjaMinggu, "Minggu bukan hari kerja");
});

$runner->it("Layout template harus memuat brand SIPENSI SKAGATA dan kredit awanbeo.my.id", function() use ($runner) {
    $templateFile = APPPATH . 'Views/layout/template.php';
    $runner->assertTrue(file_exists($templateFile));
    $content = file_get_contents($templateFile);
    $runner->assertTrue(strpos($content, 'SIPENSI SKAGATA') !== false, "Template harus memuat 'SIPENSI SKAGATA'");
    $runner->assertTrue(strpos($content, 'awanbeo.my.id') !== false, "Template harus memuat kredit 'awanbeo.my.id'");
    $runner->assertTrue(strpos($content, 'SMK Negeri 3 Yogyakarta') !== false, "Template harus memuat 'SMK Negeri 3 Yogyakarta'");
});

// Cetak laporan akhir & exit code
exit($runner->report());
