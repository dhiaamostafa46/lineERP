<?php

namespace Modules\Invoices\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvSettingRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ── Sales ──────────────────────────────────────────────────
            'sales_prefix'                => ['nullable', 'string', 'max:20', 'regex:/^\S+$/'],
            'sales_next_number'           => ['nullable', 'integer', 'min:1'],
            'sales_auto_post'             => ['nullable', 'boolean'],
            'sales_terms'                 => ['nullable', 'string', 'max:2000'],

            // ── Sales Return ────────────────────────────────────────────
            'sales_return_prefix'         => ['nullable', 'string', 'max:20', 'regex:/^\S+$/'],
            'sales_return_next_number'    => ['nullable', 'integer', 'min:1'],

            // ── Purchase ────────────────────────────────────────────────
            'purchase_prefix'             => ['nullable', 'string', 'max:20', 'regex:/^\S+$/'],
            'purchase_next_number'        => ['nullable', 'integer', 'min:1'],
            'purchase_auto_post'          => ['nullable', 'boolean'],
            'purchase_terms'              => ['nullable', 'string', 'max:2000'],

            // ── Purchase Return ─────────────────────────────────────────
            'purchase_return_prefix'      => ['nullable', 'string', 'max:20', 'regex:/^\S+$/'],
            'purchase_return_next_number' => ['nullable', 'integer', 'min:1'],

            // ── Purchase Order ──────────────────────────────────────────
            'purchase_order_prefix'       => ['nullable', 'string', 'max:20', 'regex:/^\S+$/'],
            'purchase_order_next_number'  => ['nullable', 'integer', 'min:1'],

            // ── Quotation ───────────────────────────────────────────────
            'quotation_prefix'            => ['nullable', 'string', 'max:20', 'regex:/^\S+$/'],
            'quotation_next_number'       => ['nullable', 'integer', 'min:1'],
            'quotation_validity_days'     => ['nullable', 'integer', 'min:1'],
            'quotation_terms'             => ['nullable', 'string', 'max:2000'],

            // ── Shipping ────────────────────────────────────────────────
            'enable_shipping'             => ['nullable', 'boolean'],
            'default_shipping_vat_rate'   => ['nullable', 'numeric', 'min:0', 'max:100'],

            // ── Tax & Zakat ─────────────────────────────────────────────
            'prices_include_vat'          => ['nullable', 'boolean'],
            'default_vat_rate'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'zakat_rate'                  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'zakat_calculation_method'    => ['nullable', 'string', 'in:fixed,net_worth'],

            // ── General & UI ────────────────────────────────────────────
            'show_logo_in_print'          => ['nullable', 'boolean'],
            'show_product_image'          => ['nullable', 'boolean'],
            'show_discount_column'        => ['nullable', 'boolean'],
            'show_unit_price_after_vat'   => ['nullable', 'boolean'],
            'show_customer_balance'       => ['nullable', 'boolean'],
            'invoice_footer_text'         => ['nullable', 'string', 'max:255'],
            'allow_negative_stock'        => ['nullable', 'boolean'],
            'require_cost_center'         => ['nullable', 'boolean'],

            // ── ZATCA Phase 2 ───────────────────────────────────────────
            'zatca_environment'              => ['nullable', 'string', 'in:sandbox,simulation,production'],
            'zatca_uuid'                     => ['nullable', 'string', 'max:255'],
            'zatca_organization_name'        => ['nullable', 'string', 'max:255'],
            'zatca_organization_unit'        => ['nullable', 'string', 'max:255'],
            'zatca_common_name'              => ['nullable', 'string', 'max:255'],
            'zatca_vat_number'               => ['nullable', 'string', 'max:15'],
            'zatca_vat_name'                 => ['nullable', 'string', 'max:255'],
            'zatca_cv'                       => ['nullable', 'string', 'max:255'],
            'zatca_activity_classification'  => ['nullable', 'string', 'max:255'],
            'zatca_registered_address'       => ['nullable', 'string', 'max:500'],
            'zatca_otp'                      => ['nullable', 'string', 'max:255'],
            'zatca_otp_confirmation'         => ['nullable', 'string', 'max:255', 'same:zatca_otp'],
            'zatca_status'                   => ['nullable', 'string', 'max:100'],
            'zatca_building_number'          => ['nullable', 'string', 'max:10'],
            'zatca_street_name'              => ['nullable', 'string', 'max:255'],
            'zatca_district_name'            => ['nullable', 'string', 'max:255'],
            'zatca_city_name'                => ['nullable', 'string', 'max:255'],
            'zatca_postal_code'              => ['nullable', 'string', 'max:5'],
            'zatca_country_code'             => ['nullable', 'string', 'max:2'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'sales_prefix.regex'               => __('invoices::models/invoices_setting.hints.single_word'),
            'sales_return_prefix.regex'        => __('invoices::models/invoices_setting.hints.single_word'),
            'purchase_prefix.regex'            => __('invoices::models/invoices_setting.hints.single_word'),
            'purchase_return_prefix.regex'     => __('invoices::models/invoices_setting.hints.single_word'),
            'quotation_prefix.regex'           => __('invoices::models/invoices_setting.hints.single_word'),
        ];
    }
}
