<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:inv_customers,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
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
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
            'payment_terms' => 'nullable|string',
            'validity_period' => 'nullable|string',
        ];
    }
}
