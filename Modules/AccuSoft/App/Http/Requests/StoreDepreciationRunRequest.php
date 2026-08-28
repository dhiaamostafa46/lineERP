<?php

namespace Modules\AccuSoft\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepreciationRunRequest extends FormRequest
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
        return [
            'run_month' => 'required|integer|min:1|max:12',
            'run_year' => 'required|integer|min:2000',
            'uses_individual_entries' => 'nullable|boolean',
        ];
    }
}
