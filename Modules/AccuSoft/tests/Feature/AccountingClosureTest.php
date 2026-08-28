<?php

namespace Modules\AccuSoft\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\JournalEntryDetail;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\AccuSoft\AccountMapping;
use Modules\AccuSoft\App\Services\AccountingClosureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AccountingClosureTest extends TestCase
{
    use RefreshDatabase;

    private $service;
    private $user;
    private $fiscalYear;
    private $revenueAccount;
    private $expenseAccount;
    private $retainedEarningsAccount;


    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AccountingClosureService();
        $this->user = User::factory()->create();

        // Create Fiscal Year
        $this->fiscalYear = FiscalYear::create([
            'start_date' => Carbon::now()->startOfYear(),
            'end_date' => Carbon::now()->endOfYear(),
            'is_current' => true,
            'is_closed' => false,
        ]);

        // Create Accounts
        $this->revenueAccount = TreeAccounts::create([
            'code' => '4001',
            'account_type' => TreeAccounts::ACCOUNT_TYPE_REVENUE,
            'type' => TreeAccounts::TYPE_CREDIT,
            'status' => TreeAccounts::STATUS_ACTIVE,
        ]);
        $this->revenueAccount->setTranslation('name', 'en', 'Sales Revenue');

        $this->expenseAccount = TreeAccounts::create([
            'code' => '5001',
            'account_type' => TreeAccounts::ACCOUNT_TYPE_EXPENSE,
            'type' => TreeAccounts::TYPE_DEBIT,
            'status' => TreeAccounts::STATUS_ACTIVE,
        ]);
        $this->expenseAccount->setTranslation('name', 'en', 'Office Expense');

        $this->retainedEarningsAccount = TreeAccounts::create([
            'code' => '302',
            'account_type' => TreeAccounts::ACCOUNT_TYPE_EQUITY,
            'type' => TreeAccounts::TYPE_CREDIT,
            'status' => TreeAccounts::STATUS_ACTIVE,
        ]);
        $this->retainedEarningsAccount->setTranslation('name', 'en', 'Retained Earnings');
    }

    public function test_can_close_fiscal_year_with_profit()
    {
        // 1. Create a Profit Scenario (Revenue > Expense)
        // Revenue: 1000 (Credit), Expense: 400 (Debit) -> Net: 600 Profit

        $entry = JournalEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'entry_date' => now(),
            'entry_number' => 'JE-001',
            'entry_type' => JournalEntry::ENTRY_TYPE_MANUAL,
            'status' => JournalEntry::STATUS_POSTED,
            'total_debit' => 1400,
            'total_credit' => 1400,
        ]);

        // Revenue (Credit 1000)
        JournalEntryDetail::create([
            'journal_entry_id' => $entry->id,
            'tree_account_id' => $this->revenueAccount->id,
            'debit' => 0,
            'credit' => 1000,
        ]);

        // Expense (Debit 400)
        JournalEntryDetail::create([
            'journal_entry_id' => $entry->id,
            'tree_account_id' => $this->expenseAccount->id,
            'debit' => 400,
            'credit' => 0,
        ]);

        // Balancing side (Bank/Cash) just to balance the entry
        // Bank Debit 600 (Net Cash In)
        $bank = TreeAccounts::create(['code' => '1001', 'account_type' => TreeAccounts::ACCOUNT_TYPE_ASSET, 'type' => 1]);
        JournalEntryDetail::create([
            'journal_entry_id' => $entry->id,
            'tree_account_id' => $bank->id,
            'debit' => 600,
            'credit' => 0,
        ]);


        // 2. Perform Closure
        $closingEntry = $this->service->closeFiscalYear($this->fiscalYear, $this->user);

        // 3. Assertions
        $this->assertTrue($this->fiscalYear->fresh()->is_closed);
        $this->assertEquals(JournalEntry::ENTRY_TYPE_CLOSING, $closingEntry->entry_type);

        // Check Details
        // Revenue should be Debited 1000 to close
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $closingEntry->id,
            'tree_account_id' => $this->revenueAccount->id,
            'debit' => 1000,
            'credit' => 0,
        ]);

        // Expense should be Credited 400 to close
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $closingEntry->id,
            'tree_account_id' => $this->expenseAccount->id,
            'debit' => 0,
            'credit' => 400,
        ]);

        // Retained Earnings should be Credited 600 (Profit)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $closingEntry->id,
            'tree_account_id' => $this->retainedEarningsAccount->id,
            'debit' => 0,
            'credit' => 600,
        ]);
    }

    public function test_can_close_fiscal_year_with_loss()
    {
        // 1. Create a Loss Scenario (Revenue < Expense)
        // Revenue: 200 (Credit), Expense: 500 (Debit) -> Net: -300 Loss

        $entry = JournalEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'entry_date' => now(),
            'entry_number' => 'JE-002',
            'entry_type' => JournalEntry::ENTRY_TYPE_MANUAL,
            'status' => JournalEntry::STATUS_POSTED,
        ]);

        // Revenue (Credit 200)
        JournalEntryDetail::create(['journal_entry_id' => $entry->id, 'tree_account_id' => $this->revenueAccount->id, 'debit' => 0, 'credit' => 200]);

        // Expense (Debit 500)
        JournalEntryDetail::create(['journal_entry_id' => $entry->id, 'tree_account_id' => $this->expenseAccount->id, 'debit' => 500, 'credit' => 0]);

        // Balancing (Bank Credit 300)
        $bank = TreeAccounts::create(['code' => '1002', 'account_type' => TreeAccounts::ACCOUNT_TYPE_ASSET, 'type' => 1]);
        JournalEntryDetail::create(['journal_entry_id' => $entry->id, 'tree_account_id' => $bank->id, 'debit' => 0, 'credit' => 300]); // Overdraft/Payable implies credit logic, simplifying here


        // 2. Perform Closure
        $closingEntry = $this->service->closeFiscalYear($this->fiscalYear, $this->user);

        // 3. Assertions
        $this->assertTrue($this->fiscalYear->fresh()->is_closed);

        // Retained Earnings should be Debited 300 (Loss)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $closingEntry->id,
            'tree_account_id' => $this->retainedEarningsAccount->id,
            'debit' => 300,
            'credit' => 0,
        ]);
    }

    public function test_cannot_close_if_draft_entries_exist()
    {
        JournalEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'entry_date' => now(),
            'entry_number' => 'JE-DRAFT',
            'status' => JournalEntry::STATUS_DRAFT,
        ]);

        $this->expectExceptionMessage(__('accusoft::general.cannot_close_with_draft_entries'));

        $this->service->closeFiscalYear($this->fiscalYear, $this->user);
    }

    public function test_can_auto_post_drafts_option()
    {
        // Create a draft entry
        JournalEntry::create([
            'fiscal_year_id' => $this->fiscalYear->id,
            'entry_date' => now(),
            'entry_number' => 'JE-DRAFT-AUTO',
            'status' => JournalEntry::STATUS_DRAFT,
            'total_debit' => 100,
            'total_credit' => 100,
        ]);

        // Try closing without option -> Should Fail
        try {
            $this->service->closeFiscalYear($this->fiscalYear, $this->user);
            $this->fail('Should have failed due to draft entries');
        } catch (\Exception $e) {
            $this->assertStringContainsString('draft', $e->getMessage());
        }

        // Try closing WITH option -> Should Success
        $this->service->closeFiscalYear($this->fiscalYear, $this->user, [
            AccountingClosureService::OPT_AUTO_POST_DRAFTS => true
        ]);

        $this->assertTrue($this->fiscalYear->fresh()->is_closed);

        // Assert draft became posted
        $this->assertDatabaseHas('journal_entries', [
            'entry_number' => 'JE-DRAFT-AUTO',
            'status' => JournalEntry::STATUS_POSTED
        ]);
    }
}
