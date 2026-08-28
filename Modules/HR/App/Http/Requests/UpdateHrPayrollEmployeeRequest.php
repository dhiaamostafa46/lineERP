<?php

namespace Modules\HR\App\Http\Requests;

use Modules\HR\App\Models\HrPayrollEmployee;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHrPayrollEmployeeRequest extends FormRequest
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
        $rules = HrPayrollEmployee::$rules;

        return $rules;
    }
}
