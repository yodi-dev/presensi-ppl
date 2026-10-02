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
}
