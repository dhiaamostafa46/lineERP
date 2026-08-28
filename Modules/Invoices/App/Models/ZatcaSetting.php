<?php

namespace Modules\Invoices\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Branch;
use Modules\Invoices\Database\Factories\ZatcaSettingFactory;

class ZatcaSetting extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    // ZATCA Technical Statuses
    const ZATCA_STATUS_NOT_LINKED = 'not_linked';
    const ZATCA_STATUS_LINKED     = 'linked';
    const ZATCA_STATUS_PRODUCTION = 'production_issued';

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    protected $fillable = [
        'branch_id',
        'uuid',
        'common_name',
        'environment',
        'private_key',
        'csr',
        'binary_security_token',
        'secret',
        'expiry_date',
        'user_id',
        'is_active',
        'business_category',
        'organization_name',
        'organization_unit_name',
        'building_number',
        'street_name',
        'district_name',
        'city_name',
        'postal_code',
        'country_code',
        'vat_number',
        'vat_name',
        'cv',
        'activity_classification',
        'registered_address',
        'otp',
        'otp_confirmation',
        'status',
        'serial_number',
        'prk',
        'prod_secret',
        'request_id',
        'csr_response_binary_token',
        'inv_type',
        'isVatGroup',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'business_category' => 'json',
    ];

    /**
     * الحقول الإضافية التي تظهر عند تحويل المودل إلى JSON
     */
    protected $appends = ['status_text', 'status_badge', 'zatca_status_text'];

    /**
     * الحصول على مسمى حالة الربط مع الزكاة بشكل مقروء
     */
    public function getZatcaStatusTextAttribute()
    {
        return match($this->status) {
            'linked' => __('invoices::models/invoices_setting.fields.zatca_status_linked'),
            'production_issued' => __('invoices::models/invoices_setting.fields.zatca_status_production'),
            default => __('invoices::models/invoices_setting.fields.zatca_status_not_linked'),
        };
    }

    /**
     * الحصول على مسمى الحالة (تفعيل/تعطيل)
     */
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->is_active ? self::STATUS_ACTIVE : self::STATUS_INACTIVE] ?? __('lang.inactive');
    }

    /**
     * الحصول على شارة الحالة (Badge)
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active ? 'badge badge-success' : 'badge badge-danger';
    }

    /**
     * التحقق مما إذا كانت إعدادات المرحلة الثانية مفعلة ومربوطة بنجاح
     */
    public function isPhase2Enabled(): bool
    {
        return (bool) ($this->is_active &&
            in_array($this->status, [self::ZATCA_STATUS_LINKED, self::ZATCA_STATUS_PRODUCTION]) &&
            !empty($this->binary_security_token) &&
            !empty($this->private_key) &&
            !empty($this->secret));
    }

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

    /**
     * Scope لجلب الإعدادات النشطة فقط
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Resolve the appropriate ZatcaSetting for a branch.
     * Respects the organization's tax registration type (unified vs branches).
     */
    public static function resolveForBranch($branchId)
    {
        $organization = \App\Models\Organization::first();
        $zatcaSetting = null;

        if (!$organization || $organization->tax_registration_type !== 'branches') {
            // 1. Unified Tax Registration (Default): Always use the main setting
            $zatcaSetting = self::whereNull('branch_id')->first();
        } else {
            // 2. Branch-specific Tax Registration:
            // a. Check invoice's branch
            $zatcaSetting = self::where('branch_id', $branchId)->first();
            
            // b. Check user's branch if different
            if (!$zatcaSetting && auth()->check() && auth()->user()->branch_id) {
                $zatcaSetting = self::where('branch_id', auth()->user()->branch_id)->first();
            }
        }

        // 3. Ultimate fallback
        if (!$zatcaSetting) {
            $zatcaSetting = self::whereNull('branch_id')->first() ?? self::first();
        }

        return $zatcaSetting ?? new self(['branch_id' => $branchId]);
    }
}

