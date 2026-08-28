<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\invApp\SalesInvoice;

class CreateSalesReturnInvoiceRequest extends FormRequest
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
            'status' => 'nullable|in:1,2,3,4,5,6,7',
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
            'parent_id' => 'nullable|exists:sales_invoices,id',
            'return_reason' => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->parent_id)) {
                $branchId = $this->branch_id ?? (auth()->check() ? auth()->user()->branch_id : 1);
                $zatcaSetting = \Modules\Invoices\App\Models\ZatcaSetting::resolveForBranch($branchId);
                
                $isPhase2Enabled = ($zatcaSetting && $zatcaSetting->is_active &&
                    in_array($zatcaSetting->status, [\Modules\Invoices\App\Models\ZatcaSetting::ZATCA_STATUS_LINKED, \Modules\Invoices\App\Models\ZatcaSetting::ZATCA_STATUS_PRODUCTION]) &&
                    !empty($zatcaSetting->binary_security_token) &&
                    !empty($zatcaSetting->private_key) &&
                    !empty($zatcaSetting->secret));
                    
                if ($isPhase2Enabled) {
                    $validator->errors()->add('parent_id', 'تمنع هيئة الزكاة والضريبة والجمارك إنشاء الفاتورة في هذه الحالة (يجب ربطها بفاتورة أصلية).');
                }
            }
        });
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

