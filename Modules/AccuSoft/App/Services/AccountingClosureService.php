<?php

namespace Modules\AccuSoft\App\Services;

use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\JournalEntryDetail;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * خدمة الإقفال المحاسبي للسنة المالية
 * تطبيق دقيق ومعياري للمعايير المحاسبية الدولية (IFRS & GAAP)
 */
class AccountingClosureService
{
    const OPT_AUTO_POST_DRAFTS = 'auto_post_drafts';
    const INCOME_SUMMARY_ACCOUNT_CODE = '3202';
    const RETAINED_EARNINGS_ACCOUNT_CODE = '3201';

    /**
     * إقفال السنة المالية
     *
     * الخطوات المحاسبية:
     * 1. التحقق من أهلية الإقفال والتوازن
     * 2. معالجة وتأكيد القيود المسودة (إن وجدت)
     * 3. إقفال حسابات الإيرادات (5xxx) → في ملخص الدخل (Income Summary)
     * 4. إقفال حسابات المصروفات وتكلفة المبيعات (4xxx) → في ملخص الدخل (Income Summary)
     * 5. إقفال رصيد ملخص الدخل (صافي الربح / الخسارة) → في الأرباح المبقاة (Retained Earnings)
     * 6. التحقق النهائي من تصفير قائمة الدخل
     * 7. إغلاق السنة المالية وقفل جميع قيودها
     * 8. تدوير أرصدة الميزانية العمومية وإنشاء قيد الأرصدة الافتتاحية للسنة التالية
     */
    public function closeFiscalYear(FiscalYear $fiscalYear, User $user, array $options = []): JournalEntry
    {
        if ($fiscalYear->is_closed) {
            throw new \Exception('السنة المالية مغلقة بالفعل');
        }

        return DB::transaction(function () use ($fiscalYear, $user, $options) {
            // 1. التحقق من الأهلية
            $eligibility = $this->validateClosureEligibility($fiscalYear);
            if (!$eligibility['success']) {
                throw new \Exception(implode(', ', $eligibility['errors']));
            }

            // 2. تنظيف وحذف أي قيود إقفال سابقة لنفس السنة منعاً للتكرار
            $oldClosing = JournalEntry::where('fiscal_year_id', $fiscalYear->id)
                ->where('entry_type', JournalEntry::ENTRY_TYPE_CLOSING)
                ->get();
            foreach ($oldClosing as $oc) {
                $oc->details()->delete();
                $oc->forceDelete();
            }

            // 3. معالجة القيود المسودة
            $this->handleDraftEntries($fiscalYear, $user, $options);

            // 4. الحصول على الحسابات الوسيطة (ملخص الدخل والأرباح المبقاة)
            $incomeSummaryAccount = $this->getIncomeSummaryAccount();
            $retainedEarningsAccount = $this->getRetainedEarningsAccount();

            // 5. إقفال الإيرادات (Root 5: 5xxx) إلى ملخص الدخل
            $this->closeRevenueAccounts($fiscalYear, $user, $incomeSummaryAccount);

            // 5. إقفال المصروفات وتكلفة المبيعات (Root 4: 4xxx) إلى ملخص الدخل
            $this->closeExpenseAccounts($fiscalYear, $user, $incomeSummaryAccount);

            // 6. حساب صافي النتيجة ونقلها من ملخص الدخل إلى الأرباح المبقاة
            $netIncome = $this->calculateIncomeSummaryBalance($fiscalYear, $incomeSummaryAccount);
            $netIncomeEntry = $this->transferNetIncomeToRetainedEarnings(
                $fiscalYear,
                $user,
                $incomeSummaryAccount,
                $retainedEarningsAccount,
                $netIncome
            );

            // 7. التحقق النهائي من تصفير حسابات الأرباح والخسائر
            $this->performFinalValidation($fiscalYear, $incomeSummaryAccount);

            // 8. تحديث حالة السنة المالية كـ مغلقة
            $fiscalYear->update([
                'is_closed' => true,
                'closed_at' => now(),
                'closed_by' => $user->id,
            ]);

            // 9. قفل جميع قيود السنة المالية لمنع أي تعديل أو حذف لاحق
            $this->lockAllFiscalYearEntries($fiscalYear, $user);

            // 10. تدوير أرصدة الميزانية وإنشاء قيد افتتاحي للسنة الجديدة
            try {
                $this->createOpeningBalancesForNextYear($fiscalYear, $user);
            } catch (\Exception $e) {
                Log::warning("فشل إنشاء الأرصدة الافتتاحية للسنة التالية: " . $e->getMessage());
            }

            Log::info("تم إقفال السنة المالية {$fiscalYear->id} بنجاح بواسطة المستخدم {$user->id}");

            return $netIncomeEntry;
        });
    }

    /**
     * إلغاء إقفال السنة المالية وإعادة فتحها
     *
     * الخطوات:
     * 1. حذف قيود الإقفال المحاسبي للسنة (Entry Type = 3)
     * 2. حذف قيود الأرصدة الافتتاحية المنشأة في السنة التالية (إن وجدت)
     * 3. إلغاء قفل القيود المحاسبية وتفاصيلها للسنة (is_locked = false)
     * 4. تحديث حالة السنة المالية كـ مفتوحة (is_closed = false)
     */
    public function reopenFiscalYear(FiscalYear $fiscalYear, User $user): void
    {
        if (!$fiscalYear->is_closed) {
            throw new \Exception('السنة المالية مفتوحة بالفعل');
        }

        DB::transaction(function () use ($fiscalYear, $user) {
            // 1. حذف قيود الإقفال المحاسبي
            $closingEntries = JournalEntry::where('fiscal_year_id', $fiscalYear->id)
                ->where('entry_type', JournalEntry::ENTRY_TYPE_CLOSING)
                ->get();

            foreach ($closingEntries as $closingEntry) {
                $closingEntry->details()->delete();
                $closingEntry->forceDelete();
            }

            // 2. حذف قيد الأرصدة الافتتاحية في السنة التالية (إن وجد)
            $nextYear = FiscalYear::whereDate('start_date', '>', $fiscalYear->end_date)
                ->orderBy('start_date', 'asc')
                ->first();

            if ($nextYear) {
                $openingEntries = JournalEntry::where('fiscal_year_id', $nextYear->id)
                    ->where('entry_type', JournalEntry::ENTRY_TYPE_OPENING)
                    ->get();

                foreach ($openingEntries as $opEntry) {
                    $opEntry->details()->delete();
                    $opEntry->forceDelete();
                }
            }

            // 3. إلغاء قفل جميع قيود وتفاصيل السنة المالية
            JournalEntryDetail::whereIn('journal_entry_id', function ($query) use ($fiscalYear) {
                $query->select('id')->from('journal_entries')->where('fiscal_year_id', $fiscalYear->id);
            })->update([
                'is_locked' => false,
                'locked_at' => null,
                'locked_by' => null,
            ]);

            JournalEntry::where('fiscal_year_id', $fiscalYear->id)->update([
                'is_locked' => false,
                'locked_at' => null,
                'locked_by' => null,
            ]);

            // 4. تحديث حالة السنة المالية
            $fiscalYear->update([
                'is_closed' => false,
                'closed_at' => null,
                'closed_by' => null,
            ]);

            Log::info("تمت إعادة فتح السنة المالية {$fiscalYear->id} بنجاح بواسطة المستخدم {$user->id}");
        });
    }

    /**
     * التحقق من أهلية الإقفال
     */
    public function validateClosureEligibility(FiscalYear $fiscalYear): array
    {
        $errors = [];
        $warnings = [];
        $info = [];

        if ($fiscalYear->is_closed) {
            $errors[] = 'السنة المالية مغلقة بالفعل';
        }

        // التحقق من القيود المسودة (Draft)
        $draftCount = JournalEntry::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', JournalEntry::STATUS_DRAFT)
            ->count();

        if ($draftCount > 0) {
            $errors[] = "يوجد $draftCount قيد مسودة في هذه السنة. يجب اعتمادها وترحيلها أو حذفها قبل الإقفال";
        }

        // التحقق من القيود المعلقة (Pending)
        $pendingCount = JournalEntry::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', JournalEntry::STATUS_PENDING)
            ->count();

        if ($pendingCount > 0) {
            $errors[] = "يوجد $pendingCount قيد معلق في هذه السنة. يجب مراجعتها وتأكيد ترحيلها من شاشة القيود المعلقة قبل الإقفال";
        }

        // التحقق من توازن القيود المرحلة
        $unbalancedCount = JournalEntry::where('fiscal_year_id', $fiscalYear->id)
            ->where('status', JournalEntry::STATUS_POSTED)
            ->get()
            ->filter(function ($entry) {
                return !$entry->isBalanced();
            })
            ->count();

        if ($unbalancedCount > 0) {
            $errors[] = "يوجد $unbalancedCount قيود غير متوازنة (المدين لا يساوي الدائن)";
        }

        // التحقق من وجود حسابات الإقفال
        try {
            $this->getIncomeSummaryAccount();
            $this->getRetainedEarningsAccount();
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        // حساب صافي النتيجة التقديرية (الإيرادات 5xxx - المصروفات 4xxx)
        $revCredits = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->where('ta.code', 'like', '5%')
            ->selectRaw('COALESCE(SUM(jed.credit) - SUM(jed.debit), 0) as net_rev')
            ->value('net_rev') ?? 0;

        $expDebits = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->where('ta.code', 'like', '4%')
            ->selectRaw('COALESCE(SUM(jed.debit) - SUM(jed.credit), 0) as net_exp')
            ->value('net_exp') ?? 0;

        $netIncome = $revCredits - $expDebits;
        $info['total_revenues'] = number_format($revCredits, 2);
        $info['total_expenses'] = number_format($expDebits, 2);
        $info['estimated_net_result'] = number_format($netIncome, 2);
        $info['result_type'] = $netIncome >= 0 ? 'صافي ربح' : 'صافي خسارة';

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info,
        ];
    }

    /**
     * معالجة القيود المسودة والمعلقة
     */
    private function handleDraftEntries(FiscalYear $fiscalYear, User $user, array $options): void
    {
        $unpostedEntries = JournalEntry::where('fiscal_year_id', $fiscalYear->id)
            ->whereIn('status', [JournalEntry::STATUS_DRAFT, JournalEntry::STATUS_PENDING])
            ->get();

        if ($unpostedEntries->isEmpty()) {
            return;
        }

        if (!($options[self::OPT_AUTO_POST_DRAFTS] ?? false)) {
            throw new \Exception("يوجد {$unpostedEntries->count()} قيد غير مرحل (مسودة أو معلق). يجب ترحيلها أو حذفها قبل الإقفال");
        }

        JournalEntry::where('fiscal_year_id', $fiscalYear->id)
            ->whereIn('status', [JournalEntry::STATUS_DRAFT, JournalEntry::STATUS_PENDING])
            ->update([
                'status' => JournalEntry::STATUS_POSTED,
                'posted_by' => $user->id,
                'posted_at' => now(),
            ]);
    }

    /**
     * إقفال حسابات الإيرادات (Root 5: 5xxx) في حساب ملخص الدخل
     *
     * القيد المحاسبي:
     * من حـ/ الإيرادات (مدين لتصفير رصيدها الدائن)
     *   إلى حـ/ ملخص الدخل (دائن بإجمالي الإيرادات)
     */
    private function closeRevenueAccounts(
        FiscalYear $fiscalYear,
        User $user,
        TreeAccounts $incomeSummaryAccount
    ): ?JournalEntry {
        $balances = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->where('ta.code', 'like', '5%')
            ->select('jed.tree_account_id', DB::raw('SUM(jed.debit) as total_debit'), DB::raw('SUM(jed.credit) as total_credit'))
            ->groupBy('jed.tree_account_id')
            ->get();

        if ($balances->isEmpty()) {
            return null;
        }

        $details = [];
        $totalDebitToSummary = 0;
        $totalCreditToSummary = 0;

        foreach ($balances as $balance) {
            // الرصيد الصافي لحساب الإيراد = الدائن - المدين
            $netBalance = (float) $balance->total_credit - (float) $balance->total_debit;

            if (abs($netBalance) < 0.005) {
                continue;
            }

            if ($netBalance > 0) {
                // رصيد دائن طبيعي: نجعله مديناً لإقفاله، ويضاف دائن إلى ملخص الدخل
                $details[] = [
                    'tree_account_id' => $balance->tree_account_id,
                    'debit' => abs($netBalance),
                    'credit' => 0,
                    'description' => 'إقفال حساب الإيراد في ملخص الدخل',
                ];
                $totalCreditToSummary += abs($netBalance);
            } else {
                // رصيد مدين شاذ (مثل خصم أو مرتجع مسجل كإيراد سالب): نجعله دائناً لإقفاله، ويضاف مدين إلى ملخص الدخل
                $details[] = [
                    'tree_account_id' => $balance->tree_account_id,
                    'debit' => 0,
                    'credit' => abs($netBalance),
                    'description' => 'إقفال رصيد مدين للإيراد في ملخص الدخل',
                ];
                $totalDebitToSummary += abs($netBalance);
            }
        }

        if (empty($details)) {
            return null;
        }

        // الطرف المقابل في ملخص الدخل
        $netSummaryBalance = $totalCreditToSummary - $totalDebitToSummary;
        if ($netSummaryBalance > 0) {
            $details[] = [
                'tree_account_id' => $incomeSummaryAccount->id,
                'debit' => 0,
                'credit' => abs($netSummaryBalance),
                'description' => 'إجمالي الإيرادات المقفلة',
            ];
        } elseif ($netSummaryBalance < 0) {
            $details[] = [
                'tree_account_id' => $incomeSummaryAccount->id,
                'debit' => abs($netSummaryBalance),
                'credit' => 0,
                'description' => 'إجمالي الإيرادات المقفلة (صافي مدين)',
            ];
        }

        $closingEntry = JournalEntry::create([
            'fiscal_year_id' => $fiscalYear->id,
            'entry_date' => $fiscalYear->end_date,
            'entry_number' => JournalEntry::generateEntryNumber(),
            'entry_type' => JournalEntry::ENTRY_TYPE_CLOSING,
            'source' => JournalEntry::SOURCE_CLOSING,
            'status' => JournalEntry::STATUS_POSTED,
            'description' => 'قيد إقفال حسابات الإيرادات للسنة المالية ' . ($fiscalYear->name ?? $fiscalYear->start_date->year),
            'created_by' => $user->id,
            'posted_by' => $user->id,
            'posted_at' => now(),
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);

        foreach ($details as $detail) {
            JournalEntryDetail::create(array_merge($detail, [
                'journal_entry_id' => $closingEntry->id,
            ]));
        }

        $closingEntry->calculateTotals();
        $closingEntry->save();

        return $closingEntry;
    }

    /**
     * إقفال حسابات المصروفات وتكلفة المبيعات (Root 4: 4xxx) في حساب ملخص الدخل
     *
     * القيد المحاسبي:
     * من حـ/ ملخص الدخل (مدين بإجمالي المصروفات)
     *   إلى حـ/ المصروفات وتكلفة المبيعات (دائن لتصفير رصيدها المدين)
     */
    private function closeExpenseAccounts(
        FiscalYear $fiscalYear,
        User $user,
        TreeAccounts $incomeSummaryAccount
    ): ?JournalEntry {
        $balances = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->where('ta.code', 'like', '4%')
            ->select('jed.tree_account_id', DB::raw('SUM(jed.debit) as total_debit'), DB::raw('SUM(jed.credit) as total_credit'))
            ->groupBy('jed.tree_account_id')
            ->get();

        if ($balances->isEmpty()) {
            return null;
        }

        $details = [];
        $totalDebitToSummary = 0;
        $totalCreditToSummary = 0;

        foreach ($balances as $balance) {
            // الرصيد الصافي لحساب المصروف = المدين - الدائن
            $netBalance = (float) $balance->total_debit - (float) $balance->total_credit;

            if (abs($netBalance) < 0.005) {
                continue;
            }

            if ($netBalance > 0) {
                // رصيد مدين طبيعي: نجعله دائناً لإقفاله، ويضاف مدين إلى ملخص الدخل
                $details[] = [
                    'tree_account_id' => $balance->tree_account_id,
                    'debit' => 0,
                    'credit' => abs($netBalance),
                    'description' => 'إقفال حساب المصروف في ملخص الدخل',
                ];
                $totalDebitToSummary += abs($netBalance);
            } else {
                // رصيد دائن شاذ (مثل خصم مكتسب أو تسوية دائنة مسجلة كمصروف سالب): نجعله مديناً لإقفاله
                $details[] = [
                    'tree_account_id' => $balance->tree_account_id,
                    'debit' => abs($netBalance),
                    'credit' => 0,
                    'description' => 'إقفال رصيد دائن للمصروف في ملخص الدخل',
                ];
                $totalCreditToSummary += abs($netBalance);
            }
        }

        if (empty($details)) {
            return null;
        }

        // الطرف المقابل في ملخص الدخل
        $netSummaryBalance = $totalDebitToSummary - $totalCreditToSummary;
        if ($netSummaryBalance > 0) {
            array_unshift($details, [
                'tree_account_id' => $incomeSummaryAccount->id,
                'debit' => abs($netSummaryBalance),
                'credit' => 0,
                'description' => 'إجمالي المصروفات المقفلة',
            ]);
        } elseif ($netSummaryBalance < 0) {
            $details[] = [
                'tree_account_id' => $incomeSummaryAccount->id,
                'debit' => 0,
                'credit' => abs($netSummaryBalance),
                'description' => 'إجمالي المصروفات المقفلة (صافي دائن)',
            ];
        }

        $closingEntry = JournalEntry::create([
            'fiscal_year_id' => $fiscalYear->id,
            'entry_date' => $fiscalYear->end_date,
            'entry_number' => JournalEntry::generateEntryNumber(),
            'entry_type' => JournalEntry::ENTRY_TYPE_CLOSING,
            'source' => JournalEntry::SOURCE_CLOSING,
            'status' => JournalEntry::STATUS_POSTED,
            'description' => 'قيد إقفال حسابات المصروفات وتكلفة المبيعات للسنة المالية ' . ($fiscalYear->name ?? $fiscalYear->start_date->year),
            'created_by' => $user->id,
            'posted_by' => $user->id,
            'posted_at' => now(),
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);

        foreach ($details as $detail) {
            JournalEntryDetail::create(array_merge($detail, [
                'journal_entry_id' => $closingEntry->id,
            ]));
        }

        $closingEntry->calculateTotals();
        $closingEntry->save();

        return $closingEntry;
    }

    /**
     * نقل صافي النتيجة من ملخص الدخل (Income Summary) إلى الأرباح المبقاة / المحتجزة (Retained Earnings)
     *
     * في حالة صافي ربح (Net Profit > 0):
     *   من حـ/ ملخص الدخل (3202) -> مدين
     *     إلى حـ/ الأرباح المبقاة (3201) -> دائن
     *
     * في حالة صافي خسارة (Net Loss > 0):
     *   من حـ/ الأرباح المبقاة (3201) -> مدين
     *     إلى حـ/ ملخص الدخل (3202) -> دائن
     */
    private function transferNetIncomeToRetainedEarnings(
        FiscalYear $fiscalYear,
        User $user,
        TreeAccounts $incomeSummaryAccount,
        TreeAccounts $retainedEarningsAccount,
        float $netIncome
    ): JournalEntry {
        $closingEntry = JournalEntry::create([
            'fiscal_year_id' => $fiscalYear->id,
            'entry_date' => $fiscalYear->end_date,
            'entry_number' => JournalEntry::generateEntryNumber(),
            'entry_type' => JournalEntry::ENTRY_TYPE_CLOSING,
            'source' => JournalEntry::SOURCE_CLOSING,
            'status' => JournalEntry::STATUS_POSTED,
            'description' => ($netIncome >= 0 ? 'إقفال صافي الربح' : 'إقفال صافي الخسارة') . ' ونقله للأرباح المبقاة للسنة ' . ($fiscalYear->name ?? $fiscalYear->start_date->year),
            'created_by' => $user->id,
            'posted_by' => $user->id,
            'posted_at' => now(),
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);

        if ($netIncome > 0.005) {
            // صافي ربح
            JournalEntryDetail::create([
                'journal_entry_id' => $closingEntry->id,
                'tree_account_id' => $incomeSummaryAccount->id,
                'debit' => abs($netIncome),
                'credit' => 0,
                'description' => 'إقفال ملخص الدخل (صافي ربح)',
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $closingEntry->id,
                'tree_account_id' => $retainedEarningsAccount->id,
                'debit' => 0,
                'credit' => abs($netIncome),
                'description' => 'تحويل صافي أرباح العام إلى الأرباح المبقاة',
            ]);
        } elseif ($netIncome < -0.005) {
            // صافي خسارة
            JournalEntryDetail::create([
                'journal_entry_id' => $closingEntry->id,
                'tree_account_id' => $retainedEarningsAccount->id,
                'debit' => abs($netIncome),
                'credit' => 0,
                'description' => 'تحميل صافي خسائر العام على الأرباح المبقاة',
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $closingEntry->id,
                'tree_account_id' => $incomeSummaryAccount->id,
                'debit' => 0,
                'credit' => abs($netIncome),
                'description' => 'إقفال ملخص الدخل (صافي خسارة)',
            ]);
        }

        $closingEntry->calculateTotals();
        $closingEntry->save();

        return $closingEntry;
    }

    /**
     * حساب رصيد ملخص الدخل بعد قيود إقفال الإيرادات والمصروفات
     *
     * الرصيد = إجمالي الدائن (الإيرادات) - إجمالي المدين (المصروفات)
     */
    private function calculateIncomeSummaryBalance(FiscalYear $fiscalYear, TreeAccounts $incomeSummaryAccount): float
    {
        $result = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->where('jed.tree_account_id', $incomeSummaryAccount->id)
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->where('je.entry_type', JournalEntry::ENTRY_TYPE_CLOSING)
            ->selectRaw('COALESCE(SUM(jed.debit), 0) as total_debit, COALESCE(SUM(jed.credit), 0) as total_credit')
            ->first();

        return (float) (($result->total_credit ?? 0) - ($result->total_debit ?? 0));
    }

    /**
     * التحقق النهائي من تصفير جميع حسابات قائمة الدخل وملخص الدخل
     */
    private function performFinalValidation(FiscalYear $fiscalYear, TreeAccounts $incomeSummaryAccount): void
    {
        // التحقق من تصفير حسابات 4xxx و 5xxx
        $unclosedPLAccounts = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->where(fn($q) =>
                $q->where('ta.code', 'like', '4%')
                  ->orWhere('ta.code', 'like', '5%')
            )
            ->select('jed.tree_account_id')
            ->groupBy('jed.tree_account_id')
            ->havingRaw('ABS(SUM(jed.debit) - SUM(jed.credit)) >= 0.01')
            ->get()
            ->count();

        if ($unclosedPLAccounts > 0) {
            throw new \Exception("$unclosedPLAccounts حسابات إيرادات/مصروفات لم يتم تصفيرها وإقفالها بشكل سليم.");
        }

        // التحقق من تصفير حساب ملخص الدخل نفسه
        $summaryBalance = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->where('jed.tree_account_id', $incomeSummaryAccount->id)
            ->selectRaw('ABS(SUM(jed.debit) - SUM(jed.credit)) as bal')
            ->value('bal') ?? 0;

        if ($summaryBalance >= 0.01) {
            throw new \Exception("رصيد حساب ملخص الدخل لم يتم تصفيره بالكامل (المتبقي: $summaryBalance).");
        }
    }

    /**
     * قفل جميع القيود المحاسبية وتفاصيلها للسنة المالية المقفلة
     */
    private function lockAllFiscalYearEntries(FiscalYear $fiscalYear, User $user): void
    {
        JournalEntryDetail::whereIn('journal_entry_id', function ($query) use ($fiscalYear) {
            $query->select('id')->from('journal_entries')->where('fiscal_year_id', $fiscalYear->id);
        })->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);

        JournalEntry::where('fiscal_year_id', $fiscalYear->id)->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);
    }

    /**
     * تدوير أرصدة الميزانية العمومية (الأصول والخصوم وحقوق الملكية) وإنشاء قيد الأرصدة الافتتاحية للسنة التالية
     */
    public function createOpeningBalancesForNextYear(FiscalYear $closedYear, User $user): ?JournalEntry
    {
        $nextYear = FiscalYear::whereDate('start_date', '>', $closedYear->end_date)
            ->orderBy('start_date', 'asc')
            ->first();

        // إنشاء السنة المالية الجديدة تلقائياً إذا لم تكن منشأة
        if (!$nextYear) {
            $nextStart = \Carbon\Carbon::parse($closedYear->end_date)->addDay()->startOfDay();
            $nextEnd = $nextStart->copy()->addYear()->subDay()->endOfDay();
            $nextYear = FiscalYear::create([
                'name' => (string) $nextStart->year,
                'start_date' => $nextStart,
                'end_date' => $nextEnd,
                'is_current' => true,
                'is_closed' => false,
                'notes' => 'تم إنشاؤها تلقائياً عند إقفال السنة المالية ' . ($closedYear->name ?? $closedYear->start_date->year),
            ]);
            $closedYear->update(['is_current' => false]);
        }

        if (JournalEntry::where('fiscal_year_id', $nextYear->id)
            ->where('entry_type', JournalEntry::ENTRY_TYPE_OPENING)
            ->exists()) {
            return null;
        }

        // جلب الأرصدة التراكمية الختامية لحسابات الميزانية: 1xx (أصول), 2xx (خصوم), 3xx (حقوق ملكية)
        $balances = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $closedYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->where(fn($q) =>
                $q->where('ta.code', 'like', '1%')
                  ->orWhere('ta.code', 'like', '2%')
                  ->orWhere('ta.code', 'like', '3%')
            )
            ->select('jed.tree_account_id', DB::raw('SUM(jed.debit) as total_debit'), DB::raw('SUM(jed.credit) as total_credit'))
            ->groupBy('jed.tree_account_id')
            ->havingRaw('ABS(SUM(jed.debit) - SUM(jed.credit)) >= 0.005')
            ->get();

        if ($balances->isEmpty()) {
            return null;
        }

        $openingEntry = JournalEntry::create([
            'fiscal_year_id' => $nextYear->id,
            'entry_date' => $nextYear->start_date,
            'entry_number' => JournalEntry::generateEntryNumber(),
            'entry_type' => JournalEntry::ENTRY_TYPE_OPENING,
            'source' => JournalEntry::SOURCE_CLOSING,
            'status' => JournalEntry::STATUS_POSTED,
            'description' => 'قيد الأرصدة الافتتاحية المدورة من السنة المالية ' . ($closedYear->name ?? $closedYear->start_date->year),
            'created_by' => $user->id,
            'posted_by' => $user->id,
            'posted_at' => now(),
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);

        foreach ($balances as $balance) {
            $net = (float) $balance->total_debit - (float) $balance->total_credit;

            if (abs($net) < 0.005) {
                continue;
            }

            if ($net > 0) {
                // رصيد مدين (أصول أو مدينين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $openingEntry->id,
                    'tree_account_id' => $balance->tree_account_id,
                    'debit' => abs($net),
                    'credit' => 0,
                    'description' => 'رصيد افتتاحي مدور',
                ]);
            } else {
                // رصيد دائن (خصوم، التزامات، حقوق ملكية، أرباح مبقاة)
                JournalEntryDetail::create([
                    'journal_entry_id' => $openingEntry->id,
                    'tree_account_id' => $balance->tree_account_id,
                    'debit' => 0,
                    'credit' => abs($net),
                    'description' => 'رصيد افتتاحي مدور',
                ]);
            }
        }

        $openingEntry->calculateTotals();
        $openingEntry->save();

        return $openingEntry;
    }

    /**
     * الحصول على حساب ملخص الدخل (Income Summary)
     */
    public function getIncomeSummaryAccount(): TreeAccounts
    {
        // 1. فحص الربط المخصص (Account Mapping)
        $mapping = AccountMapping::where('mapping_key', 'income_summary')->with('account')->first();
        if ($mapping && $mapping->account) {
            return $mapping->account;
        }

        // 2. فحص كود 3202
        $account = TreeAccounts::where('code', self::INCOME_SUMMARY_ACCOUNT_CODE)->first();
        if ($account) {
            return $account;
        }

        // 3. فحص الحساب بالاسم
        $account = TreeAccounts::where('code', 'like', '3%')
            ->where('is_leaf', true)
            ->where(function ($q) {
                $q->whereHas('translations', function ($t) {
                    $t->where('name', 'like', '%ملخص الدخل%')
                      ->orWhere('name', 'like', '%Income Summary%');
                });
            })
            ->first();

        if ($account) {
            return $account;
        }

        // 4. إنشاء الحساب تحت حقوق الملكية إن لم يكن موجوداً
        $parent = TreeAccounts::where('code', '32')->first() ?? TreeAccounts::where('code', '3')->first();
        return TreeAccounts::create([
            'code' => self::INCOME_SUMMARY_ACCOUNT_CODE,
            'parent_id' => $parent?->id,
            'level' => ($parent?->level ?? 1) + 1,
            'type' => TreeAccounts::TYPE_CREDIT,
            'account_type' => TreeAccounts::ACCOUNT_TYPE_EQUITY,
            'status' => TreeAccounts::STATUS_ACTIVE,
            'is_leaf' => true,
            'ar' => ['name' => 'ملخص الدخل'],
            'en' => ['name' => 'Income Summary'],
        ]);
    }

    /**
     * الحصول على حساب الأرباح المبقاة / المحتجزة (Retained Earnings)
     */
    public function getRetainedEarningsAccount(): TreeAccounts
    {
        // 1. فحص الربط المخصص (Account Mapping)
        $mapping = AccountMapping::where('mapping_key', 'retained_earnings')->with('account')->first();
        if ($mapping && $mapping->account) {
            return $mapping->account;
        }

        // 2. فحص كود 3201
        $account = TreeAccounts::where('code', self::RETAINED_EARNINGS_ACCOUNT_CODE)->first();
        if ($account) {
            return $account;
        }

        // 3. فحص كود 302
        $account = TreeAccounts::where('code', '302')->first();
        if ($account) {
            return $account;
        }

        // 4. فحص الحساب بالاسم
        $account = TreeAccounts::where('code', 'like', '3%')
            ->where('is_leaf', true)
            ->where(function ($q) {
                $q->whereHas('translations', function ($t) {
                    $t->where('name', 'like', '%أرباح مبقاة%')
                      ->orWhere('name', 'like', '%أرباح مرحلة%')
                      ->orWhere('name', 'like', '%أرباح محتجزة%')
                      ->orWhere('name', 'like', '%Retained Earnings%');
                });
            })
            ->first();

        if ($account) {
            return $account;
        }

        throw new \Exception('حساب الأرباح المبقاة / المرحلة (3201) غير معين وغير موجود في دليل الحسابات.');
    }
}
