<?php

namespace Modules\HR\App\Http\Requests;

use Modules\HR\App\Models\HrAdvance;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHrAdvanceRequest extends FormRequest
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
        $rules = HrAdvance::$rules;

        return $rules;
    }
}
