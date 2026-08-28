<?php

namespace App\Http\Requests\api;

// use Modules\HR\App\Models\HrAbsentRequests;
 use Illuminate\Foundation\Http\FormRequest;

class ApiAdvanceRequest extends FormRequest
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
        return [
        'description' => 'required|string',
        'amount' => 'required|numeric|min:0',
        'from_date' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        'installments.*.date' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        'installments.*.amount' => 'required|numeric|min:1',
        'status' => 'nullable|in:1,2,3',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        'reason' => 'nullable|string|max:255',

        ];
    }
}
