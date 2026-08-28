<?php

namespace Modules\HR\App\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\HR\App\Models\HrTracker;
use Illuminate\Foundation\Http\FormRequest;

class CreateHrTrackerRequest extends FormRequest
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
        $rules = HrTracker::$rules;
        $department_id = $this->department_id;
        $type = $this->type;
        $rules['department_id'] = ['required',
            Rule::unique('hr_trackers')->where(function ($query) use ($department_id, $type) {
                return $query->where('department_id', $department_id)
                ->where('type', $type);
            })];
        return $rules;
    }
}
