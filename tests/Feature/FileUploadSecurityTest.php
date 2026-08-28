<?php

namespace Tests\Feature;

use App\Helpers\ImageUploaderTrait;
use App\Helpers\LivewireUploadTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    use ImageUploaderTrait;
    use LivewireUploadTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * اختبار حظر الملفات التنفيذية وملفات الويب شيل
     */
    public function test_blocks_dangerous_executable_extensions()
    {
        $blockedExtensions = [
            'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps',
            'html', 'htm', 'xhtml', 'svg', 'js', 'jsp', 'asp', 'aspx',
            'cgi', 'pl', 'sh', 'bash', 'exe', 'bat', 'cmd', 'vbs', 'py',
            'env', 'htaccess', 'ini', 'sql'
        ];

        foreach ($blockedExtensions as $ext) {
            $this->assertTrue($this->isExtensionBlocked($ext), "Extension .{$ext} should be marked as blocked.");
            $this->assertFalse($this->isExtensionAllowed($ext), "Extension .{$ext} should not be allowed.");

            $file = UploadedFile::fake()->create("exploit.{$ext}", 100);
            $generated = $this->createFileName($file);

            $this->assertNull($generated, "Uploading a .{$ext} file should return null and be blocked.");
        }
    }

    /**
     * اختبار كشف هجمات الامتداد المزدوج (Double Extension)
     */
    public function test_detects_and_blocks_double_extension_attacks()
    {
        $dangerousNames = [
            'shell.php.jpg',
            'payload.phtml.png',
            'backdoor.phar.gif',
            'exploit.exe.pdf',
            'script.sh.docx',
            'test.php.tmp.jpg',
        ];

        foreach ($dangerousNames as $name) {
            $this->assertTrue($this->hasDoubleExtensionRisk($name), "Filename '{$name}' must be flagged as double extension risk.");

            $file = UploadedFile::fake()->create($name, 100);
            $generated = $this->createFileName($file);

            $this->assertNull($generated, "File '{$name}' with double extension risk must be rejected.");
        }
    }

    /**
     * اختبار صد تزييف نوع المحتوى (MIME-Type Spoofing)
     * رفع ملف PHP تحت اسم ملف صورة
     */
    public function test_blocks_mime_spoofed_files()
    {
        // إنشاء ملف مؤقت يحتوي على كود PHP حقيقي لكن بامتداد jpg
        $tempPath = tempnam(sys_get_temp_dir(), 'test_spoof') . '.jpg';
        file_put_contents($tempPath, "<?php echo 'malicious payload'; system(\$_GET['cmd']); ?>");

        $file = new UploadedFile($tempPath, 'avatar.jpg', 'text/x-php', null, true);

        $generated = $this->createFileName($file);
        $this->assertNull($generated, "MIME-spoofed file containing PHP code should be rejected.");

        @unlink($tempPath);
    }

    /**
     * اختبار كشف وفحص محتوى الملفات المشبوهة والوسوم الخبيثة
     */
    public function test_rejects_files_with_malicious_script_tags()
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'test_malicious') . '.png';
        // ترويسة PNG صالحة لكن متبوعة بكود PHP
        file_put_contents($tempPath, "\x89PNG\r\n\x1a\n" . "<?php eval(\$_POST['c']); ?>");

        $file = new UploadedFile($tempPath, 'clean_image.png', 'image/png', null, true);

        $generated = $this->createFileName($file);
        $this->assertNull($generated, "Image containing embedded PHP code should be rejected.");

        @unlink($tempPath);
    }

    /**
     * اختبار قبول الصور والملفات النظامية الصالحة
     */
    public function test_accepts_valid_image_and_document_files()
    {
        // 1. صورة JPEG نظيفة
        $tempJpeg = tempnam(sys_get_temp_dir(), 'test_valid') . '.jpg';
        file_put_contents($tempJpeg, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00");
        $jpegFile = new UploadedFile($tempJpeg, 'photo.jpg', 'image/jpeg', null, true);

        $generatedJpeg = $this->createFileName($jpegFile);
        $this->assertNotNull($generatedJpeg, "Valid JPEG image should be accepted.");
        $this->assertStringEndsWith('.jpg', $generatedJpeg);
        @unlink($tempJpeg);

        // 2. مستند PDF نظيف
        $tempPdf = tempnam(sys_get_temp_dir(), 'test_valid') . '.pdf';
        file_put_contents($tempPdf, "%PDF-1.4\n%Valid PDF test content\n%%EOF");
        $pdfFile = new UploadedFile($tempPdf, 'document.pdf', 'application/pdf', null, true);

        $generatedPdf = $this->createFileName($pdfFile);
        $this->assertNotNull($generatedPdf, "Valid PDF document should be accepted.");
        $this->assertStringEndsWith('.pdf', $generatedPdf);
        @unlink($tempPdf);
    }

    /**
     * اختبار الحماية من هجمات تجاوز المسارات (Path Traversal Protection)
     */
    public function test_sanitizes_path_traversal_attempts()
    {
        $maliciousPath = '../../../../etc/passwd';
        $sanitizedFile = $this->sanitizeFileName($maliciousPath);
        $this->assertEquals('passwd', $sanitizedFile);

        $maliciousFolder = '../../../secret_folder/../';
        $sanitizedFolder = $this->sanitizeFolderName($maliciousFolder);
        $this->assertStringNotContainsString('..', $sanitizedFolder);
        $this->assertStringNotContainsString('/', $sanitizedFolder);
    }

    /**
     * اختبار التحقق الأمني في base64Image
     */
    public function test_base64_image_security()
    {
        // كود خبيث مشفر بـ base64
        $maliciousPayload = 'data:image/png;base64,' . base64_encode('<?php echo "pwned"; ?>');
        $result = $this->base64Image($maliciousPayload, 'test.png');
        $this->assertFalse($result, "base64 containing PHP code should be rejected.");

        // صورة غير صالحة
        $invalidBase64 = 'invalid_base64_string@@@';
        $resultInvalid = $this->base64Image($invalidBase64, 'test.png');
        $this->assertFalse($resultInvalid, "Invalid base64 string should be rejected.");
    }

    /**
     * اختبار LivewireUploadTrait لصد الملفات الخبيثة في المجلد المؤقت
     */
    public function test_livewire_upload_trait_blocks_malicious_files()
    {
        $tmpDir = public_path('uploads/tmp');
        if (!File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0755, true);
        }

        // ملف PHP مؤقت
        $badTmp = 'temp_shell.php';
        file_put_contents($tmpDir . '/' . $badTmp, '<?php echo "bad"; ?>');

        $files = [
            [
                'tmpFilename' => $badTmp,
                'name' => 'test.php',
                'size' => 100,
            ]
        ];

        $this->saveAttachments($files, 'App\\Models\\User', 1);

        // التأكد من عدم نقله إلى uploads/files
        $this->assertFileDoesNotExist(public_path('uploads/files/' . $badTmp));

        @unlink($tmpDir . '/' . $badTmp);
    }
}

