<?php

namespace Modules\Store\App\Livewire;

use App\Models\BasicDataApp\Product as BasicDataAppProduct;
use Livewire\Component;
use Modules\Store\App\Models\StOpeningBalance;
use Modules\Store\App\Models\Product;
use App\Models\BasicDataApp\Store;
use App\Models\BasicDataApp\Status;
use App\Models\StoreApp\Store as StoreAppStore;

class OpeningBalanceForm extends Component
{
    // Main model properties
    public $store_id;
    public $document_number;
    public $document_date;
    public $status;
    public $notes;

    // Items array - source of truth
    public array $items = [];

    // Data for dropdowns
    public $stores;
    public $statuses;
    public $allProducts;
    public array $allUnits = [];

    // Listeners for events from child component
    protected $listeners = [
        'itemAdded' => 'addItem',
        'itemRemoved' => 'removeItem',
        'itemUpdated' => 'updateItem',
    ];

    public function mount($openingBalance = null)
    {
        // Load data for dropdowns
        $this->stores = StoreAppStore::pluck('name', 'id');
        //$this->statuses = Status::pluck('name', 'id'); // Assuming Status model
        $this->allProducts = BasicDataAppProduct::where('status', BasicDataAppProduct::STATUS_ACTIVE)->get()->pluck('name', 'id');

        $this->document_number = StOpeningBalance::generateDocumentNumber();
        $this->document_date = now()->format('Y-m-d');
        $this->status = StOpeningBalance::STATUS_DRAFT;

        if ($openingBalance) {
            $this->store_id = $openingBalance->store_id;
            $this->document_number = $openingBalance->document_number;
            $this->document_date = $openingBalance->document_date->format('Y-m-d');
            $this->status = $openingBalance->status;
            $this->notes = $openingBalance->notes;

            foreach ($openingBalance->items as $item) {
                $this->items[] = [
                    'product_id' => $item->product_id,
                    'unit_id' => $item->unit_id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'notes' => $item->notes,
                ];
            }
        } else {
            // Start with one empty item
            $this->addItem();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'unit_id' => '',
            'quantity' => 1,
            'unit_cost' => 0,
            'total_cost' => 0,
            'notes' => '',
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function updateItem($data)
    {
        $index = $data['index'];
        $field = $data['field'];
        $value = $data['value'];

        $this->items[$index][$field] = $value;

        if ($field === 'product_id') {
            $product = BasicDataAppProduct::with(['units.unit'])->find($value);
            if($product){
                $this->allUnits[$index] = $product->units->pluck('unit.name', 'unit_id')->toArray();
                $defaultUnit = $product->units->firstWhere('is_default', true) ?? $product->units->first();
                if($defaultUnit){
                    $this->items[$index]['unit_id'] = $defaultUnit->unit_id;
                    $this->items[$index]['unit_cost'] = (float)$defaultUnit->cost_price;
                }
            }
        }

        $this->recalculateRowTotal($index);
    }

    protected function recalculateRowTotal($index)
    {
        $quantity = (float)($this->items[$index]['quantity'] ?? 0);
        $unitCost = (float)($this->items[$index]['unit_cost'] ?? 0);
        $this->items[$index]['total_cost'] = round($quantity * $unitCost, 4);
    }

    public function save()
    {
        $this->validate([
            'store_id' => 'required|exists:st_stores,id',
            'document_date' => 'required|date',
            'status' => 'required',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:st_products,id',
            'items.*.unit_id' => 'required|exists:st_units,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        // ... Logic to save the opening balance and its items ...

        session()->flash('message', 'Opening Balance saved successfully.');

        // return redirect()->to('/opening-balances');
    }

    public function render()
    {
        return view('store::livewire.opening-balance-form');
    }
}
