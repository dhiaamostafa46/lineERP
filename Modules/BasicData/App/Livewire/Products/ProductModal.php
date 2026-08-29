<?php

namespace Modules\BasicData\App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Modules\BasicData\App\Helpers\HasModalForm;
use Modules\BasicData\App\Repositories\DbProductRepository;

class ProductModal extends Component
{
    use WithFileUploads, HasModalForm;

    // 1. Basic Info
    public string $barcode = '';
    public string $category_id = '';
    public string $tax_id = '';
    public int $type = 1; // 1 = Product, 2 = Service
    public float $cost_price = 0.00;
    public float $prod_price = 0.00;
    public int $status = 1;
    public $img;
    public $existing_img = null;
    public array $details = [];

    // 2. Units
    public array $units = [];

    // 3. Sizes
    public bool $have_sizes = false;
    public array $sizes = [];

    protected $repository;

    public function boot(DbProductRepository $repository): void
    {
        $this->repository = $repository;
    }

    protected function rules(): array
    {
        $rules = [
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required',
            'tax_id' => 'nullable',
            'type' => 'required|in:1,2',
            'cost_price' => 'nullable|numeric|min:0',
            'prod_price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
            'img' => 'nullable|image|max:2048',
            'have_sizes' => 'boolean',
        ];

        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $rules["name.$locale"] = 'required|string|max:255';
            $rules["details.$locale"] = 'nullable|string';
        }

        if ($this->have_sizes) {
            foreach ($this->sizes as $idx => $size) {
                $rules["sizes.$idx.ar.name"] = 'nullable|string|max:255';
                $rules["sizes.$idx.sale_price"] = 'nullable|numeric|min:0';
                $rules["sizes.$idx.cost_price"] = 'nullable|numeric|min:0';
            }
        }

        return $rules;
    }

    public function mount(): void
    {
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->resetModalState();
        $this->initTranslations();
        
        $this->details = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->details[$locale] = '';
        }
        
        $this->barcode = '';
        
        $categoriesList = $this->repository ? $this->repository->categories() : [];
        $this->category_id = !empty($categoriesList) ? (string)array_key_first($categoriesList) : '';

        $vatsList = $this->repository ? $this->repository->vats() : [];
        $this->tax_id = !empty($vatsList) ? (string)array_key_first($vatsList) : '';

        $unitsList = $this->repository ? $this->repository->units() : [];
        $firstUnitId = !empty($unitsList) ? (string)array_key_first($unitsList) : '';

        $this->type = 1;
        $this->cost_price = 0.00;
        $this->prod_price = 0.00;
        $this->status = 1;
        $this->img = null;
        $this->existing_img = null;

        $this->have_sizes = false;
        $this->sizes = [];

        $this->units = [
            [
                'unit_id' => $firstUnitId,
                'conversion_factor' => 1,
                'is_base' => true,
            ]
        ];
    }

    public function addSizeRow(): void
    {
        $newSize = [
            'cost_price' => 0.00,
            'sale_price' => 0.00,
            'barcode' => '',
        ];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $newSize[$locale] = ['name' => ''];
        }
        $this->sizes[] = $newSize;
        $this->have_sizes = true;
    }

    public function removeSizeRow(int $index): void
    {
        unset($this->sizes[$index]);
        $this->sizes = array_values($this->sizes);
        if (empty($this->sizes)) {
            $this->have_sizes = false;
        }
    }

    public function addSize(): void
    {
        $this->addSizeRow();
    }

    public function removeSize(int $index): void
    {
        $this->removeSizeRow($index);
    }

    public function updatedHaveSizes($value): void
    {
        if ($value && empty($this->sizes)) {
            $this->addSizeRow();
        }
    }

    public function addUnitRow(): void
    {
        $unitsList = $this->repository->units();
        $this->units[] = [
            'unit_id' => !empty($unitsList) ? (string)array_key_first($unitsList) : '',
            'conversion_factor' => 1,
            'is_base' => false,
        ];
    }

    public function removeUnitRow(int $index): void
    {
        unset($this->units[$index]);
        $this->units = array_values($this->units);
    }

    public function addUnit(): void
    {
        $this->addUnitRow();
    }

    public function removeUnit(int $index): void
    {
        $this->removeUnitRow($index);
    }

    public function setBaseUnit(int $index): void
    {
        foreach ($this->units as $i => &$unit) {
            $unit['is_base'] = ($i === $index);
        }
    }

    #[On('openCreateModal')]
    public function openCreate($type = 1): void
    {
        $this->resetFields();
        if (is_array($type) && isset($type['type'])) {
            $type = $type['type'];
        }
        $this->type = in_array((int)$type, [1, 2]) ? (int)$type : 1;
        
        $unitsList = $this->repository->units();
        if (!empty($unitsList)) {
            $this->units[0]['unit_id'] = (string)array_key_first($unitsList);
            $this->units[0]['conversion_factor'] = 1;
            $this->units[0]['is_base'] = true;
        }

        $vatsList = $this->repository->vats();
        if (!empty($vatsList)) {
            $this->tax_id = (string)array_key_first($vatsList);
        }

        $this->openModal();
    }

    #[On('openEditModal')]
    public function openEdit($id): void
    {
        $this->resetFields();
        $product = $this->repository->find($id);
        if ($product) {
            $this->model_id = $id;
            $this->is_edit = true;
            $this->populateTranslations($product);
            
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->details[$locale] = $product->translate($locale)?->details ?? '';
            }

            $this->barcode = (string)($product->barcode ?? '');
            $this->category_id = (string)$product->category_id;
            $this->tax_id = (string)($product->tax_id ?? '');
            $this->type = (int)($product->type ?? 1);
            $this->cost_price = (float)$product->cost_price;
            $this->prod_price = (float)$product->prod_price;
            $this->status = (int)$product->status;
            $this->existing_img = $product->imgThumbPath;

            $this->have_sizes = (bool)$product->have_sizes;
            $this->sizes = [];
            if ($product->sizes && $product->sizes->count() > 0) {
                foreach ($product->sizes as $size) {
                    $sizeItem = [
                        'id' => $size->id,
                        'cost_price' => (float)$size->cost_price,
                        'sale_price' => (float)$size->sale_price,
                        'barcode' => $size->barcode,
                    ];
                    foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                        $sizeItem[$locale] = ['name' => $size->translate($locale)?->name ?? ''];
                    }
                    $this->sizes[] = $sizeItem;
                }
            }

            $this->units = [];
            if ($product->units && $product->units->count() > 0) {
                foreach ($product->units as $unit) {
                    $this->units[] = [
                        'unit_id' => (string)$unit->unit_id,
                        'conversion_factor' => $unit->conversion_factor,
                        'is_base' => (bool)$unit->is_base,
                    ];
                }
            } else {
                $unitsList = $this->repository->units();
                $this->units[] = [
                    'unit_id' => (string)($product->base_unit_id ?: (array_key_first($unitsList) ?? '')),
                    'conversion_factor' => 1,
                    'is_base' => true,
                ];
            }

            $this->openModal();
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $formattedUnits = [];
            if (!empty($this->units)) {
                foreach ($this->units as $u) {
                    if (!empty($u['unit_id'])) {
                        $formattedUnits[] = [
                            'unit_id' => $u['unit_id'],
                            'conversion_factor' => $u['conversion_factor'] ?? 1,
                            'is_base' => !empty($u['is_base']) ? 1 : 0,
                        ];
                    }
                }
            }

            $data = [
                'barcode' => $this->barcode ?: null,
                'category_id' => $this->category_id,
                'tax_id' => $this->tax_id ?: null,
                'type' => $this->type,
                'cost_price' => $this->cost_price ?: 0,
                'prod_price' => $this->prod_price ?: 0,
                'status' => $this->status,
                'have_sizes' => $this->have_sizes ? 1 : 0,
                'units' => $formattedUnits,
                'sizes' => $this->sizes,
            ];

            if ($this->img) {
                $data['img'] = $this->img;
            }

            foreach ($this->name as $locale => $val) {
                $data[$locale] = [
                    'name' => $val,
                    'details' => $this->details[$locale] ?? '',
                ];
            }

            $modelKey = $this->type == 2 ? 'basicdata::models/db_products.service' : 'basicdata::models/db_products.product';
            return $this->saveRecord($data, $modelKey, 'basicdata.products.index', ['type' => $this->type]);

        } catch (\Exception $e) {
            $this->addError('save_error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('basicdata::livewire.products.product-modal', [
            'categories' => $this->repository->categories(),
            'unitsList' => $this->repository->units(),
            'vats' => $this->repository->vats(),
            'statuses' => $this->repository->statuses(),
        ]);
    }
}
