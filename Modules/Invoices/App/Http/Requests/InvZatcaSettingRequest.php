<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvZatcaSettingRequest extends FormRequest
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
        $isRequired = $this->filled('zatca_otp') ? 'required' : 'nullable';

        return [
            // ── Environment ─────────────────────────────────────────────
            'zatca_environment'              => ['nullable', 'string', 'in:sandbox,simulation,production'],
            'zatca_uuid'                     => ['nullable', 'string', 'max:255'],

            // ── Basic Fields ─────────────────────────────────────────────
            'zatca_common_name'              => [$isRequired, 'string', 'max:255'],
            'zatca_organization_name'        => [$isRequired, 'string', 'max:255'],
            'zatca_vat_number'               => [$isRequired, 'string', 'max:15'],
            'zatca_organization_unit_name'   => [$isRequired, 'string', 'max:255'],
            'zatca_activity_classification'  => [$isRequired, 'string', 'max:255'],
            'zatca_registered_address'       => [$isRequired, 'string', 'max:500'],
            'zatca_cv'                       => [$isRequired, 'string', 'max:255'],
            'zatca_otp'                      => ['nullable', 'string', 'max:255'],
            'zatca_otp_confirmation'         => ['nullable', 'string', 'max:255', 'same:zatca_otp'],

            // ── Additional Info ──────────────────────────────────────────
            'zatca_building_number'          => [$isRequired, 'string', 'max:10'],
            'zatca_street_name'              => [$isRequired, 'string', 'max:255'],
            'zatca_district_name'            => [$isRequired, 'string', 'max:255'],
            'zatca_city_name'                => [$isRequired, 'string', 'max:255'],
            'zatca_postal_code'              => [$isRequired, 'string', 'max:5'],
            'zatca_country_code'             => ['nullable', 'string', 'max:2'],
            'zatca_inv_type'                 => ['nullable', 'string', 'in:0100,1000,1100'],
            'zatca_isVatGroup'               => ['nullable', 'boolean'],
            
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'zatca_otp_confirmation.same' => 'تأكيد OTP يجب أن يطابق قيمة OTP',
            'zatca_environment.in'        => 'بيئة التشغيل يجب أن تكون: تجريبية أو محاكاة أو إنتاج',
        ];
    }
}
