<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Invoices\App\Models\PurchaseOrder;

class UpdatePurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'issue_date' => 'required|date',
            'supplier_id' => 'required|exists:inv_suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'type_inv' => 'required|in:1,2',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $inProducts = \Illuminate\Support\Facades\DB::table('products')->where('id', $value)->exists();
                        $inSizes = \Illuminate\Support\Facades\DB::table('product_sizes')->where('id', $value)->exists();
                        if (!$inProducts && !$inSizes) {
                            $fail(__('validation.exists', ['attribute' => $attribute]));
                        }
                    }
                },
            ],
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
