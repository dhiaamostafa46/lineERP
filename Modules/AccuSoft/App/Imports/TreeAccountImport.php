<?php

namespace Modules\AccuSoft\App\Imports;

use App\Models\AccuSoft\TreeAccounts;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TreeAccountImport implements ToCollection, WithHeadingRow
{
    private $errors = [];

    private $successCount = 0;

    private $updatedCount = 0;

    public function collection(Collection $rows)
    {
        // Sort by code length to ensure parents exist before children
        $sortedRows = $rows->sortBy(function ($row) {
            return strlen(trim($row['code'] ?? ''));
        });

        foreach ($sortedRows as $index => $row) {
            try {
                DB::beginTransaction();

                $code = preg_replace('/[^0-9]/', '', $row['code'] ?? '');
                if (empty($code)) {
                    continue;
                }

                $nameAr = trim($row['name'] ?? $row['name_ar'] ?? '');
                $nameEn = trim($row['name_en'] ?? '');

                if (empty($nameAr)) {
                    throw new Exception('اسم الحساب (عربي) مطلوب.');
                }

                // 1. Determine Parent - prefer explicit parent_code if provided
                $parentCodeInput = trim($row['parent_code'] ?? '');
                if (! empty($parentCodeInput)) {
                    $parent = TreeAccounts::where('code', preg_replace('/[^0-9]/', '', $parentCodeInput))->first();
                    if (! $parent) {
                        throw new Exception("Parent with code {$parentCodeInput} not found.");
                    }
                    $parentId = $parent->id;
                } else {
                    $parentId = $this->determineParentId($code);
                }

                // 2. Accounting Logic (Based on Seeder) or explicit inputs
                // account_type: accept Arabic text (from template) or numeric
                $accountTypeInput = trim($row['account_type'] ?? '');
                if (! empty($accountTypeInput)) {
                    $accountTypeMap = [
                        'الأصول' => TreeAccounts::ACCOUNT_TYPE_ASSET,
                        'الالتزامات' => TreeAccounts::ACCOUNT_TYPE_LIABILITY,
                        'الملكية' => TreeAccounts::ACCOUNT_TYPE_EQUITY,
                        'الإيرادات' => TreeAccounts::ACCOUNT_TYPE_REVENUE,
                        'المصاريف' => TreeAccounts::ACCOUNT_TYPE_EXPENSE,
                        'تكلفة المبيعات' => TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES,
                        'الموردين' => TreeAccounts::ACCOUNT_TYPE_SUPPLIERS,
                        'الخزانة' => TreeAccounts::ACCOUNT_TYPE_TREASURY,
                        'البنوك' => TreeAccounts::ACCOUNT_TYPE_BANK,
                        'المخزون' => TreeAccounts::ACCOUNT_TYPE_INVENTORY,
                        'العملاء' => TreeAccounts::ACCOUNT_TYPE_CUSTOMERS,
                        'المبيعات' => TreeAccounts::ACCOUNT_TYPE_SALES,
                        'المشتريات' => TreeAccounts::ACCOUNT_TYPE_PURCHASES,
                    ];

                    if (is_numeric($accountTypeInput)) {
                        $accountType = (int) $accountTypeInput;
                    } elseif (isset($accountTypeMap[$accountTypeInput])) {
                        $accountType = $accountTypeMap[$accountTypeInput];
                    } else {
                        throw new Exception("قيمة غير صالحة للعمود account_type: {$accountTypeInput}");
                    }
                } else {
                    $accountType = $this->inferAccountType($code, $parentId);
                }

                // type (nature): accept Arabic 'مدين'/'دائن' or numeric 1/2
                $typeInput = trim($row['type'] ?? '');
                if (! empty($typeInput)) {
                    $typeMap = [
                        'مدين' => TreeAccounts::TYPE_DEBIT,
                        'دائن' => TreeAccounts::TYPE_CREDIT,
                    ];

                    if (is_numeric($typeInput)) {
                        $type = (int) $typeInput;
                    } elseif (isset($typeMap[$typeInput])) {
                        $type = $typeMap[$typeInput];
                    } else {
                        throw new Exception("قيمة غير صالحة للعمود type: {$typeInput}");
                    }
                } else {
                    $type = $this->inferNature($code, $parentId);
                }

                // 3. Upsert Logic
                $account = TreeAccounts::where('code', $code)->first();

                if ($account) {
                    // Update existing account
                    $account->update([
                        'account_type' => $accountType,
                        'parent_id' => $parentId,
                        'type' => $type,
                    ]);
                    $this->updatedCount++;
                } else {
                    // Create new account
                    $account = TreeAccounts::create([
                        'code' => $code,
                        'account_type' => $accountType,
                        'parent_id' => $parentId,
                        'type' => $type,
                        'status' => TreeAccounts::STATUS_ACTIVE,
                        'is_leaf' => true,
                        'is_system' => false,
                    ]);
                    $this->successCount++;
                }

                // Update Translations
                $account->translateOrNew('ar')->name = $nameAr;
                if (! empty($nameEn)) {
                    $account->translateOrNew('en')->name = $nameEn;
                }
                $account->save();

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $rowArray = $row->toArray();
                $rowArray['error_reason'] = $e->getMessage();
                $this->errors[] = $rowArray;
            }
        }
    }

    private function determineParentId($code)
    {
        if (strlen($code) <= 1) {
            return null;
        }

        // Try stripping last characters to find parent (e.g. 111 -> 11)
        for ($i = 1; $i <= 3; $i++) {
            $parentCode = substr($code, 0, -$i);
            $parent = TreeAccounts::where('code', $parentCode)->first();
            if ($parent) {
                return $parent->id;
            }
        }

        return null;
    }

    private function inferAccountType($code, $parentId)
    {
        // Follow AccountingSeeder logic
        $firstDigit = substr($code, 0, 1);
        $inferred = match ($firstDigit) {
            '1' => TreeAccounts::ACCOUNT_TYPE_ASSET,
            '2' => TreeAccounts::ACCOUNT_TYPE_LIABILITY,
            '3' => TreeAccounts::ACCOUNT_TYPE_EQUITY,
            '4' => TreeAccounts::ACCOUNT_TYPE_EXPENSE,
            '5' => TreeAccounts::ACCOUNT_TYPE_REVENUE,
            default => TreeAccounts::ACCOUNT_TYPE_ASSET,
        };

        // If it starts with 41, it's Cost of Sales (as per seeder)
        if (substr($code, 0, 2) === '41') {
            $inferred = TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES;
        }

        return $inferred;
    }

    private function inferNature($code, $parentId)
    {
        // Standard Accounting Nature based on type
        $firstDigit = substr($code, 0, 1);

        // 1 (Assets) and 4 (Expenses) are usually Debtor (1)
        // 2 (Liabilities), 3 (Equity), 5 (Revenues) are usually Creditor (2)
        if (in_array($firstDigit, ['1', '4'])) {
            return TreeAccounts::TYPE_DEBIT;
        }

        return TreeAccounts::TYPE_CREDIT;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }
}
