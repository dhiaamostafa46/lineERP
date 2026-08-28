<?php

namespace Modules\HR\App\Http\Requests;

use Modules\HR\App\Models\HrJob;
use Illuminate\Foundation\Http\FormRequest;

class CreateHrJobRequest extends FormRequest
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
        return HrJob::rules();
    }
}
