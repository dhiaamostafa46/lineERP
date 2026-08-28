<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Image;
use Throwable;

trait ImageUploaderTrait
{
    /**
     * القائمة السوداء الصارمة للامتدادات الخطرة والممنوعة منعاً باتاً
     */
    protected static array $blockedExtensions = [
        // سكربتات PHP
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'pht', 'phpt', 'pgif',
        // سكربتات الويب والـ HTML / SVG
        'html', 'htm', 'xhtml', 'shtml', 'shtm', 'svg', 'svgz', 'xml',
        // سكربتات العميل والخادم
        'js', 'jsp', 'jspx', 'asp', 'aspx', 'axd', 'ashx', 'asmx',
        // أوامر وسكربتات الشل والنظام
        'cgi', 'pl', 'sh', 'bash', 'zsh', 'exe', 'bat', 'cmd', 'com',
        'vbs', 'vbe', 'wsf', 'wsh', 'ps1', 'ps2', 'py', 'pyc', 'pyo',
        // المكتبات والملفات الثنائية
        'rb', 'dll', 'so', 'dylib', 'bin', 'msi', 'jar', 'war',
        // ملفات التهيئة والإعدادات الحساسة
        'htaccess', 'htpasswd', 'env', 'ini', 'config', 'sql', 'bak', 'swp'
    ];

    /**
     * القائمة البيضاء للامتدادات المسموح برفعها
     */
    protected static array $allowedExtensions = [
        // الصور
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'ico',
        // المستندات والملفات المكتبية
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'odt', 'ods', 'ppt', 'pptx',
        // الأرشيف المضغوط المعتمد
        'zip', 'rar', '7z', 'tar', 'gz'
    ];

    /**
     * قائمة الامتدادات المسموحة للصور فقط
     */
    protected static array $allowedImageExtensions = [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'ico'
    ];

    /**
     * جدول مطابقة الامتدادات مع أنواع MIME الحقيقية
     */
    protected static array $allowedMimeTypes = [
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png'  => ['image/png', 'image/x-png'],
        'webp' => ['image/webp'],
        'gif'  => ['image/gif'],
        'bmp'  => ['image/bmp', 'image/x-bmp', 'image/x-ms-bmp'],
        'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon', 'image/ico'],
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword', 'application/octet-stream'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/octet-stream'
        ],
        'xls'  => ['application/vnd.ms-excel', 'application/msexcel', 'application/octet-stream'],
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/octet-stream'
        ],
        'csv'  => ['text/csv', 'text/plain', 'application/csv', 'text/x-csv', 'application/vnd.ms-excel', 'text/x-comma-separated-values'],
        'txt'  => ['text/plain'],
        'rtf'  => ['application/rtf', 'text/rtf'],
        'odt'  => ['application/vnd.oasis.opendocument.text', 'application/zip'],
        'ods'  => ['application/vnd.oasis.opendocument.spreadsheet', 'application/zip'],
        'ppt'  => ['application/vnd.ms-powerpoint', 'application/octet-stream'],
        'pptx' => [
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/octet-stream'
        ],
        'zip'  => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
        'rar'  => ['application/vnd.rar', 'application/x-rar-compressed', 'application/x-rar', 'application/octet-stream'],
        '7z'   => ['application/x-7z-compressed', 'application/octet-stream'],
        'tar'  => ['application/x-tar', 'application/tar'],
        'gz'   => ['application/gzip', 'application/x-gzip'],
    ];

    /**
     * البصمات السحرية (Magic Bytes) لتأكيد صحة ترويسات الملفات الشائعة
     */
    protected static array $magicSignatures = [
        'jpg'  => ["\xFF\xD8\xFF"],
        'jpeg' => ["\xFF\xD8\xFF"],
        'png'  => ["\x89PNG\r\n\x1a\n"],
        'gif'  => ['GIF87a', 'GIF89a'],
        'bmp'  => ['BM'],
        'pdf'  => ['%PDF-'],
        'zip'  => ["PK\x03\x04", "PK\x05\x06", "PK\x07\x08"],
        'docx' => ["PK\x03\x04"],
        'xlsx' => ["PK\x03\x04"],
        'pptx' => ["PK\x03\x04"],
    ];

    /**
     * الحد الأقصى الافتراضي لحجم الملف بالبايت (20 ميجابايت)
     */
    protected static int $maxFileSizeBytes = 20 * 1024 * 1024;

    /**
     * إنشاء اسم آمن ومشفر وفريد للملف المرفوع مع التحقق الأمني الشامل
     *
     * @param mixed $file
     * @return string|null
     */
    public function createFileName($file)
    {
        try {
            // إذا كان المدخل نصاً (اسم ملف موجود مسبقاً)
            if (is_string($file)) {
                $sanitized = $this->sanitizeFileName($file);
                $ext = strtolower(pathinfo($sanitized, PATHINFO_EXTENSION));
                if ($this->isExtensionBlocked($ext)) {
                    Log::warning('Security Alert: Blocked existing file string with dangerous extension: ' . $sanitized, [
                        'ip' => request()->ip() ?? 'N/A',
                        'user_id' => auth()->id() ?? 'N/A',
                    ]);
                    return null;
                }
                return $sanitized;
            }

            // التحقق من صلاحية كائن الملف
            if (!$this->isValidUploadObject($file)) {
                return null;
            }

            // استخراج وتدقيق الامتداد
            $originalName = method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'upload';
            $extension = $this->extractSafeExtension($file);

            if (!$extension || !$this->isExtensionAllowed($extension) || $this->isExtensionBlocked($extension)) {
                Log::warning('Security Alert: Blocked upload attempt with unauthorized extension', [
                    'original_name' => $originalName,
                    'detected_extension' => $extension,
                    'ip' => request()->ip() ?? 'N/A',
                    'user_id' => auth()->id() ?? 'N/A',
                ]);
                return null;
            }

            // فحص هجمات الامتداد المزدوج (Double Extension Attack) مثل shell.php.jpg
            if ($this->hasDoubleExtensionRisk($originalName)) {
                Log::warning('Security Alert: Double extension risk detected in uploaded file', [
                    'original_name' => $originalName,
                    'ip' => request()->ip() ?? 'N/A',
                    'user_id' => auth()->id() ?? 'N/A',
                ]);
                return null;
            }

            // التحقق من نوع الـ MIME الحقيقي وتطابقه مع الامتداد
            $realMime = $this->getRealMimeType($file);
            if (!$this->isMimeTypeAllowed($extension, $realMime)) {
                Log::warning('Security Alert: MIME-type spoofing attempt detected', [
                    'original_name' => $originalName,
                    'extension' => $extension,
                    'detected_mime' => $realMime,
                    'ip' => request()->ip() ?? 'N/A',
                    'user_id' => auth()->id() ?? 'N/A',
                ]);
                return null;
            }

            // فحص البصمة السحرية للملف (Magic Bytes)
            if (!$this->matchesMagicBytes($file, $extension)) {
                Log::warning('Security Alert: File magic bytes mismatch', [
                    'original_name' => $originalName,
                    'extension' => $extension,
                    'ip' => request()->ip() ?? 'N/A',
                    'user_id' => auth()->id() ?? 'N/A',
                ]);
                return null;
            }

            // فحص محتوى الملف لمنع الأكواد الخبيثة وسكربتات PHP/HTML
            if (!$this->isContentSafe($file)) {
                Log::warning('Security Alert: Malicious script content detected inside uploaded file', [
                    'original_name' => $originalName,
                    'ip' => request()->ip() ?? 'N/A',
                    'user_id' => auth()->id() ?? 'N/A',
                ]);
                return null;
            }

            // التحقق من حجم الملف
            $fileSize = method_exists($file, 'getSize') ? $file->getSize() : 0;
            if ($fileSize > static::$maxFileSizeBytes) {
                Log::warning('File Upload Warning: File exceeded max size limit', [
                    'original_name' => $originalName,
                    'size' => $fileSize,
                    'max_allowed' => static::$maxFileSizeBytes,
                ]);
                return null;
            }

            // توليد اسم عشوائي آمن ومشفر وفريد تماماً
            $cleanBaseName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $shortName = $cleanBaseName ? substr($cleanBaseName, 0, 20) . '_' : '';
            $randomToken = Str::random(24);
            $fileName = date('YmdHis') . '_' . $shortName . $randomToken . '.' . $extension;

            return $fileName;
        } catch (Throwable $e) {
            Log::error('Error in createFileName: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * استخراج الامتداد بشكل آمن ومطابق
     */
    protected function extractSafeExtension($file): string
    {
        $extension = '';

        if (method_exists($file, 'getClientOriginalExtension')) {
            $extension = $file->getClientOriginalExtension();
        }

        if (!$extension && method_exists($file, 'extension')) {
            $extension = $file->extension();
        }

        if (!$extension && method_exists($file, 'getClientOriginalName')) {
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        }

        return strtolower(trim($extension));
    }

    /**
     * استخراج نوع الـ MIME الحقيقي للملف من المحتوى الفعلي
     */
    public function getRealMimeType($file): ?string
    {
        try {
            $path = null;
            if (is_string($file) && file_exists($file)) {
                $path = $file;
            } elseif (is_object($file) && method_exists($file, 'getRealPath')) {
                $path = $file->getRealPath();
            }

            if ($path && file_exists($path)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mime = finfo_file($finfo, $path);
                    finfo_close($finfo);
                    if ($mime) {
                        return strtolower(trim($mime));
                    }
                }
                if (function_exists('mime_content_type')) {
                    $mime = mime_content_type($path);
                    if ($mime) {
                        return strtolower(trim($mime));
                    }
                }
            }

            if (is_object($file) && method_exists($file, 'getMimeType')) {
                $mime = $file->getMimeType();
                if ($mime) {
                    return strtolower(trim($mime));
                }
            }

            return null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * التحقق من مطابقة نوع الـ MIME مع الامتداد
     */
    public function isMimeTypeAllowed(string $extension, ?string $mimeType): bool
    {
        $ext = strtolower(trim($extension));
        if (!$mimeType) {
            return false;
        }

        $mime = strtolower(trim($mimeType));

        if (!isset(static::$allowedMimeTypes[$ext])) {
            return false;
        }

        return in_array($mime, static::$allowedMimeTypes[$ext], true);
    }

    /**
     * التحقق من البصمات السحرية للملف
     */
    protected function matchesMagicBytes($file, string $extension): bool
    {
        $ext = strtolower(trim($extension));
        if (!isset(static::$magicSignatures[$ext])) {
            return true; // إذا لم تكن هناك بصمة محددة، نعتمد على MIME والفحص الشامل
        }

        try {
            $path = null;
            if (is_string($file) && file_exists($file)) {
                $path = $file;
            } elseif (is_object($file) && method_exists($file, 'getRealPath')) {
                $path = $file->getRealPath();
            }

            if (!$path || !file_exists($path)) {
                return false;
            }

            $handle = @fopen($path, 'rb');
            if (!$handle) {
                return false;
            }

            $header = fread($handle, 16);
            fclose($handle);

            if ($header === false || strlen($header) === 0) {
                return false;
            }

            foreach (static::$magicSignatures[$ext] as $sig) {
                if (str_starts_with($header, $sig)) {
                    return true;
                }
            }

            // معالجة خاصة لملفات WEBP
            if ($ext === 'webp') {
                if (str_starts_with($header, 'RIFF') && str_contains($header, 'WEBP')) {
                    return true;
                }
            }

            return false;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * التحقق مما إذا كان الامتداد ممنوعاً
     */
    public function isExtensionBlocked(string $extension): bool
    {
        return in_array(strtolower(trim($extension)), static::$blockedExtensions, true);
    }

    /**
     * التحقق مما إذا كان الامتداد مسموحاً
     */
    public function isExtensionAllowed(string $extension): bool
    {
        return in_array(strtolower(trim($extension)), static::$allowedExtensions, true);
    }

    /**
     * التحقق من خطر الامتدادات المزدوجة (مثل shell.php.png أو evil.phtml.jpg)
     */
    public function hasDoubleExtensionRisk(string $fileName): bool
    {
        $cleanName = str_replace(["\0", "\\", "/"], '', $fileName);
        $parts = explode('.', strtolower($cleanName));
        if (count($parts) > 2) {
            for ($i = 0; $i < count($parts) - 1; $i++) {
                if (in_array($parts[$i], static::$blockedExtensions, true)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * التحقق من سلامة محتوى الملف وخلوه من سكربتات PHP/HTML/XSS الخبيثة
     */
    public function isContentSafe($file): bool
    {
        try {
            $path = null;
            if (is_string($file) && file_exists($file)) {
                $path = $file;
            } elseif (is_object($file) && method_exists($file, 'getRealPath')) {
                $path = $file->getRealPath();
            }

            if (!$path || !file_exists($path)) {
                return false;
            }

            $size = @filesize($path);
            if ($size === 0) {
                return false;
            }

            // قراءة أول 8 كيلوبايت من الملف لفحص الترويسة
            $handle = @fopen($path, 'rb');
            if (!$handle) {
                return false;
            }

            $buffer = fread($handle, 8192);

            // قراءة آخر 4 كيلوبايت من الملف لفحص الأكواد المحقونة في النهاية
            $tail = '';
            if ($size > 8192) {
                fseek($handle, max(0, $size - 4096));
                $tail = fread($handle, 4096) ?: '';
            }
            fclose($handle);

            if ($buffer === false) {
                return false;
            }

            $contentSample = strtolower($buffer . ' ' . $tail);

            // فحص وجود وسوم PHP وسكربتات XSS المحقونة
            $dangerousPatterns = [
                '<?php',
                '<?=',
                '<script',
                '</script>',
                'language="php"',
                'language=\'php\'',
                '__halt_compiler()',
                '<%',
                '<!doctype html',
                '<html',
                '<body',
            ];

            foreach ($dangerousPatterns as $pattern) {
                if (str_contains($contentSample, $pattern)) {
                    return false;
                }
            }

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * التحقق من صلاحية كائن الرفع
     */
    protected function isValidUploadObject($file): bool
    {
        if (!$file || !is_object($file)) {
            return false;
        }

        if (method_exists($file, 'isValid') && !$file->isValid()) {
            return false;
        }

        return true;
    }

    /**
     * تعقيم اسم الملف لمنع Path Traversal
     */
    public function sanitizeFileName(?string $fileName): string
    {
        if (!$fileName) {
            return '';
        }

        // استخراج اسم الملف الأساسي أولاً بعد توحيد الفواصل
        $base = basename(str_replace(["\0", "\\"], '/', $fileName));
        // إزالة أي رموز تجاوز مسار إضافية ومحارف غير آمنة لنظام الملفات
        $clean = str_replace(['..', "\0"], '', $base);
        return preg_replace('/[^\w\.\-_]/', '', $clean) ?? '';
    }

    /**
     * تعقيم اسم المجلد لمنع Path Traversal
     */
    public function sanitizeFolderName(?string $folderName): string
    {
        if (!$folderName) {
            return 'general';
        }

        // إزالة أي رموز تجاوز مسار أو محارف خاصة
        $clean = str_replace(['..', "\0", "\\"], '', $folderName);
        $clean = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $clean);
        $clean = trim($clean, '/');

        return $clean ?: 'general';
    }

    /**
     * التأكد من وجود المجلد وصلاحياته 0755
     */
    protected function ensureDirectoryExists(string $path): bool
    {
        try {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            return true;
        } catch (Throwable $e) {
            Log::error('Directory creation failed: ' . $path . ' Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حفظ المرفقات في مجلد السلف Advances
     */
    private function saveAttachmentFile($file, $fileName)
    {
        try {
            if (!$fileName || !is_object($file) || !method_exists($file, 'move')) {
                return false;
            }

            $uploadPath = public_path('uploads/images/Advances');
            $this->ensureDirectoryExists($uploadPath);

            $cleanFileName = $this->sanitizeFileName($fileName);
            $file->move($uploadPath, $cleanFileName);
            return true;
        } catch (Throwable $e) {
            Log::error('saveAttachmentFile Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حفظ الملف بحسب المجلد المحدد مع الفحص والتعقيم الكامل
     */
    public function saveFileType($file, $fileName, $folderName)
    {
        try {
            if (!$fileName || !is_object($file) || !method_exists($file, 'move')) {
                return false;
            }

            $cleanFolder = $this->sanitizeFolderName($folderName);
            $destinationPath = public_path('uploads/images/' . $cleanFolder);
            $this->ensureDirectoryExists($destinationPath);

            $cleanFileName = $this->sanitizeFileName($fileName);
            $file->move($destinationPath, $cleanFileName);
            return true;
        } catch (Throwable $e) {
            Log::error('saveFileType Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف الملف بأمان
     */
    public function deleteFile($fileName, $folderName)
    {
        try {
            if (!$fileName) {
                return false;
            }

            $cleanFolder = $this->sanitizeFolderName($folderName);
            $cleanFile = $this->sanitizeFileName($fileName);
            $filePath = public_path('uploads/images/' . $cleanFolder . '/' . $cleanFile);

            if (File::exists($filePath) && File::isFile($filePath)) {
                File::delete($filePath);
                return true;
            }

            return false;
        } catch (Throwable $e) {
            Log::error('deleteFile Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف ملف مرفق من Advances
     */
    private function deleteAttachmentFile($fileName)
    {
        try {
            if (!$fileName) {
                return false;
            }

            $cleanFile = $this->sanitizeFileName($fileName);
            $filePath = public_path('uploads/images/Advances/' . $cleanFile);

            if (File::exists($filePath) && File::isFile($filePath)) {
                File::delete($filePath);
                return true;
            }
            return false;
        } catch (Throwable $e) {
            Log::error('deleteAttachmentFile Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * الحصول على مسار الملف الكامل
     */
    public function getAttachmentPathAttribute()
    {
        if ($this->attachment) {
            $cleanFile = $this->sanitizeFileName($this->attachment);
            if ($cleanFile && File::exists(public_path('uploads/images/Advances/' . $cleanFile))) {
                return 'uploads/images/Advances/' . $cleanFile;
            }
        }
        return null;
    }

    public function getAttachmentInfoAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        $cleanFile = $this->sanitizeFileName($this->attachment);
        $path = public_path('uploads/images/Advances/' . $cleanFile);

        if (!$cleanFile || !File::exists($path) || !File::isFile($path)) {
            return null;
        }

        return [
            'name' => $cleanFile,
            'path' => 'uploads/images/Advances/' . $cleanFile,
            'size' => File::size($path),
            'size_formatted' => $this->formatBytes(File::size($path)),
            'extension' => File::extension($path),
            'mime' => File::mimeType($path),
            'url' => asset('uploads/images/Advances/' . $cleanFile),
        ];
    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= 1 << 10 * $pow;

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * حفظ الملف في uploads/files
     */
    public function saveFile($file, $fileName)
    {
        try {
            if (!$fileName || !is_object($file) || !method_exists($file, 'move')) {
                return false;
            }

            $destination = public_path('uploads/files');
            $this->ensureDirectoryExists($destination);

            $cleanFileName = $this->sanitizeFileName($fileName);
            $file->move($destination, $cleanFileName);
            return true;
        } catch (Throwable $e) {
            Log::error('saveFile Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حفظ الملف الأصلي في uploads/files/original
     */
    public function SaveFileOriginal($file, $fileName)
    {
        try {
            if (!$fileName || !is_object($file) || !method_exists($file, 'move')) {
                return false;
            }

            $destination = public_path('uploads/files/original');
            $this->ensureDirectoryExists($destination);

            $cleanFileName = $this->sanitizeFileName($fileName);
            $file->move($destination, $cleanFileName);
            return true;
        } catch (Throwable $e) {
            Log::error('SaveFileOriginal Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حفظ الملفات في مجلد Holiday
     */
    public function saveHolidayFile($file, $fileName)
    {
        try {
            if (!$fileName || !is_object($file) || !method_exists($file, 'move')) {
                return false;
            }

            $destinationPath = public_path('uploads/images/Holiday');
            $this->ensureDirectoryExists($destinationPath);

            $cleanFileName = $this->sanitizeFileName($fileName);
            $file->move($destinationPath, $cleanFileName);
            return true;
        } catch (Throwable $e) {
            Log::error('Holiday File Upload Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حفظ الملفات في مجلد Justification
     */
    public function saveJustificationFile($file, $fileName)
    {
        try {
            if (!$fileName || !is_object($file) || !method_exists($file, 'move')) {
                return false;
            }

            $destinationPath = public_path('uploads/images/Justification');
            $this->ensureDirectoryExists($destinationPath);

            $cleanFileName = $this->sanitizeFileName($fileName);
            $file->move($destinationPath, $cleanFileName);
            return true;
        } catch (Throwable $e) {
            Log::error('Justification File Upload Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف ملف من مجلد Holiday
     */
    public function deleteHolidayFile($fileName)
    {
        try {
            if (!$fileName) {
                return false;
            }

            $cleanFile = $this->sanitizeFileName($fileName);
            $filePath = public_path('uploads/images/Holiday/' . $cleanFile);
            if (File::exists($filePath) && File::isFile($filePath)) {
                File::delete($filePath);
                return true;
            }
            return false;
        } catch (Throwable $e) {
            Log::error('Holiday File Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * التحقق من أن الملف صورة صالحة قبل المعالجة
     */
    public function isValidImageFile($file): bool
    {
        try {
            $path = null;
            if (is_string($file)) {
                $path = $file;
            } elseif (is_object($file) && method_exists($file, 'getRealPath')) {
                $path = $file->getRealPath();
            }

            if (!$path || !file_exists($path)) {
                return false;
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext && !in_array($ext, static::$allowedImageExtensions, true)) {
                return false;
            }

            $imageInfo = @getimagesize($path);
            if ($imageInfo === false) {
                return false;
            }

            // التحقق من أن نوع الصورة الناتج معتمد
            $validTypes = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP, IMAGETYPE_WEBP, IMAGETYPE_ICO];
            return in_array($imageInfo[2], $validTypes, true);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * حفظ الصورة بالحجم الأصلي
     */
    public function originalImage($file, $current_name)
    {
        try {
            if (!$current_name) {
                return false;
            }

            $destination = public_path('uploads/images/original');
            $this->ensureDirectoryExists($destination);

            $cleanName = $this->sanitizeFileName($current_name);

            if ($this->isValidImageFile($file)) {
                Image::make($file)->save($destination . '/' . $cleanName);
                return true;
            } elseif (is_object($file) && method_exists($file, 'move')) {
                $file->move($destination, $cleanName);
                return true;
            }
            return false;
        } catch (Throwable $e) {
            Log::error('originalImage Error: ' . $e->getMessage());
            return false;
        }
    }

    public function logoImage($file, $current_name)
    {
        try {
            if (!$current_name) {
                return false;
            }

            $destination = public_path('uploads/images/logo');
            $this->ensureDirectoryExists($destination);

            $cleanName = $this->sanitizeFileName($current_name);

            if ($this->isValidImageFile($file)) {
                Image::make($file)->save($destination . '/' . $cleanName);
                return true;
            } elseif (is_object($file) && method_exists($file, 'move')) {
                $file->move($destination, $cleanName);
                return true;
            }
            return false;
        } catch (Throwable $e) {
            Log::error('logoImage Error: ' . $e->getMessage());
            return false;
        }
    }

    public function signatureImage($file, $current_name)
    {
        try {
            if (!$current_name) {
                return false;
            }

            $destination = public_path('uploads/images/signature');
            $this->ensureDirectoryExists($destination);

            $cleanName = $this->sanitizeFileName($current_name);

            if ($this->isValidImageFile($file)) {
                Image::make($file)->save($destination . '/' . $cleanName);
                return true;
            } elseif (is_object($file) && method_exists($file, 'move')) {
                $file->move($destination, $cleanName);
                return true;
            }
            return false;
        } catch (Throwable $e) {
            Log::error('signatureImage Error: ' . $e->getMessage());
            return false;
        }
    }

    public function mediumImage($file, $current_name, $width = 600, $height = 300)
    {
        try {
            if (!$current_name) {
                return false;
            }

            $destination = public_path('uploads/images/medium');
            $this->ensureDirectoryExists($destination);

            $cleanName = $this->sanitizeFileName($current_name);

            if ($this->isValidImageFile($file)) {
                Image::make($file)
                    ->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save($destination . '/' . $cleanName);
                return true;
            }
            return false;
        } catch (Throwable $e) {
            Log::error('mediumImage Error: ' . $e->getMessage());
            return false;
        }
    }

    public function thumbImage($file, $current_name, $width = 100, $height = 100)
    {
        try {
            if (!$current_name) {
                return false;
            }

            $destination = public_path('uploads/images/thumbnail');
            $this->ensureDirectoryExists($destination);

            $cleanName = $this->sanitizeFileName($current_name);

            if ($this->isValidImageFile($file)) {
                Image::make($file)
                    ->resize($width, $height)
                    ->save($destination . '/' . $cleanName);
                return true;
            }
            return false;
        } catch (Throwable $e) {
            Log::error('thumbImage Error: ' . $e->getMessage());
            return false;
        }
    }

    public function base64Image($file, $fileName)
    {
        try {
            if (!$file || !$fileName || !is_string($file)) {
                return false;
            }

            // التحقق من أن المدخل هو base64 صالح للصورة وليس مسار محلي أو رابط خارجي (منع SSRF)
            if (!preg_match('/^data:image\/(jpeg|png|webp|gif|bmp);base64,([A-Za-z0-9+\/=\r\n]+)$/', $file, $matches)) {
                // إذا لم يحتوي على رأس data URI، التحقق من أنه base64 خالص
                $decoded = base64_decode($file, true);
                if ($decoded === false) {
                    Log::warning('Security Alert: Invalid base64 image data supplied', [
                        'ip' => request()->ip() ?? 'N/A',
                    ]);
                    return false;
                }
            } else {
                $decoded = base64_decode($matches[2], true);
                if ($decoded === false) {
                    return false;
                }
            }

            // فحص البايتات المفكوكة لمنع الأكواد الخبيثة
            $sample = strtolower(substr($decoded, 0, 4096));
            if (str_contains($sample, '<?php') || str_contains($sample, '<?=') || str_contains($sample, '<script')) {
                Log::warning('Security Alert: Malicious payload found inside base64 image', [
                    'ip' => request()->ip() ?? 'N/A',
                ]);
                return false;
            }

            $destination = public_path('uploads/images/original');
            $this->ensureDirectoryExists($destination);

            $cleanName = $this->sanitizeFileName($fileName);

            Image::make($file)->save($destination . '/' . $cleanName);
            return true;
        } catch (Throwable $e) {
            Log::error('base64Image Error: ' . $e->getMessage());
            return false;
        }
    }
}
