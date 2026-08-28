<?php

namespace Modules\BasicData\App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Unit;
use App\Models\AccuSoft\TaxAccount;

class ProductModal extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $product_id = null;
    public $is_edit = false;

    public $name = [];
    public $barcode = '';
    public $category_id = '';
    public $base_unit_id = '';
    public $tax_id = '';
    public $type = 1; // 1 = Product, 2 = Service
    public $cost_price = 0.00;
    public $prod_price = 0.00;
    public $status = 1;
    public $img;
    public $existing_img = null;

    protected function rules(): array
    {
        $rules = [
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'base_unit_id' => 'nullable|exists:units,id',
            'tax_id' => 'nullable',
            'type' => 'required|in:1,2',
            'cost_price' => 'nullable|numeric|min:0',
            'prod_price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
            'img' => 'nullable|image|max:2048',
        ];

        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $rules["name.$locale"] = 'required|string|max:255';
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
        $this->name = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->name[$locale] = '';
        }
        $this->barcode = '';
        $this->category_id = Category::first()?->id ?? '';
        $this->base_unit_id = Unit::first()?->id ?? '';
        $this->tax_id = '';
        $this->type = 1;
        $this->cost_price = 0.00;
        $this->prod_price = 0.00;
        $this->status = 1;
        $this->img = null;
        $this->existing_img = null;
        $this->resetErrorBag();
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
        $product = Product::find($id);
        if ($product) {
            $this->product_id = $id;
            $this->is_edit = true;
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $product->translate($locale)->name ?? '';
            }
            $this->barcode = $product->barcode;
            $this->category_id = $product->category_id;
            $this->base_unit_id = $product->base_unit_id;
            $this->tax_id = $product->tax_id;
            $this->type = $product->type ?? 1;
            $this->cost_price = $product->cost_price ?? 0.00;
            $this->prod_price = $product->prod_price ?? 0.00;
            $this->status = $product->status ?? 1;
            $this->existing_img = $product->imgThumbPath;
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

        $data = [
            'barcode' => $this->barcode ?: null,
            'category_id' => $this->category_id,
            'base_unit_id' => $this->base_unit_id ?: null,
            'tax_id' => $this->tax_id ?: null,
            'type' => $this->type,
            'cost_price' => $this->cost_price ?: 0,
            'prod_price' => $this->prod_price ?: 0,
            'status' => $this->status,
        ];

        foreach ($this->name as $locale => $val) {
            $data[$locale] = ['name' => $val];
        }

        if ($this->is_edit && $this->product_id) {
            $product = Product::findOrFail($this->product_id);
            $product->update($data);

            if ($this->img) {
                $product->clearMediaCollection('products');
                $product->addMedia($this->img->getRealPath())->toMediaCollection('products');
            }

            flash()->success($this->type == 2 ? 'تم تعديل الخدمة بنجاح!' : 'تم تعديل المنتج بنجاح!');
        } else {
            $product = Product::create($data);

            if ($this->img) {
                $product->addMedia($this->img->getRealPath())->toMediaCollection('products');
            }

            flash()->success($this->type == 2 ? 'تم إضافة الخدمة بنجاح!' : 'تم إضافة المنتج بنجاح!');
        }

        $this->closeModal();
        return $this->redirect(route('basicdata.products.index'), navigate: true);
    }

    public function render()
    {
        $categories = Category::all();
        $units = Unit::all();
        $taxes = class_exists(TaxAccount::class) ? TaxAccount::where('status', 2)->get() : collect([]);

        return view('basicdata::livewire.products.product-modal', [
            'categories' => $categories,
            'units' => $units,
            'taxes' => $taxes,
        ]);
    }
}
