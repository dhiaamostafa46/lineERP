<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
        $rules = Employee::$rules;
        $rules['email'] = 'nullable|email|unique:employees,email,' . $this->employee;
        $rules['username'] = 'nullable|string|max:255|unique:employees,username,' . $this->employee;
        return $rules;
    }
}
