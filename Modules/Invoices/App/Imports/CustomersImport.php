<?php

namespace Modules\Invoices\App\Imports;

use App\Models\invApp\InvCustomer;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\AccuSoft\AccountMapping;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomersImport implements ToModel, WithChunkReading, WithBatchInserts, WithValidation, SkipsOnError, SkipsEmptyRows, WithStartRow
{
    private $errors = [];
    private $successCount = 0;
    private $errorCount = 0;
    private $parentId;

    public function __construct()
    {
        // 1. تحديد الحساب الأب للعملاء من الربط المحاسبي
        $this->parentId = AccountMapping::getAccountId('customer');
    }

    public function startRow(): int
    {
        return 3;
    }

    public function model(array $row)
    {
        try {
            if (!$this->parentId) {
                throw new \Exception('يرجى ضبط الحساب الأب للعملاء في إعدادات الربط المحاسبي أولاً.');
            }

            // فصل الاسم العربي والإنجليزي
            $names = $this->parseName($row[0]);

            if (empty($names['ar'])) {
                throw new \Exception('اسم العميل حقل إجباري.');
            }

            DB::beginTransaction();

            // البحث عن العميل بالاسم العربي
            $customer = InvCustomer::whereHas('translations', function ($q) use ($names) {
                $q->where('name', $names['ar'])->where('locale', 'ar');
            })->first();

            $phone = !empty($row[1]) ? trim((string)$row[1]) : null;
            $email = !empty($row[2]) ? trim((string)$row[2]) : null;
            $vat_number = !empty($row[3]) ? trim((string)$row[3]) : null;
            $cr_number = !empty($row[4]) ? trim((string)$row[4]) : null;
            $country = $this->sanitizeCountry($row[5] ?? null);
            $city = !empty($row[6]) ? trim((string)$row[6]) : null;
            $district = !empty($row[7]) ? trim((string)$row[7]) : null;
            $street = !empty($row[8]) ? trim((string)$row[8]) : null;
            $building_number = !empty($row[9]) ? trim((string)$row[9]) : null;
            $postal_code = !empty($row[10]) ? trim((string)$row[10]) : null;
            $additional_number = !empty($row[11]) ? trim((string)$row[11]) : null;
            $credit_limit = !empty($row[12]) ? $this->sanitizeNumeric($row[12]) : 0;

            if ($customer) {
                // تحديث الحساب المالي
                if ($customer->treeAccount) {
                    $accountUpdateData = [];
                    foreach (config('langs') as $locale => $language) {
                        $accountUpdateData[$locale]['name'] = $names[$locale] ?? $names['ar'];
                    }
                    $customer->treeAccount->update($accountUpdateData);
                }

                // تحديث العميل
                $customer->update(array_merge([
                    'phone' => $phone,
                    'email' => $email,
                    'vat_number' => $vat_number,
                    'cr_number' => $cr_number,
                    'country' => $country,
                    'city' => $city,
                    'district' => $district,
                    'street' => $street,
                    'building_number' => $building_number,
                    'postal_code' => $postal_code,
                    'additional_number' => $additional_number,
                    'credit_limit' => $credit_limit,
                    'status' => 1,
                ], $this->formatTranslations($names)));
            } else {
                // إنشاء الحساب المالي الجديد
                $parentAccount = TreeAccounts::find($this->parentId);
                $accountData = [
                    'parent_id' => $this->parentId,
                    'account_type' => TreeAccounts::ACCOUNT_TYPE_CUSTOMERS,
                    'type' => 1, // Debit - مدين
                    'is_leaf' => true,
                    'level' => $parentAccount ? $parentAccount->level + 1 : 1,
                    'status' => 1,
                    'code' => TreeAccounts::generateCode($this->parentId),
                ];

                foreach (config('langs') as $locale => $language) {
                    $accountData[$locale]['name'] = $names[$locale] ?? $names['ar'];
                }

                $treeAccount = TreeAccounts::create($accountData);

                // إنشاء سجل العميل
                $customer = InvCustomer::create(array_merge([
                    'tree_account_id' => $treeAccount->id,
                    'phone' => $phone,
                    'email' => $email,
                    'vat_number' => $vat_number,
                    'cr_number' => $cr_number,
                    'country' => $country,
                    'city' => $city,
                    'district' => $district,
                    'street' => $street,
                    'building_number' => $building_number,
                    'postal_code' => $postal_code,
                    'additional_number' => $additional_number,
                    'credit_limit' => $credit_limit,
                    'status' => 1,
                ], $this->formatTranslations($names)));
            }

            DB::commit();
            $this->successCount++;
            return $customer;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;
            $this->errors[] = [
                'row' => $row,
                'error' => $e->getMessage(),
            ];

            Log::error('Customer import error at row', [
                'data' => $row,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function parseName($name): array
    {
        $name = trim((string)$name);
        $separators = [' - ', ' / ', ' | ', '|', "\n", "\r\n"];

        foreach ($separators as $separator) {
            if (str_contains($name, $separator)) {
                $parts = explode($separator, $name, 2);
                return [
                    'ar' => trim($parts[0]),
                    'en' => trim($parts[1] ?? $parts[0]),
                ];
            }
        }

        return ['ar' => $name, 'en' => $name];
    }

    private function formatTranslations(array $names, string $field = 'name'): array
    {
        $data = [];
        foreach ($names as $locale => $value) {
            $data[$locale] = [$field => $value];
        }
        return $data;
    }

    private function sanitizeNumeric($value): float
    {
        if (empty($value)) return 0.0;
        if (is_numeric($value)) return (float) $value;
        $cleaned = preg_replace('/[^0-9.]/', '', str_replace(',', '', (string)$value));
        return (float) ($cleaned ?: 0);
    }

    private function sanitizeCountry($value): string
    {
        $value = trim((string)$value);
        if (empty($value)) {
            return 'SA';
        }

        $normalized = mb_strtolower($value, 'UTF-8');
        
        $mapping = [
            'السعودية' => 'SA',
            'المملكة العربية السعودية' => 'SA',
            'sa' => 'SA',
            'ksa' => 'SA',
            'saudi' => 'SA',
            'saudi arabia' => 'SA',
            
            'مصر' => 'EG',
            'eg' => 'EG',
            'egypt' => 'EG',
            
            'الإمارات' => 'AE',
            'الامارات' => 'AE',
            'الإمارات العربية المتحدة' => 'AE',
            'الامارات العربية المتحدة' => 'AE',
            'ae' => 'AE',
            'uae' => 'AE',
        ];

        foreach ($mapping as $key => $code) {
            if ($normalized === $key || str_contains($normalized, $key)) {
                return $code;
            }
        }

        return mb_substr($value, 0, 5);
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string', 'max:500'],
            '1' => ['nullable', 'max:255'],
            '2' => ['nullable', 'max:255'], // Removed strict 'email' validation to avoid failing on custom phone formats in email column or empty placeholders
            '3' => ['nullable', 'max:255'],
            '4' => ['nullable', 'max:255'],
            '5' => ['nullable', 'max:255'],
            '6' => ['nullable', 'max:255'],
            '7' => ['nullable', 'max:255'],
            '8' => ['nullable', 'max:255'],
            '9' => ['nullable', 'max:255'],
            '10' => ['nullable', 'max:255'],
            '11' => ['nullable', 'max:255'],
            '12' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function onError(Throwable $e)
    {
        Log::error('Customer import process error: ' . $e->getMessage());
    }

    public function getImportSummary(): array
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'errors' => $this->errors,
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return empty(array_filter($row, fn($v) => !is_null($v) && $v !== ''));
    }
}
