<?php

namespace Modules\Invoices\App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\invApp\SalesInvoice;

class SalesDebitNoteRepository extends SalesInvoiceRepository
{
    public function model(): string
    {
        return SalesInvoice::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit)->where('type_inv', SalesInvoice::TYPE_DEBIT_NOTE);

        // Scoped Access Logic
        if (auth()->check() && ! auth()->user()->can('invoices.sales_debit.scopedaccess')) {
            $query->where('created_by', auth()->id());
        }

        if (auth()->check()) {
            $table = $this->model()::newModelInstance()->getTable();
            $permissionPrefix = 'invoices.sales_debit';


        }

        return $query;

    }

    /**
     * Store a new Debit Note
     */
    public function store(array $input): SalesInvoice
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            $payments = $input['payments'] ?? [];
            unset($input['items'], $input['payments']);

            $input = $this->prepareInvoiceData($input, 'sales_debit');
            $input['type_inv'] = SalesInvoice::TYPE_DEBIT_NOTE;

            // Default status for new Debit Note
            $input['status'] = $input['status'] ?? SalesInvoice::STATUS_SUBMITTED;

            /** @var SalesInvoice $model */
            $model = $this->create($input);

            if (! empty($items)) {
                $this->saveItems($model, $items);
            }
            if (! empty($payments)) {
                $this->savePayments($model, $payments);
            }

            $this->processDebitNoteLinking($model);

            return $model;
        });
    }

    /**
     * Update Debit Note
     */
    public function update(array $input, int $id, bool $withLog = true)
    {
        return DB::transaction(function () use ($input, $id) {
            $items = $input['items'] ?? null;
            $payments = $input['payments'] ?? null;
            unset($input['items'], $input['payments']);

            /** @var SalesInvoice $model */
            $model = $this->find($id);
            if (! $model) {
                throw new \Exception(__('messages.not_found'));
            }

            if ($model->status != SalesInvoice::STATUS_DRAFT) {
                throw new \Exception('لا يمكن تعديل الإشعار المدين بعد ترحيله وفقاً لأنظمة هيئة الزكاة.');
            }

            // Handle transition from Draft to Official Number
            if (($input['status'] ?? $model->status) != SalesInvoice::STATUS_DRAFT &&
                (empty($model->invoice_number) || str_starts_with($model->invoice_number, 'DRAFT-'))) {
                $input['invoice_number'] = $this->generateNumber('sales_debit', false);
            }

            $input['type_inv'] = SalesInvoice::TYPE_DEBIT_NOTE;
            $model = parent::update($input, $id);

            if ($items !== null) {
                $model->items()->delete();
                $this->saveItems($model, $items);
            }
            if ($payments !== null) {
                $model->payments()->delete();
                $this->savePayments($model, $payments);
            }

            $this->processDebitNoteLinking($model);

            return $model;
        });
    }

    protected function processDebitNoteLinking(SalesInvoice $model)
    {
        if ($model->status == SalesInvoice::STATUS_DRAFT) {
            return;
        }

        $zatcaSuccess = $this->handleZatcaIntegration($model);
        if (! $zatcaSuccess && $model->status == SalesInvoice::STATUS_DRAFT) {
            return;
        }

        // Stock movement for Debit Note (Usually Debit Note increases quantity/price)
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
        app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->generateEntries($model);
    }

    public function name(): string
    {
        return __('invoices::models/sales_debit_notes.plural') ?: 'الإشعارات المدينة';
    }

    protected function prepareInvoiceData(array $input, string $type): array
    {
        $input = parent::prepareInvoiceData($input, $type);
        $input['type_inv'] = SalesInvoice::TYPE_DEBIT_NOTE;

        return $input;
    }

    public function getHeaders(): array
    {
        return [
            __('invoices::models/sales_debit_notes.fields.invoice_number') ?: __('invoices::models/sales_invoices.fields.invoice_number'),
            __('invoices::models/sales_debit_notes.fields.customer_id') ?: __('invoices::models/sales_invoices.fields.customer_id'),
            __('invoices::models/sales_debit_notes.fields.branch_id') ?: __('invoices::models/sales_invoices.fields.branch_id'),
            __('invoices::models/sales_debit_notes.fields.issue_date') ?: __('invoices::models/sales_invoices.fields.issue_date'),
            __('invoices::models/sales_debit_notes.fields.total_exclusive_vat') ?: __('invoices::models/sales_invoices.fields.total_exclusive_vat'),
            __('invoices::models/sales_debit_notes.fields.total_vat') ?: __('invoices::models/sales_invoices.fields.total_vat'),
            __('invoices::models/sales_debit_notes.fields.total_discount') ?: __('invoices::models/sales_invoices.fields.total_discount'),
            __('invoices::models/sales_debit_notes.fields.total_inclusive_vat') ?: __('invoices::models/sales_invoices.fields.total_inclusive_vat'),
            __('invoices::models/sales_debit_notes.fields.status') ?: __('invoices::models/sales_invoices.fields.status'),
            __('invoices::models/sales_debit_notes.fields.created_by') ?: __('invoices::models/sales_invoices.fields.created_by'),
        ];
    }

    public function dataExcel(): array
    {
        return SalesInvoice::isDebitNote()
            ->with(['customer', 'store', 'createdBy'])
            ->get()
            ->map(function ($invoice) {
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
            })
            ->toArray();
    }
}

