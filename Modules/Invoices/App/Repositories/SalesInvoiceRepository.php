<?php

namespace Modules\Invoices\App\Repositories;

use App\Models\BasicDataApp\Product;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\App\Helpers\HasInvoiceSharedLogic;
use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\ZatcaSetting;

class SalesInvoiceRepository extends BaseRepository
{
    use \App\Helpers\StockManagementTrait;
    use HasInvoiceSharedLogic;

    protected $fieldSearchable = ['invoice_number', 'customer_invoice_number', 'customer_id', 'issue_date', 'status', 'store_id'];

    public function model(): string
    {
        return SalesInvoice::class;
    }
    
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Get all query with default filters
     */
    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (static::class === SalesInvoiceRepository::class) {
            if (isset($search['type_inv']) && !empty($search['type_inv'])) {
                if (is_array($search['type_inv'])) {
                    $query->whereIn('type_inv', $search['type_inv']);
                } else {
                    $query->where('type_inv', $search['type_inv']);
                }
            } else {
                $query->whereIn('type_inv', [SalesInvoice::TYPE_INVOICE, SalesInvoice::TYPE_POS]);
            }
        }

        if (isset($search['created_by']) && !empty($search['created_by'])) {
            $userId = $search['created_by'];
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('user_id', $userId);
            });
        }

        // Scoped Access Logic
        if (auth()->check() && ! auth()->user()->can('invoices.sales.scopedaccess')) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    // =========================================================================
    // SECTION: Core CRUD Operations
    // =========================================================================

    /**
     * Create a new sales invoice
     */
    public function CreateSales($input): SalesInvoice
    {

        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            $payments = $input['payments'] ?? [];
            unset($input['items'], $input['payments']);

            $input = $this->prepareInvoiceData($input, 'sales');

            /** @var SalesInvoice $model */
            $model = $this->create($input);

            if (! empty($items)) {
                $this->saveItems($model, $items);
            }

            if (! empty($payments)) {
                $this->savePayments($model, $payments);
            }

            $this->processSalesLinking($model);

            // Ensure non-draft invoice has official invoice number
            $model->refresh();
            if ($model->status != SalesInvoice::STATUS_DRAFT && (empty($model->invoice_number) || str_starts_with(strtoupper($model->invoice_number), 'DRAFT'))) {
                $officialNumber = $this->generateNumber('sales', false);
                $model->updateQuietly(['invoice_number' => $officialNumber]);
            }

            // Update Quotation status if converted
            if (! empty($input['quotation_id'])) {
                \Modules\Invoices\App\Models\Quotation::where('id', $input['quotation_id'])
                    ->update(['status' => \Modules\Invoices\App\Models\Quotation::STATUS_CONVERTED]);
            }

            return $model;
        });
    }

    /**
     * Update an existing sales invoice
     */
    public function updateSales($input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            /** @var SalesInvoice $model */
            $model = $this->find($id);

            // قانونياً: لا يمكن تعديل أي فاتورة بعد إصدارها (يُسمح فقط للمسودات)
            if ($model->status != SalesInvoice::STATUS_DRAFT) {
                throw new \Exception('لا يمكن تعديل الفاتورة بعد ترحيلها وفقاً لأنظمة هيئة الزكاة. يرجى استخدام المرتجع بدلاً من ذلك.');
            }

            $items = $input['items'] ?? null;
            $payments = $input['payments'] ?? null;
            unset($input['items'], $input['payments']);

            $newStatus = (int) ($input['status'] ?? $model->status);

            // Handle transition from Draft to Official Number
            if ($newStatus != SalesInvoice::STATUS_DRAFT &&
                (empty($model->invoice_number) || str_starts_with(strtoupper($model->invoice_number), 'DRAFT'))
            ) {
                $input['invoice_number'] = $this->generateNumber('sales', false);
            }

            // تحديث البيانات الأساسية
            $model = $this->update($input, $id);

            // تحديث الأصناف
            if ($items !== null) {
                $model->items()->delete();
                $this->saveItems($model, $items);
            }

            // تحديث الدفعات
            if ($payments !== null) {
                $model->payments()->delete();
                $this->savePayments($model, $payments);
            }

            // تنفيذ الربط فقط إذا تم تغيير الحالة من مسودة إلى مرحلة أثناء التحديث
            $this->processSalesLinking($model);

            // Ensure non-draft invoice has official invoice number
            $model->refresh();
            if ($model->status != SalesInvoice::STATUS_DRAFT && (empty($model->invoice_number) || str_starts_with(strtoupper($model->invoice_number), 'DRAFT'))) {
                $officialNumber = $this->generateNumber('sales', false);
                $model->updateQuietly(['invoice_number' => $officialNumber]);
            }

            return $model;
        });
    }

    /**
     * Delete a sales invoice (Only allowed for Drafts per ZATCA regulations)
     */
    public function deleteSales($id): ?bool
    {
        return DB::transaction(function () use ($id) {
            $model = $this->find($id);
            if (! $model) {
                return false;
            }

            // قانونياً: لا يمكن حذف أي فاتورة بعد إصدارها (يُسمح فقط للمسودات)
            if ($model->status != SalesInvoice::STATUS_DRAFT) {
                throw new \Exception('لا يمكن حذف الفاتورة بعد ترحيلها وفقاً لأنظمة هيئة الزكاة. يرجى استخدام المرتجع بدلاً من ذلك.');
            }

            app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->deleteJournalEntries($model);

            $model->items()->delete();
            $model->payments()->delete();

            return parent::delete($id);
        });
    }

    // =========================================================================
    // SECTION: Data Preparation & Helpers
    // =========================================================================

    protected function prepareInvoiceData(array $input, string $type): array
    {
        $input['status'] = $input['status'] ?? SalesInvoice::STATUS_SUBMITTED;

        if (empty($input['invoice_number'])) {
            $isDraft = ($input['status'] == SalesInvoice::STATUS_DRAFT);
            $input['invoice_number'] = $this->generateNumber($type, $isDraft);
        }

        $input['type_inv'] = $input['type_inv'] ?? SalesInvoice::TYPE_INVOICE;
        $input['issue_date'] = $input['issue_date'] ?? now();
        $input['branch_id'] = $input['branch_id'] ?? (auth()->user()->branch_id ?? 1);
        $input['created_by'] = $input['created_by'] ?? (auth()->id() ?? 1);

        return $input;
    }

    protected function saveItems(SalesInvoice $model, array $items)
    {
        $usedSerials = [];
        foreach ($items as $index => &$item) {
            $item['have_sizes'] = isset($item['have_sizes']) ? filter_var($item['have_sizes'], FILTER_VALIDATE_BOOLEAN) : false;

            if ($item['have_sizes']) {
                $productSize = \App\Models\BasicDataApp\ProductSize::with('product')->find($item['product_id']);
                $product = $productSize ? $productSize->product : null;
            } else {
                $product = Product::find($item['product_id']);
            }
            $unitId = null;

            // 1. Check if unit name was sent
            $unitName = $item['unit'] ?? null;
            if (! empty($unitName)) {
                $unit = \App\Models\BasicDataApp\Unit::whereTranslation('name', $unitName)->first();
                if ($unit) {
                    $unitId = $unit->id;
                }
            }

            // 2. Check if unit_id was sent (ProductUnit ID or direct Unit ID)
            if (empty($unitId) && ! empty($item['unit_id']) && $product) {
                $prodUnit = \App\Models\BasicDataApp\ProductUnit::where('product_id', $product->id)
                    ->where(function ($q) use ($item) {
                        $q->where('unit_id', $item['unit_id'])
                            ->orWhere('id', $item['unit_id']);
                    })
                    ->first();
                if ($prodUnit) {
                    $unitId = $prodUnit->unit_id;
                } else {
                    $unit = \App\Models\BasicDataApp\Unit::find($item['unit_id']);
                    if ($unit) {
                        $unitId = $unit->id;
                    }
                }
            }

            // 3. Fallback to product's base unit
            if (empty($unitId) && $product) {
                $unitId = $product->base_unit_id;
            }

            $item['unit_id'] = $unitId;

            // التعامل مع الوحدة بناءً على نوع المنتج
            if ($product && $product->type != Product::TYPE_SERVICE) {
                // إذا كان منتج عادي ولم نجد له وحدة، نوقف العملية ونظهر رسالة
                if (empty($item['unit_id'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.{$index}.unit_id" => __('invoices::models/sales_invoices.validation.product_no_unit'),
                    ]);
                }
            } else {
                // إذا كانت خدمة، نسمح بأن تكون null
                $item['unit_id'] = $item['unit_id'] ?: null;
            }

            // Ensure a 6-digit serial per item, unique within the invoice
            if (empty($item['serial'])) {
                // generate random 6-digit serial that's not already used in this invoice
                do {
                    $serial = str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (in_array($serial, $usedSerials) || $model->items()->where('serial', $serial)->exists());
                $item['serial'] = $serial;
            } else {
                // normalize to 6 chars
                $item['serial'] = str_pad((string) $item['serial'], 6, '0', STR_PAD_LEFT);
                if (in_array($item['serial'], $usedSerials) || $model->items()->where('serial', $item['serial'])->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.{$index}.serial" => __('Serial must be unique within the invoice.'),
                    ]);
                }
            }

            $usedSerials[] = $item['serial'];
        }
        $model->items()->createMany($items);
    }

    protected function savePayments(SalesInvoice $model, array $payments)
    {
        $validPayments = array_filter($payments, function ($payment) {
            return ! empty($payment['account_id']);
        });

        if (! empty($validPayments)) {
            $model->payments()->createMany($validPayments);
        }
    }

    // =========================================================================
    // SECTION: Integration Logic (Stock & GL)
    // =========================================================================

    protected function processSalesLinking(SalesInvoice $model)
    {
        if ($model->status == SalesInvoice::STATUS_DRAFT) {
            return;
        }

        $zatcaSuccess = $this->handleZatcaIntegration($model);

        if (! $zatcaSuccess && $model->status == SalesInvoice::STATUS_DRAFT) {
            return;
        }

        // Ensure items and their products are loaded from the database
        // before processing stock movements. This prevents the bug where
        // $model->items is cached as an empty collection after model creation.
        $model->load('items.product');

        foreach ($model->items as $item) {
            if ($item->product && $item->product->type == 2) {
                continue;
            }
            $this->handleStockMovement($model, $item);
        }

        $this->generateJournalEntries($model);
    }

    protected function generateJournalEntries(SalesInvoice $model)
    {
        if (in_array($model->type_inv, [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS]) && $model->pos_session_id) {
            $session = \Modules\Pos\App\Models\PosSession::with('device')->find($model->pos_session_id);
            if ($session && $session->device && !$session->device->auto_journal_entry) {
                return; // Skip journal entries if POS device setting is disabled
            }
        }
        
        app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->generateEntries($model);
    }

    /**
     * Idempotent retry for failed or pending sales linking (ZATCA, Stock, GL)
     */
    public function retrySalesLinking(SalesInvoice $model): bool
    {
        $allSuccess = true;

        // 1. ZATCA Retry
        if (!$model->isReported()) {
            try {
                $this->handleZatcaIntegration($model);
                // If we reach here, ZATCA was either successful or skipped (not enabled)
                // If it was skipped, it didn't throw an exception.
                // We'll update the status if it was stuck in DRAFT but shouldn't be
                if ($model->status == SalesInvoice::STATUS_DRAFT && empty($model->zatca_errors)) {
                    $model->updateQuietly(['status' => SalesInvoice::STATUS_SUBMITTED]);
                }
            } catch (\Exception $e) {
                $allSuccess = false;
                \Illuminate\Support\Facades\Log::warning("Retry ZATCA failed for Invoice ID {$model->id}: " . $e->getMessage());
            }
        }

        // 2. Stock Movement Retry
        $hasStockMovements = \App\Models\StoreApp\StockMovement::where('document_id', $model->id)
            ->where('document_type', get_class($model))
            ->exists();

        if (!$hasStockMovements) {
            try {
                foreach ($model->items as $item) {
                    if ($item->product && $item->product->type == 2) {
                        continue;
                    }
                    $this->handleStockMovement($model, $item);
                }
            } catch (\Exception $e) {
                $allSuccess = false;
                \Illuminate\Support\Facades\Log::warning("Retry Stock Movement failed for Invoice ID {$model->id}: " . $e->getMessage());
            }
        }

        // 3. Accounting Retry
        if (empty($model->journal_entry_id)) {
            try {
                $this->generateJournalEntries($model);
            } catch (\Exception $e) {
                $allSuccess = false;
                \Illuminate\Support\Facades\Log::warning("Retry Journal Entries failed for Invoice ID {$model->id}: " . $e->getMessage());
            }
        }

        return $allSuccess;
    }

    // =========================================================================
    // SECTION: ZATCA Integration
    // =========================================================================

    protected function handleZatcaIntegration(SalesInvoice $model): bool
    {
        try {
            $zatcaSetting = ZatcaSetting::resolveForBranch($model->branch_id);
            $zatcaService = app(\Modules\Invoices\App\Services\ZatcaService::class);
            $zatcaPhase2Service = app(\Modules\Invoices\App\Services\ZatcaPhase2Service::class);

            $isPhase2Enabled = ($zatcaSetting && $zatcaSetting->isPhase2Enabled());

            if ($isPhase2Enabled && in_array($model->type_inv, [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS]) && $model->pos_session_id) {
                $posSession = \Modules\Pos\App\Models\PosSession::with('device')->find($model->pos_session_id);
                if ($posSession && $posSession->device && !$posSession->device->send_to_zatca_phase2) {
                    $isPhase2Enabled = false;
                }
            }
              
            if ($isPhase2Enabled) {
                // Determine Invoice Type based on Settings and Customer Data
                try {
                    $this->determineZatcaInvoiceType($model, $zatcaSetting);
                } catch (\Exception $e) {
                    $model->updateQuietly(['status' => SalesInvoice::STATUS_DRAFT, 'zatca_errors' => $e->getMessage()]);
                    throw $e;
                }

                try {
                    // Use the specialized Phase 2 Service
                    $result = $zatcaPhase2Service->processAndReport($model, $zatcaSetting);

                    $apiResult = $result['api_result'];
                    $signedData = $result['signed_data'];
                    $isB2B = ($model->invoice_subtype_code === '1000');

                    // Update Invoice with Results
                    $newStatus = $model->status;
                    if ($apiResult) {
                        if (($apiResult['reportingStatus'] ?? '') == 'REPORTED' || ($apiResult['clearanceStatus'] ?? '') == 'CLEARED') {
                            $newStatus = ($isB2B ? SalesInvoice::STATUS_CLEARED : SalesInvoice::STATUS_REPORTED);
                        } else {
                            // Capture detailed validation errors
                            $errors = [];
                            if (isset($apiResult['validationResults']['errorMessages'])) {
                                foreach ($apiResult['validationResults']['errorMessages'] as $err) {
                                    $errors[] = ($err['code'] ?? '').': '.($err['message'] ?? '');
                                }
                            }
                            $errorMsg = ! empty($errors) ? implode(' | ', $errors) : ($apiResult['message'] ?? 'رفض من الهيئة');

                            $model->updateQuietly(['status' => SalesInvoice::STATUS_DRAFT, 'zatca_errors' => $errorMsg]);
                            throw new \Exception('تم رفض الفاتورة من الهيئة: '.$errorMsg);
                        }
                    }

                    $model->updateQuietly([
                        'qr_code' => $signedData['qr_code'],
                        'status' => $newStatus,
                        'zatca_errors' => null,
                    ]);

                    // Log detailed ZATCA response
                    \Modules\Invoices\App\Models\SalesInvoiceZatca::updateOrCreate(['sales_invoice_id' => $model->id], [
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'xml_content' => $signedData['signed_xml'] ?? null,
                        'request_id' => $zatcaSetting->request_id,
                        'previous_invoice_hash' => $signedData['hash'] ?? $model->zatcaDetails->previous_invoice_hash, // Store current hash for next invoice
                        'request_payload' => json_encode(['uuid' => $model->zatcaDetails->uuid ?? null, 'hash' => $signedData['hash']]),
                        'response_payload' => $apiResult ? json_encode($apiResult) : null,
                        'validation_results' => isset($apiResult['validationResults']) ? json_encode($apiResult['validationResults']) : null,
                    ]);

                    return true;
                } catch (\Exception $e) {
                    $model->updateQuietly(['status' => SalesInvoice::STATUS_DRAFT, 'zatca_errors' => $e->getMessage()]);
                    throw $e;
                }
            }

            $this->generatePhase1Data($model, $zatcaSetting, $zatcaService);

            return true;
        } catch (\Exception $e) {
            $model->updateQuietly(['status' => SalesInvoice::STATUS_DRAFT, 'zatca_errors' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function generatePhase1Data(SalesInvoice $model, ?ZatcaSetting $setting, $service)
    {
        $qrCode = $service->generatePhase1QrCode($model, $setting);
        $model->updateQuietly(['qr_code' => $qrCode]);
    }

    // =========================================================================
    // SECTION: Metadata & Reporting
    // =========================================================================

    public function getHeaders(): array
    {
        return [
            __('invoices::models/sales_invoices.fields.invoice_number'),
            __('invoices::models/sales_invoices.fields.customer_id'),
            __('invoices::models/sales_invoices.fields.branch_id'),
            __('invoices::models/sales_invoices.fields.issue_date'),
            __('invoices::models/sales_invoices.fields.total_exclusive_vat'),
            __('invoices::models/sales_invoices.fields.total_vat'),
            __('invoices::models/sales_invoices.fields.total_discount'),
            __('invoices::models/sales_invoices.fields.total_inclusive_vat'),
            __('invoices::models/sales_invoices.fields.status'),
            __('invoices::models/sales_invoices.fields.created_by'),
        ];
    }

    public function dataExcel(): array
    {
        return SalesInvoice::isInvoice()->with(['customer', 'store', 'createdBy'])->get()->map(function ($invoice) {
            return [
                'invoice_number' => $invoice->invoice_number,
                'customer_id' => $invoice->customer->name ?? '---',
                'branch_id' => $invoice->store ? $invoice->store->name : '---',
                'issue_date' => $invoice->issue_date ? ($invoice->issue_date instanceof \Carbon\Carbon ? $invoice->issue_date->format('Y-m-d') : date('Y-m-d', strtotime($invoice->issue_date))) : '---',
                'total_exclusive_vat' => number_format($invoice->total_exclusive_vat, 2),
                'total_vat' => number_format($invoice->total_vat, 2),
                'total_discount' => number_format($invoice->total_discount, 2),
                'total_inclusive_vat' => number_format($invoice->total_inclusive_vat, 2),
                'status' => $invoice->status_text,
                'created_by' => $invoice->createdBy->name ?? '---',
            ];
        })->toArray();
    }

    public function name()
    {
        return __('invoices::models/sales_invoices.plural');
    }

    /**
     * Determine ZATCA Invoice Type (Standard vs Simplified) based on Settings
     * Implement user logic: 0100 (Simplified), 1000 (Standard), 1100 (Both/Dynamic)
     */
    protected function determineZatcaInvoiceType(SalesInvoice $invoice, ZatcaSetting $setting)
    {
        $invType = $setting->inv_type ?? '1100';
        
        // ZATCA Internal Codes matching settings: 1000 (Standard), 0100 (Simplified)
        $standardCode = '1000';
        $simplifiedCode = '0100';
        
        // Force Simplified for POS Invoices and Returns
        if ($invoice->type_inv == SalesInvoice::TYPE_POS || $invoice->type_inv == SalesInvoice::TYPE_RETURN_POS || !empty($invoice->pos_session_id)) {
            $invoice->updateQuietly(['invoice_subtype_code' => $simplifiedCode]);
            return;
        }

        $customer = $invoice->customer;

        // 1. Check for complete customer data (Required for Standard/B2B)
        $missingFields = [];
        if (empty($customer->name)) {
            $missingFields[] = trans('invoices::models/inv_customers.fields.name');
        }
        $hasValidVat = !empty($customer->vat_number) && preg_match('/^3[0-9]{14}$/', preg_replace('/\s+/', '', $customer->vat_number));
        if (!$hasValidVat) {
            $missingFields[] = trans('invoices::models/inv_customers.fields.vat_number') . ' (يجب أن يتكون الرقم الضريبي للمشتري من 15 خانة ويبدأ وينتهي بـ 3)';
        }
        if (empty($customer->street)) {
            $missingFields[] = trans('invoices::models/inv_customers.fields.street');
        }
        if (empty($customer->building_number)) {
            $missingFields[] = trans('invoices::models/inv_customers.fields.building_number');
        }
        if (empty($customer->district)) {
            $missingFields[] = trans('invoices::models/inv_customers.fields.district');
        }
        if (empty($customer->city)) {
            $missingFields[] = trans('invoices::models/inv_customers.fields.city');
        }
        if (empty($customer->postal_code)) {
            $missingFields[] = trans('invoices::models/inv_customers.fields.postal_code');
        }

       
        $isDataComplete = empty($missingFields);

        // ZATCA Internal Codes matching settings: 1000 (Standard), 0100 (Simplified)
        $standardCode = '1000';
        $simplifiedCode = '0100';

        $finalSubtype = $simplifiedCode; // Default


       
        if ($invType == '0100') {
            // Simplified Only
            $finalSubtype = $simplifiedCode;
        } elseif ($invType == '1000') {
            // Standard Only - Enforce Data
            if (! $isDataComplete) {
                throw new \Exception(trans('invoices::messages.incomplete_customer_data').': '.implode(', ', $missingFields));
            }
            $finalSubtype = $standardCode;
        } else {
            // 1100 (Both) - Prefer Standard if data complete
            if ($isDataComplete) {
                $finalSubtype = $standardCode;
            } else {
                // Check if user forced simplified (from front-end decision) OR if invoice originated from POS
                if (request('force_simplified') == 'true' || !empty($invoice->pos_session_id)) {
                    $finalSubtype = $simplifiedCode;
                } else {
                    // Throw a specific error that the UI can catch to show options
                    $errorMsg = trans('invoices::messages.incomplete_customer_data_confirmation', [
                        'fields' => implode(', ', $missingFields),
                    ]);

                    throw new \Exception('CONFIRM_SIMPLIFIED|'.$errorMsg);
                }
            }
        }

        // Determine Invoice Type Code (Document Type)
        // 388: Invoice, 381: Credit Note, 383: Debit Note
        $typeCode = '388';
        if ($invoice->type_inv == SalesInvoice::TYPE_RETURN) {
            $typeCode = '381';
        } elseif ($invoice->type_inv == SalesInvoice::TYPE_DEBIT_NOTE) {
            $typeCode = '383';
        }

        $invoice->updateQuietly([
            'invoice_type_code' => $typeCode,
            'invoice_subtype_code' => $finalSubtype,
        ]);
    }

    public function create(array $input, bool $withLog = true): \Illuminate\Database\Eloquent\Model
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return parent::create($input, $withLog);
    }

    public function update(array $input, int $id, bool $withLog = true)
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return parent::update($input, $id, $withLog);
    }
}

