<?php

namespace Modules\Invoices\App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\App\Helpers\HasInvoiceSharedLogic;
use Modules\Invoices\App\Helpers\InvoiceHelper;
use Modules\Invoices\App\Models\Quotation;
use Modules\Invoices\App\Services\ZatcaService;

class QuotationRepository extends BaseRepository
{
    use HasInvoiceSharedLogic;

    protected $fieldSearchable = [
        'quotation_number',
        'customer_id',
        'issue_date',
        'status',
        'store_id',
    ];

    private $zatcaService;

    public function __construct(ZatcaService $zatcaService)
    {
        $this->zatcaService = $zatcaService;
        parent::__construct();
    }

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Quotation::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (isset($search['created_by']) && !empty($search['created_by'])) {
            $userId = $search['created_by'];
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('user_id', $userId);
            });
        }

        // Scoped Access Logic
        if (auth()->check() && ! auth()->user()->can('invoices.quotations.scopedaccess')) {
            $query->where('created_by', auth()->id());
        }

        if (auth()->check()) {
            $table = $this->model()::newModelInstance()->getTable();
            $permissionPrefix = 'invoices.quotations';


        }

        return $query;
    }

    public function store(array $input): Quotation
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['quotation_number'] = $this->generateQuotationNumber();
            $input['created_by'] = auth()->id();
            $input['branch_id'] = $input['branch_id'] ?? auth()->user()->branch_id;

            /** @var Quotation $quotation */
            $quotation = $this->create($input);

            // Generate QR Code (Base64 TLV)
            $zatcaSetting = \Modules\Invoices\App\Models\ZatcaSetting::resolveForBranch($quotation->branch_id);
            $qrCode = $this->zatcaService->generatePhase1QrCode($quotation, $zatcaSetting, true);
            $quotation->update(['qr_code' => $qrCode]);

            if (! empty($items)) {
                $this->saveItems($quotation, $items);
            }

            return $quotation;
        });
    }

    public function update(array $input, int $id, bool $withLog = true): Quotation
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return DB::transaction(function () use ($input, $id) {
            $items = $input['items'] ?? null;
            unset($input['items']);

            /** @var Quotation $quotation */
            $quotation = parent::update($input, $id);

            // Generate/Update QR Code (Base64 TLV)
            $zatcaSetting = \Modules\Invoices\App\Models\ZatcaSetting::resolveForBranch($quotation->branch_id);
            $qrCode = $this->zatcaService->generatePhase1QrCode($quotation, $zatcaSetting, true);
            $quotation->update(['qr_code' => $qrCode]);

            if ($items !== null) {
                $quotation->items()->delete();
                $this->saveItems($quotation, $items);
            }

            return $quotation;
        });
    }

    protected function generateQuotationNumber(): string
    {
        $settings = InvoiceHelper::getSettings();
        $prefix = $settings->quotation_prefix ?? 'QT';
        $nextNum = $settings->quotation_next_number ?? 1;

        do {
            $number = $prefix.'-'.str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            $exists = Quotation::where('quotation_number', $number)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        if ($settings->id) {
            $settings->update(['quotation_next_number' => $nextNum + 1]);
            InvoiceHelper::clearCache();
        }

        return $number;
    }

    public function getHeaders(): array
    {
        return [
            __('invoices::models/quotations.fields.quotation_number'),
            __('invoices::models/quotations.fields.customer_id'),
            __('invoices::models/quotations.fields.branch_id'),
            __('invoices::models/quotations.fields.issue_date'),
            __('invoices::models/quotations.fields.total_exclusive_vat'),
            __('invoices::models/quotations.fields.total_vat'),
            __('invoices::models/quotations.fields.total_discount'),
            __('invoices::models/quotations.fields.total_inclusive_vat'),
            __('invoices::models/quotations.fields.status'),
            __('invoices::models/quotations.fields.created_by'),
        ];
    }

    public function dataExcel(): array
    {
        return Quotation::with(['customer', 'store', 'createdBy'])->get()->map(function ($quotation) {
            return [
                'quotation_number' => $quotation->quotation_number,
                'customer_id' => $quotation->customer->name ?? '---',
                'branch_id' => $quotation->store ? $quotation->store->name : '---',
                'issue_date' => $quotation->issue_date ? ($quotation->issue_date instanceof \Carbon\Carbon ? $quotation->issue_date->format('Y-m-d') : date('Y-m-d', strtotime($quotation->issue_date))) : '---',
                'total_exclusive_vat' => number_format($quotation->total_exclusive_vat, 2),
                'total_vat' => number_format($quotation->total_vat, 2),
                'total_discount' => number_format($quotation->total_discount, 2),
                'total_inclusive_vat' => number_format($quotation->total_inclusive_vat, 2),
                'status' => $quotation->status_text,
                'created_by' => $quotation->createdBy->name ?? '---',
            ];
        })->toArray();
    }

    public function name()
    {
        return __('invoices::models/quotations.plural');
    }

    public function create(array $input, bool $withLog = true): \Illuminate\Database\Eloquent\Model
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return parent::create($input, $withLog);
    }

    protected function saveItems(Quotation $model, array $items)
    {
        foreach ($items as $index => &$item) {
            $item['type_discount'] = $item['type_discount'] ?? 1;
            $item['number_discount'] = $item['number_discount'] ?? 0;
            $item['total_discount'] = $item['total_discount'] ?? 0;
            $item['have_sizes'] = isset($item['have_sizes']) ? filter_var($item['have_sizes'], FILTER_VALIDATE_BOOLEAN) : false;

            if ($item['have_sizes']) {
                $productSize = \App\Models\BasicDataApp\ProductSize::with('product')->find($item['product_id']);
                $product = $productSize ? $productSize->product : null;
            } else {
                $product = \App\Models\BasicDataApp\Product::find($item['product_id']);
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
            if ($product && $product->type != \App\Models\BasicDataApp\Product::TYPE_SERVICE) {
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
        }
        $model->items()->createMany($items);
    }
}
