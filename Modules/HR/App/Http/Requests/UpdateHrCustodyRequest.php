<?php

namespace Modules\HR\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHrCustodyRequest extends FormRequest
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
            'employee_id' => 'required|exists:hr_employees,id',
            'asset_id'    => 'required|exists:hr_assets,id',
            'details'     => 'required|string',
            'received_id' => 'nullable|exists:users,id',
            'received_at' => 'nullable|date',
            'status'      => 'nullable|string',
        ];
    }
}
