<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\invApp\InvSupplier;

class CreateInvSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'address' => 'nullable|string',
            'phone' => 'nullable',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
