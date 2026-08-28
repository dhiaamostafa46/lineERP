<?php

namespace Modules\AccuSoft\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetCategoryRequest extends FormRequest
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
                'branch_id' => auth()->check() ? auth()->user()->branch_id : null,
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
            'has_accounting_effect' => 'boolean',
            'asset_account_id' => 'required_if:has_accounting_effect,1|nullable|exists:tree_accounts,id',
            'accumulated_depreciation_account_id' => 'nullable|exists:tree_accounts,id',
            'depreciation_expense_account_id' => 'nullable|exists:tree_accounts,id',
            'default_depreciation_method' => 'required|string',
            'default_useful_life' => 'nullable|integer|min:1',
            'calculation_type' => 'nullable|string',
            'useful_life_type' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
