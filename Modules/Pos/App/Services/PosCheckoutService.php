<?php

namespace Modules\Pos\App\Services;

use DB;
use Exception;
use Modules\Pos\App\Models\PosDevice;
use Modules\Pos\App\Models\PosPaymentMethod;
use Modules\Pos\App\Models\PosSession;
use Modules\Pos\App\Models\PosSessionTransaction;
use Modules\Invoices\App\Repositories\SalesInvoiceRepository;

class PosCheckoutService
{
    protected $salesInvoiceRepository;
    protected $salesReturnInvoiceRepository;

    public function __construct(
        SalesInvoiceRepository $salesInvoiceRepository,
        \Modules\Invoices\App\Repositories\SalesReturnInvoiceRepository $salesReturnInvoiceRepository
    ) {
        $this->salesInvoiceRepository = $salesInvoiceRepository;
        $this->salesReturnInvoiceRepository = $salesReturnInvoiceRepository;
    }

    /**
     * Process POS Checkout transaction.
     *
     * @param array $data
     * @param string $deviceUuid
     * @param int $userId
     * @return \App\Models\invApp\SalesInvoice
     * @throws Exception
     */
    public function processCheckout(array $data, string $deviceUuid, int $userId)
    {
        DB::beginTransaction();

        try {
            $device = PosDevice::with(['store', 'branch'])->where('uuid', $deviceUuid)->first();
            if (!$device) {
                throw new Exception(__('Device not found'));
            }

            // Inject setting for stock validation trait
            app()->instance('pos_allow_negative_stock', (bool) $device->allow_negative_stock);

            // Find active session
            $session = PosSession::where('device_id', $device->id)
                ->where('user_id', $userId)
                ->whereNull('closed_at')
                ->first();

            if (!$session) {
                throw new Exception(__('No active session found. Please open a shift first.'));
            }

            $isReturn = $data['is_return'] ?? false;

            // Map frontend cart to SalesInvoice items structure
            $invoiceItems = [];

            foreach ($data['items'] as $item) {
                $invoiceItems[] = [
                    'product_id' => $item['id'],
                    'product_name' => $item['name'] ?? 'Unknown',
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'vat_rate' => $item['vat'] ?? 0,
                    'type_discount' => $item['type_discount'] ?? 1,
                    'number_discount' => $item['number_discount'] ?? 0,
                    'tax_id' => $item['tax_id'] ?? null,
                    'unit_id' => $item['unit_id'] ?? null,
                    'have_sizes' => $item['have_sizes'] ?? false,
                    'store_id' => $device->store_id, // For stock deduction
                ];
            }

            // Map payments
            $invoicePayments = [];
            $sessionTransactions = [];
            
            foreach ($data['payments'] as $payment) {
                $method = PosPaymentMethod::find($payment['method_id']);
                if ($method && $method->account_id) {
                    $invoicePayments[] = [
                        'payment_method_code' => $method->type == 'cash' ? '10' : '48', // 10 Cash, 48 Bank Card
                        'account_id' => $method->account_id,
                        'amount' => $payment['amount'],
                    ];
                    
                    // Log session transaction
                    $sessionTransactions[] = [
                        'pos_session_id' => $session->id,
                        'pos_payment_method_id' => $method->id,
                        'user_id' => $userId,
                        'amount' => $isReturn ? -abs($payment['amount']) : abs($payment['amount']),
                        'type' => $isReturn ? PosSessionTransaction::TYPE_RETURN : PosSessionTransaction::TYPE_SALE,
                        'notes' => $isReturn ? 'POS Return' : 'POS Sale',
                    ];
                }
            }

            // Apply calculation service instead of trusting frontend directly
            $calculator = new \Modules\Invoices\App\Services\InvoiceCalculatorService();
            $calcResult = $calculator->calculate(
                $invoiceItems,
                (float)($data['number_discount'] ?? ($data['discount'] ?? 0)),
                (int)($data['type_discount'] ?? 1),
                $device->prices_include_tax,
                (float)($data['shipping_cost'] ?? 0),
                (float)($data['shipping_vat_rate'] ?? 0)
            );

            // Prepare Invoice Data
            $typeInv = $isReturn ? \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS : \App\Models\invApp\SalesInvoice::TYPE_POS;
            
            $invoiceData = [
                'type_inv' => $typeInv,
                'status' => \App\Models\invApp\SalesInvoice::STATUS_SUBMITTED, // Force submission immediately
                'customer_id' => !empty($data['customer_id']) ? $data['customer_id'] : $device->default_customer_id,
                'branch_id' => $device->branch_id,
                'store_id' => $device->store_id,
                'issue_date' => now(),
                'pos_session_id' => $session->id,
                'type_discount' => $data['type_discount'] ?? 1,
                'number_discount' => $data['number_discount'] ?? ($data['discount'] ?? 0),
                'total_exclusive_vat' => $calcResult['total_exclusive_vat'],
                'total_vat' => $calcResult['total_vat'],
                'total_inclusive_vat' => $calcResult['total_inclusive_vat'],
                'total_discount' => $calcResult['total_discount'],
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'shipping_vat_rate' => $data['shipping_vat_rate'] ?? 0,
                'shipping_tax_id' => $data['shipping_tax_id'] ?? null,
                'items' => $calcResult['items'],
                'payments' => $invoicePayments,
                'parent_id' => $data['parent_id'] ?? null,
            ];

            // 1. Create Invoice or Return (this also deducts stock & creates GL entries)
            if ($isReturn) {
                $invoice = $this->salesReturnInvoiceRepository->createReturn($invoiceData);
            } else {
                $invoice = $this->salesInvoiceRepository->CreateSales($invoiceData);
            }

            // 2. Insert Session Transactions
            foreach ($sessionTransactions as &$tx) {
                $tx['reference_id'] = $invoice->id;
                $tx['created_at'] = now();
                $tx['updated_at'] = now();
            }
            PosSessionTransaction::insert($sessionTransactions);

            DB::commit();

            return $invoice;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
