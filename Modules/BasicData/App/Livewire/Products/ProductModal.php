<?php

namespace Modules\BasicData\App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Unit;
use App\Models\BasicDataApp\ProductSize;
use App\Models\BasicDataApp\ProductUnit;
use Modules\BasicData\App\Models\DbKitchen;
use App\Models\AccuSoft\TaxAccount;

class ProductModal extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $product_id = null;
    public $is_edit = false;
    public $activeTab = 'basic'; // 'basic', 'sizes', 'units', 'other'

    // 1. Basic Info
    public $name = [];
    public $barcode = '';
    public $category_id = '';
    public $kitchen_id = '';
    public $base_unit_id = '';
    public $tax_id = '';
    public $type = 1; // 1 = Product, 2 = Service
    public $cost_price = 0.00;
    public $prod_price = 0.00;
    public $status = 1;
    public $img;
    public $existing_img = null;

    // 2. Sizes
    public $have_sizes = false;
    public $sizes = [];

    // 3. Multiple Units
    public $units = [];

    // 4. Other Info & Schedule
    public $details = [];
    public $min_quantity = 0;
    public $calories = 0;
    public $s_from = null;
    public $s_to = null;
    public $work_days = [];

    protected function rules(): array
    {
        $rules = [
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'kitchen_id' => 'nullable',
            'base_unit_id' => 'nullable|exists:units,id',
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

    public function mount()
    {
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->product_id = null;
        $this->is_edit = false;
        $this->activeTab = 'basic';
        
        $this->name = [];
        $this->details = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->name[$locale] = '';
            $this->details[$locale] = '';
        }
        
        $this->barcode = '';
        $this->category_id = Category::first()?->id ?? '';
        $this->kitchen_id = '';
        $this->base_unit_id = Unit::first()?->id ?? '';
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
                'unit_id' => Unit::first()?->id ?? '',
                'conversion_factor' => 1,
                'is_base' => 1,
            ]
        ];

        $this->min_quantity = 0;
        $this->calories = 0;
        $this->s_from = null;
        $this->s_to = null;
        $this->work_days = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];

        $this->resetErrorBag();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function addSizeRow()
    {
        $this->sizes[] = [
            'ar' => ['name' => ''],
            'en' => ['name' => ''],
            'cost_price' => 0.00,
            'sale_price' => 0.00,
            'barcode' => '',
        ];
    }

    public function removeSizeRow($index)
    {
        unset($this->sizes[$index]);
        $this->sizes = array_values($this->sizes);
    }

    public function addUnitRow()
    {
        $this->units[] = [
            'unit_id' => Unit::first()?->id ?? '',
            'conversion_factor' => 1,
            'is_base' => 0,
        ];
    }

    public function removeUnitRow($index)
    {
        unset($this->units[$index]);
        $this->units = array_values($this->units);
    }

    #[On('openCreateModal')]
    public function openCreate($type = 1)
    {
        $this->resetFields();
        $this->type = in_array((int)$type, [1, 2]) ? (int)$type : 1;
        $this->isOpen = true;
    }

    #[On('openEditModal')]
    public function openEdit($id)
    {
        $this->resetFields();
        $product = Product::with(['sizes', 'units'])->find($id);
        if ($product) {
            $this->product_id = $id;
            $this->is_edit = true;
            
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $product->translate($locale)->name ?? '';
                $this->details[$locale] = $product->translate($locale)->details ?? '';
            }

            $this->barcode = $product->barcode;
            $this->category_id = $product->category_id;
            $this->kitchen_id = $product->kitchen_id;
            $this->base_unit_id = $product->base_unit_id;
            $this->tax_id = $product->tax_id;
            $this->type = $product->type ?? 1;
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
                        'ar' => ['name' => $size->translate('ar')->name ?? ''],
                        'en' => ['name' => $size->translate('en')->name ?? ''],
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
                    'unit_id' => $product->base_unit_id ?? Unit::first()?->id ?? '',
                    'conversion_factor' => 1,
                    'is_base' => 1,
                ];
            }

            $this->min_quantity = $product->min_quantity ?? 0;
            $this->calories = $product->calories ?? 0;
            $this->s_from = $product->s_from;
            $this->s_to = $product->s_to;
            $this->work_days = is_array($product->work_days) ? $product->work_days : ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];

            $this->isOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetFields();
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $vatRate = 15;
            if (!empty($this->tax_id)) {
                $taxAccount = TaxAccount::find($this->tax_id);
                $vatRate = $taxAccount ? $taxAccount->rate : 15;
            }

            $data = [
                'barcode' => $this->barcode ?: null,
                'category_id' => $this->category_id,
                'kitchen_id' => $this->kitchen_id ?: null,
                'base_unit_id' => $this->base_unit_id ?: null,
                'tax_id' => $this->tax_id ?: null,
                'vat' => $vatRate,
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
            ];

            foreach ($this->name as $locale => $val) {
                $data[$locale] = [
                    'name' => $val,
                    'details' => $this->details[$locale] ?? '',
                ];
            }

            if ($this->is_edit && $this->product_id) {
                $product = Product::findOrFail($this->product_id);
                $product->update($data);

                if ($this->img) {
                    $product->clearMediaCollection('products');
                    $product->addMedia($this->img->getRealPath())->toMediaCollection('products');
                }

                // Delete old units & sizes
                $product->units()->delete();
                $product->sizes()->delete();

                flash()->success($this->type == 2 ? 'تم تعديل الخدمة بنجاح!' : 'تم تعديل المنتج بنجاح!');
            } else {
                $product = Product::create($data);

                if ($this->img) {
                    $product->addMedia($this->img->getRealPath())->toMediaCollection('products');
                }

                flash()->success($this->type == 2 ? 'تم إضافة الخدمة بنجاح!' : 'تم إضافة المنتج بنجاح!');
            }

            // Save Product Units
            if (!empty($this->units)) {
                foreach ($this->units as $u) {
                    if (!empty($u['unit_id'])) {
                        ProductUnit::create([
                            'product_id' => $product->id,
                            'unit_id' => $u['unit_id'],
                            'conversion_factor' => $u['conversion_factor'] ?: 1,
                            'is_base' => !empty($u['is_base']) ? 1 : 0,
                        ]);
                    }
                }
            }

            // Save Product Sizes
            if ($this->have_sizes && !empty($this->sizes)) {
                foreach ($this->sizes as $s) {
                    if (!empty($s['ar']['name']) || !empty($s['en']['name'])) {
                        $sizeData = [
                            'product_id' => $product->id,
                            'cost_price' => $s['cost_price'] ?: 0,
                            'sale_price' => $s['sale_price'] ?: 0,
                            'barcode' => $s['barcode'] ?: null,
                            'status' => 1,
                        ];
                        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $langName) {
                            $sizeData[$locale] = ['name' => $s[$locale]['name'] ?? ($s['ar']['name'] ?? '')];
                        }
                        ProductSize::create($sizeData);
                    }
                }
            }

            DB::commit();

            $this->closeModal();
            return $this->redirect(route('basicdata.products.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('save_error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = Category::all();
        $availableUnits = Unit::all();
        $kitchens = class_exists(DbKitchen::class) ? DbKitchen::where('status', 1)->get() : collect([]);
        $taxes = class_exists(TaxAccount::class) ? TaxAccount::where('status', 2)->get() : collect([]);

        return view('basicdata::livewire.products.product-modal', [
            'categories' => $categories,
            'availableUnits' => $availableUnits,
            'kitchens' => $kitchens,
            'taxes' => $taxes,
        ]);
    }
}
