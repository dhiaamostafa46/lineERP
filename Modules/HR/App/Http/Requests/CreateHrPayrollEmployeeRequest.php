<?php

namespace Modules\HR\App\Http\Requests;

use Modules\HR\App\Models\HrPayrollEmployee;
use Illuminate\Foundation\Http\FormRequest;

class CreateHrPayrollEmployeeRequest extends FormRequest
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
        return HrPayrollEmployee::$rules;
    }
}
