<?php

namespace Modules\Pos\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosCheckoutRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:inv_customers,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.vat' => 'nullable|numeric|min:0',
            'items.*.unit_id' => 'nullable|integer',
            'items.*.have_sizes' => 'nullable|boolean',
            'items.*.name' => 'nullable|string',
            'items.*.tax_id' => 'nullable|integer',
            'items.*.type_discount' => 'nullable|integer',
            'items.*.number_discount' => 'nullable|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.method_id' => 'required|exists:pos_payment_methods,id',
            'payments.*.amount' => 'required|numeric',
            'total' => 'required|numeric',
            'discount' => 'nullable|numeric|min:0',
            'number_discount' => 'nullable|numeric|min:0',
            'type_discount' => 'nullable|integer',
            'shipping_cost' => 'nullable|numeric|min:0',
            'shipping_vat_rate' => 'nullable|numeric|min:0',
            'shipping_tax_id' => 'nullable|integer',
            'prices_include_vat' => 'nullable|boolean',
            'is_return' => 'nullable|boolean',
            'parent_id' => 'nullable|integer|exists:sales_invoices,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
