<?php

namespace App\Http\Requests;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;

class citiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->route('city');
        if ($id instanceof City) {
            $id = $id->getKey();
        }

        return City::rules($id ? (int) $id : null);
    }
}
