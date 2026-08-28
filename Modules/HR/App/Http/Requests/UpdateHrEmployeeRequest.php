<?php

namespace Modules\HR\App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\App\Models\HrEmployee;

class UpdateHrEmployeeRequest extends FormRequest
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
        $employee = HrEmployee::find($this->employee);
        $main_employee = Employee::find($employee->employee_id);

        $rules = [];
        // HR Employee
        $rules['job_id'] = 'required|exists:hr_jobs,id';
        $rules['department_id'] = 'required|exists:hr_departments,id';
        $rules['shift_id'] = 'exists:hr_shift_types,id';
        $rules['max_off_days'] = 'integer|min:0';
        $rules['max_advance'] = 'numeric|min:0';
        $rules['branch_id'] = 'required|exists:branches,id';
        // $rules['job_level']          = 'required';
        // $rules['specialty']          = 'required';
        $rules['start_at'] = 'required';
        // $rules['license_expired_at'] = 'required';

        // Employee
        $rules['full_name'] = 'required|string|max:255';
        $rules['username'] = ['required', 'string', 'max:255', Rule::unique('employees', 'username')->ignore($main_employee->id)->whereNull('deleted_at')];
        $rules['phone'] = ['required', 'string', 'max:255', Rule::unique('employees', 'phone')->ignore($main_employee->id)->whereNull('deleted_at')];
        $rules['email'] = ['required', 'string', 'max:255', Rule::unique('employees', 'email')->ignore($main_employee->id)->whereNull('deleted_at')];
        $rules['dob'] = 'nullable|max:255';
        $rules['address'] = 'required|string|max:255';
        // $rules['national_address']   = 'required|string|max:255';
        // $rules['religion']           = 'required|string|max:255';
        $rules['gender'] = 'required|integer';
        // $rules['marital_status']     = 'required|integer';
        // $rules['number_of_children'] = 'required|integer';
        // $rules['nationality']        = 'required|string|max:255';

        // Bank
        $rules['bank_name'] = 'nullable|max:255';
        $rules['iban'] = ['nullable', 'max:255', Rule::unique('employee_banks', 'iban')->ignore($main_employee->bank?->id)->whereNull('deleted_at')];

        // Identity
        $rules['identity_type'] = 'integer';

        $rules['identity_no'] = ['nullable', 'max:255', Rule::unique('employee_identities', 'identity_no')->ignore($main_employee->identity?->id)->whereNull('deleted_at')];
        $rules['insurance_no'] = ['nullable', 'max:255', Rule::unique('employee_identities', 'insurance_no')->ignore($main_employee->identity?->id)->whereNull('deleted_at')];
        $rules['identity_expired_at'] = 'nullable|max:255';
        // $rules['insurance_expired_at'] = 'required|string|max:255';
        $rules['job_number'] = ['required', Rule::unique('hr_employees', 'job_number')->ignore($employee->id)->whereNull('deleted_at')];
        // User
        $rules['user.photo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        $rules['user.name'] = 'required|string|max:255';
        $rules['user.phone'] = ['required', 'max:255', Rule::unique('users', 'phone')->ignore($main_employee->user_id)->whereNull('deleted_at')];
        // $rules['user.role_id'] = 'nullable|string|max:255';
        $rules['user.email'] = ['required', Rule::unique('users', 'email')->ignore($main_employee->user_id)->whereNull('deleted_at')];
        // $rules['user.password'] = 'nullable|confirmed|min:6';
        $rules['user.status'] = 'required|integer';

        return $rules;
    }
}
