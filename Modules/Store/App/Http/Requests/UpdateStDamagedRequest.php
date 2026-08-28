<?php

namespace Modules\Store\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Store\App\Models\StDamaged;

class UpdateStDamagedRequest extends FormRequest
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
        return [ ];
    }
}
