<?php

namespace App\Models\invApp;

use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\StoreApp\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\invApp\InvCustomer;
use App\Helpers\ImageUploaderTrait;
use Illuminate\Support\Facades\File;
use Modules\Invoices\App\Models\SalesInvoiceZatca;

class SalesInvoice extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes, ImageUploaderTrait;

    // Invoice Types
    // أنواع الفواتير
    const TYPE_INVOICE = 1;
    const TYPE_RETURN = 2;
    const TYPE_DEBIT_NOTE = 3;
    const TYPE_POS = 4;

    const TYPE_RETURN_POS = 5;

    // Statuses
    // حالات الفاتورة
    // Statuses
    // حالات الفاتورة
    const STATUS_DRAFT = 1;
    const STATUS_SUBMITTED = 2;
    const STATUS_REPORTED = 3;
    const STATUS_CLEARED = 4;
    const STATUS_REJECTED = 5;
    const STATUS_RETURNED = 6;
    const STATUS_PARTIALLY_RETURNED = 7;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type_inv',
        'invoice_number',
        'customer_invoice_number',
        'issue_date',
        'invoice_type_code',
        'invoice_subtype_code',
        'customer_id',
        'branch_id',
        'store_id',
        'cost_center_id',
        'journal_entry_id',
        'fiscal_year_id',
        'user_id',
        'total_exclusive_vat',
        'total_discount',
        'type_discount',
        'number_discount',
        'total_vat',
        'total_inclusive_vat',
        'parent_id',
        'return_reason',
        'qr_code',
        'status',
        'technical_errors',
        'zatca_errors',
        'created_by',
        'file',
        'notes',
        'shipping_cost',
        'shipping_tax_id',
        'shipping_vat_rate',
        'shipping_vat_amount',
        'pos_session_id'
    ];
    protected $guarded = ['id'];

    protected $appends = ['type_text', 'status_text', 'status_badge'];

    protected $casts = [
        'issue_date' => 'datetime',
        'total_exclusive_vat' => 'decimal:4',
        'total_discount' => 'decimal:4',
        'number_discount' => 'decimal:4',
        'total_vat' => 'decimal:4',
        'total_inclusive_vat' => 'decimal:4',
        'shipping_cost' => 'decimal:4',
        'shipping_vat_rate' => 'decimal:2',
        'shipping_vat_amount' => 'decimal:4',
        'type_discount' => 'integer',
    ];

    protected static function booted()
    {
        // uuid generation moved to Zatca details if needed
    }

    public function setFileAttribute($file)
    {
        try {
            if ($file) {
                // حذف الملف القديم إذا كان موجوداً
                if ($this->file) {
                    $this->deleteFile($this->file, 'sales_invoices');
                }

                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'sales_invoices');
                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['file'] = null;
        }
    }

    public function getFileOriginalPathAttribute()
    {
        if ($this->file && File::exists(public_path('uploads/images/sales_invoices/' . $this->file))) {
            return 'uploads/images/sales_invoices/' . $this->file;
        }
        return null;
    }

    public function getFileUrlAttribute()
    {
        return $this->file_original_path ? asset($this->file_original_path) : null;
    }

    /**
     * Localized status names
     */
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => __('invoices::models/sales_invoices.status.1'),
            self::STATUS_SUBMITTED => __('invoices::models/sales_invoices.status.2'),
            self::STATUS_REPORTED => __('invoices::models/sales_invoices.status.3'),
            self::STATUS_CLEARED => __('invoices::models/sales_invoices.status.4'),
            self::STATUS_REJECTED => __('invoices::models/sales_invoices.status.5'),
            self::STATUS_RETURNED => __('invoices::models/sales_invoices.status.6'),
            self::STATUS_PARTIALLY_RETURNED => __('invoices::models/sales_invoices.status.7'),
        ];
    }

    public  static  function  statusesinv()
    {

         return [
            self::STATUS_DRAFT => __('invoices::models/sales_invoices.status.1'),
            self::STATUS_SUBMITTED => __('invoices::models/sales_invoices.status.2'),
        
        ];

    }

    public static function statusesreturn()
    {
        return [
            self::STATUS_DRAFT => __('invoices::models/sales_invoices.status.1'),
            self::STATUS_RETURNED => __('invoices::models/sales_invoices.status.6'),
        ];
    }

    /**
     * Localized type names
     */
    public static function types()
    {
        return [
            self::TYPE_INVOICE => __('invoices::models/sales_invoices.invoice_types.1'),
            self::TYPE_RETURN => __('invoices::models/sales_invoices.invoice_types.2'),
            self::TYPE_DEBIT_NOTE => __('invoices::models/sales_debit_notes.singular'),
            self::TYPE_POS => __('pos::messages.pos_invoice'),
            self::TYPE_RETURN_POS => __('pos::messages.pos_return_invoice'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_DRAFT => 'badge badge-secondary',
            self::STATUS_SUBMITTED => 'badge badge-info',
            self::STATUS_REPORTED => 'badge badge-primary',
            self::STATUS_CLEARED => 'badge badge-success',
            self::STATUS_REJECTED => 'badge badge-danger',
            self::STATUS_RETURNED => 'badge badge-danger',
            self::STATUS_PARTIALLY_RETURNED => 'badge badge-dark',
        ];

        return $badges[$this->status] ?? 'badge badge-light';
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type_inv] ?? __('lang.invoice');
    }

    public function getTypeBadgeAttribute()
    {
        return in_array($this->type_inv, [self::TYPE_RETURN, self::TYPE_RETURN_POS]) ? 'badge badge-danger' : 'badge badge-primary';
    }

    public function getZatcaTypeTextAttribute()
    {
        if ($this->status == self::STATUS_DRAFT) {
            return '---';
        }
        
        // إذا كان العميل يملك رقماً ضريبياً فهي فاتورة ضريبية (Standard)
        if ($this->customer && !empty($this->customer->vat_number)) {
            return 'Standard';
        }
        
        return 'Standard';
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeIsInvoice($query)
    {
        return $query->where('type_inv', self::TYPE_INVOICE);
    }

    public function scopeIsReturn($query)
    {
        return $query->whereIn('type_inv', [self::TYPE_RETURN, self::TYPE_RETURN_POS]);
    }

    public function scopeIsDebitNote($query)
    {
        return $query->where('type_inv', self::TYPE_DEBIT_NOTE);
    }

    public function scopeFilterStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /**
     * حساب المبلغ المتبقي غير المدفوع
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_inclusive_vat - $this->payments()->sum('amount');
    }

    public function getStatusLabelAttribute(): string
    {
        return __('invoices::models.sales_invoices.status.' . $this->status);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(InvCustomer::class, 'customer_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(\App\Models\AccuSoft\CostCenters::class, 'cost_center_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function parent()
    {
        return $this->belongsTo(SalesInvoice::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SalesInvoice::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(SalesInvoicePayment::class, 'sales_invoice_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function zatcaDetails()
    {
        return $this->hasOne(SalesInvoiceZatca::class, 'sales_invoice_id');
    }

    public function posSession()
    {
        return $this->belongsTo(\Modules\Pos\App\Models\PosSession::class, 'pos_session_id');
    }

    /**
     * Check if invoice is already reported to ZATCA
     */
    public function isReported(): bool
    {
        return in_array($this->status, [self::STATUS_REPORTED, self::STATUS_CLEARED]);
    }

    protected static function newFactory()
    {
        // return \Modules\Invoices\Database\Factories\SalesInvoiceFactory::new();
    }
}
