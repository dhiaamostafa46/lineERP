<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\invApp\SalesInvoice;

class UpdateSalesInvoiceRequest extends FormRequest
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
            'customer_id' => 'required|exists:inv_customers,id',
            'cost_center_id' => \Modules\Invoices\App\Helpers\InvoiceHelper::getSetting('require_cost_center') ? 'required|exists:cost_centers,id' : 'nullable|exists:cost_centers,id',
            'store_id' => 'required|exists:stores,id',
            'status' => 'nullable|in:1,2',
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

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'cost_center_id.required' => __('invoices::models/invoices_setting.hints.cost_center_required'),
        ];
    }
}

