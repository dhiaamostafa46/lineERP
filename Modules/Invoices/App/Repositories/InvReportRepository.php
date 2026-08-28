<?php

namespace Modules\Invoices\App\Repositories;

use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\PurchaseInvoice;
use App\Models\invApp\SalesInvoiceItem;
use Modules\Invoices\App\Models\PurchaseInvoiceItem;
use App\Models\invApp\InvCustomer;
use App\Models\invApp\InvSupplier;
use App\Models\StoreApp\Store;
use App\Models\User;
use App\Models\AccuSoft\TaxAccount;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvReportRepository
{
    /**
     * Apply scoped access to a query based on user permissions.
     */
    protected function applyScope($query, $permission, $userField = 'created_by')
    {
        if (auth()->check() && !auth()->user()->can($permission)) {
            $query->where($userField, auth()->id());
        }
        return $query;
    }



    /**
     * Get all customers for filters
     */
    public function getCustomers()
    {
        $query = InvCustomer::ActiveOnly();
        if (auth()->check() && !auth()->user()->can('global.viewBranches')) {
            $branchId = auth()->user()->branch_id;
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('salesInvoices', function ($qInv) use ($branchId) {
                      $qInv->where('branch_id', $branchId);
                  });
            });
        }
        return $query->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get all stores for filters
     */
    public function getStores()
    {
        return Store::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get all branches for filters
     */
    public function getBranches()
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get all employees (users) for filters
     */
    public function getEmployees()
    {
        return User::active()->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get invoice types for filters
     */
    public function getInvoiceTypes()
    {
        return collect(SalesInvoice::types())->prepend(__('lang.all'), '')->toArray();
    }

    /**
     * Get purchase invoice types for filters
     */
    public function getPurchaseInvoiceTypes()
    {
        return collect(PurchaseInvoice::types())->prepend(__('lang.all'), '')->toArray();
    }

    /**
     * Get invoice statuses for filters
     */

    public function getInvoiceStatuses()
    {
        return SalesInvoice::statuses();
    }

    /**
     * Get all suppliers for filters
     */
    public function getSuppliers()
    {
        $query = InvSupplier::activeOnly();
        if (auth()->check() && !auth()->user()->can('global.viewBranches')) {
            $branchId = auth()->user()->branch_id;
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('purchaseInvoices', function ($qInv) use ($branchId) {
                      $qInv->where('branch_id', $branchId);
                  });
            });
        }
        return $query->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get default from date
     */
    public function getFromDate()
    {
        return now()->startOfMonth()->format('Y-m-d');
    }

    /**
     * Get default to date
     */
    public function getToDate()
    {
        return now()->format('Y-m-d');
    }






    /**
     * Customer Aging Report with Detailed Logic
     */
    public function getCustomerAging($filters)
    {
        $query = InvCustomer::query();
        if (auth()->check() && !auth()->user()->can('global.viewBranches')) {
            $branchId = auth()->user()->branch_id;
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('salesInvoices', function ($qInv) use ($branchId) {
                      $qInv->where('branch_id', $branchId);
                  });
            });
        }

        if (!empty($filters['customer_id'])) {
            $query->where('id', $filters['customer_id']);
        }

        return $query->get()->map(function ($customer) use ($filters) {
            if (!$customer->tree_account_id) return null;

            $detailsQuery = \App\Models\AccuSoft\JournalEntryDetail::where('tree_account_id', $customer->tree_account_id)
                ->whereHas('journalEntry', function ($q) use ($filters) {
                    $q->where('status', \App\Models\AccuSoft\JournalEntry::STATUS_POSTED);
                    if (!empty($filters['branch_id'])) {
                        $q->where('branch_id', $filters['branch_id']);
                    }
                    if (!empty($filters['fromDate'])) {
                        $q->whereDate('entry_date', '>=', $filters['fromDate']);
                    }
                    if (!empty($filters['toDate'])) {
                        $q->whereDate('entry_date', '<=', $filters['toDate']);
                    }
                    if (!empty($filters['store_id'])) {
                        // الفلترة بالمستودع تتطلب أن يكون القيد مرتبطاً بفاتورة تحتوي على المستودع
                        $q->where(function($qStore) use ($filters) {
                            $qStore->whereHasMorph('reference', [\App\Models\invApp\SalesInvoice::class, \Modules\Invoices\App\Models\PurchaseInvoice::class], function ($query) use ($filters) {
                                $query->where('store_id', $filters['store_id']);
                            });
                        });
                    }
                })
                ->with('journalEntry');
            $details = $detailsQuery->get();

            $aging = [
                'current' => ['debit' => 0.0, 'credit' => 0.0],
                '1_30'    => ['debit' => 0.0, 'credit' => 0.0],
                '31_60'   => ['debit' => 0.0, 'credit' => 0.0],
                '61_90'   => ['debit' => 0.0, 'credit' => 0.0],
                'over_90' => ['debit' => 0.0, 'credit' => 0.0],
                'total'   => ['debit' => 0.0, 'credit' => 0.0, 'balance' => 0.0]
            ];

            foreach ($details as $entry) {
                if (!$entry->journalEntry || !$entry->journalEntry->entry_date) continue;

                $entryDate = Carbon::parse($entry->journalEntry->entry_date)->startOfDay();
                $days = now()->startOfDay()->diffInDays($entryDate);

                if ($days == 0) $bucket = 'current';
                elseif ($days <= 30) $bucket = '1_30';
                elseif ($days <= 60) $bucket = '31_60';
                elseif ($days <= 90) $bucket = '61_90';
                else $bucket = 'over_90';

                $d = (float) $entry->debit;
                $c = (float) $entry->credit;

                $aging[$bucket]['debit'] += $d;
                $aging[$bucket]['credit'] += $c;

                $aging['total']['debit'] += $d;
                $aging['total']['credit'] += $c;
            }

            $aging['total']['balance'] = $aging['total']['debit'] - $aging['total']['credit'];
            $aging['total_due'] = $aging['total']['balance'];

            $customer->aging = $aging;
            return $customer;
        })->filter(function ($c) use ($filters) {
            if (!$c) return false;
            if (!empty($filters['customer_id'])) {
                return ($c->aging['total']['debit'] > 0 || $c->aging['total']['credit'] > 0);
            }
            return abs(round($c->aging['total']['balance'], 2)) > 0;
        });
    }

    /**
     * Supplier Aging Report based on Journal Entries
     */
    public function getSupplierAging($filters)
    {
        $query = InvSupplier::query();
        if (auth()->check() && !auth()->user()->can('global.viewBranches')) {
            $branchId = auth()->user()->branch_id;
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('purchaseInvoices', function ($qInv) use ($branchId) {
                      $qInv->where('branch_id', $branchId);
                  });
            });
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('id', $filters['supplier_id']);
        }

        return $query->get()->map(function ($supplier) use ($filters) {
            if (!$supplier->tree_account_id) return null;

            $detailsQuery = \App\Models\AccuSoft\JournalEntryDetail::where('tree_account_id', $supplier->tree_account_id)
                ->whereHas('journalEntry', function ($q) use ($filters) {
                    $q->where('status', \App\Models\AccuSoft\JournalEntry::STATUS_POSTED);
                    if (!empty($filters['branch_id'])) {
                        $q->where('branch_id', $filters['branch_id']);
                    }
                    if (!empty($filters['fromDate'])) {
                        $q->whereDate('entry_date', '>=', $filters['fromDate']);
                    }
                    if (!empty($filters['toDate'])) {
                        $q->whereDate('entry_date', '<=', $filters['toDate']);
                    }
                    if (!empty($filters['store_id'])) {
                        $q->where(function($qStore) use ($filters) {
                            $qStore->whereHasMorph('reference', [\App\Models\invApp\SalesInvoice::class, \Modules\Invoices\App\Models\PurchaseInvoice::class], function ($query) use ($filters) {
                                $query->where('store_id', $filters['store_id']);
                            });
                        });
                    }
                })
                ->with('journalEntry');
            $details = $detailsQuery->get();

            $aging = [
                'current' => ['debit' => 0.0, 'credit' => 0.0],
                '1_30'    => ['debit' => 0.0, 'credit' => 0.0],
                '31_60'   => ['debit' => 0.0, 'credit' => 0.0],
                '61_90'   => ['debit' => 0.0, 'credit' => 0.0],
                'over_90' => ['debit' => 0.0, 'credit' => 0.0],
                'total'   => ['debit' => 0.0, 'credit' => 0.0, 'balance' => 0.0]
            ];

            foreach ($details as $entry) {
                if (!$entry->journalEntry || !$entry->journalEntry->entry_date) continue;

                $entryDate = Carbon::parse($entry->journalEntry->entry_date)->startOfDay();
                $days = now()->startOfDay()->diffInDays($entryDate);

                if ($days == 0) $bucket = 'current';
                elseif ($days <= 30) $bucket = '1_30';
                elseif ($days <= 60) $bucket = '31_60';
                elseif ($days <= 90) $bucket = '61_90';
                else $bucket = 'over_90';

                $d = (float) $entry->debit;
                $c = (float) $entry->credit;

                $aging[$bucket]['debit'] += $d;
                $aging[$bucket]['credit'] += $c;

                $aging['total']['debit'] += $d;
                $aging['total']['credit'] += $c;
            }

            $aging['total']['balance'] = $aging['total']['credit'] - $aging['total']['debit'];
            $aging['total_due'] = $aging['total']['balance'];

            $supplier->aging = $aging;
            return $supplier;
        })->filter(function ($s) use ($filters) {
            if (!$s) return false;
            if (!empty($filters['supplier_id'])) {
                return ($s->aging['total']['debit'] > 0 || $s->aging['total']['credit'] > 0);
            }
            return abs(round($s->aging['total']['balance'], 2)) > 0;
        });
    }

    /**
     * Product Profitability Report
     */
    public function getProductProfit($filters)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoice_items as sii')
            ->isolateBranch('si')
            ->join('sales_invoices as si', 'sii.sales_invoice_id', '=', 'si.id')
            ->leftJoin('product_units as pu', function ($join) {
                $join->on('sii.product_id', '=', 'pu.product_id')
                    ->on('sii.unit_id', '=', 'pu.unit_id');
            })
            ->leftJoin('stock_movements as sm', function ($join) {
                $join->on('sii.sales_invoice_id', '=', 'sm.reference_id')
                    ->where('sm.reference_type', '=', 'App\Models\invApp\SalesInvoice')
                    ->on('sii.product_id', '=', 'sm.product_id')
                    ->on('sii.unit_id', '=', 'sm.unit_id'); // ربط بالوحدة أيضاً لزيادة الدقة
            })
            ->whereIn('si.status', [2, 3, 4, 6, 7])
            ->whereBetween('si.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ])
            ->whereNull('si.deleted_at');

        if (!empty($filters['branch_id'])) {
            $query->where('si.branch_id', $filters['branch_id']);
        }
        if (!empty($filters['store_id'])) {
            $query->where('si.store_id', $filters['store_id']);
        }

        return $query->select(
                'sii.product_id',
                DB::raw('MAX(sii.product_name) as product_name'),
                // الكمية المحولة للوحدة الأساسية
                DB::raw('SUM(CASE WHEN si.type_inv IN (1, 3) 
                    THEN sii.quantity * COALESCE(pu.conversion_factor, 1) 
                    ELSE -(sii.quantity * COALESCE(pu.conversion_factor, 1)) 
                END) as total_qty'),
                // إجمالي المبيعات (صافي بعد الخصم)
                DB::raw('SUM(CASE WHEN si.type_inv IN (1, 3) 
                    THEN (sii.quantity * sii.unit_price) - sii.total_discount 
                    ELSE -((sii.quantity * sii.unit_price) - sii.total_discount) 
                END) as total_sales'),
                // إجمالي التكلفة من واقع حركات المخزون
                DB::raw('SUM(COALESCE(sm.total_cost, 0)) as total_cost')
            )
            ->groupBy('sii.product_id')
            ->get()
            ->map(function ($item) {
                $item->total_profit = $item->total_sales - $item->total_cost;
                return $item;
            });
    }

    /**
     * Daily ERP Summary - Full Match with View
     */
    public function getDailySummary($filters)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $salesQuery = SalesInvoice::query()->isolateBranch()
            ->whereBetween('issue_date', [Carbon::parse($fromDate)->startOfDay(), Carbon::parse($toDate)->endOfDay()])
            ->where('status', '!=', SalesInvoice::STATUS_DRAFT);

        $purchaseQuery = PurchaseInvoice::query()->isolateBranch()
            ->whereBetween('issue_date', [Carbon::parse($fromDate)->startOfDay(), Carbon::parse($toDate)->endOfDay()])
            ->where('status', '!=', PurchaseInvoice::STATUS_DRAFT);

        if (!empty($filters['branch_id'])) {
            $salesQuery->where('branch_id', $filters['branch_id']);
            $purchaseQuery->where('branch_id', $filters['branch_id']);
        }
        if (!empty($filters['store_id'])) {
            $salesQuery->where('store_id', $filters['store_id']);
            $purchaseQuery->where('store_id', $filters['store_id']);
        }
        
        if (!empty($filters['user_id'])) {
            $salesQuery->where(function ($q) use ($filters) {
                $q->where('created_by', $filters['user_id'])->orWhere('user_id', $filters['user_id']);
            });
            $purchaseQuery->where(function ($q) use ($filters) {
                $q->where('created_by', $filters['user_id'])->orWhere('user_id', $filters['user_id']);
            });
        }

        $this->applyScope($salesQuery, 'invoices.sales.scopedaccess');
        $this->applyScope($purchaseQuery, 'invoices.purchase.scopedaccess');

        $sales = (clone $salesQuery)->whereIn('type_inv', [SalesInvoice::TYPE_INVOICE, SalesInvoice::TYPE_DEBIT_NOTE])->selectRaw('COUNT(*) as count, COALESCE(SUM(total_inclusive_vat), 0) as total')->first();
        $salesReturns = (clone $salesQuery)->where('type_inv', SalesInvoice::TYPE_RETURN)->selectRaw('COUNT(*) as count, COALESCE(SUM(total_inclusive_vat), 0) as total')->first();
        $purchases = (clone $purchaseQuery)->whereIn('type_inv', [PurchaseInvoice::TYPE_INVOICE, PurchaseInvoice::TYPE_DEBIT_NOTE])->selectRaw('COUNT(*) as count, COALESCE(SUM(total_inclusive_vat), 0) as total')->first();
        $purchaseReturns = (clone $purchaseQuery)->where('type_inv', PurchaseInvoice::TYPE_RETURN)->selectRaw('COUNT(*) as count, COALESCE(SUM(total_inclusive_vat), 0) as total')->first();

        // توزيع طرق الدفع
        $paymentsQuery = DB::table('sales_invoice_payments as sip')
            ->isolateBranch('si')
            ->join('sales_invoices as si', 'sip.sales_invoice_id', '=', 'si.id')
            ->whereBetween('si.issue_date', [Carbon::parse($fromDate)->startOfDay(), Carbon::parse($toDate)->endOfDay()])
            ->where('si.status', '!=', SalesInvoice::STATUS_DRAFT);
            
        if (!empty($filters['branch_id'])) {
            $paymentsQuery->where('si.branch_id', $filters['branch_id']);
        }
        if (!empty($filters['store_id'])) {
            $paymentsQuery->where('si.store_id', $filters['store_id']);
        }
        if (!empty($filters['user_id'])) {
            $paymentsQuery->where(function ($q) use ($filters) {
                $q->where('si.created_by', $filters['user_id'])->orWhere('si.user_id', $filters['user_id']);
            });
        }

        $payments = $paymentsQuery->select('sip.payment_method_code', DB::raw('SUM(sip.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('sip.payment_method_code')
            ->get();

        // توزيع الحالات (ZATCA Statuses)
        $statuses = (clone $salesQuery)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_inclusive_vat) as total'))
            ->groupBy('status')
            ->get();

        return [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'sales' => $sales,
            'sales_returns' => $salesReturns,
            'purchases' => $purchases,
            'purchase_returns' => $purchaseReturns,
            'net_sales' => $sales->total - $salesReturns->total,
            'net_purchases' => $purchases->total - $purchaseReturns->total,
            'payments' => $payments,
            'statuses' => $statuses
        ];
    }

    /**
     * Generic Invoices Report for Lists
     */
    public function getInvoicesReport($filters, $modelClass, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = $modelClass::query()->isolateBranch();

        // Eager Loading لضمان ظهور البيانات (الموظف، العميل/المورد)
        if ($modelClass === SalesInvoice::class) {
            $query->with(['user', 'customer']);
        } elseif ($modelClass === PurchaseInvoice::class) {
            $query->with(['user', 'supplier', 'createdBy']);
        }

        // نطاق التاريخ
        if ($fromDate && $toDate) {
            $query->whereBetween('issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        // تطبيق نطاق الوصول (Scoped Access)
        $permission = ($modelClass === SalesInvoice::class) ? 'invoices.sales.scopedaccess' : 'invoices.purchase.scopedaccess';
        $this->applyScope($query, $permission);

        // الفلاتر الإضافية
        if (!empty($filters['customer_id'])) $query->where('customer_id', $filters['customer_id']);
        if (!empty($filters['supplier_id'])) $query->where('supplier_id', $filters['supplier_id']);
        if (!empty($filters['type_inv'])) $query->where('type_inv', $filters['type_inv']);
        if (!empty($filters['user_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('created_by', $filters['user_id'])->orWhere('user_id', $filters['user_id']);
            });
        }
        if (!empty($filters['store_id'])) $query->where('store_id', $filters['store_id']);
        if (!empty($filters['branch_id'])) $query->where('branch_id', $filters['branch_id']);

        $totalsQuery = clone $query;
        $summary = $totalsQuery->selectRaw("
            SUM(CASE WHEN type_inv != 2 THEN total_exclusive_vat ELSE 0 END) as gross_exclusive_vat,
            SUM(CASE WHEN type_inv = 2 THEN total_exclusive_vat ELSE 0 END) as return_exclusive_vat,
            SUM(CASE WHEN type_inv != 2 THEN total_vat ELSE 0 END) as gross_vat,
            SUM(CASE WHEN type_inv = 2 THEN total_vat ELSE 0 END) as return_vat,
            SUM(CASE WHEN type_inv != 2 THEN total_inclusive_vat ELSE 0 END) as gross_inclusive_vat,
            SUM(CASE WHEN type_inv = 2 THEN total_inclusive_vat ELSE 0 END) as return_inclusive_vat
        ")->first();

        $grossExcl = (float) ($summary->gross_exclusive_vat ?? 0);
        $returnExcl = (float) ($summary->return_exclusive_vat ?? 0);

        $grossVat = (float) ($summary->gross_vat ?? 0);
        $returnVat = (float) ($summary->return_vat ?? 0);

        $grossIncl = (float) ($summary->gross_inclusive_vat ?? 0);
        $returnIncl = (float) ($summary->return_inclusive_vat ?? 0);

        $totals = [
            'gross_exclusive_vat' => $grossExcl,
            'return_exclusive_vat' => $returnExcl,
            'net_exclusive_vat' => $grossExcl - $returnExcl,

            'gross_vat' => $grossVat,
            'return_vat' => $returnVat,
            'net_vat' => $grossVat - $returnVat,

            'gross_inclusive_vat' => $grossIncl,
            'return_inclusive_vat' => $returnIncl,
            'net_inclusive_vat' => $grossIncl - $returnIncl,
        ];

        $invoices = $query->orderBy('issue_date', 'desc')->paginate($perPage);
        $invoices->accountingTotals = $totals;

        return $invoices;
    }

    public function getTaxReportData($filters)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $calc = function($modelClass, $dateField) use ($fromDate, $toDate, $filters) {
            $query = $modelClass::whereBetween($dateField, [
                    Carbon::parse($fromDate)->startOfDay(),
                    Carbon::parse($toDate)->endOfDay()
                ])
                ->isolateBranch()
                ->where('status', '!=', 1) // Assuming 1 is Draft
                ->with('items');
                
            if (!empty($filters['branch_id'])) {
                $query->where('branch_id', $filters['branch_id']);
            }
            
            $invoices = $query->get();

            $res = [
                'standard' => ['amount' => 0, 'vat' => 0, 'adj' => 0],
                'zero'     => ['amount' => 0, 'vat' => 0, 'adj' => 0],
                'exempt'   => ['amount' => 0, 'vat' => 0, 'adj' => 0],
            ];

            foreach ($invoices as $inv) {
                $isReturn = ($inv->type_inv == 2);

                foreach ($inv->items as $item) {
                    $taxId = $item->tax_id;
                    $lineTotal = ($item->quantity * $item->unit_price) - ($item->total_discount ?? 0);
                    $lineVat = (float)($item->vat_amount ?? 0);

                    // 1. Strictly determine Bucket
                    if ($taxId == 2) {
                        $bucket = 'exempt';
                    } elseif ($taxId == 3) {
                        $bucket = 'zero';
                    } else {
                        $bucket = 'standard'; // Default for 1 or null
                    }

                    // 2. Enforce Tax Rates & Fix Missing VAT
                    if ($bucket === 'exempt' || $bucket === 'zero') {
                        $lineVat = 0.00; // Must be 0
                    } elseif ($bucket === 'standard' && $lineVat == 0) {
                        // Computationally enforce 15% VAT for Standard if missing
                        $rate = (float)($item->vat_rate ?? 15);
                        if ($rate <= 0) $rate = 15;
                        $lineVat = $lineTotal * ($rate / 100);
                    }

                    // 3. Apply to Bucket
                    if ($isReturn) {
                        $res[$bucket]['adj'] -= $lineTotal;
                        $res[$bucket]['vat'] -= $lineVat;
                    } else {
                        // Regular Invoices and Debit Notes (type_inv 1 and 3)
                        $res[$bucket]['amount'] += $lineTotal;
                        $res[$bucket]['vat'] += $lineVat;
                    }
                }
                
                // Also add shipping tax
                if ($inv->shipping_cost > 0) {
                    $taxId = $inv->shipping_tax_id;
                    $lineTotal = collect($inv->shipping_cost)->sum();
                    $lineVat = (float)($inv->shipping_vat_amount ?? 0);

                    if ($taxId == 2) {
                        $bucket = 'exempt';
                    } elseif ($taxId == 3) {
                        $bucket = 'zero';
                    } else {
                        $bucket = 'standard'; // Default for 1 or null
                    }

                    if ($bucket === 'exempt' || $bucket === 'zero') {
                        $lineVat = 0.00;
                    } elseif ($bucket === 'standard' && $lineVat == 0) {
                        $rate = (float)($inv->shipping_vat_rate ?? 15);
                        if ($rate <= 0) $rate = 15;
                        $lineVat = $lineTotal * ($rate / 100);
                    }

                    if ($isReturn) {
                        $res[$bucket]['adj'] -= $lineTotal;
                        $res[$bucket]['vat'] -= $lineVat;
                    } else {
                        $res[$bucket]['amount'] += $lineTotal;
                        $res[$bucket]['vat'] += $lineVat;
                    }
                }
            }
            return $res;
        };

        $sales = $calc(SalesInvoice::class, 'issue_date');
        $purchases = $calc(PurchaseInvoice::class, 'issue_date');

        return [
            'sales' => $sales,
            'purchases' => $purchases,
            'summary' => [
                'total_due' => $sales['standard']['vat'] - $purchases['standard']['vat'],
                'carried_forward' => 0,
                'corrections' => 0,
                'net_final' => $sales['standard']['vat'] - $purchases['standard']['vat'],
            ]
        ];
    }
}

