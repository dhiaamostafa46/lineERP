<?php

namespace App\Http\Requests;

use App\Models\CompanyContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class companyContractsRequest extends FormRequest
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
        return CompanyContract::rules();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $start = $this->input('start_date');
            $end = $this->input('end_date');
            if ($start && $end && strtotime((string) $end) < strtotime((string) $start)) {
                $validator->errors()->add('end_date', __('validation.after_or_equal', [
                    'attribute' => __('models/CompanyContracts.fields.end_date'),
                    'date' => __('models/CompanyContracts.fields.start_date'),
                ]));
            }
        });
    }
}
