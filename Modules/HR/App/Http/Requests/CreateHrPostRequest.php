<?php

namespace Modules\HR\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\HR\App\Models\HrPost;

class CreateHrPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return HrPost::rules();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateAudience($validator);
        });
    }

    protected function validateAudience(Validator $validator): void
    {
        $flage = (int) $this->input('flage');

        if ($flage === HrPost::FLAG_EMPLOYEES && empty($this->input('employee_id'))) {
            $validator->errors()->add('employee_id', __('hr::lang.please_select_employee'));
        }

        if ($flage === HrPost::FLAG_DEPARTMENT && empty($this->input('department_id'))) {
            $validator->errors()->add('department_id', __('hr::lang.please_select_department'));
        }

        if ($flage === HrPost::FLAG_BRANCHES && empty($this->input('branch_id'))) {
            $validator->errors()->add('branch_id', __('hr::lang.please_select_branch'));
        }
    }
}
