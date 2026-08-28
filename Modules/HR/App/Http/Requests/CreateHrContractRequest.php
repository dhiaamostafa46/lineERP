<?php

namespace Modules\HR\App\Http\Requests;

use Modules\HR\App\Models\HrContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHrContractRequest extends FormRequest
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
        $rules = HrContract::$rules;
        $rules['contract_number'] = ['required', 'string', Rule::unique('hr_contracts', 'contract_number')->whereNull('deleted_at')];
        return $rules;
    }
}
