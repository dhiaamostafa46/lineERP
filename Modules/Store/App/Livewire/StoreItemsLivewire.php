<?php

namespace Modules\Store\App\Livewire;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductUnit;
use Livewire\Component;
use Illuminate\Support\Collection;

class StoreItemsLivewire extends Component
{
    public $openingBalance;
    public array $items = [];
    public Collection $allProducts;
    public array $allUnits = [];

    protected function rules()
    {
        return [
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.unit_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.total_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
        ];
    }

    protected $messages = [
        'items.*.product_id.required' => 'يجب اختيار المنتج',
        'items.*.product_id.exists' => 'المنتج المحدد غير موجود',
        'items.*.unit_id.required' => 'يجب اختيار الوحدة',
        'items.*.unit_id.exists' => 'الوحدة المحددة غير موجودة',
        'items.*.quantity.required' => 'يجب إدخال الكمية',
        'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من 0',
        'items.*.unit_cost.required' => 'يجب إدخال التكلفة',
        'items.*.unit_cost.min' => 'التكلفة يجب أن تكون 0 على الأقل',
    ];

    public function mount($openingBalance = null)
    {
        $this->openingBalance = $openingBalance;

        // جلب جميع المنتجات النشطة
        $this->allProducts = Product::where('status', Product::STATUS_ACTIVE)
            ->with('translations')
            ->get()
            ->mapWithKeys(function ($product) {
                $name = $product->translate(app()->getLocale())->name ?? 'Unknown';
                $code = $product->code ?? '';
                $label = $code ? $code . ' - ' . $name : $name;
                return [$product->id => $label];
            });

        // تحميل البيانات الموجودة أو إضافة صف جديد
        if ($this->openingBalance && $this->openingBalance->items->count() > 0) {
            $items = $this->openingBalance->items;
            foreach ($items as $index => $item) {
                $this->items[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name ?? '',
                    'unit_id' => $item->unit_id,
                    'unit_name' => $item->unit_name ?? '',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost ?? 0,
                    'total_cost' => $item->total_cost ?? 0,
                    'notes' => $item->notes ?? '',
                ];
                $this->loadProductUnits($index, $item->product_id);
            }
        } else {
            $this->addItem();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'product_name' => '',
            'unit_id' => '',
            'unit_name' => '',
            'quantity' => 1,
            'unit_cost' => 0,
            'total_cost' => 0,
            'notes' => '',
        ];

        $index = count($this->items) - 1;
        $this->allUnits[$index] = [];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            unset($this->allUnits[$index]);

            $this->items = array_values($this->items);
            $this->allUnits = array_values($this->allUnits);
        }
    }

    /**
     * يتم استدعاء هذه الدالة تلقائياً عند تغيير أي حقل في items
     */
    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);

        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $field = $parts[1];

            // عند تغيير المنتج - جلب الوحدات والتكلفة
            if ($field === 'product_id') {
                $this->loadProductUnits($index, $value);
                  $this->updateUnitCost($index);
            }

            // عند تغيير الوحدة - تحديث التكلفة
            // elseif ($field === 'unit_id') {
            //     $this->updateUnitCost($index);
            // }

            // إعادة حساب إجمالي الصف عند تغيير الكمية أو التكلفة
            if (in_array($field, ['quantity', 'unit_cost'])) {
                $this->recalculateRowTotal($index);
            }
        }
    }

    /**
     * جلب وحدات المنتج وتحديث البيانات
     */
    protected function loadProductUnits($index, $productId)
    {
        // إعادة تعيين البيانات
        $this->allUnits[$index] = [];
        $this->items[$index]['unit_id'] = '';
        $this->items[$index]['unit_name'] = '';
        $this->items[$index]['unit_cost'] = 0;

        if (!$productId) {
            return;
        }

        // جلب معلومات المنتج مع الترجمة
        $product = Product::with('translations')->find($productId);
        if ($product) {
            $this->items[$index]['product_name'] = $product->translate(app()->getLocale())->name ?? 'Unknown';
        }

        // جلب وحدات المنتج من جدول product_units
        $productUnits = ProductUnit::where('product_id', $productId)
            ->with('unit.translations')
            ->get();

        if ($productUnits->count() > 0) {
            // تحويل الوحدات إلى مصفوفة للعرض
            $unitsArray = [];
            foreach ($productUnits as $productUnit) {
                if ($productUnit->unit) {
                    $unitName = $productUnit->unit->translate(app()->getLocale())->name ?? 'Unknown';
                    $unitsArray[$productUnit->unit_id] = $unitName;
                }
            }

            $this->allUnits[$index] = $unitsArray;

            // اختيار الوحدة الأساسية تلقائياً
            $baseUnit = $productUnits->where('is_base', true)->first();
            $defaultUnit = $baseUnit ?? $productUnits->first();

            if ($defaultUnit) {
                $this->items[$index]['unit_id'] = $defaultUnit->unit_id;

                // الحصول على اسم الوحدة
                if ($defaultUnit->unit) {
                    $this->items[$index]['unit_name'] = $defaultUnit->unit->translate(app()->getLocale())->name ?? 'Unknown';
                }

                // تعيين سعر التكلفة من cost_price في جدول product_units
                $this->items[$index]['unit_cost'] = $defaultUnit->cost_price ?? 0;

                $this->recalculateRowTotal($index);
            }
        }
    }

    /**
     * تحديث التكلفة عند تغيير الوحدة
     */
    protected function updateUnitCost($index)
    {
        $unitId = $this->items[$index]['unit_id'] ?? null;
        $productId = $this->items[$index]['product_id'] ?? null;

        if ($productId && $unitId) {
            $product = Product::where('id', $productId)->first();

            if ($product) {
                // الحصول على اسم الوحدة مع الترجمة
                // if ($productUnit->unit) {
                //     $this->items[$index]['unit_name'] = $productUnit->unit->translate(app()->getLocale())->name ?? 'Unknown';
                // }

                // تعيين سعر التكلفة من cost_price
                $this->items[$index]['unit_cost'] = $product->cost_price ?? 0;
            }
        }

        $this->recalculateRowTotal($index);
    }

    /**
     * إعادة حساب إجمالي الصف
     */
    protected function recalculateRowTotal($index)
    {
        $quantity = (float) ($this->items[$index]['quantity'] ?? 0);
        $unitCost = (float) ($this->items[$index]['unit_cost'] ?? 0);

        // الإجمالي = الكمية × سعر التكلفة
        $this->items[$index]['total_cost'] = round($quantity * $unitCost, 2);
    }

    /**
     * الخصائص المحسوبة للإجماليات
     */
    public function getTotalItemsProperty()
    {
        return count($this->items);
    }

    public function getTotalQuantityProperty()
    {
        return array_sum(array_column($this->items, 'quantity'));
    }

    public function getTotalValueProperty()
    {
        return array_sum(array_column($this->items, 'total_cost'));
    }

    public function render()
    {
        return view('store::livewire.store-items-livewire');
    }
}
