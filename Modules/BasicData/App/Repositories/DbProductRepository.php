<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\AccuSoft\TaxAccount;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductSize;
use App\Models\BasicDataApp\ProductUnit;
use App\Models\BasicDataApp\Unit;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\BasicData\App\Models\DbKitchen;

class DbProductRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = [
        'name',
        'status'
    ];
    protected ?string $modelTranslation = 'basicdata::models/db_products.singular';

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $sortBy = request('sort_by', 'created_at');
        $sortDir = request('sort_dir', 'desc');
        $allowedSorts = ['name', 'category_id', 'type', 'cost_price', 'prod_price', 'status', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, strtolower($sortDir) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        return $query;
    }

    public function model(): string
    {
        return Product::class;
    }

    public function statuses(): array
    {
        return Product::statuses();
    }

    public function listItems($id)
    {
        return Product::findOrFail($id);
    }

    public function vats(): array
    {
        return TaxAccount::Active()->get()->pluck('name', 'id')->toArray();
    }

    public function types(): array
    {
        return Product::types();
    }

    public function categories(): array
    {
        return Category::get()->pluck('name', 'id')->toArray();
    }

    public function kitchens(): array
    {
        return DbKitchen::where('status', 1)->get()->pluck('name', 'id')->toArray();
    }

    public function units(): array
    {
        return Unit::get()->pluck('name', 'id')->toArray();
    }

    /**
     * Create product with related units, sizes, and tax calculations
     */
    public function createWithRelations(array $input): Product
    {
        return DB::transaction(function () use ($input) {
            // Calculate Tax Rate
            if (!empty($input['tax_id'])) {
                $taxAccount = TaxAccount::find($input['tax_id']);
                $input['vat'] = $taxAccount ? $taxAccount->rate : 15;
            } else {
                $defaultTax = TaxAccount::Active()->first();
                $input['vat'] = $defaultTax ? $defaultTax->rate : 15;
            }

            // 1. Create Base Product
            $product = $this->create($input);

            // 2. Create Product Units
            if (!empty($input['units']) && is_array($input['units'])) {
                foreach ($input['units'] as $unitData) {
                    if (!empty($unitData['unit_id'])) {
                        ProductUnit::create([
                            'product_id' => $product->id,
                            'unit_id' => $unitData['unit_id'],
                            'conversion_factor' => !empty($unitData['conversion_factor']) ? $unitData['conversion_factor'] : 1,
                            'is_base' => !empty($unitData['is_base']) ? 1 : 0,
                        ]);
                    }
                }
            }

            // 3. Create Product Sizes
            if (!empty($input['have_sizes']) && !empty($input['sizes']) && is_array($input['sizes'])) {
                foreach ($input['sizes'] as $sizeData) {
                    if (!empty($sizeData['ar']['name']) || !empty($sizeData['en']['name'])) {
                        $sizePayload = [
                            'product_id' => $product->id,
                            'cost_price' => $sizeData['cost_price'] ?? 0,
                            'sale_price' => $sizeData['sale_price'] ?? 0,
                            'barcode' => $sizeData['barcode'] ?? null,
                            'status' => 1,
                        ];
                        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $langName) {
                            $sizePayload[$locale] = ['name' => $sizeData[$locale]['name'] ?? ($sizeData['ar']['name'] ?? '')];
                        }
                        ProductSize::create($sizePayload);
                    }
                }
            }

            return $product;
        });
    }

    /**
     * Update product with related units, sizes, and tax calculations
     */
    public function updateWithRelations(array $input, int $id): Product
    {
        return DB::transaction(function () use ($input, $id) {
            $product = $this->find($id);
            if (!$product) {
                throw new \Exception(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
            }

            // Calculate Tax Rate
            if (!empty($input['tax_id'])) {
                $taxAccount = TaxAccount::find($input['tax_id']);
                $input['vat'] = $taxAccount ? $taxAccount->rate : 15;
            } else {
                $defaultTax = TaxAccount::Active()->first();
                $input['vat'] = $defaultTax ? $defaultTax->rate : 15;
            }

            // 1. Update Base Product
            $product = $this->update($input, $id);

            // 2. Re-create Product Units
            $product->units()->delete();
            if (!empty($input['units']) && is_array($input['units'])) {
                foreach ($input['units'] as $unitData) {
                    if (!empty($unitData['unit_id'])) {
                        ProductUnit::create([
                            'product_id' => $product->id,
                            'unit_id' => $unitData['unit_id'],
                            'conversion_factor' => !empty($unitData['conversion_factor']) ? $unitData['conversion_factor'] : 1,
                            'is_base' => !empty($unitData['is_base']) ? 1 : 0,
                        ]);
                    }
                }
            }

            // 3. Re-create Product Sizes
            $product->sizes()->delete();
            if (!empty($input['have_sizes']) && !empty($input['sizes']) && is_array($input['sizes'])) {
                foreach ($input['sizes'] as $sizeData) {
                    if (!empty($sizeData['ar']['name']) || !empty($sizeData['en']['name'])) {
                        $sizePayload = [
                            'product_id' => $product->id,
                            'cost_price' => $sizeData['cost_price'] ?? 0,
                            'sale_price' => $sizeData['sale_price'] ?? 0,
                            'barcode' => $sizeData['barcode'] ?? null,
                            'status' => 1,
                        ];
                        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $langName) {
                            $sizePayload[$locale] = ['name' => $sizeData[$locale]['name'] ?? ($sizeData['ar']['name'] ?? '')];
                        }
                        ProductSize::create($sizePayload);
                    }
                }
            }

            return $product;
        });
    }

    public function header(): array
    {
        return [
            __('basicdata::models/db_products.fields.name'),
            __('basicdata::models/db_products.fields.category_id'),
            __('basicdata::models/db_products.fields.type'),
            __('basicdata::models/db_products.fields.barcode'),
            __('basicdata::models/db_products.fields.cost_price'),
            __('basicdata::models/db_products.fields.prod_price'),
            __('basicdata::models/db_products.fields.vat'),
            __('basicdata::models/db_products.fields.have_sizes'),
        ];
    }

    public function dataExel(): array
    {
        return Product::with(['translations', 'category'])
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'category' => $product->category ? $product->category->name : '',
                    'type' => $product->type_text,
                    'barcode' => $product->barcode,
                    'cost_price' => $product->cost_price,
                    'prod_price' => $product->prod_price,
                    'vat' => $product->vat,
                    'have_sizes' => $product->have_sizes_text,
                ];
            })
            ->toArray();
    }

    public function name(): string
    {
        return __('basicdata::models/db_products.singular');
    }
}
