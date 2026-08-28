<?php

namespace Modules\Pos\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\TaxAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Modules\Pos\App\Models\PosDevice;
use Modules\Pos\App\Models\PosPaymentMethod;
use App\Models\BasicDataApp\Product;
use Illuminate\Support\Facades\Hash;
use App\Services\ProductService;
use App\Models\User;
use App\Models\invApp\SalesInvoice;
use DB;

class PosApiController extends Controller
{
    /**
     * Initialize POS devices and customers.
     */
    public function init(Request $request)
    {
        $query = PosDevice::where('is_active', true)
            ->with(['paymentMethods' => function($q) {
                $q->where('is_active', true)->with('account');
            }]);
            
        if ($request->has('device_id')) {
            $query->where('id', $request->device_id);
        }
        
        $devices = $query->get()->filter(function($device) {
            if ($device->is_users_linked) {
                $linkedUsers = is_array($device->linked_users) ? $device->linked_users : (json_decode($device->linked_users, true) ?? []);
                return in_array(auth()->id(), $linkedUsers);
            }
            return true;
        })->values();
        
        $customersQuery = \App\Models\invApp\InvCustomer::with('translations')
            ->select('id', 'phone')
            ->limit(100);

        // Include default customer if not in the first 100
        $defaultCustomer = null;
        if (!empty($devices) && isset($devices[0]->default_customer_id)) {
            $defaultCustomer = \App\Models\invApp\InvCustomer::with('translations')
                ->select('id', 'phone')
                ->find($devices[0]->default_customer_id);
        }

        $customers = $customersQuery->get();
        if ($defaultCustomer && !$customers->contains('id', $defaultCustomer->id)) {
            $customers->push($defaultCustomer);
        }

        $customers = $customers->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'devices' => $devices,
                'customers' => $customers,
            ]
        ]);
    }

    /**
     * Get active products for POS.
     */
    public function products(Request $request, ProductService $productService)
    {
        $deviceUuid = $request->header('X-Device-UUID');
        if ($deviceUuid) {
            $device = \Modules\Pos\App\Models\PosDevice::where('uuid', $deviceUuid)->first();
            if ($device) {
                $request->merge(['store' => $device->store_id]);
            }
        }

        // Use ProductService to get robust search, size splitting, and store filtering
        $request->merge(['q' => $request->search]);
        $data = $productService->searchProductsAndServices($request);
        
        $results = $data['results'] ?? [];
        
        $defaultTax = TaxAccount::first();
        // Enhance results with POS specific needs directly from ProductService structure
        $posProducts = array_map(function($p) use ($defaultTax) {
            $baseUnit = null;
            if (!empty($p['units'])) {
                // Find base unit
                foreach ($p['units'] as $u) {
                    if (!empty($u['is_base'])) {
                        $baseUnit = $u;
                        break;
                    }
                }
                // Fallback to first unit if no base unit
                if (!$baseUnit) {
                    $baseUnit = $p['units'][0];
                }
            }

            return [
                'id' => $p['id'],
                'name' => $p['text'],
                'barcode' => $p['barcode'],
                'price' => $p['sale_price'],
                'vat' => $p['tax_rate'] ?? $defaultTax->rate,
                'tax_id' => $p['tax_id'] ?? $defaultTax->id,
                'have_sizes' => $p['is_size'],
                'img' => $p['image_url'] ?? asset('uploads/images/products/no_img.jpg'),
                'unit' => $baseUnit ? $baseUnit['name'] : null,
                'unit_id' => $baseUnit ? $baseUnit['id'] : null,
                'quantity' => $p['quantity'] ?? 0,
            ];
        }, $results);

        return response()->json([
            'status' => true,
            'data' => [
                'data' => $posProducts // Wrapping in data for frontend compatibility
            ]
        ]);
    }

    /**
     * Process checkout and save SalesInvoice.
     */
    public function checkout(\Modules\Pos\App\Http\Requests\PosCheckoutRequest $request, \Modules\Pos\App\Services\PosCheckoutService $checkoutService)
    {
        $deviceUuid = $request->header('X-Device-UUID');
        if (!$deviceUuid) {
            return response()->json(['status' => false, 'message' => 'Missing Device UUID header'], 400);
        }

        try {
            $invoice = $checkoutService->processCheckout($request->validated(), $deviceUuid, auth()->id());

            return response()->json([
                'status' => true,
                'message' => __('Order processed successfully'),
                'invoice_id' => $invoice->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Checkout Exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function sessionStatus(Request $request)
    {
        $deviceUuid = $request->header('X-Device-UUID') ?? $request->device_uuid;
        $device = PosDevice::where('uuid', $deviceUuid)->first();

        if (!$device) {
            return response()->json([
                'status' => false,
                'message' => 'Device not found'
            ], 404);
        }
        
        $session = \Modules\Pos\App\Models\PosSession::where('device_id', $device->id)
            ->whereNull('closed_at')
            ->first();

        if ($session) {
            \Modules\Pos\App\Models\PosSessionAudit::create([
                'pos_session_id' => $session->id,
                'user_id' => auth()->id(),
                'device_id' => $device->id,
                'action' => \Modules\Pos\App\Models\PosSessionAudit::ACTION_SESSION_RESUMED_OR_TAKEN_OVER,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'status' => true,
                'has_active_session' => true,
                'session' => $session
            ]);
        }

        return response()->json([
            'status' => true,
            'has_active_session' => false,
            'session' => null
        ]);
    }

    public function openSession(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $deviceUuid = $request->header('X-Device-UUID') ?? $request->device_uuid;
        $device = PosDevice::where('uuid', $deviceUuid)->first();
        if (!$device) {
            return response()->json(['status' => false, 'message' => 'Device not found'], 404);
        }

        $activeOnDevice = \Modules\Pos\App\Models\PosSession::where('device_id', $device->id)
            ->whereNull('closed_at')
            ->first();

        if ($activeOnDevice) {
            $this->autoCloseSession($activeOnDevice);
        }

        $session = \Modules\Pos\App\Models\PosSession::create([
            'device_id' => $device->id,
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_balance' => $request->opening_balance,
            'status' => \Modules\Pos\App\Models\PosSession::STATUS_OPEN
        ]);

        \Modules\Pos\App\Models\PosSessionAudit::create([
            'pos_session_id' => $session->id,
            'user_id' => auth()->id(),
            'device_id' => $device->id,
            'action' => \Modules\Pos\App\Models\PosSessionAudit::ACTION_SESSION_OPENED,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Session opened successfully'),
            'session' => $session
        ]);
    }

    protected function autoCloseSession($session)
    {
        $cashMethods = PosPaymentMethod::where('device_id', $session->device_id)->where('type', 'cash')->pluck('id')->toArray();
        $cashSales = \Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->whereIn('pos_payment_method_id', $cashMethods)
            ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE)
            ->sum('amount');
        $cashReturns = abs(\Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->whereIn('pos_payment_method_id', $cashMethods)
            ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_RETURN)
            ->sum('amount'));
        $cashDeposits = \Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->whereIn('pos_payment_method_id', $cashMethods)
            ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_DEPOSIT)
            ->sum('amount');
        $cashWithdrawals = abs(\Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->whereIn('pos_payment_method_id', $cashMethods)
            ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_WITHDRAWAL)
            ->sum('amount'));
            
        $expectedCash = $session->opening_balance + $cashSales - $cashReturns + $cashDeposits - $cashWithdrawals;
        
        $session->update([
            'closed_at' => now(),
            'closing_balance' => $expectedCash,
            'shortage_amount' => 0,
            'overage_amount' => 0,
            'status' => \Modules\Pos\App\Models\PosSession::STATUS_CLOSED,
            'notes' => 'تم الإغلاق تلقائياً لفتح وردية جديدة',
        ]);
    }

    public function ping(Request $request)
    {
        $sessionId = $request->session_id;

        if (!$sessionId) {
             return response()->json(['valid' => false, 'reason' => 'missing_session_id']);
        }

        $session = \Modules\Pos\App\Models\PosSession::find($sessionId);

        if (!$session) {
            return response()->json(['valid' => false, 'reason' => 'not_found']);
        }

        if ($session->closed_at) {
            return response()->json(['valid' => false, 'reason' => 'session_closed']);
        }

        // We assume token validation is handled by Sanctum middleware.
        return response()->json(['valid' => true]);
    }

    /**
     * Close the active session and calculate variance.
     */
    public function closeSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:pos_sessions,id',
            'closing_balance' => 'required|numeric|min:0', // Actual counted cash
            'transfer_cash' => 'nullable|boolean',
        ]);

        $session = \Modules\Pos\App\Models\PosSession::with('device')->findOrFail($request->session_id);
        
        if ($session->closed_at) {
            return response()->json(['status' => false, 'message' => __('Session is already closed.')], 400);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Calculate expected cash
            $cashMethods = PosPaymentMethod::where('device_id', $session->device_id)->where('type', 'cash')->pluck('id')->toArray();
            $cashSales = \Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
                ->whereIn('pos_payment_method_id', $cashMethods)
                ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE)
                ->sum('amount');
            $cashReturns = abs(\Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
                ->whereIn('pos_payment_method_id', $cashMethods)
                ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_RETURN)
                ->sum('amount'));
            $cashDeposits = \Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
                ->whereIn('pos_payment_method_id', $cashMethods)
                ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_DEPOSIT)
                ->sum('amount');
            $cashWithdrawals = abs(\Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
                ->whereIn('pos_payment_method_id', $cashMethods)
                ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_WITHDRAWAL)
                ->sum('amount'));
                
            $expectedCash = $session->opening_balance + $cashSales - $cashReturns + $cashDeposits - $cashWithdrawals;
            
            $actualCash = $request->closing_balance;
            $variance = $actualCash - $expectedCash;

            $shortage = 0;
            $overage = 0;

            if ($variance < 0) {
                $shortage = abs($variance);
            } elseif ($variance > 0) {
                $overage = $variance;
            }

            // Accounting Entries for Variance using Device Settings
            $deviceSettings = $session->device;
            if ($deviceSettings) {
                if ($deviceSettings->auto_journal_entry) {
                    // Check if there are any unposted invoices in this session
                    $unpostedInvoicesCount = \App\Models\invApp\SalesInvoice::where('pos_session_id', $session->id)
                        ->whereNull('journal_entry_id')
                        ->count();
                    
                    if ($unpostedInvoicesCount > 0) {
                        throw new \Exception('لا يمكن إغلاق الوردية: يوجد فواتير أو مرتجعات غير مرحلة محاسبياً.');
                    }

                    $journalEntryService = app(\App\Services\AccuSoft\JournalEntryService::class);
                    $mainSafeAccountId = $deviceSettings->main_safe_account_id;
                    
                    // Get POS Drawer Account (Cash Payment Method)
                    $cashMethod = \Modules\Pos\App\Models\PosPaymentMethod::where('device_id', $session->device_id)
                        ->where('type', 'cash')
                        ->first();
                    $posDrawerAccountId = $cashMethod ? $cashMethod->account_id : null;

                    if ($actualCash > 0 && !$posDrawerAccountId) {
                        throw new \Exception('لا يمكن إغلاق الوردية: حساب صندوق نقطة البيع غير محدد.');
                    }

                    if ($shortage > 0 && !$deviceSettings->shortage_account_id) {
                        throw new \Exception('لا يمكن إغلاق الوردية: حساب عجز الصندوق غير محدد في إعدادات الجهاز.');
                    }

                    if ($overage > 0 && !$deviceSettings->overage_account_id) {
                        throw new \Exception('لا يمكن إغلاق الوردية: حساب زيادة الصندوق غير محدد في إعدادات الجهاز.');
                    }

                    $details = [];

                    // 1. Transfer actual cash from POS Drawer to Main Safe (if requested)
                    $transferCash = $request->boolean('transfer_cash', true);
                    if ($transferCash && $actualCash > 0 && $mainSafeAccountId && $mainSafeAccountId != $posDrawerAccountId) {
                        $details[] = [
                            'tree_account_id' => $mainSafeAccountId,
                            'debit' => $actualCash,
                            'credit' => 0,
                            'description' => __('Transfer Cash to Main Safe - Session #') . $session->id,
                        ];
                        $details[] = [
                            'tree_account_id' => $posDrawerAccountId,
                            'debit' => 0,
                            'credit' => $actualCash,
                            'description' => __('Transfer Cash to Main Safe - Session #') . $session->id,
                        ];
                    }

                    // 2. Shortage (if any)
                    if ($shortage > 0 && $deviceSettings->shortage_account_id) {
                        $details[] = [
                            'tree_account_id' => $deviceSettings->shortage_account_id,
                            'debit' => $shortage,
                            'credit' => 0,
                            'description' => __('POS Session Shortage - Session #') . $session->id,
                        ];
                        $details[] = [
                            'tree_account_id' => $posDrawerAccountId,
                            'debit' => 0,
                            'credit' => $shortage,
                            'description' => __('POS Session Shortage - Session #') . $session->id,
                        ];
                    }

                    // 3. Overage (if any)
                    if ($overage > 0 && $deviceSettings->overage_account_id) {
                        $details[] = [
                            'tree_account_id' => $posDrawerAccountId,
                            'debit' => $overage,
                            'credit' => 0,
                            'description' => __('POS Session Overage - Session #') . $session->id,
                        ];
                        $details[] = [
                            'tree_account_id' => $deviceSettings->overage_account_id,
                            'debit' => 0,
                            'credit' => $overage,
                            'description' => __('POS Session Overage - Session #') . $session->id,
                        ];
                    }

                    // Create the consolidated journal entry if there are details
                    if (!empty($details)) {
                        $journalEntryService->create([
                            'entry_date' => now()->format('Y-m-d'),
                            'description' => __('POS Session Closing - Session #') . $session->id,
                            'entry_type' => \App\Models\AccuSoft\JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                            'status' => \App\Models\AccuSoft\JournalEntry::STATUS_POSTED,
                            'reference_type' => \Modules\Pos\App\Models\PosSession::class,
                            'reference_id' => $session->id,
                            'details' => $details
                        ]);
                    }
                } else {
                    // Generate Consolidated Session Entry when immediate posting is disabled
                    $accountingService = app(\Modules\Invoices\App\Services\InvoiceAccountingService::class);
                    $accountingService->generateConsolidatedSessionEntry($session, $actualCash, $request->notes ?? '', $request->boolean('transfer_cash', true));
                }
            }

            $session->update([
                'closed_at' => now(),
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $variance,
                'status' => \Modules\Pos\App\Models\PosSession::STATUS_CLOSED,
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('Session closed successfully'),
                'session_id' => $session->id
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get invoices for the active session.
     */
    public function sessionInvoices(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:pos_sessions,id'
        ]);

        $session = \Modules\Pos\App\Models\PosSession::findOrFail($request->session_id);

        $query = \App\Models\invApp\SalesInvoice::where('pos_session_id', $session->id)
            ->with(['customer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->whereTranslationLike('name', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('id', 'desc')->get()->map(function($inv) {
            return [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'customer_name' => $inv->customer ? $inv->customer->name : 'عميل عام',
                'total' => $inv->total_inclusive_vat,
                'date' => $inv->issue_date ? \Carbon\Carbon::parse($inv->issue_date)->format('Y-m-d H:i') : null,
                'status' => $inv->status,
                'type' => in_array($inv->type_inv, [\App\Models\invApp\SalesInvoice::TYPE_RETURN, \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS]) ? 'مرتجع' : 'مبيعات'
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $invoices
        ]);
    }

    /**
     * Get invoice details for return
     */
    public function getInvoiceForReturn(Request $request, $id)
    {
        $device = $request->attributes->get('pos_device');
        if ($device && !$device->enable_pos_returns) {
            return response()->json(['status' => false, 'message' => __('الإرجاع غير مفعل في إعدادات هذا الجهاز')]);
        }

        $invoice = \App\Models\invApp\SalesInvoice::with(['items.product', 'customer'])->find($id);

        if (!$invoice) {
            return response()->json(['status' => false, 'message' => __('الفاتورة غير موجودة')]);
        }

        if ($invoice->type_inv == 2) {
            return response()->json(['status' => false, 'message' => __('لا يمكن إرجاع فاتورة مرتجع')]);
        }

        $items = $invoice->items->map(function($item) {
            return [
                'id' => $item->product_id,
                'name' => $item->product_name,
                'qty' => $item->quantity,
                'price' => $item->unit_price,
                'vat' => $item->vat_rate,
                'unit_id' => $item->unit_id,
                'have_sizes' => $item->have_sizes
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'invoice_number' => $invoice->invoice_number,
                'customer_id' => $invoice->customer_id,
                'discount_type' => $invoice->type_discount,
                'discount_amount' => $invoice->number_discount,
                'items' => $items
            ]
        ]);
    }

    /**
     * Record a cash withdrawal or deposit in the active session.
     */
    public function sessionTransaction(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:pos_sessions,id',
            'type' => 'required|in:' . \Modules\Pos\App\Models\PosSessionTransaction::TYPE_WITHDRAWAL . ',' . \Modules\Pos\App\Models\PosSessionTransaction::TYPE_DEPOSIT,
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'required|string|max:255',
        ]);

        $session = \Modules\Pos\App\Models\PosSession::findOrFail($request->session_id);

        if ($session->closed_at) {
            return response()->json(['status' => false, 'message' => __('Session is already closed.')], 400);
        }

        $cashMethod = PosPaymentMethod::where('device_id', $session->device_id)->where('type', 'cash')->first();

        $transaction = \Modules\Pos\App\Models\PosSessionTransaction::create([
            'pos_session_id' => $session->id,
            'pos_payment_method_id' => $cashMethod ? $cashMethod->id : null,
            'user_id' => auth()->id(),
            'amount' => $request->type == 'withdrawal' ? -$request->amount : $request->amount,
            'type' => $request->type,
            'notes' => $request->notes,
        ]);

        // Generate Journal Entry for POS Transaction (Withdrawal / Deposit)
        $deviceSettings = $session->device;
        if ($deviceSettings && $deviceSettings->auto_journal_entry && $cashMethod && $cashMethod->account_id) {
            $journalEntryService = app(\App\Services\AccuSoft\JournalEntryService::class);
            $posDrawerAccountId = $cashMethod->account_id;
            
            // Assume the offset account is the main safe account for deposits, and expense account for withdrawals
            $offsetAccountId = $request->type == 'withdrawal' ? $deviceSettings->expense_account_id : $deviceSettings->main_safe_account_id;
            
            if ($offsetAccountId && $offsetAccountId != $posDrawerAccountId) {
                $details = [];
                $amount = $request->amount;
                
                if ($request->type == 'withdrawal') {
                    // Withdrawal: Expense DEBIT, POS Drawer CREDIT
                    $details[] = [
                        'tree_account_id' => $offsetAccountId,
                        'debit' => $amount,
                        'credit' => 0,
                        'description' => __('POS Cash Withdrawal') . ' - ' . $request->notes,
                    ];
                    $details[] = [
                        'tree_account_id' => $posDrawerAccountId,
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => __('POS Cash Withdrawal') . ' - ' . $request->notes,
                    ];
                } else {
                    // Deposit: POS Drawer DEBIT, Safe CREDIT
                    $details[] = [
                        'tree_account_id' => $posDrawerAccountId,
                        'debit' => $amount,
                        'credit' => 0,
                        'description' => __('POS Cash Deposit') . ' - ' . $request->notes,
                    ];
                    $details[] = [
                        'tree_account_id' => $offsetAccountId,
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => __('POS Cash Deposit') . ' - ' . $request->notes,
                    ];
                }

                $journalEntryService->create([
                    'entry_date' => now()->format('Y-m-d'),
                    'description' => ($request->type == 'withdrawal' ? __('POS Withdrawal') : __('POS Deposit')) . ' - ' . $request->notes,
                    'entry_type' => \App\Models\AccuSoft\JournalEntry::ENTRY_TYPE_AUTO,
                    'status' => \App\Models\AccuSoft\JournalEntry::STATUS_POSTED,
                    'reference_type' => \Modules\Pos\App\Models\PosSessionTransaction::class,
                    'reference_id' => $transaction->id,
                    'branch_id' => $deviceSettings->branch_id,
                    'details' => $details
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => __('Transaction recorded successfully'),
        ]);
    }

    /**
     * Create a new customer from POS.
     */
    public function createCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        try {
            \DB::beginTransaction();

            // 1. تحديد الحساب الأب للعملاء من الربط المحاسبي (مثلاً: حساب المدينون)
            $parentId = \App\Models\AccuSoft\AccountMapping::getAccountId('customer');

            if (!$parentId) {
                throw new \Exception(__('يرجى ضبط الحساب الأب للعملاء في إعدادات الربط المحاسبي أولاً.'));
            }

            // 2. إنشاء سجل الحساب المالي في شجرة الحسابات
            $parentAccount = \App\Models\AccuSoft\TreeAccounts::find($parentId);
            $accountData = [
                'parent_id' => $parentId,
                'account_type' => \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_CUSTOMERS, // Assets - أصول
                'type' => 1, // Debit - مدين
                'is_leaf' => true,
                'level' => $parentAccount ? $parentAccount->level + 1 : 1,
                'status' => 1, // Active
                'code' => \App\Models\AccuSoft\TreeAccounts::generateCode($parentId),
                'ar' => ['name' => $request->name],
                'en' => ['name' => $request->name],
            ];

            $treeAccount = \App\Models\AccuSoft\TreeAccounts::create($accountData);

            // 3. ربط العميل بالحساب المالي الجديد وحفظ سجل العميل
            $customer = \App\Models\invApp\InvCustomer::create([
                'ar' => ['name' => $request->name],
                'en' => ['name' => $request->name],
                'phone' => $request->phone,
                'status' => 1,
                'tree_account_id' => $treeAccount->id,
            ]);

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('pos::pos.customer_created_success') ?? 'تم إضافة العميل بنجاح',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone
                ]
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
