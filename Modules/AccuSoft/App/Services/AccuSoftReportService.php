<?php

namespace Modules\AccuSoft\App\Services;

use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\Branch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccuSoftReportService
{
    // ==========================================
    // ACCOUNT NATURE (Removed, now relies directly on TreeAccounts 'type' field)
    // ==========================================

    // ==========================================
    // TRIAL BALANCE
    // ==========================================

    public function generateTrialBalance(?int $branchId, ?string $fromDate, ?string $toDate): array
    {
        $accounts = $this->getAccounts();
        $movements = $this->getTrialBalanceMovements($branchId, $fromDate, $toDate);

        $balances = $this->calculateBalancesWithPostOrder($accounts, $movements, [
            'opening_debit' => 'opening_debit',
            'opening_credit' => 'opening_credit',
            'period_debit' => 'period_debit',
            'period_credit' => 'period_credit',
        ], function ($node) {
            $openingBalance = $node['nature'] === 'debit' 
                ? $node['total_opening_debit'] - $node['total_opening_credit']
                : $node['total_opening_credit'] - $node['total_opening_debit'];

            $closingBalance = $node['nature'] === 'debit'
                ? ($node['total_opening_debit'] + $node['total_period_debit']) - ($node['total_opening_credit'] + $node['total_period_credit'])
                : ($node['total_opening_credit'] + $node['total_period_credit']) - ($node['total_opening_debit'] + $node['total_period_debit']);

            $directOpeningBalance = $node['nature'] === 'debit' 
                ? $node['direct_opening_debit'] - $node['direct_opening_credit']
                : $node['direct_opening_credit'] - $node['direct_opening_debit'];

            $directClosingBalance = $node['nature'] === 'debit'
                ? ($node['direct_opening_debit'] + $node['direct_period_debit']) - ($node['direct_opening_credit'] + $node['direct_period_credit'])
                : ($node['direct_opening_credit'] + $node['direct_period_credit']) - ($node['direct_opening_debit'] + $node['direct_period_debit']);

            return [
                'opening_debit_balance' => $node['nature'] === 'debit' ? $openingBalance : 0,
                'opening_credit_balance' => $node['nature'] === 'credit' ? $openingBalance : 0,
                'closing_debit_balance' => $node['nature'] === 'debit' ? $closingBalance : 0,
                'closing_credit_balance' => $node['nature'] === 'credit' ? $closingBalance : 0,
                
                'direct_opening_debit_balance' => $node['nature'] === 'debit' ? $directOpeningBalance : 0,
                'direct_opening_credit_balance' => $node['nature'] === 'credit' ? $directOpeningBalance : 0,
                'direct_closing_debit_balance' => $node['nature'] === 'debit' ? $directClosingBalance : 0,
                'direct_closing_credit_balance' => $node['nature'] === 'credit' ? $directClosingBalance : 0,
            ];
        });

        $hierarchicalData = $this->buildHierarchy($balances);

        return [
            'accounts' => $hierarchicalData,
            'totals' => $this->calculateTrialBalanceTotals($balances),
        ];
    }

    private function getTrialBalanceMovements(?int $branchId, ?string $fromDate, ?string $toDate): Collection
    {
        return DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_CLOSING]) // Exclude closing entries so the period balance doesn't zero out!
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->when($toDate, fn($q) => $q->where('je.entry_date', '<=', $toDate))
            ->select(
                'jed.tree_account_id', 
                DB::raw("SUM(CASE WHEN je.entry_type = " . JournalEntry::ENTRY_TYPE_OPENING . ($fromDate ? " OR je.entry_date < '$fromDate'" : "") . " THEN jed.debit ELSE 0 END) as opening_debit"), 
                DB::raw("SUM(CASE WHEN je.entry_type = " . JournalEntry::ENTRY_TYPE_OPENING . ($fromDate ? " OR je.entry_date < '$fromDate'" : "") . " THEN jed.credit ELSE 0 END) as opening_credit"), 
                DB::raw("SUM(CASE WHEN je.entry_type != " . JournalEntry::ENTRY_TYPE_OPENING . ($fromDate ? " AND je.entry_date >= '$fromDate'" : "") . " THEN jed.debit ELSE 0 END) as period_debit"), 
                DB::raw("SUM(CASE WHEN je.entry_type != " . JournalEntry::ENTRY_TYPE_OPENING . ($fromDate ? " AND je.entry_date >= '$fromDate'" : "") . " THEN jed.credit ELSE 0 END) as period_credit")
            )
            ->groupBy('jed.tree_account_id')
            ->get()
            ->keyBy('tree_account_id');
    }

    private function calculateTrialBalanceTotals(array $balances): array
    {
        // Only sum ROOT nodes to avoid double counting
        $rootNodes = array_filter($balances, fn($b) => empty($b['parent_id']));

        $totals = array_reduce(
            $rootNodes,
            function ($carry, $item) {
                $carry['opening_debit'] += $item['total_opening_debit'];
                $carry['opening_credit'] += $item['total_opening_credit'];
                $carry['period_debit'] += $item['total_period_debit'];
                $carry['period_credit'] += $item['total_period_credit'];
                $carry['closing_debit_balance'] += $item['closing_debit_balance'];
                $carry['closing_credit_balance'] += $item['closing_credit_balance'];
                return $carry;
            },
            ['opening_debit' => 0, 'opening_credit' => 0, 'period_debit' => 0, 'period_credit' => 0, 'closing_debit_balance' => 0, 'closing_credit_balance' => 0],
        );

        $totals['opening_difference'] = $totals['opening_debit'] - $totals['opening_credit'];
        $totals['period_difference'] = $totals['period_debit'] - $totals['period_credit'];
        $totals['closing_difference'] = $totals['closing_debit_balance'] - $totals['closing_credit_balance'];

        return $totals;
    }

    // ==========================================
    // INCOME STATEMENT
    // ==========================================

    public function generateIncomeStatement(?int $branchId, ?string $fromDate, ?string $toDate): array
    {
        $accounts = $this->getAccounts([
            TreeAccounts::ACCOUNT_TYPE_REVENUE, 
            TreeAccounts::ACCOUNT_TYPE_EXPENSE,
            TreeAccounts::ACCOUNT_TYPE_SALES,
            TreeAccounts::ACCOUNT_TYPE_PURCHASES,
            TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES
        ]);

        $movements = $this->getIncomeMovements($branchId, $fromDate, $toDate);

        $balances = $this->calculateBalancesWithPostOrder($accounts, $movements, ['debit' => 'debit', 'credit' => 'credit'], function ($node) {
            $balance = $node['nature'] === 'debit' 
                ? $node['total_debit'] - $node['total_credit'] 
                : $node['total_credit'] - $node['total_debit'];
            return ['balance' => $balance];
        });

        $hierarchicalData = $this->buildHierarchy($balances);

        $revenues = collect($hierarchicalData)->filter(fn($i) => $i['nature'] === 'credit')->values();
        $expenses = collect($hierarchicalData)->filter(fn($i) => $i['nature'] === 'debit')->values();

        $totalRevenue = $revenues->filter(fn($i) => empty($i['parent_id']))->sum('balance');
        $totalExpense = $expenses->filter(fn($i) => empty($i['parent_id']))->sum('balance');

        $netIncome = $totalRevenue - $totalExpense;

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netIncome' => $netIncome,
        ];
    }

    private function getIncomeMovements(?int $branchId, ?string $fromDate, ?string $toDate): Collection
    {
        return DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->when($fromDate, fn($q) => $q->where('je.entry_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->where('je.entry_date', '<=', $toDate))
            ->select('jed.tree_account_id', DB::raw('SUM(jed.debit) as debit'), DB::raw('SUM(jed.credit) as credit'))
            ->groupBy('jed.tree_account_id')
            ->get()
            ->keyBy('tree_account_id');
    }

    // ==========================================
    // BALANCE SHEET
    // ==========================================

    public function generateBalanceSheet(?int $branchId, string $fromDate, ?string $toDate): array
    {
        $accounts = $this->getAccounts([
            TreeAccounts::ACCOUNT_TYPE_ASSET, 
            TreeAccounts::ACCOUNT_TYPE_LIABILITY, 
            TreeAccounts::ACCOUNT_TYPE_EQUITY,
            TreeAccounts::ACCOUNT_TYPE_TREASURY,
            TreeAccounts::ACCOUNT_TYPE_BANK,
            TreeAccounts::ACCOUNT_TYPE_INVENTORY,
            TreeAccounts::ACCOUNT_TYPE_CUSTOMERS,
            TreeAccounts::ACCOUNT_TYPE_SUPPLIERS,
            TreeAccounts::ACCOUNT_TYPE_FIXED_ASSET
        ]);

        $movements = $this->getBalanceSheetMovements($branchId, $fromDate, $toDate);

        // Cumulative Net Income up to the Balance Sheet date (As-of Date)
        $netIncome = $this->calculateNetIncome($branchId, null, $toDate);

        // -- Virtual Movement Injection for Net Income --
        // Inject into Retained Earnings (Equity -> Credit Nature) BEFORE running Post-order Traversal
        if ($netIncome != 0) {
            $retainedEarningsAcc = $accounts->firstWhere('code', '3201'); // Standard Retained Earnings
            if ($retainedEarningsAcc) {
                if (!$movements->has($retainedEarningsAcc->id)) {
                    $movements->put($retainedEarningsAcc->id, (object)['debit' => 0, 'credit' => 0]);
                }
                if ($netIncome > 0) {
                    $movements->get($retainedEarningsAcc->id)->credit += abs($netIncome);
                } else {
                    $movements->get($retainedEarningsAcc->id)->debit += abs($netIncome);
                }
            }
        }

        $balances = $this->calculateBalancesWithPostOrder($accounts, $movements, ['debit' => 'debit', 'credit' => 'credit'], function ($node) {
            $balance = $node['nature'] === 'debit' 
                ? $node['total_debit'] - $node['total_credit'] 
                : $node['total_credit'] - $node['total_debit'];
            return ['balance' => $balance];
        });

        $hierarchicalData = $this->buildHierarchy($balances);

        $assets = collect($hierarchicalData)->filter(fn($i) => in_array($i['account_type'], [TreeAccounts::ACCOUNT_TYPE_ASSET, TreeAccounts::ACCOUNT_TYPE_TREASURY, TreeAccounts::ACCOUNT_TYPE_BANK, TreeAccounts::ACCOUNT_TYPE_INVENTORY, TreeAccounts::ACCOUNT_TYPE_CUSTOMERS, TreeAccounts::ACCOUNT_TYPE_FIXED_ASSET]))->values();
        $liabilities = collect($hierarchicalData)->filter(fn($i) => in_array($i['account_type'], [TreeAccounts::ACCOUNT_TYPE_LIABILITY, TreeAccounts::ACCOUNT_TYPE_SUPPLIERS]))->values();
        $equity = collect($hierarchicalData)->filter(fn($i) => in_array($i['account_type'], [TreeAccounts::ACCOUNT_TYPE_EQUITY]))->values();

        // Calculate totals using ROOT nodes (empty parent_id) to avoid double counting
        $totalAssets = $assets->filter(fn($i) => empty($i['parent_id']))->sum('balance');
        $totalLiabilities = $liabilities->filter(fn($i) => empty($i['parent_id']))->sum('balance');
        $totalEquity = $equity->filter(fn($i) => empty($i['parent_id']))->sum('balance');

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'totalLiabilitiesAndEquity' => $totalLiabilities + $totalEquity,
        ];
    }

    private function getBalanceSheetMovements(?int $branchId, ?string $fromDate, ?string $toDate): Collection
    {
        return DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_CLOSING])
            ->when($toDate, fn($q) => $q->where('je.entry_date', '<=', $toDate))
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->select('jed.tree_account_id', DB::raw('SUM(jed.debit) as debit'), DB::raw('SUM(jed.credit) as credit'))
            ->groupBy('jed.tree_account_id')
            ->get()
            ->keyBy('tree_account_id');
    }

    private function calculateNetIncome(?int $branchId, ?string $fromDate, ?string $toDate): float
    {
        return (float) (DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->when($fromDate, fn($q) => $q->where('je.entry_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->where('je.entry_date', '<=', $toDate))
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->where(function ($q) {
                $q->where('ta.code', 'like', '5%')
                  ->orWhere('ta.code', 'like', '4%');
            })
            ->selectRaw('COALESCE(SUM(jed.credit - jed.debit), 0) as net_income')
            ->value('net_income') ?? 0);
    }

    // ==========================================
    // ACCOUNT STATEMENT
    // ==========================================

    public function generateAccountStatement(array $data): array
    {
        $accountId = $data['accountId'] ?? throw new \Exception(__('accusoft::general.account_required'));
        $account = TreeAccounts::select('id', 'code', 'type', 'account_type')->findOrFail($accountId);
        $createdBy = $data['createdBy'] ?? null;
        $costCenterId = $data['costCenterId'] ?? $data['costCenter'] ?? null;

        $opening = $this->getOpeningBalance($accountId, $account->type, $costCenterId, $data['branchId'] ?? null, $data['fromDate'], $createdBy);

        $transactions = $this->getTransactions($accountId, $account->type, $costCenterId, $data['branchId'] ?? null, $data['fromDate'], $data['toDate'], $opening, $createdBy);

        $totals = $this->calculateAccountTotals($transactions, $opening, $account->type);

        return [
            'account' => [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'type_text' => $account->type_text,
                'account_type' => $account->account_type,
                'account_type_text' => $account->account_type_text,
            ],
            'branch_name' => $this->getBranchName($data['branchId'] ?? null),
            'user_name' => $createdBy ? (\App\Models\User::find($createdBy)?->name) : null,
            'period' => ['date_from' => $data['fromDate'], 'date_to' => $data['toDate']],
            'cost_center_id' => $costCenterId,
            'opening_balance' => $this->formatBalance($opening),
            'transactions' => $transactions,
            'totals' => $totals,
            'closing_balance' => $this->formatBalance([
                'debit' => $totals['closing_debit'],
                'credit' => $totals['closing_credit'],
                'balance' => $totals['closing_balance'],
                'balance_type' => $totals['closing_balance_type'],
            ]),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function getOpeningBalance(int $accountId, int $accountTypeNature, ?int $costCenterId, ?int $branchId, ?string $beforeDate, ?int $createdBy = null): array
    {
        $nature = $accountTypeNature == TreeAccounts::TYPE_DEBIT ? 'debit' : 'credit';

        if (!$beforeDate) {
            return ['debit' => 0, 'credit' => 0, 'balance' => 0, 'balance_type' => $nature];
        }

        $result = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->where('jed.tree_account_id', $accountId)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_CLOSING])
            ->whereDate('je.entry_date', '<', $beforeDate)
            ->when($costCenterId, fn($q) => $q->where('jed.cost_center_id', $costCenterId))
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->when($createdBy, fn($q) => $q->where('je.created_by', $createdBy))
            ->selectRaw('COALESCE(SUM(jed.debit), 0) as debit, COALESCE(SUM(jed.credit), 0) as credit')
            ->first();

        $debit = $result->debit ?? 0;
        $credit = $result->credit ?? 0;
        $balance = $nature === 'debit' ? $debit - $credit : $credit - $debit;

        return [
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
            'balance_type' => $nature,
        ];
    }

    private function getTransactions(int $accountId, int $accountTypeNature, ?int $costCenterId, ?int $branchId, ?string $dateFrom, ?string $dateTo, array $opening, ?int $createdBy = null): array
    {
        $details = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->leftJoin('cost_centers as cc', 'jed.cost_center_id', '=', 'cc.id')
            ->leftJoin('cost_center_translations as cct', function ($join) {
                $join->on('cc.id', '=', 'cct.cost_centers_id')->where('cct.locale', app()->getLocale());
            })
            ->leftJoin('users as u', 'je.created_by', '=', 'u.id')
            ->where('jed.tree_account_id', $accountId)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->when($dateFrom, fn($q) => $q->where('je.entry_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('je.entry_date', '<=', $dateTo))
            ->when($costCenterId, fn($q) => $q->where('jed.cost_center_id', $costCenterId))
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->when($createdBy, fn($q) => $q->where('je.created_by', $createdBy))
            ->select('jed.id', 'je.id as journal_entry_id', 'je.entry_date', 'je.entry_number', 'je.entry_type', 'je.description as entry_description', 'jed.description', 'jed.debit', 'jed.credit', 'cc.id as cost_center_id', 'cc.code as cost_center_code', 'cct.name as cost_center_name', 'u.name as creator_name')
            ->orderBy('je.entry_date')
            ->orderBy('jed.id')
            ->get();

        $nature = $accountTypeNature == TreeAccounts::TYPE_DEBIT ? 'debit' : 'credit';
        $transactions = [];
        $runningBalance = $opening['balance'];

        foreach ($details as $detail) {
            $movement = $nature === 'debit' ? $detail->debit - $detail->credit : $detail->credit - $detail->debit;
            $runningBalance += $movement;

            $transactions[] = [
                'id' => $detail->id,
                'journal_entry_id' => $detail->journal_entry_id,
                'date' => $detail->entry_date,
                'entry_number' => $detail->entry_number,
                'entry_type' => JournalEntry::types()[$detail->entry_type] ?? __('lang.unknown'),
                'description' => $detail->description ?? $detail->entry_description,
                'created_by_user' => $detail->creator_name ?? '—',
                'cost_center' => $detail->cost_center_id
                    ? [
                        'id' => $detail->cost_center_id,
                        'code' => $detail->cost_center_code,
                        'name' => $detail->cost_center_name,
                    ]
                    : null,
                'debit' => number_format($detail->debit, 2),
                'credit' => number_format($detail->credit, 2),
                'balance' => number_format($runningBalance, 2),
                'balance_type' => $runningBalance >= 0 ? 'debit' : 'credit',
            ];
        }

        return $transactions;
    }

    private function calculateAccountTotals(array $transactions, array $opening, int $accountTypeNature): array
    {
        $nature = $accountTypeNature == TreeAccounts::TYPE_DEBIT ? 'debit' : 'credit';
        $totalDebit = array_sum(array_map(fn($t) => (float) str_replace(',', '', $t['debit']), $transactions));
        $totalCredit = array_sum(array_map(fn($t) => (float) str_replace(',', '', $t['credit']), $transactions));
        
        $periodMovement = $nature === 'debit' ? $totalDebit - $totalCredit : $totalCredit - $totalDebit;
        $closingValue = $opening['balance'] + $periodMovement;

        return [
            'total_debit' => number_format($totalDebit, 2),
            'total_credit' => number_format($totalCredit, 2),
            'period_movement' => number_format($periodMovement, 2),
            'period_movement_type' => $nature,
            'closing_debit' => $nature === 'debit' ? $closingValue : 0, 
            'closing_credit' => $nature === 'credit' ? $closingValue : 0,
            'closing_balance' => $closingValue,
            'closing_balance_type' => $nature,
        ];
    }

    // ==========================================
    // COST CENTER STATEMENT
    // ==========================================

    public function generateCostCenterStatement(array $data): array
    {
        if (!$data['costCenterId']) {
            throw new \Exception(__('accusoft::general.cost_center_required'));
        }
        $costCenter = CostCenters::select('id', 'code')->findOrFail($data['costCenterId']);
        $transactions = $this->getCostCenterTransactions($data['costCenterId'], $data['branchId'] ?? null, $data['fromDate'] ?? null, $data['toDate'] ?? null);
        $totals = $this->calculateSimpleTotals($transactions);

        return [
            'cost_center' => ['id' => $costCenter->id, 'code' => $costCenter->code, 'name' => $costCenter->name],
            'branch_name' => $this->getbranchName($data['branchId'] ?? null),
            'period' => ['date_from' => $data['fromDate'] ?? null, 'date_to' => $data['toDate'] ?? null],
            'transactions' => $transactions,
            'totals' => ['total_debit' => number_format($totals['debit'], 2), 'total_credit' => number_format($totals['credit'], 2)],
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function getCostCenterTransactions(int $costCenterId, ?int $branchId, ?string $dateFrom, ?string $dateTo): array
    {
        $details = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->leftJoin('tree_account_translations as tat', function ($join) {
                $join->on('ta.id', '=', 'tat.tree_accounts_id')->where('tat.locale', app()->getLocale());
            })
            ->where('jed.cost_center_id', $costCenterId)
            ->whereIn('je.status', [JournalEntry::STATUS_POSTED, JournalEntry::STATUS_REVERSED])
            ->when($dateFrom, fn($q) => $q->whereDate('je.entry_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('je.entry_date', '<=', $dateTo))
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->select('jed.id', 'je.id as journal_entry_id', 'je.entry_date', 'je.entry_number', 'je.entry_type', 'je.description as entry_description', 'jed.description', 'jed.debit', 'jed.credit', 'ta.code as account_code', 'tat.name as account_name')
            ->orderBy('je.entry_date')
            ->orderBy('jed.id')
            ->get();

        return $details->map(fn($d) => [
            'id' => $d->id,
            'journal_entry_id' => $d->journal_entry_id,
            'date' => $d->entry_date,
            'entry_number' => $d->entry_number,
            'entry_type' => JournalEntry::types()[$d->entry_type] ?? __('lang.unknown'),
            'account' => ['code' => $d->account_code, 'name' => $d->account_name],
            'description' => $d->description ?? $d->entry_description,
            'debit' => number_format($d->debit, 2),
            'credit' => number_format($d->credit, 2),
        ])->toArray();
    }

    // ==========================================
    // CASH FLOW STATEMENT
    // ==========================================

    public function generateCashFlow(?int $branchId, string $fromDate, string $toDate): array
    {
        $netIncome = $this->calculateNetIncome($branchId, $fromDate, $toDate);

        // 4. Cash Reconciliation (Actual Balances) 
        $beginningCash = $this->getCashBalance($branchId, $fromDate);
        $toDatePlus1 = \Carbon\Carbon::parse($toDate)->addDay()->toDateString();
        $endingCash = $this->getCashBalance($branchId, $toDatePlus1);
        $netChangeInCash = $endingCash - $beginningCash;

        // Fetch Cash Accounts dynamically using account types
        $cashAccountsIds = TreeAccounts::whereIn('account_type', [TreeAccounts::ACCOUNT_TYPE_TREASURY, TreeAccounts::ACCOUNT_TYPE_BANK])->pluck('id')->toArray();
        $fixedAssetsIds = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_FIXED_ASSET)->pluck('id')->toArray();
        $equityIds = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_EQUITY)->pluck('id')->toArray();
        $liabilityIds = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_LIABILITY)->pluck('id')->toArray();

        // Analyze Actual Cash Movements for Investing and Financing
        $cashEntries = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->whereBetween('je.entry_date', [$fromDate, $toDate])
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->select('je.id as je_id', 'jed.tree_account_id', 'jed.debit', 'jed.credit')
            ->get();

        $investingCashFlow = 0.0;
        $financingLoansCashFlow = 0.0;
        $financingCapitalCashFlow = 0.0;

        foreach ($cashEntries->groupBy('je_id') as $lines) {
            $cashLines = $lines->filter(fn($l) => in_array($l->tree_account_id, $cashAccountsIds));
            if ($cashLines->isEmpty()) continue;

            $deltaCash = $cashLines->sum('debit') - $cashLines->sum('credit'); // Positive = Cash Inflow
            if (round($deltaCash, 4) == 0.0) continue;

            $nonCashLines = $lines->filter(fn($l) => !in_array($l->tree_account_id, $cashAccountsIds));
            
            // To allocate cash flow, we find non-cash lines whose offset (Credit - Debit) matches the sign of deltaCash.
            foreach ($nonCashLines as $l) {
                $l->offset = $l->credit - $l->debit; 
            }
            
            $matchingLines = $nonCashLines->filter(fn($l) => ($deltaCash > 0 && $l->offset > 0) || ($deltaCash < 0 && $l->offset < 0));
            $totalOffset = $matchingLines->sum('offset');

            if (round($totalOffset, 4) != 0.0) {
                foreach ($matchingLines as $l) {
                    $flow = $deltaCash * ($l->offset / $totalOffset);
                    if (in_array($l->tree_account_id, $fixedAssetsIds)) {
                        $investingCashFlow += $flow;
                    } elseif (in_array($l->tree_account_id, $liabilityIds)) {
                        $financingLoansCashFlow += $flow;
                    } elseif (in_array($l->tree_account_id, $equityIds)) {
                        $financingCapitalCashFlow += $flow;
                    }
                }
            }
        }

        // 1. Operating Activities 
        $operatingCashFlow = $netChangeInCash - $investingCashFlow - $financingLoansCashFlow - $financingCapitalCashFlow;

        $depreciationExpense = $this->getAccountBalanceChangeByCodePrefix('42304%', $branchId, $fromDate, $toDate);
        $receivablesChange = $this->getAccountBalanceChangeByTypes([TreeAccounts::ACCOUNT_TYPE_CUSTOMERS], $branchId, $fromDate, $toDate);
        $inventoryChange = $this->getAccountBalanceChangeByTypes([TreeAccounts::ACCOUNT_TYPE_INVENTORY], $branchId, $fromDate, $toDate);
        $payablesChange = $this->getAccountBalanceChangeByTypes([TreeAccounts::ACCOUNT_TYPE_SUPPLIERS], $branchId, $fromDate, $toDate);

        $calculatedOpFlow = $netIncome + $depreciationExpense - $receivablesChange - $inventoryChange - $payablesChange;
        $otherAdjustments = $operatingCashFlow - $calculatedOpFlow;

        return [
            'sections' => [
                'operating' => [
                    'netIncome' => $netIncome,
                    'adjustments' => ['depreciation' => $depreciationExpense],
                    'workingCapital' => [
                        'receivables' => -$receivablesChange,
                        'inventory' => -$inventoryChange,
                        'otherAssets' => $otherAdjustments,
                        'payables' => -$payablesChange,
                        'otherLiabilities' => 0,
                    ],
                    'total' => $operatingCashFlow,
                ],
                'investing' => [
                    'fixedAssets' => $investingCashFlow,
                    'total' => $investingCashFlow,
                ],
                'financing' => [
                    'loans' => $financingLoansCashFlow,
                    'capital' => $financingCapitalCashFlow,
                    'total' => $financingLoansCashFlow + $financingCapitalCashFlow,
                ],
            ],
            'reconciliation' => [
                'netChange' => $netChangeInCash,
                'beginning' => $beginningCash,
                'ending' => $endingCash,
            ],
        ];
    }

    private function getAccountBalanceChangeByTypes(array $accountTypes, ?int $branchId, string $fromDate, string $toDate): float
    {
        $movement = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->whereIn('je.status', [JournalEntry::STATUS_POSTED, JournalEntry::STATUS_REVERSED])
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_CLOSING])
            ->whereIn('ta.account_type', $accountTypes)
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->whereBetween('je.entry_date', [$fromDate, $toDate])
            ->selectRaw('COALESCE(SUM(jed.debit) - SUM(jed.credit), 0) as net_diff')
            ->value('net_diff') ?? 0;

        return (float) $movement;
    }

    private function getAccountBalanceChangeByCodePrefix(string $codePrefix, ?int $branchId, string $fromDate, string $toDate): float
    {
        $movement = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->whereIn('je.status', [JournalEntry::STATUS_POSTED, JournalEntry::STATUS_REVERSED])
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_CLOSING])
            ->where('ta.code', 'like', $codePrefix)
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->whereBetween('je.entry_date', [$fromDate, $toDate])
            ->selectRaw('COALESCE(SUM(jed.debit) - SUM(jed.credit), 0) as net_diff')
            ->value('net_diff') ?? 0;

        return (float) $movement;
    }

    private function getCashBalance(?int $branchId, string $beforeDate): float
    {
        return DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->whereIn('je.status', [JournalEntry::STATUS_POSTED, JournalEntry::STATUS_REVERSED])
            ->whereIn('ta.account_type', [TreeAccounts::ACCOUNT_TYPE_TREASURY, TreeAccounts::ACCOUNT_TYPE_BANK])
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->where('je.entry_date', '<', $beforeDate)
            ->selectRaw('COALESCE(SUM(jed.debit) - SUM(jed.credit), 0) as balance')
            ->value('balance') ?? 0;
    }

    // ==========================================
    // JOURNAL ENTRIES REPORT
    // ==========================================

    public function generateJournalEntriesReport(?string $fromDate, ?string $toDate, ?int $branchId, ?int $costCenterId, ?int $accountId, ?int $entryType): array
    {
        $query = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->leftJoin('tree_account_translations as tat', function ($join) {
                $join->on('ta.id', '=', 'tat.tree_accounts_id')->where('tat.locale', app()->getLocale());
            })
            ->leftJoin('cost_centers as cc', 'jed.cost_center_id', '=', 'cc.id')
            ->leftJoin('cost_center_translations as cct', function ($join) {
                $join->on('cc.id', '=', 'cct.cost_centers_id')->where('cct.locale', app()->getLocale());
            })
            ->whereIn('je.status', [JournalEntry::STATUS_POSTED, JournalEntry::STATUS_REVERSED])
            ->when($fromDate, fn($q) => $q->where('je.entry_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->where('je.entry_date', '<=', $toDate))
            ->when($branchId, fn($q) => $q->where('je.branch_id', $branchId))
            ->when($costCenterId, fn($q) => $q->where('jed.cost_center_id', $costCenterId))
            ->when($accountId, fn($q) => $q->where('jed.tree_account_id', $accountId))
            ->when($entryType !== null, fn($q) => $q->where('je.entry_type', $entryType))
            ->select(
                'je.id as journal_entry_id',
                'je.entry_number',
                'je.entry_date',
                'je.entry_type',
                'ta.code as account_code',
                'tat.name as account_name',
                'cct.name as cost_center_name',
                'jed.debit',
                'jed.credit',
                'jed.description as line_description',
                'je.description as main_description'
            )
            ->orderBy('je.entry_date', 'asc')
            ->orderBy('je.entry_number', 'asc');

        $items = $query->get();

        $journalTypes = JournalEntry::types();
        $formattedItems = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($items as $item) {
            $totalDebit += $item->debit;
            $totalCredit += $item->credit;
            
            $formattedItems[] = [
                'journal_entry_id' => $item->journal_entry_id,
                'entry_number' => $item->entry_number,
                'entry_date' => $item->entry_date,
                'entry_type' => $journalTypes[$item->entry_type] ?? __('lang.unknown'),
                'account_code' => $item->account_code,
                'account_name' => $item->account_name,
                'cost_center' => $item->cost_center_name,
                'description' => $item->line_description ?? $item->main_description,
                'debit' => number_format($item->debit, 2),
                'credit' => number_format($item->credit, 2),
            ];
        }

        return [
            'items' => $formattedItems,
            'total_debit' => number_format($totalDebit, 2),
            'total_credit' => number_format($totalCredit, 2),
        ];
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function getAccounts(?array $types = null): Collection
    {
        return TreeAccounts::where(function ($q) {
                $q->where('status', TreeAccounts::STATUS_ACTIVE)->orWhereNull('status');
            })
            ->when($types, fn($q) => $q->whereIn('account_type', $types))
            ->select('id', 'code', 'type', 'account_type', 'parent_id', 'level', 'is_leaf')
            ->orderBy('code')
            ->get()
            ->each(function ($acc) {
                // Attach name without performing extra queries inside the recursive func
                $acc->name = $acc->name; 
                $acc->account_type_text = $acc->account_type_text;
            })
            ->keyBy('id');
    }

    private function buildChildrenMap(Collection $accounts): array
    {
        $map = [];
        foreach ($accounts as $account) {
            if ($account->parent_id) {
                $map[$account->parent_id][] = $account->id;
            }
        }
        return $map;
    }

    /**
     * Post-order Traversal for Hierarchical Accumulation
     */
    private function calculateBalancesWithPostOrder(Collection $accounts, Collection $movements, array $fieldsMap, callable $balanceCalculator): array
    {
        $balances = [];
        $childrenMap = $this->buildChildrenMap($accounts);

        $traverse = function ($accountId) use (&$traverse, $accounts, $movements, $childrenMap, &$balances, $fieldsMap, $balanceCalculator) {
            $account = $accounts->get($accountId);
            $movement = $movements->get($accountId);
            
            // Initialize totals with direct movements
            $direct = [];
            $totals = [];
            foreach ($fieldsMap as $source => $dest) {
                $direct[$dest] = $movement ? ($movement->$source ?? 0) : 0;
                $totals[$dest] = $direct[$dest];
            }

            // Process children first (Post-order)
            if (isset($childrenMap[$accountId])) {
                foreach ($childrenMap[$accountId] as $childId) {
                    // Recursively calculate child if not already done
                    if (!isset($balances[$childId])) {
                        $traverse($childId);
                    }
                    
                    // Add child totals to current parent totals
                    foreach ($fieldsMap as $source => $dest) {
                        $totals[$dest] += $balances[$childId]['total_' . $dest] ?? 0;
                    }
                }
            }

            // Construct balance node
            $node = [
                'account_id' => $account->id,
                'account_code' => $account->code,
                'account_name' => $account->name,
                'parent_id' => $account->parent_id,
                'level' => $account->level,
                'is_leaf' => $account->is_leaf,
                'account_type' => $account->account_type,
                'account_type_text' => $account->account_type_text,
                'nature' => $account->type == TreeAccounts::TYPE_DEBIT ? 'debit' : 'credit',
            ];

            foreach ($fieldsMap as $source => $dest) {
                $node['direct_' . $dest] = $direct[$dest];
                $node['total_' . $dest] = $totals[$dest];
            }

            // Calculate final balances using the injected calculator
            $node = array_merge($node, $balanceCalculator($node));

            $balances[$accountId] = $node;
        };

        // Trigger traversal for all accounts
        foreach ($accounts as $account) {
            if (!isset($balances[$account->id])) {
                // If it's a root or its parent is missing from the filtered list, traverse it
                if (empty($account->parent_id) || !$accounts->has($account->parent_id)) {
                    $traverse($account->id);
                }
            }
        }

        return $balances;
    }

    private function buildHierarchy(array $balances): array
    {
        $grouped = [];
        foreach ($balances as $balance) {
            $parentId = $balance['parent_id'] ?? 0;
            $grouped[$parentId][] = $balance;
        }

        $result = [];
        $traverse = function($parentId) use (&$grouped, &$result, &$traverse) {
            if (!isset($grouped[$parentId])) return;
            foreach ($grouped[$parentId] as $node) {
                $result[] = $node;
                $traverse($node['account_id']);
            }
        };

        $traverse(0);
        return $result;
    }

    private function calculateSimpleTotals(array $transactions): array
    {
        return [
            'debit' => array_sum(array_map(fn($t) => (float) str_replace(',', '', $t['debit']), $transactions)),
            'credit' => array_sum(array_map(fn($t) => (float) str_replace(',', '', $t['credit']), $transactions)),
        ];
    }

    private function resolveFiscalPeriod(?int $fiscalYearId, ?string $dateFrom, ?string $dateTo): array
    {
        $fiscalYear = null;
        if ($fiscalYearId) {
            $fiscalYear = FiscalYear::find($fiscalYearId) ?? throw new \Exception(__('accusoft::general.fiscal_year_not_found'));
            $dateFrom = $dateFrom ?? $fiscalYear->start_date->format('Y-m-d');
            $dateTo = $dateTo ?? $fiscalYear->end_date->format('Y-m-d');
        } else {
            $fiscalYear = FiscalYear::current()->first();
            if ($fiscalYear) {
                $dateFrom = $dateFrom ?? $fiscalYear->start_date->format('Y-m-d');
                $dateTo = $dateTo ?? $fiscalYear->end_date->format('Y-m-d');
            }
        }
        return ['fiscal_year' => $fiscalYear, 'date_from' => $dateFrom, 'date_to' => $dateTo];
    }

    private function formatFiscalYear($fiscalYear): ?array
    {
        return $fiscalYear
            ? [
                'id' => $fiscalYear->id,
                'name' => $fiscalYear->full_name,
                'start_date' => $fiscalYear->start_date->format('Y-m-d'),
                'end_date' => $fiscalYear->end_date->format('Y-m-d'),
            ]
            : null;
    }

    private function getBranchName(?int $branchId): ?string
    {
        return $branchId ? Branch::find($branchId)?->name : null;
    }

    private function formatBalance(array $balance): array
    {
        return [
            'debit' => number_format($balance['debit'], 2),
            'credit' => number_format($balance['credit'], 2),
            'balance' => number_format($balance['balance'], 2),
            'balance_type' => $balance['balance_type'],
        ];
    }
}
