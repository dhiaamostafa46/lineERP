<?php

namespace Modules\BasicData\App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Modules\BasicData\App\Livewire\Concerns\HasModalForm;
use Modules\BasicData\App\Repositories\DbProductRepository;

class ProductModal extends Component
{
    use WithFileUploads, HasModalForm;

    public string $activeTab = 'basic'; // 'basic', 'sizes', 'units', 'other'

    // 1. Basic Info
    public string $barcode = '';
    public string $category_id = '';
    public string $kitchen_id = '';
    public string $base_unit_id = '';
    public string $tax_id = '';
    public int $type = 1; // 1 = Product, 2 = Service
    public float $cost_price = 0.00;
    public float $prod_price = 0.00;
    public int $status = 1;
    public $img;
    public $existing_img = null;

    // 2. Sizes
    public bool $have_sizes = false;
    public array $sizes = [];

    // 3. Multiple Units
    public array $units = [];

    // 4. Other Info & Schedule
    public array $details = [];
    public float $min_quantity = 0;
    public float $calories = 0;
    public $s_from = null;
    public $s_to = null;
    public array $work_days = [];

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
            'kitchen_id' => 'nullable',
            'base_unit_id' => 'nullable',
            'tax_id' => 'nullable',
            'type' => 'required|in:1,2',
            'cost_price' => 'nullable|numeric|min:0',
            'prod_price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
            'img' => 'nullable|image|max:2048',
            'have_sizes' => 'boolean',
            'min_quantity' => 'nullable|numeric|min:0',
            'calories' => 'nullable|numeric|min:0',
            's_from' => 'nullable',
            's_to' => 'nullable',
        ];

        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $rules["name.$locale"] = 'required|string|max:255';
            $rules["details.$locale"] = 'nullable|string';
        }

        if ($this->have_sizes) {
            foreach ($this->sizes as $idx => $size) {
                $rules["sizes.$idx.ar.name"] = 'required|string|max:255';
                $rules["sizes.$idx.sale_price"] = 'required|numeric|min:0';
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
        
        $this->activeTab = 'basic';
        $this->details = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->details[$locale] = '';
        }
        
        $this->barcode = '';
        $this->category_id = '';
        $this->kitchen_id = '';
        $this->base_unit_id = '';
        $this->tax_id = '';
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
                'unit_id' => '',
                'conversion_factor' => 1,
                'is_base' => 1,
            ]
        ];

        $this->min_quantity = 0;
        $this->calories = 0;
        $this->s_from = null;
        $this->s_to = null;
        $this->work_days = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function addSizeRow(): void
    {
        $this->sizes[] = [
            'ar' => ['name' => ''],
            'en' => ['name' => ''],
            'cost_price' => 0.00,
            'sale_price' => 0.00,
            'barcode' => '',
        ];
    }

    public function removeSizeRow(int $index): void
    {
        unset($this->sizes[$index]);
        $this->sizes = array_values($this->sizes);
    }

    public function addUnitRow(): void
    {
        $this->units[] = [
            'unit_id' => '',
            'conversion_factor' => 1,
            'is_base' => 0,
        ];
    }

    public function removeUnitRow(int $index): void
    {
        unset($this->units[$index]);
        $this->units = array_values($this->units);
    }

    #[On('openCreateModal')]
    public function openCreate($type = 1): void
    {
        $this->resetFields();
        $this->type = in_array((int)$type, [1, 2]) ? (int)$type : 1;
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

            $this->barcode = (string)$product->barcode;
            $this->category_id = (string)$product->category_id;
            $this->kitchen_id = (string)$product->kitchen_id;
            $this->base_unit_id = (string)$product->base_unit_id;
            $this->tax_id = (string)$product->tax_id;
            $this->type = (int)($product->type ?? 1);
            $this->cost_price = (float)$product->cost_price;
            $this->prod_price = (float)$product->prod_price;
            $this->status = (int)$product->status;
            $this->existing_img = $product->imgThumbPath;

            $this->have_sizes = (bool)$product->have_sizes;
            $this->sizes = [];
            if ($product->sizes && $product->sizes->count() > 0) {
                foreach ($product->sizes as $size) {
                    $this->sizes[] = [
                        'id' => $size->id,
                        'ar' => ['name' => $size->translate('ar')?->name ?? ''],
                        'en' => ['name' => $size->translate('en')?->name ?? ''],
                        'cost_price' => (float)$size->cost_price,
                        'sale_price' => (float)$size->sale_price,
                        'barcode' => $size->barcode,
                    ];
                }
            }

            $this->units = [];
            if ($product->units && $product->units->count() > 0) {
                foreach ($product->units as $unit) {
                    $this->units[] = [
                        'unit_id' => $unit->unit_id,
                        'conversion_factor' => $unit->conversion_factor,
                        'is_base' => $unit->is_base ? 1 : 0,
                    ];
                }
            } else {
                $this->units[] = [
                    'unit_id' => $product->base_unit_id ?? '',
                    'conversion_factor' => 1,
                    'is_base' => 1,
                ];
            }

            $this->min_quantity = (float)($product->min_quantity ?? 0);
            $this->calories = (float)($product->calories ?? 0);
            $this->s_from = $product->s_from;
            $this->s_to = $product->s_to;
            $this->work_days = is_array($product->work_days) ? $product->work_days : ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];

            $this->openModal();
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'barcode' => $this->barcode ?: null,
                'category_id' => $this->category_id,
                'kitchen_id' => $this->kitchen_id ?: null,
                'base_unit_id' => $this->base_unit_id ?: null,
                'tax_id' => $this->tax_id ?: null,
                'type' => $this->type,
                'cost_price' => $this->cost_price ?: 0,
                'prod_price' => $this->prod_price ?: 0,
                'status' => $this->status,
                'have_sizes' => $this->have_sizes ? 1 : 0,
                'min_quantity' => $this->min_quantity ?: 0,
                'calories' => $this->calories ?: 0,
                's_from' => $this->s_from ?: null,
                's_to' => $this->s_to ?: null,
                'work_days' => $this->work_days,
                'units' => $this->units,
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

            if ($this->is_edit && $this->model_id) {
                $this->repository->updateWithRelations($data, $this->model_id);
                flash()->success($this->type == 2 ? 'تم تعديل الخدمة بنجاح!' : 'تم تعديل المنتج بنجاح!');
            } else {
                $this->repository->createWithRelations($data);
                flash()->success($this->type == 2 ? 'تم إضافة الخدمة بنجاح!' : 'تم إضافة المنتج بنجاح!');
            }

            $this->closeModal();
            return $this->redirect(route('basicdata.products.index'), navigate: true);

        } catch (\Exception $e) {
            $this->addError('save_error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('basicdata::livewire.products.product-modal', [
            'categories' => $this->repository->categories(),
            'availableUnits' => $this->repository->units(),
            'kitchens' => $this->repository->kitchens(),
            'taxes' => $this->repository->vats(),
        ]);
    }
}
