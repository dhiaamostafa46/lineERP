<?php

namespace Modules\AccuSoft\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->has('branch_id') || empty($this->branch_id)) {
            $this->merge([
                'branch_id' => auth()->check() ? (auth()->user()->branch_id ?? 1) : 1,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'branch_id' => 'required',
            'asset_category_id' => 'required_if:depreciation_status,category|nullable|exists:asset_categories,id',
            'parent_account_id' => 'required_if:depreciation_status,custom|nullable|exists:tree_accounts,id',
            'payment_account_id' => 'required_unless:depreciation_status,none|nullable|exists:tree_accounts,id',
            'tax_amount' => 'required_unless:depreciation_status,none|nullable',
            'tax_type' => 'required_with:tax_amount|in:inclusive,exclusive',
            'purchase_date' => 'required_unless:depreciation_status,none|nullable|date',
            'purchase_value' => 'required_unless:depreciation_status,none|nullable|numeric|min:0',
            'useful_life' => 'nullable|integer|min:1',
            'salvage_value' => 'required_unless:depreciation_status,none|nullable|numeric|min:0',
            'calculation_type' => 'nullable|string',
            'useful_life_type' => 'nullable|string',
            'depreciation_method' => 'nullable',
            'depreciation_status' => 'nullable|string',
            'cost_center_id' => 'required_unless:depreciation_status,none|nullable|exists:cost_centers,id',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
