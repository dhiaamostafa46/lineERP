<?php

namespace Modules\HR\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHrContractRequest extends FormRequest
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
        $contractId = $this->route('hr_contract')
            ?? $this->route('contract')
            ?? $this->id;

        return [
            'employee_id'     => 'required|exists:hr_employees,id',
            'type_id'         => 'required|exists:hr_contract_types,id',
            'contract_number' => ['required', 'string', Rule::unique('hr_contracts', 'contract_number')->ignore($contractId)->whereNull('deleted_at')],
            'file'            => 'nullable|file|max:10240',
            'qiwa_no'         => 'nullable|string',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after:start_date',
        ];
    }
}
