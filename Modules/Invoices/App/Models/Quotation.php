<?php

namespace Modules\Invoices\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Models\invApp\InvCustomer;
use App\Helpers\ImageUploaderTrait;
use Illuminate\Support\Facades\File;

class Quotation extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes, ImageUploaderTrait;

    const STATUS_NEW = 'new';
    const STATUS_SENT = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CONVERTED = 'converted';

    protected $fillable = [
        'uuid',
        'quotation_number',
        'issue_date',
        'expiry_date',
        'customer_id',
        'branch_id',
        'store_id',
        'fiscal_year_id',
        'cost_center_id',
        'user_id',
        'created_by',
        'total_exclusive_vat',
        'total_discount',
        'type_discount',
        'number_discount',
        'total_vat',
        'total_inclusive_vat',
        'shipping_cost',
        'shipping_tax_id',
        'shipping_vat_rate',
        'shipping_vat_amount',
        'status',
        'notes',
        'qr_code',
        'file',
        'payment_terms',
        'validity_period'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'total_exclusive_vat' => 'decimal:4',
        'total_discount' => 'decimal:4',
        'total_vat' => 'decimal:4',
        'total_inclusive_vat' => 'decimal:4',
        'shipping_cost' => 'decimal:4',
        'shipping_vat_rate' => 'decimal:2',
        'shipping_vat_amount' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::creating(function ($quotation) {
            $quotation->uuid = (string) Str::uuid();
        });
    }

    public function setFileAttribute($file)
    {
        try {
            if ($file) {
                if ($this->file) {
                    $this->deleteFile($this->file, 'quotations');
                }
                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'quotations');
                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['file'] = null;
        }
    }

    public function getFileOriginalPathAttribute()
    {
        return $this->file ? 'uploads/images/quotations/' . $this->file : null;
    }

    public function getFileUrlAttribute()
    {
        return $this->file_original_path ? asset($this->file_original_path) : null;
    }

    public static function statuses()
    {
        return [
            self::STATUS_NEW => __('invoices::models/quotations.status.new'),
            self::STATUS_SENT => __('invoices::models/quotations.status.sent'),
            self::STATUS_ACCEPTED => __('invoices::models/quotations.status.accepted'),
            self::STATUS_REJECTED => __('invoices::models/quotations.status.rejected'),
            self::STATUS_EXPIRED => __('invoices::models/quotations.status.expired'),
            self::STATUS_CONVERTED => __('invoices::models/quotations.status.converted'),
        ];
    }


    public static function statusesSelect()
    {
        return [
            self::STATUS_NEW => __('invoices::models/quotations.status.new'),
            // self::STATUS_SENT => __('invoices::models/quotations.status.sent'),
            // self::STATUS_ACCEPTED => __('invoices::models/quotations.status.accepted'),
             self::STATUS_REJECTED => __('invoices::models/quotations.status.rejected'),
            // self::STATUS_EXPIRED => __('invoices::models/quotations.status.expired'),
            self::STATUS_CONVERTED => __('invoices::models/quotations.status.converted'),
        ];
    }


    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_NEW => 'badge badge-secondary',
            self::STATUS_SENT => 'badge badge-info',
            self::STATUS_ACCEPTED => 'badge badge-success',
            self::STATUS_REJECTED => 'badge badge-danger',
            self::STATUS_EXPIRED => 'badge badge-warning',
            self::STATUS_CONVERTED => 'badge badge-dark',
        ];
        return $badges[$this->status] ?? 'badge badge-light';
    }

    public function customer()
    {
        return $this->belongsTo(InvCustomer::class, 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(\App\Models\AccuSoft\CostCenters::class, 'cost_center_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalExclusiveVatFormattedAttribute()
    {
        return number_format($this->total_exclusive_vat, 2);
    }

    public function getTotalVatFormattedAttribute()
    {
        return number_format($this->total_vat, 2);
    }

    public function getTotalInclusiveVatFormattedAttribute()
    {
        return number_format($this->total_inclusive_vat, 2);
    }
}
