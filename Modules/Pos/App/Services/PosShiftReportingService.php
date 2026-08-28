<?php

namespace Modules\Pos\App\Services;

use Modules\Pos\App\Models\PosSession;
use Modules\Pos\App\Models\PosSessionTransaction;
use App\Models\invApp\SalesInvoice;
use Illuminate\Support\Facades\DB;
use App\Models\AccuSoft\JournalEntry;

class PosShiftReportingService
{
    /**
     * Get the unified summary data for a POS Session.
     * This is the single source of truth for all POS Shift Reports.
     */
    public function getShiftSummary(PosSession $session)
    {
        $session->loadMissing(['device', 'cashier']);
        $deviceSettings = $session->device;

        // 1. Invoices & Returns Revenue
        $salesData = SalesInvoice::where('pos_session_id', $session->id)
            ->where('type_inv', SalesInvoice::TYPE_POS)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('
                COALESCE(SUM(total_exclusive_vat), 0) as pre_tax,
                COALESCE(SUM(total_vat), 0) as tax,
                COALESCE(SUM(total_inclusive_vat), 0) as post_tax
            ')->first();

        $returnsData = SalesInvoice::where('pos_session_id', $session->id)
            ->where('type_inv', SalesInvoice::TYPE_RETURN_POS)
            ->selectRaw('
                COALESCE(SUM(total_exclusive_vat), 0) as pre_tax,
                COALESCE(SUM(total_vat), 0) as tax,
                COALESCE(SUM(total_inclusive_vat), 0) as post_tax
            ')->first();

        // 2. Cost & Profitability
        // Sales Cost
        $salesCost = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->where('sales_invoices.pos_session_id', $session->id)
            ->where('sales_invoices.type_inv', SalesInvoice::TYPE_POS)
            ->where('sales_invoices.status', '!=', 'cancelled')
            ->sum(DB::raw('products.cost_price * sales_invoice_items.quantity'));

        // Returns Cost
        $returnsCost = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->where('sales_invoices.pos_session_id', $session->id)
            ->where('sales_invoices.type_inv', SalesInvoice::TYPE_RETURN_POS)
            ->sum(DB::raw('products.cost_price * sales_invoice_items.quantity'));

        $netSales = $salesData->pre_tax - $returnsData->pre_tax;
        $netCost = $salesCost - $returnsCost;
        $netProfit = $netSales - $netCost;

        // 3. Collections by Payment Method
        $paymentMethods = DB::table('pos_payment_methods')
            ->where('device_id', $session->device_id)
            ->get();

        $collections = [];
        $cashSalesAmt = 0;
        $cashReturnsAmt = 0;
        $cashMethodsIds = [];

        foreach ($paymentMethods as $method) {
            $methodSales = PosSessionTransaction::where('pos_session_id', $session->id)
                ->where('pos_payment_method_id', $method->id)
                ->where('type', PosSessionTransaction::TYPE_SALE)
                ->sum('amount');

            $methodReturns = abs(PosSessionTransaction::where('pos_session_id', $session->id)
                ->where('pos_payment_method_id', $method->id)
                ->where('type', PosSessionTransaction::TYPE_RETURN)
                ->sum('amount'));

            $collections[] = [
                'id' => $method->id,
                'name' => $method->name,
                'type' => $method->type,
                'account_id' => $method->account_id,
                'collected' => $methodSales,
                'returned' => $methodReturns,
                'net' => $methodSales - $methodReturns,
            ];

            if ($method->type === 'cash') {
                $cashSalesAmt += $methodSales;
                $cashReturnsAmt += $methodReturns;
                $cashMethodsIds[] = $method->id;
            }
        }

        // 4. Cash Drawer Movements
        $deposits = PosSessionTransaction::where('pos_session_id', $session->id)
            ->whereIn('pos_payment_method_id', $cashMethodsIds)
            ->where('type', PosSessionTransaction::TYPE_DEPOSIT)
            ->sum('amount');

        $withdrawals = abs(PosSessionTransaction::where('pos_session_id', $session->id)
            ->whereIn('pos_payment_method_id', $cashMethodsIds)
            ->where('type', PosSessionTransaction::TYPE_WITHDRAWAL)
            ->sum('amount'));

        $openingBalance = $session->opening_balance ?? 0;
        $expectedCash = $openingBalance + $cashSalesAmt - $cashReturnsAmt + $deposits - $withdrawals;
        $actualCash = $session->actual_cash ?? 0;
        
        // Use database value if closed, else calculate live
        if ($session->closed_at) {
            $variance = $session->difference ?? 0;
        } else {
            $variance = $actualCash - $expectedCash;
        }

        $varianceType = 'matched';
        if ($variance < 0) $varianceType = 'shortage';
        if ($variance > 0) $varianceType = 'overage';

        // 5. Accounting Entries Summary
        $autoJournalEntry = $deviceSettings ? $deviceSettings->auto_journal_entry : false;
        
        $journalEntries = [];
        // Closing/Settlement Entries
        $closingEntries = JournalEntry::where('reference_type', PosSession::class)
            ->where('reference_id', $session->id)
            ->get();
            
        foreach ($closingEntries as $je) {
            $journalEntries[] = [
                'id' => $je->id,
                'number' => $je->entry_number,
                'date' => $je->entry_date,
                'type' => __('Closing/Settlement'),
                'total' => $je->total_debit,
                'status' => $je->status
            ];
        }

        if ($autoJournalEntry) {
            // Include individual invoice JEs
            $invoiceIds = SalesInvoice::where('pos_session_id', $session->id)->where('type_inv', SalesInvoice::TYPE_POS)->pluck('id')->toArray();
            $invoiceEntries = JournalEntry::where('reference_type', SalesInvoice::class)
                ->whereIn('reference_id', $invoiceIds)
                ->get();
            foreach ($invoiceEntries as $je) {
                $journalEntries[] = [
                    'id' => $je->id,
                    'number' => $je->entry_number,
                    'date' => $je->entry_date,
                    'type' => __('Sales Invoice'),
                    'total' => $je->total_debit,
                    'status' => $je->status
                ];
            }

            // Include individual return JEs
            $returnIds = SalesInvoice::where('pos_session_id', $session->id)->where('type_inv', SalesInvoice::TYPE_RETURN_POS)->pluck('id')->toArray();
            $returnEntries = JournalEntry::where('reference_type', SalesInvoice::class)
                ->whereIn('reference_id', $returnIds)
                ->get();
            foreach ($returnEntries as $je) {
                $journalEntries[] = [
                    'id' => $je->id,
                    'number' => $je->entry_number,
                    'date' => $je->entry_date,
                    'type' => __('Return Invoice'),
                    'total' => $je->total_debit,
                    'status' => $je->status
                ];
            }
        }

        return [
            'session' => $session,
            'policy' => [
                'auto_journal_entry' => $autoJournalEntry,
                'policy_name' => $autoJournalEntry ? __('Immediate Posting (Per Invoice)') : __('Consolidated Posting (On Close)'),
            ],
            'revenue' => [
                'sales_pre_tax' => $salesData->pre_tax,
                'sales_tax' => $salesData->tax,
                'sales_post_tax' => $salesData->post_tax,
                'returns_pre_tax' => $returnsData->pre_tax,
                'returns_tax' => $returnsData->tax,
                'returns_post_tax' => $returnsData->post_tax,
                'net_sales' => $netSales,
                'cost_of_sales' => $salesCost,
                'cost_of_returns' => $returnsCost,
                'net_cost' => $netCost,
                'net_profit' => $netProfit,
            ],
            'collections' => $collections,
            'drawer' => [
                'opening_balance' => $openingBalance,
                'cash_sales' => $cashSalesAmt,
                'cash_returns' => $cashReturnsAmt,
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'variance' => $variance,
                'variance_type' => $varianceType,
                'net_cash_collected' => $cashSalesAmt - $cashReturnsAmt,
            ],
            'journal_entries' => collect($journalEntries)->sortBy('id')->values()->all()
        ];
    }

    /**
     * Get Cash Drawer Ledger sequentially.
     */
    public function getShiftCashLedger(PosSession $session)
    {
        $ledger = [];
        $runningBalance = 0;

        // 1. Opening
        if ($session->opening_balance > 0) {
            $runningBalance += $session->opening_balance;
            $ledger[] = [
                'time' => $session->opened_at,
                'type' => 'Opening Float',
                'description' => __('Opening Cash Drawer'),
                'in' => $session->opening_balance,
                'out' => 0,
                'balance' => $runningBalance
            ];
        }

        // 2. Transactions
        $transactions = PosSessionTransaction::with('paymentMethod')
            ->where('pos_session_id', $session->id)
            ->whereHas('paymentMethod', function ($q) {
                $q->where('type', 'cash');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($transactions as $tx) {
            $in = 0;
            $out = 0;
            $typeStr = '';
            
            if (in_array($tx->type, [PosSessionTransaction::TYPE_SALE, PosSessionTransaction::TYPE_DEPOSIT])) {
                $in = $tx->amount;
                $typeStr = $tx->type === PosSessionTransaction::TYPE_SALE ? 'Cash Sale' : 'Deposit';
            } else {
                $out = abs($tx->amount);
                $typeStr = $tx->type === PosSessionTransaction::TYPE_RETURN ? 'Cash Return' : 'Withdrawal';
            }

            $runningBalance += $in - $out;

            $ledger[] = [
                'time' => $tx->created_at,
                'type' => $typeStr,
                'description' => $tx->notes ?? __('Transaction') . ' #' . $tx->id,
                'in' => $in,
                'out' => $out,
                'balance' => $runningBalance
            ];
        }

        // 3. Closing adjustments
        if ($session->closed_at) {
            $expected = $runningBalance;
            $variance = $session->variance ?? 0;
            
            if ($variance != 0) {
                $typeStr = $variance < 0 ? 'Shortage' : 'Overage';
                $in = $variance > 0 ? $variance : 0;
                $out = $variance < 0 ? abs($variance) : 0;
                $runningBalance += $in - $out;

                $ledger[] = [
                    'time' => $session->closed_at,
                    'type' => $typeStr,
                    'description' => __('System Variance Adjustment'),
                    'in' => $in,
                    'out' => $out,
                    'balance' => $runningBalance
                ];
            }
            
            // Transfer to Safe
            if ($runningBalance > 0) {
                $transferOut = $runningBalance;
                $runningBalance -= $transferOut;
                $ledger[] = [
                    'time' => $session->closed_at,
                    'type' => 'Transfer',
                    'description' => __('Transfer Cash to Main Safe'),
                    'in' => 0,
                    'out' => $transferOut,
                    'balance' => $runningBalance
                ];
            }
        }

        return $ledger;
    }

    /**
     * Get Sold Items for the session.
     */
    public function getShiftSoldItems(PosSession $session)
    {
        // We will combine items from SalesInvoices (sales) and SalesInvoices (returns)
        $salesItems = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->where('sales_invoices.pos_session_id', $session->id)
            ->where('sales_invoices.type_inv', SalesInvoice::TYPE_POS)
            ->where('sales_invoices.status', '!=', 'cancelled')
            ->select(
                'products.id',
                'sales_invoice_items.product_name as name',
                'products.sku',
                DB::raw('SUM(sales_invoice_items.quantity) as sold_qty'),
                DB::raw('SUM(sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) as revenue'),
                DB::raw('SUM(products.cost_price * sales_invoice_items.quantity) as cost')
            )
            ->groupBy('products.id', 'sales_invoice_items.product_name', 'products.sku')
            ->get();

        $returnItems = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->where('sales_invoices.pos_session_id', $session->id)
            ->where('sales_invoices.type_inv', SalesInvoice::TYPE_RETURN_POS)
            ->select(
                'products.id',
                DB::raw('SUM(sales_invoice_items.quantity) as returned_qty'),
                DB::raw('SUM(sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) as return_revenue'),
                DB::raw('SUM(products.cost_price * sales_invoice_items.quantity) as return_cost')
            )
            ->groupBy('products.id')
            ->get()->keyBy('id');

        $result = [];
        foreach ($salesItems as $item) {
            $ret = $returnItems->get($item->id);
            $retQty = $ret ? $ret->returned_qty : 0;
            $retRev = $ret ? $ret->return_revenue : 0;
            $retCost = $ret ? $ret->return_cost : 0;

            $netQty = $item->sold_qty - $retQty;
            $netRev = $item->revenue - $retRev;
            $netCost = $item->cost - $retCost;
            $profit = $netRev - $netCost;

            $result[] = [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'sold_qty' => $item->sold_qty,
                'returned_qty' => $retQty,
                'net_qty' => $netQty,
                'net_revenue' => $netRev,
                'net_cost' => $netCost,
                'profit' => $profit,
            ];
        }

        // Add items that were only returned (rare, but possible if returned from a previous session but linked to this session? Usually POS returns are linked to the session they were performed in)
        foreach ($returnItems as $id => $ret) {
            if (!collect($result)->contains('id', $id)) {
                $product = DB::table('products')->where('id', $id)->first();
                $result[] = [
                    'id' => $id,
                    'name' => $product->sku, // Best effort fallback
                    'sku' => $product->sku,
                    'sold_qty' => 0,
                    'returned_qty' => $ret->returned_qty,
                    'net_qty' => -$ret->returned_qty,
                    'net_revenue' => -$ret->return_revenue,
                    'net_cost' => -$ret->return_cost,
                    'profit' => -($ret->return_revenue - $ret->return_cost),
                ];
            }
        }

        return collect($result)->sortByDesc('net_revenue')->values()->all();
    }
}
