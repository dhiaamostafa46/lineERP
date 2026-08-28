<?php

namespace App\Helpers;

use App\Models\Attachment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

trait LivewireUploadTrait
{
    /**
     * القائمة السوداء الصارمة للامتدادات الممنوعة
     */
    protected static array $lwBlockedExtensions = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'pht', 'phpt',
        'html', 'htm', 'xhtml', 'shtml', 'svg', 'svgz', 'xml',
        'js', 'jsp', 'jspx', 'asp', 'aspx', 'axd', 'ashx', 'asmx',
        'cgi', 'pl', 'sh', 'bash', 'zsh', 'exe', 'bat', 'cmd', 'com',
        'vbs', 'py', 'dll', 'so', 'bin', 'env', 'htaccess', 'htpasswd', 'ini', 'config', 'sql'
    ];

    /**
     * القائمة البيضاء للامتدادات المسموح بها للمرفقات
     */
    protected static array $lwAllowedExtensions = [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'ico',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'odt', 'ods', 'ppt', 'pptx',
        'zip', 'rar', '7z', 'tar', 'gz'
    ];

    /**
     * معالجة وحفظ المرفقات من Livewire بأمان فائق
     */
    public function saveAttachments($files, $attachable_type, $attachable_id)
    {
        try {
            if (!$files || !is_iterable($files)) {
                return;
            }

            $targetDir = public_path('uploads/files');
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            foreach ($files as $file) {
                if (empty($file['tmpFilename'])) {
                    continue;
                }

                // تعقيم اسم الملف المؤقت ومنع أي Path Traversal
                $rawTmpFilename = str_replace(["\0", "\\", "/", ".."], '', $file['tmpFilename']);
                $tmpFilename = basename($rawTmpFilename);
                $tmpFilename = preg_replace('/[^\w\.\-_]/', '', $tmpFilename);

                if (empty($tmpFilename)) {
                    continue;
                }

                $extension = strtolower(pathinfo($tmpFilename, PATHINFO_EXTENSION));

                // فحص القائمة السوداء والقائمة البيضاء
                if (in_array($extension, static::$lwBlockedExtensions, true) || !in_array($extension, static::$lwAllowedExtensions, true)) {
                    Log::warning('Security Alert: Blocked unauthorized file extension in LivewireUploadTrait: ' . $tmpFilename, [
                        'extension' => $extension,
                        'ip' => request()->ip() ?? 'N/A',
                        'user_id' => auth()->id() ?? 'N/A',
                    ]);
                    continue;
                }

                // فحص الامتداد المزدوج (Double Extension)
                $parts = explode('.', strtolower($tmpFilename));
                if (count($parts) > 2) {
                    $hasDoubleExt = false;
                    for ($i = 0; $i < count($parts) - 1; $i++) {
                        if (in_array($parts[$i], static::$lwBlockedExtensions, true)) {
                            $hasDoubleExt = true;
                            break;
                        }
                    }
                    if ($hasDoubleExt) {
                        Log::warning('Security Alert: Double extension risk in LivewireUploadTrait: ' . $tmpFilename, [
                            'ip' => request()->ip() ?? 'N/A',
                        ]);
                        continue;
                    }
                }

                $sourcePath = public_path('uploads/tmp/' . $tmpFilename);
                if (!File::exists($sourcePath)) {
                    continue;
                }

                // فحص محتوى الملف لمنع الأكواد الخبيثة
                if (!$this->isLwContentSafe($sourcePath)) {
                    Log::warning('Security Alert: Malicious script detected in temporary file in LivewireUploadTrait: ' . $tmpFilename, [
                        'ip' => request()->ip() ?? 'N/A',
                    ]);
                    @File::delete($sourcePath);
                    continue;
                }

                // توليد اسم نهائي آمن وفريد
                $cleanOriginalName = !empty($file['name']) ? basename(str_replace(["\0", "\\", "/", ".."], '', $file['name'])) : $tmpFilename;
                $cleanOriginalName = preg_replace('/[^\w\.\-_]/', '', $cleanOriginalName);

                $destPath = public_path('uploads/files/' . $tmpFilename);

                $moved = File::move($sourcePath, $destPath);
                if ($moved) {
                    Attachment::create([
                        'attachable_type' => $attachable_type,
                        'attachable_id'   => $attachable_id,
                        'path'            => 'uploads/files/' . $tmpFilename,
                        'name'            => $cleanOriginalName ?: $tmpFilename,
                        'size'            => $file['size'] ?? (File::exists($destPath) ? File::size($destPath) : 0),
                        'extension'       => $extension,
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::error('Error in LivewireUploadTrait saveAttachments: ' . $e->getMessage());
        }
    }

    /**
     * فحص سلامة محتوى الملف المؤقت
     */
    protected function isLwContentSafe(string $path): bool
    {
        try {
            if (!file_exists($path)) {
                return false;
            }

            $size = @filesize($path);
            if ($size === 0) {
                return false;
            }

            $handle = @fopen($path, 'rb');
            if (!$handle) {
                return false;
            }

            $buffer = fread($handle, 8192);
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
}
