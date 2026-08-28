<?php

namespace Modules\Store\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStSettlementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'document_number' => 'required|string',
            'document_date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|array|min:1',
            'items.*.unit_id' => 'nullable|exists:product_units,id',
            'items.*.system_quantity' => 'required|numeric',
            'items.*.actual_quantity' => 'required|numeric',
            'items.*.unit_cost' => 'required|numeric',
        ];

        if ($this->has('items') && is_array($this->items)) {
            foreach ($this->items as $index => $item) {
                $haveSizes = filter_var($item['have_sizes'] ?? false, FILTER_VALIDATE_BOOLEAN);
                if ($haveSizes) {
                    $rules["items.$index.product_id"] = 'required|exists:product_sizes,id';
                } else {
                    $rules["items.$index.product_id"] = 'required|exists:products,id';
                }
            }
        }

        return $rules;
    }
}
