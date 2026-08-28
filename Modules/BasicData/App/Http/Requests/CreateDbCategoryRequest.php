<?php

namespace Modules\BasicData\App\Http\Requests;

use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use Illuminate\Foundation\Http\FormRequest;

class CreateDbCategoryRequest extends FormRequest
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
        return Category::rules();
    }
}
