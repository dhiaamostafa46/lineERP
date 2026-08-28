<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class companiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $cityId = $this->input('city_id');
        if ($cityId === '' || $cityId === null) {
            $this->merge(['city_id' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->route('company');
        if ($id instanceof Company) {
            $id = $id->getKey();
        }

        return Company::rules($id ? (int) $id : null);
    }
}
