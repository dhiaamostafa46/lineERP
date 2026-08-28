<?php

namespace Modules\Store\App\Http\Requests;


use App\Models\StoreApp\Store;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Store\App\Models\StOpeningBalance;

class CreateStOpeningBalanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'document_date' => 'required|date',
            'store_id'      => 'required|exists:stores,id',
            'items'         => 'required|array|min:1',
            'items.*.quantity'   => 'required|numeric|min:0.0001',
            'items.*.unit_cost'  => 'required|numeric|min:0',
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
