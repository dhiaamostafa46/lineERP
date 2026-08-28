<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Invoices\App\Models\PurchaseInvoice;

class UpdatePurchaseReturnInvoiceRequest extends FormRequest
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
            'type_inv' => 'sometimes|in:1,2',
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
            'items.*.serial' => 'nullable|string|max:6',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'parent_id' => 'nullable|exists:purchase_invoices,id',
            'return_reason' => 'nullable|string|max:1000',
            'shipping_cost' => 'nullable|numeric|min:0',
            'shipping_vat_rate' => 'nullable|numeric|min:0',
            'shipping_vat_amount' => 'nullable|numeric|min:0',
        ];
    }
}
