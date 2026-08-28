<?php

namespace Modules\HR\App\Services;

use App\Models\AccuSoft\AccountMapping;
use Modules\AccuSoft\App\Models\AccountingSettings;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\JournalEntryDetail;
use App\Models\AccuSoft\FiscalYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\HR\App\Models\HrEmployee;

class HrJournalEntryService
{
    /**
     * Create a journal entry for an HR transaction based on the unified mechanism.
     *
     * @param float $amount The total transaction amount.
     * @param int|string $debitAccount The debit account ID or mapping key.
     * @param int|string $creditAccount The credit account ID or mapping key.
     * @param string $description The description of the entry.
     * @param string|null $referenceType The morph class of the reference model (e.g. HrAdvance::class).
     * @param int|null $referenceId The ID of the reference model.
     * @param int|null $employeeId For cost center fetching, if applicable.
     * @return JournalEntry|null Returns the created JournalEntry, or null on failure.
     */
    public static function createEntry(
        float $amount,
        $debitAccount,
        $creditAccount,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $employeeId = null
    ) {
        if ($amount <= 0) {
            return null;
        }

        try {
            DB::beginTransaction();

            $debitAccountId = self::resolveAccountId($debitAccount);
            $creditAccountId = self::resolveAccountId($creditAccount);

            // 2. Fetch Cost Center if employee is provided
            $costCenterId = null;
            if ($employeeId) {
                $employee = HrEmployee::find($employeeId);
                // Assume cost_center_id is added to HrEmployee or its department
                $costCenterId = $employee->cost_center_id ?? null;
            }

            // 3. Get Accounting Settings & Fiscal Year
            $settings = AccountingSettings::getInstance();
            $isAutoPost = $settings->hr_auto_post_journal_entries;
            
            $fiscalYear = FiscalYear::where('is_closed', false)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if (!$fiscalYear) {
                throw new \Exception("No active fiscal year found for the current date.");
            }

            // 4. Determine Status
            $status = $isAutoPost ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_PENDING;

            // 5. Create Journal Entry Header
            $entry = new JournalEntry();
            $entry->entry_number = JournalEntry::generateEntryNumber();
            $entry->entry_date = now();
            $entry->description = $description;
            $entry->fiscal_year_id = $fiscalYear->id;
            $entry->entry_type = JournalEntry::ENTRY_TYPE_MANUAL;
            $entry->source = JournalEntry::SOURCE_HR;
            $entry->status = $status;
            $entry->total_debit = $amount;
            $entry->total_credit = $amount;
            $entry->created_by = Auth::id() ?? 1;
            
            if ($isAutoPost) {
                $entry->posted_by = Auth::id() ?? 1;
                $entry->posted_at = now();
            }

            if ($referenceType && $referenceId) {
                $entry->reference_type = $referenceType;
                $entry->reference_id = $referenceId;
            }

            $entry->save();

            // 6. Create Debit Detail
            $entry->details()->create([
                'tree_account_id' => $debitAccountId,
                'cost_center_id' => $costCenterId, // Debit usually takes the cost center (expense)
                'debit' => $amount,
                'credit' => 0,
                'description' => $description,
            ]);

            // 7. Create Credit Detail
            $entry->details()->create([
                'tree_account_id' => $creditAccountId,
                'cost_center_id' => null, // Typically liability/bank doesn't strictly need the employee's cost center
                'debit' => 0,
                'credit' => $amount,
                'description' => $description,
            ]);

            DB::commit();

            return $entry;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('HrJournalEntryService Error: ' . $e->getMessage());
            throw $e; // Re-throw to handle in the controller
        }
    }

    /**
     * Create a complex journal entry (e.g., Payroll with multiple allowances/deductions)
     *
     * @param string $description
     * @param array $details Array of arrays: [['mapping_key' => '...', 'account_id' => '...', 'debit' => 100, 'credit' => 0, 'employee_id' => null, 'description' => '...']]
     * @param string|null $referenceType
     * @param int|null $referenceId
     * @return JournalEntry|null
     */
    public static function createComplexEntry(
        string $description,
        array $details,
        ?string $referenceType = null,
        ?int $referenceId = null
    ) {
        if (empty($details)) {
            return null;
        }

        try {
            DB::beginTransaction();

            $totalDebit = collect($details)->sum('debit');
            $totalCredit = collect($details)->sum('credit');

            // Float comparison for balance
            if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
                throw new \Exception("Journal entry is not balanced. Debit: $totalDebit, Credit: $totalCredit");
            }

            $settings = AccountingSettings::getInstance();
            $isAutoPost = $settings->hr_auto_post_journal_entries;
            
            $fiscalYear = FiscalYear::where('is_closed', false)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if (!$fiscalYear) {
                throw new \Exception("No active fiscal year found for the current date.");
            }

            $status = $isAutoPost ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_PENDING;

            // Create Journal Entry Header
            $entry = new JournalEntry();
            $entry->entry_number = JournalEntry::generateEntryNumber();
            $entry->entry_date = now();
            $entry->description = $description;
            $entry->fiscal_year_id = $fiscalYear->id;
            $entry->entry_type = JournalEntry::ENTRY_TYPE_MANUAL;
            $entry->source = JournalEntry::SOURCE_HR;
            $entry->status = $status;
            $entry->total_debit = $totalDebit;
            $entry->total_credit = $totalCredit;
            $entry->created_by = Auth::id() ?? 1;

            if ($isAutoPost) {
                $entry->posted_by = Auth::id() ?? 1;
                $entry->posted_at = now();
            }

            if ($referenceType && $referenceId) {
                $entry->reference_type = $referenceType;
                $entry->reference_id = $referenceId;
            }

            $entry->save();

            // Process Details
            foreach ($details as $detailRow) {
                $account = $detailRow['account_id'] ?? $detailRow['mapping_key'] ?? null;

                if (!$account) {
                    throw new \Exception("Account ID or mapping key could not be determined for a journal entry detail row.");
                }

                $accountId = self::resolveAccountId($account);

                $costCenterId = null;
                if (!empty($detailRow['employee_id'])) {
                    $employee = HrEmployee::find($detailRow['employee_id']);
                    $costCenterId = $employee->cost_center_id ?? null;
                }

                $entry->details()->create([
                    'tree_account_id' => $accountId,
                    'cost_center_id' => $costCenterId,
                    'debit' => $detailRow['debit'] ?? 0,
                    'credit' => $detailRow['credit'] ?? 0,
                    'description' => $detailRow['description'] ?? $description,
                ]);
            }

            DB::commit();

            return $entry;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('HrJournalEntryService Complex Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ensures that the mapping key exists in AccountMapping table.
     * If not, it registers it with the correct account found by keyword search.
     */
    private static function ensureMappingExists(string $mappingKey): void
    {
        $exists = AccountMapping::where('mapping_key', $mappingKey)->exists();

        if (!$exists) {
            // Map each key to its Arabic/English name and search keywords
            $knownMappings = [
                'hr_salaries_expense_account' => [
                    'ar' => 'حساب مصروف الرواتب',
                    'en' => 'Salaries Expense Account',
                    'search' => ['مصروف الرواتب', 'مصروف الرواتب والأجور', 'الرواتب الأساسية'],
                    'type_hint' => 'expense'
                ],
                'hr_allowances_expense_account' => [
                    'ar' => 'حساب مصروف البدلات',
                    'en' => 'Allowances Expense Account',
                    'search' => ['مصروف البدلات', 'بدلات'],
                    'type_hint' => 'expense'
                ],
                'hr_rewards_expense_account' => [
                    'ar' => 'حساب مصروف المكافآت',
                    'en' => 'Rewards Expense Account',
                    'search' => ['مصروف المكافآت', 'مكافآت'],
                    'type_hint' => 'expense'
                ],
                'hr_deductions_account' => [
                    'ar' => 'حساب استقطاعات الرواتب',
                    'en' => 'Payroll Deductions Account',
                    'search' => ['استقطاعات الرواتب', 'استقطاعات'],
                    'type_hint' => 'liability'
                ],
                'hr_penalties_account' => [
                    'ar' => 'حساب جزاءات الموظفين',
                    'en' => 'Employee Penalties Account',
                    'search' => ['جزاءات الموظفين', 'جزاءات'],
                    'type_hint' => 'liability'
                ],
                'hr_advance_receivable_account' => [
                    'ar' => 'حساب سلف الموظفين',
                    'en' => 'Employees Advances Account',
                    'search' => ['سلف الموظفين', 'سلف'],
                    'type_hint' => 'asset'
                ],
                'hr_accrued_salaries_payable_account' => [
                    'ar' => 'حساب رواتب وأجور مستحقة',
                    'en' => 'Accrued Salaries Payable Account',
                    'search' => ['رواتب وأجور مستحقة', 'رواتب مستحقة'],
                    'type_hint' => 'liability'
                ],
                'hr_default_cash_bank_account' => [
                    'ar' => 'حساب الصندوق أو البنك',
                    'en' => 'Cash/Bank Account',
                    'search' => ['الصندوق', 'البنك', 'صندوق', 'بنك'],
                    'type_hint' => 'asset'
                ],
                'hr_eosb_expense_account' => [
                    'ar' => 'حساب مصروف مكافأة نهاية الخدمة',
                    'en' => 'EOSB Expense Account',
                    'search' => ['مكافأة نهاية الخدمة', 'مصروف مكافأة نهاية الخدمة', 'مصروف نهاية الخدمة'],
                    'type_hint' => 'expense',
                    'parent_search' => ['الرواتب والأجور وملحقاتها']
                ],
                'hr_accrued_eosb_payable_account' => [
                    'ar' => 'حساب مكافأة نهاية الخدمة المستحقة',
                    'en' => 'Accrued EOSB Payable Account',
                    'search' => ['نهاية الخدمة المستحقة', 'مكافآت مستحقة', 'مكافأة نهاية خدمة مستحقة'],
                    'type_hint' => 'liability',
                    'parent_search' => ['مخصصات طويلة الأجل']
                ]
            ];

            $meta = $knownMappings[$mappingKey] ?? null;
            $arName = $meta['ar'] ?? str_replace('_', ' ', $mappingKey);
            $enName = $meta['en'] ?? str_replace('_', ' ', $mappingKey);
            $keywords = $meta['search'] ?? [];

            // Try to find the correct account using keywords
            $accountId = null;
            foreach ($keywords as $kw) {
                $acc = \App\Models\AccuSoft\TreeAccounts::whereTranslationLike('name', "%{$kw}%")->first();
                if ($acc) {
                    $accountId = $acc->id;
                    break;
                }
            }

            // Fallback: Create the account if not found
            if (!$accountId) {
                $typeHint = $meta['type_hint'] ?? 'expense';
                $accountType = \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_EXPENSE;
                $drCrType = \App\Models\AccuSoft\TreeAccounts::TYPE_DEBIT;
                
                if ($typeHint === 'liability') {
                    $accountType = \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_LIABILITY;
                    $drCrType = \App\Models\AccuSoft\TreeAccounts::TYPE_CREDIT;
                } elseif ($typeHint === 'asset') {
                    $accountType = \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_ASSET;
                    $drCrType = \App\Models\AccuSoft\TreeAccounts::TYPE_DEBIT;
                }
                
                $parent = null;

                // Try finding specific parent if provided
                if (!empty($meta['parent_search'])) {
                    foreach ($meta['parent_search'] as $pKw) {
                        $parent = \App\Models\AccuSoft\TreeAccounts::whereTranslationLike('name', "%{$pKw}%")
                            ->where('is_leaf', false)
                            ->first();
                        if ($parent) break;
                    }
                }

                // Default fallback if specific parent not found
                if (!$parent) {
                    $parent = \App\Models\AccuSoft\TreeAccounts::where('account_type', $accountType)
                                    ->where('is_leaf', false)
                                    ->first();
                }

                // Generate a temporary unique code (max code in parent + 1, or uniqid)
                $maxCode = \App\Models\AccuSoft\TreeAccounts::where('parent_id', $parent ? $parent->id : null)->max('code');
                $newCode = $maxCode ? str_pad((int)$maxCode + 1, strlen($maxCode), '0', STR_PAD_LEFT) : rand(100000, 999999);

                $newAccount = \App\Models\AccuSoft\TreeAccounts::create([
                    'code' => $newCode,
                    'account_type' => $accountType,
                    'parent_id' => $parent ? $parent->id : null,
                    'level' => $parent ? $parent->level + 1 : 1,
                    'is_leaf' => true,
                    'status' => \App\Models\AccuSoft\TreeAccounts::STATUS_ACTIVE,
                    'type' => $drCrType,
                    'use_cost_center' => true,
                    'en' => ['name' => $enName],
                    'ar' => ['name' => $arName]
                ]);
                $accountId = $newAccount->id;
            }

            AccountMapping::create([
                'mapping_key' => $mappingKey,
                'account_id' => $accountId,
                'status' => AccountMapping::STATUS_ACTIVE,
                'en' => ['name' => $enName],
                'ar' => ['name' => $arName]
            ]);
        }
    }

    /**
     * Resolves an account ID from either a direct integer ID or a string mapping key.
     *
     * @param int|string $account
     * @return int
     * @throws \Exception
     */
    private static function resolveAccountId($account): int
    {
        if (is_numeric($account)) {
            return (int) $account;
        }

        self::ensureMappingExists((string) $account);
        $mapping = AccountMapping::where('mapping_key', (string) $account)->first();

        if (!$mapping || !$mapping->account_id) {
            throw new \Exception("لم يتم تحديد الحساب المالي المرتبط بالبند: " . ($mapping->name ?? (string) $account) . ". يرجى إعداده من شاشة الربط المحاسبي.");
        }

        return (int) $mapping->account_id;
    }
}
