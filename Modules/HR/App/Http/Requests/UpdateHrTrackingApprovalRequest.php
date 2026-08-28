<?php

namespace Modules\HR\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HR\App\Models\HrTrackingApproval;

class UpdateHrTrackingApprovalRequest extends FormRequest
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
        $rules = HrTrackingApproval::$rules;

        return $rules;
    }
}
