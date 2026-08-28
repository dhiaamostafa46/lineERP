<?php

namespace Modules\BasicData\App\Http\Requests;

use App\Models\BasicDataApp\Product;
use Illuminate\Foundation\Http\FormRequest;

class CreateDbProductRequest extends FormRequest
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
        return Product::rules();
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('type') != 2) {
                $units = $this->input('units', []);
                $hasValidUnit = false;
                if (is_array($units)) {
                    foreach ($units as $unit) {
                        if (! empty($unit['unit_id'])) {
                            $hasValidUnit = true;
                            break;
                        }
                    }
                }

                if (! $hasValidUnit) {
                    $validator->errors()->add('units', __('basicdata::models/db_products.messages.min_one_unit'));
                }
            }
        });
    }
}
