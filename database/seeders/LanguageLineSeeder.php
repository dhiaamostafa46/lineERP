<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Spatie\TranslationLoader\LanguageLine;
use Stichoza\GoogleTranslate\GoogleTranslate;

class LanguageLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $available_files_name = ['models', 'lang.php', 'crud.php'];
        $files = [];
        $count = 0;
        $filesInFolder = File::files('Modules/HR/lang/en/models');
        foreach ($filesInFolder as $path) {
            $file = pathinfo($path);
            // dd($file);
            $file_path = $file['filename'];
            $content = __($file_path, [], 'en');
            // dd($content);
            foreach ($content as $key => $value) {
                if (in_array($file_path, $available_files_name)) {
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            if (is_array($subValue)) {
                                foreach ($subValue as $subKey2 => $subValue2) {
                                    if (is_array($subValue2)) {
                                    } else {
                                        $files[$file_path][$count]['group'] = $file_path;
                                        $files[$file_path][$count]['key'] = $key . '.' . $subKey . '.' . $subKey2;
                                        $files[$file_path][$count++]['text'] = ['en' => $subValue2, 'ar' => $subValue2];
                                    }
                                }
                            } else {
                                $files[$file_path][$count]['group'] = $file_path;
                                $files[$file_path][$count]['key'] = $key . '.' . $subKey;
                                $files[$file_path][$count++]['text'] = ['en' => $subValue, 'ar' => $subValue];
                            }
                        }
                    } else {
                        $files[$file_path][$count]['group'] = $file_path;
                        $files[$file_path][$count]['key'] = $key;
                        $files[$file_path][$count++]['text'] = ['en' => $value, 'ar' => $value];
                    }
                }
            }

            ++$count;
        }

        dd($files);
        foreach ($files as $file) {

            foreach ($file as $i) {
                try {
                    LanguageLine::create([
                        'group' => $i['group'],
                        'key'   => $i['key'],
                        // 'text'  => ['en' => $i['text']['en'], 'ar' => GoogleTranslate::trans($i['text']['ar'], 'ar', 'en')],
                        'text'  => ['en' => $i['text']['en'], 'ar' => $i['text']['ar']],
                    ]);
                } catch (\Throwable $th) {
                    dd($i);
                }
            }
        }
    }
}
