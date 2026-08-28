<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\JournalEntryDetail;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\User;
use Carbon\Carbon;

class JournalEntrySeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $user = User::first() ?? User::factory()->create();
        $userId = $user->id;

        $this->command->info('Fetching required accounts...');

        $accounts = [
            'cash' => TreeAccounts::where('code', '10201001')->first(),
            'bank' => TreeAccounts::where('code', '10202001')->first(),
            'inventory' => TreeAccounts::where('code', '10203')->first(),
            'receivables' => TreeAccounts::where('code', '10204')->first(),
            'equipment' => TreeAccounts::where('code', '10102')->first(),
            'suppliers' => TreeAccounts::where('code', '20101')->first(),
            'loans' => TreeAccounts::where('code', '20201')->first(),
            'capital' => TreeAccounts::where('code', '301')->first(),
            'sales' => TreeAccounts::where('code', '401001')->first(),
            'other_revenue' => TreeAccounts::where('code', '402001')->first(),
            'rent' => TreeAccounts::where('code', '50201')->first(),
            'salary' => TreeAccounts::where('code', '50207')->first(),
            'electricity' => TreeAccounts::where('code', '50202')->first(),
            'other_expense' => TreeAccounts::where('code', '503001')->first(),
            'retained_earnings' => TreeAccounts::where('code', '302')->first(),
        ];

        foreach ($accounts as $key => $account) {
            if (!$account) {
                $this->command->error("Account '{$key}' not found. Aborting.");
                return;
            }
        }

        $this->command->info('All accounts found.');

        $startCurrentYear = Carbon::now()->startOfYear();
        $endCurrentYear = Carbon::now()->endOfYear();

        $fyCurrent = FiscalYear::firstOrCreate(
            ['start_date' => $startCurrentYear->format('Y-m-d')],
            [
                'end_date' => $endCurrentYear->format('Y-m-d'),
                'is_current' => true,
                'is_closed' => false,
                'notes' => 'السنة المالية الحالية ' . $startCurrentYear->year,
            ]
        );

        // قيد افتتاحي متوازن
        $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDay(), 'قيد افتتاحي',
            JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::STATUS_POSTED, $userId, [
            ['account' => $accounts['cash'], 'debit' => 50000, 'credit' => 0, 'desc' => 'نقد'],
            ['account' => $accounts['bank'], 'debit' => 100000, 'credit' => 0, 'desc' => 'بنك'],
            ['account' => $accounts['inventory'], 'debit' => 80000, 'credit' => 0, 'desc' => 'مخزون'],
            ['account' => $accounts['equipment'], 'debit' => 200000, 'credit' => 0, 'desc' => 'معدات'],
            ['account' => $accounts['capital'], 'debit' => 0, 'credit' => 300000, 'desc' => 'رأس المال'],
            ['account' => $accounts['loans'], 'debit' => 0, 'credit' => 130000, 'desc' => 'قروض'],
        ]);

        $this->command->info('Creating balanced sample entries...');

        // 1. مبيعات نقدية (100 قيد)
        for ($i = 1; $i <= 100; $i++) {
            $amount = rand(2000, 5000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "مبيعات نقدية #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['cash'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['sales'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 2. مبيعات آجلة (50 قيد)
        for ($i = 1; $i <= 50; $i++) {
            $amount = rand(3000, 8000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "مبيعات آجلة #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['receivables'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['sales'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 3. تحصيل من عملاء (40 قيد)
        for ($i = 1; $i <= 40; $i++) {
            $amount = rand(3000, 8000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "تحصيل من عميل #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['bank'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['receivables'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 4. شراء مخزون نقدي (30 قيد)
        for ($i = 1; $i <= 30; $i++) {
            $amount = rand(5000, 12000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "شراء مخزون نقدي #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['inventory'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['cash'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 5. شراء مخزون آجل (20 قيد)
        for ($i = 1; $i <= 20; $i++) {
            $amount = rand(5000, 10000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "شراء مخزون آجل #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['inventory'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['suppliers'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 6. دفع للموردين (15 قيد)
        for ($i = 1; $i <= 15; $i++) {
            $amount = rand(5000, 10000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "سداد لمورد #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['suppliers'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['bank'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 7. صرف رواتب (12 قيد - شهري تقريباً)
        for ($i = 1; $i <= 12; $i++) {
            $amount = 25000;
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addMonths($i - 1)->addDays(25),
                "صرف رواتب شهر $i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['salary'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['bank'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 8. دفع إيجار (12 قيد - شهري)
        for ($i = 1; $i <= 12; $i++) {
            $amount = 5000;
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addMonths($i - 1)->addDays(5),
                "دفع إيجار شهر $i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['rent'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['cash'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 9. دفع كهرباء (12 قيد - شهري)
        for ($i = 1; $i <= 12; $i++) {
            $amount = 2000;
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addMonths($i - 1)->addDays(15),
                "دفع كهرباء شهر $i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['electricity'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['cash'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 10. مصروفات متفرقة (20 قيد)
        for ($i = 1; $i <= 20; $i++) {
            $amount = rand(1000, 3000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "مصروفات متفرقة #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['other_expense'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['cash'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 11. إيرادات أخرى (10 قيد)
        for ($i = 1; $i <= 10; $i++) {
            $amount = rand(2000, 5000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "إيرادات أخرى #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_POSTED, $userId, [
                ['account' => $accounts['bank'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['other_revenue'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        // 12. بعض قيود مسودة (5 قيود)
        for ($i = 1; $i <= 5; $i++) {
            $amount = rand(3000, 8000);
            $this->createEntry($fyCurrent, $startCurrentYear->copy()->addDays(rand(1, 350)),
                "قيد مسودة #$i", JournalEntry::ENTRY_TYPE_MANUAL,
                JournalEntry::STATUS_DRAFT, $userId, [
                ['account' => $accounts['cash'], 'debit' => $amount, 'credit' => 0],
                ['account' => $accounts['other_revenue'], 'debit' => 0, 'credit' => $amount],
            ]);
        }

        $this->command->info('Journal entry seeding completed successfully.');
    }

    /**
     * إنشاء قيد متوازن
     */
    private function createEntry($fiscalYear, $date, $description, $type, $status, $userId, $details)
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($details as $detail) {
            $totalDebit += $detail['debit'];
            $totalCredit += $detail['credit'];
        }

        if ($totalDebit !== $totalCredit) {
            $this->command->error("Unbalanced: {$description} (D:{$totalDebit}, C:{$totalCredit})");
            return null;
        }

        $entry = JournalEntry::create([
            'entry_number' => $this->generateEntryNumber(),
            'entry_date' => $date,
            'description' => $description,
            'fiscal_year_id' => $fiscalYear->id,
            'entry_type' => $type,
            'status' => $status,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'created_by' => $userId,
            'branch_id' => 1,
            'posted_by' => $status == JournalEntry::STATUS_POSTED ? $userId : null,
            'posted_at' => $status == JournalEntry::STATUS_POSTED ? Carbon::now() : null,
        ]);

        foreach ($details as $detail) {
            JournalEntryDetail::create([
                'journal_entry_id' => $entry->id,
                'tree_account_id' => $detail['account']->id,
                'debit' => $detail['debit'],
                'credit' => $detail['credit'],
                'description' => $detail['desc'] ?? $description,
            ]);
        }

        return $entry;
    }

    /**
     * توليد رقم قيد
     */
    private function generateEntryNumber()
    {
        $maxId = JournalEntry::max('id') ?? 0;
        return 'JE-' . date('Y') . '-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);
    }
}
