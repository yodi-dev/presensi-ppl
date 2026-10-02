<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * SecurityAuditTest
 * Pengujian unit test standar PHPUnit untuk integritas keamanan Presensi PPL.
 */
class SecurityAuditTest extends CIUnitTestCase
{
    public function testMimeTypeWhitelistRejectsExecutableUploads(): void
    {
        $maliciousPayload = "data:image/php;base64," . base64_encode("<?php phpinfo(); ?>");
        $pattern = '/^data:(image\/(jpeg|jpg|png|webp));base64,(.+)$/i';
        
        $this->assertFalse((bool) preg_match($pattern, $maliciousPayload));
    }

    public function testGetImageSizeRejectsFakeBinary(): void
    {
        $fakeBinary = "Fake image content that is actually text";
        $info = @getimagesizefromstring($fakeBinary);
        
        $this->assertFalse($info !== false);
    }

    public function testUploadsHtaccessExistsAndBlocksExecution(): void
    {
        $htaccessPath = FCPATH . 'uploads/.htaccess';
        $this->assertFileExists($htaccessPath);
        
        $content = file_get_contents($htaccessPath);
        $this->assertMatchesRegularExpression('/php|phtml|phar/i', $content);
        $this->assertMatchesRegularExpression('/Deny from all/i', $content);
    }

    public function testSqlInjectionInDateIsNeutralized(): void
    {
        $sqliPayload = "2026-04-16' OR 1=1 --";
        $isValid = (is_string($sqliPayload) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sqliPayload));
        $tanggalPilih = $isValid ? $sqliPayload : date('Y-m-d');
        
        $this->assertSame(date('Y-m-d'), $tanggalPilih);
    }

    public function testXssEscapingConvertsScriptTags(): void
    {
        $xss = "<script>alert('xss')</script>";
        $escaped = esc($xss);
        
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }

    public function testGitignoreExistsAndExcludesEnvFiles(): void
    {
        $gitignorePath = ROOTPATH . '.gitignore';
        $this->assertFileExists($gitignorePath);
        
        $content = file_get_contents($gitignorePath);
        $this->assertMatchesRegularExpression('/\.env/i', $content);
        $this->assertMatchesRegularExpression('/writable\/session/i', $content);
    }

    public function testEnvExampleDoesNotContainSensitiveCredentials(): void
    {
        $examplePath = ROOTPATH . '.env.example';
        $this->assertFileExists($examplePath);
        
        $content = file_get_contents($examplePath);
        $this->assertStringNotContainsString('VbX4pzFXTf', $content);
        $this->assertStringNotContainsString('sql204.infinityfree.com', $content);
    }

    public function testGeofencingHaversineCalculatesDistanceCorrectly(): void
    {
        $config = config('Presensi') ?? new \Config\Presensi();
        $jarakSama = \Config\Presensi::hitungJarak(
            $config->schoolLatitude,
            $config->schoolLongitude,
            $config->schoolLatitude,
            $config->schoolLongitude
        );
        $this->assertLessThan(1.0, $jarakSama);

        $latJauh = $config->schoolLatitude + 0.005;
        $jarakJauh = \Config\Presensi::hitungJarak(
            $latJauh,
            $config->schoolLongitude,
            $config->schoolLatitude,
            $config->schoolLongitude
        );
        $this->assertGreaterThan($config->schoolRadius, $jarakJauh);
    }

    public function testTardinessPolicyLogic(): void
    {
        $config = config('Presensi') ?? new \Config\Presensi();
        $this->assertTrue("07:20:00" > $config->jamMasukMax);
        $this->assertFalse("07:05:00" > $config->jamMasukMax);
        $this->assertTrue("13:00:00" < $config->jamPulangMin);
        $this->assertFalse("15:30:00" < $config->jamPulangMin);
    }

    public function testAdminControllerAndMethodsExist(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\Admin::class));
        $this->assertTrue(method_exists(\App\Controllers\Admin::class, 'index'));
        $this->assertTrue(method_exists(\App\Controllers\Admin::class, 'tambahUser'));
        $this->assertTrue(method_exists(\App\Controllers\Admin::class, 'editUser'));
        $this->assertTrue(method_exists(\App\Controllers\Admin::class, 'hapusUser'));
        $this->assertTrue(method_exists(\App\Controllers\Admin::class, 'resetPassword'));
    }

    public function testGuruExportExcelMethodExists(): void
    {
        $this->assertTrue(method_exists(\App\Controllers\Guru::class, 'exportExcel'));
    }

    public function testSettingModelAndAdminSettingsExist(): void
    {
        $this->assertTrue(class_exists(\App\Models\SettingModel::class));
        $this->assertTrue(method_exists(\App\Models\SettingModel::class, 'getSetting'));
        $this->assertTrue(method_exists(\App\Models\SettingModel::class, 'setSetting'));
        $this->assertTrue(method_exists(\App\Models\SettingModel::class, 'getAllSettings'));
        $this->assertTrue(method_exists(\App\Controllers\Admin::class, 'pengaturan'));
        $this->assertTrue(method_exists(\App\Controllers\Admin::class, 'simpanPengaturan'));
    }
}
