<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{


    use SoftDeletes, Translatable;

    protected $table = 'templates';

    protected $fillable = [
        'org_id',
        'branch_id',
        'document_type',
        'print_format',
        'is_default',
        'header_html',
        'content_html',
        'footer_html',
        'css_styles',
        'variables',
        'status',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public $translatedAttributes = ['name'];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function documentDefaults()
    {
        return $this->hasMany(TemplateDocumentDefault::class, 'template_id');
    }

    public function customFields()
    {
        return $this->hasMany(TemplateCustomField::class, 'template_id');
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status] ?? '';
    }

    public static function rules()
    {
        $rules = [];
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        $rules['document_type'] = 'nullable|string';
        $rules['print_format'] = 'required|string';
        $rules['status'] = 'required|integer';

        return $rules;
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public static function getDummyData()
    {
        return [
            'organization_name' => 'شركة إيفيكس للتجارة',
            'branch_name' => 'الفرع الرئيسي',
            'seller_vat' => '300000000000003',
            'seller_cr' => '1010123456',
            'seller_phone' => '0501234567',
            'seller_address' => 'الرياض، المملكة العربية السعودية',
            'invoice_number' => 'INV-2026-001',
            'order_number' => 'ORD-999',
            'customer_invoice_number' => 'REF-00123',
            'issue_date' => date('Y-m-d'),
            'customer_name' => 'أحمد محمد عبد الله',
            'customer_tax' => '310000000000002',
            'customer_address_full' => 'حي الصحافة، الرياض',
            'customer_phone' => '0500000000',
            'customer_cr' => '1010000000',

            'items' => [
                [
                    'product_name' => 'جهاز كمبيوتر محمول',
                    'image' => asset('admin_assets/media/stock/600x400/img-1.jpg'),
                    'description' => 'Intel i7, 512GB SSD',
                    'unit_name' => 'حبة',
                    'unit_price' => '3,000.00',
                    'quantity' => '1.00',
                    'discount' => '0.00',
                    'discount_value' => '0.00',
                    'taxable_amount' => '3,000.00',
                    'vat_rate' => '15',
                    'tax_percent' => '15',
                    'vat_amount' => '450.00',
                    'total' => '3,450.00',
                    'barcode' => 'SKU-10091',
                    'characteristics' => 'اللون: أسود',
                    'options' => '',
                    'item_barcode_rendered' => '<div style="font-family: monospace; font-size: 0.75rem; letter-spacing: 1px;">||||| SKU-10091 |||||</div>',
                ],
                [
                    'product_name' => 'فأرة لاسلكية ذكية',
                    'image' => asset('admin_assets/media/stock/600x400/img-2.jpg'),
                    'description' => 'فأرة قابلة لإعادة الشحن',
                    'unit_name' => 'حبة',
                    'unit_price' => '100.00',
                    'quantity' => '2.00',
                    'discount' => '10.00',
                    'discount_value' => '10.00',
                    'taxable_amount' => '190.00',
                    'vat_rate' => '15',
                    'tax_percent' => '15',
                    'vat_amount' => '28.50',
                    'total' => '218.50',
                    'barcode' => 'SKU-20033',
                    'characteristics' => '',
                    'options' => '',
                    'item_barcode_rendered' => '<div style="font-family: monospace; font-size: 0.75rem; letter-spacing: 1px;">||||| SKU-20033 |||||</div>',
                ]
            ],
            'total_exclusive_vat' => '3,200.00',
            'total_discount' => '10.00',
            'total_vat' => '478.50',
            'shipping_cost' => '50.00',
            'shipping_cost_total' => '50.00',
            'total_inclusive_vat' => '3,718.50',
            'total_in_words' => 'ثلاثة آلاف وسبعمائة وثمانية عشر ريالاً وخمسون هللة',
            'created_by_name' => 'مدير النظام',
            'status_text' => 'معتمدة',
            'payment_method' => 'نقدي / Cash',
            'payment_status' => 'مدفوعة',
            'invoice_description' => 'فاتورة مبيعات معدة لأغراض العرض',
            'notes' => 'ملاحظة عامة: الفاتورة مدفوعة ولا تحتاج للمتابعة.',
            'invoice_title_ar' => 'فاتورة ضريبية',
            'invoice_title_en' => 'Tax Invoice',
            'issue_datetime' => date('Y-m-d H:i:s'),
            'invoice_type_text' => 'فاتورة ضريبية',
            'invoice_type_code' => '388',
            'invoice_subtype_text' => 'مبسطة',
            'invoice_subtype_code' => '0200000',
            'qr_code_rendered' => '<div style="width: 100%; height: 100%; background: #e5e7eb; display: flex; justify-content: center; align-items: center; color: #9ca3af; font-size: 0.8rem; font-weight: bold;">QR Code</div>',
            'barcode_rendered' => '<div style="width: 150px; height: 30px; background: #e5e7eb; margin: 0 auto; display: flex; justify-content: center; align-items: center; color: #9ca3af; font-size: 0.8rem; font-weight: bold;">BARCODE</div>',
            'company_logo' => asset('admin_assets/media/logos/Logoevix.png'),
            'qr_code' => 'AQRFVklYAg8wMDAwMDAwMDAwMDAwMDADFDIwMjYtMDYtMjJUMDg6NTg6MDBaBAQyLjg4BQQwLjM4',
            'zatca_details' => '1',
            'zatca_request_id' => 'REQ-2026-99912',
            'zatca_response_payload' => '{"validationResults":{"errorMessages":[]},"reportingStatus":"REPORTED"}',
            'status_badge' => 'badge bg-success',
        ];
    }

    public function renderPreview($previewData = null)
    {
        if (!$previewData) {
            $previewData = self::getDummyData();
        }

        $html = '<style>' . $this->css_styles . '</style>' . $this->header_html;

        try {
            $html = \Illuminate\Support\Facades\Blade::render($html, [
                'previewData' => $previewData,
                'templateConfig' => $this->variables ?? [],
                'document_type' => $this->document_type
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Blade compile error in Template preview: ' . $e->getMessage());
        }

        return $html;
    }
}
