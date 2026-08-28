<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class SaudiPhoneExists implements ValidationRule
{
    private $table;
    private $column;
    private $foundPhone;

    public function __construct($table = 'users', $column = 'phone')
    {
        $this->table = $table;
        $this->column = $column;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // الصيغ المحتملة للبحث
        $possibleFormats = $this->getPossibleFormats($value);

        // البحث عن أي صيغة في قاعدة البيانات
        $this->foundPhone = DB::table($this->table)
            ->whereIn($this->column, $possibleFormats)
            ->value($this->column);

        if (is_null($this->foundPhone)) {
            $fail('رقم الجوال غير مسجل في النظام');
        }
    }

    public function getFoundPhone()
    {
        return $this->foundPhone;
    }

    private function getPossibleFormats($phone)
    {
        // إزالة المسافات والرموز والأحرف المخفية
        $phone = preg_replace('/[^\d\+]/', '', $phone);
        $phone = ltrim($phone, '+');

        $formats = [];

        // إذا كان 05xxxxxxxx
        if (preg_match('/^05(\d{8})$/', $phone, $matches)) {
            $formats[] = '05' . $matches[1];           // 05xxxxxxxx
            $formats[] = '9665' . $matches[1];         // 9665xxxxxxxx
            $formats[] = '+9665' . $matches[1];        // +9665xxxxxxxx
            $formats[] = '5' . $matches[1];            // 5xxxxxxxx
        }
        // إذا كان 9665xxxxxxxx
        elseif (preg_match('/^9665(\d{8})$/', $phone, $matches)) {
            $formats[] = '05' . $matches[1];
            $formats[] = '9665' . $matches[1];
            $formats[] = '+9665' . $matches[1];
            $formats[] = '5' . $matches[1];
        }
        // إذا كان 5xxxxxxxx
        elseif (preg_match('/^5(\d{8})$/', $phone, $matches)) {
            $formats[] = '05' . $matches[1];
            $formats[] = '9665' . $matches[1];
            $formats[] = '+9665' . $matches[1];
            $formats[] = '5' . $matches[1];
        }
        else {
            $formats[] = $phone;
        }

        return array_unique($formats);
    }
}
