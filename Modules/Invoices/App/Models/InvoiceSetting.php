<?php

namespace Modules\Invoices\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Branch;

class InvoiceSetting extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory;



    /**
     * الحقول القابلة للتعبئة (Mass Assignable)
     */
    protected $fillable = [
        'sales_prefix',
        'sales_next_number',
        'sales_auto_post',
        'purchase_auto_post',
        'sales_terms',
        'purchase_prefix',
        'purchase_next_number',
        'purchase_terms',
        'sales_return_prefix',
        'purchase_return_prefix',
        'sales_return_next_number',
        'purchase_return_next_number',
        'sales_debit_prefix',
        'sales_debit_next_number',
        'purchase_order_prefix',
        'purchase_order_next_number',
        'quotation_prefix',
        'quotation_validity_days',
        'quotation_terms',
        'quotation_next_number',
        'enable_shipping',
        'default_shipping_vat_rate',
        'default_vat_rate',
        'prices_include_vat',
        'zakat_rate',
        'zakat_calculation_method',
        'show_logo_in_print',
        'show_product_image',
        'show_discount_column',
        'show_unit_price_after_vat',
        'show_customer_balance',
        'allow_negative_stock',
        'require_cost_center',
    ];

    /**
     * تحويل الأنواع (Casting) لضمان التعامل مع البيانات بشكل صحيح
     */
    protected $casts = [
        'sales_auto_post' => 'boolean',
        'purchase_auto_post' => 'boolean',
        'prices_include_vat' => 'boolean',
        'show_logo_in_print' => 'boolean',
        'show_product_image' => 'boolean',
        'show_discount_column' => 'boolean',
        'show_unit_price_after_vat' => 'boolean',
        'show_customer_balance' => 'boolean',
        'enable_shipping' => 'boolean',
        'default_shipping_vat_rate' => 'decimal:2',
        'default_vat_rate' => 'decimal:2',
        'zakat_rate' => 'decimal:2',
        'quotation_validity_days' => 'integer',
        'allow_negative_stock' => 'boolean',
        'require_cost_center' => 'boolean',
    ];

    /**
     * العلاقة مع الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Scope لجلب الإعدادات الافتراضية
     */
    public function scopeDefault($query)
    {
        return $query->where('id', 1);
    }
}
