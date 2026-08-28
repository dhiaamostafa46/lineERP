<?php

namespace Modules\Invoices\App\Models;

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
use App\Models\invApp\InvSupplier;
use App\Helpers\ImageUploaderTrait;
use Illuminate\Support\Facades\File;

class PurchaseInvoice extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes, ImageUploaderTrait;

    // Invoice Types
    // أنواع الفواتير
    const TYPE_INVOICE = 1;
    const TYPE_RETURN = 2;
    const TYPE_DEBIT_NOTE = 3;

    // Statuses
    // حالات الفاتورة
    const STATUS_DRAFT = 1;
    const STATUS_RECEIVED = 2;
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
        'uuid',
        'type_inv',
        'invoice_number',
        'supplier_invoice_number',
        'issue_date',
        'invoice_type_code',
        'invoice_subtype_code',
        'supplier_id',
        'branch_id',
        'store_id',
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
        'from_po_id',
        'return_reason',
        'icv',
        'previous_invoice_hash',
        'qr_code',
        'status',
        'technical_errors',
        'created_by',
        'file',
        'notes',
        'shipping_cost',
        'shipping_tax_id',
        'shipping_vat_rate',
        'shipping_vat_amount'
    ];
    protected $guarded = ['id'];

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
        static::creating(function ($invoice) {
            $invoice->uuid = (string) Str::uuid();
        });
    }

    public function setFileAttribute($file)
    {
        try {
            if ($file) {
                // حذف الملف القديم إذا كان موجوداً
                if ($this->file) {
                    $this->deleteFile($this->file, 'purchase_invoices');
                }

                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'purchase_invoices');
                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['file'] = null;
        }
    }

    public function getFileOriginalPathAttribute()
    {
        if ($this->file && File::exists(public_path('uploads/images/purchase_invoices/' . $this->file))) {
            return 'uploads/images/purchase_invoices/' . $this->file;
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
            self::STATUS_DRAFT => __('invoices::models/purchase_invoices.status.1'),
            self::STATUS_RECEIVED => __('invoices::models/purchase_invoices.status.2'),
            self::STATUS_SUBMITTED => __('invoices::models/purchase_invoices.status.2'),
            self::STATUS_REPORTED => __('invoices::models/purchase_invoices.status.3'),
            self::STATUS_CLEARED => __('invoices::models/purchase_invoices.status.4'),
            self::STATUS_REJECTED => __('invoices::models/purchase_invoices.status.5'),
            self::STATUS_RETURNED => __('invoices::models/purchase_invoices.status.6'),
            self::STATUS_PARTIALLY_RETURNED => __('invoices::models/purchase_invoices.status.7'),
        ];
    }

      public static function statusesSelect()
    {
        return [
            self::STATUS_DRAFT => __('invoices::models/purchase_invoices.status.1'),
            self::STATUS_RECEIVED => __('invoices::models/purchase_invoices.status.2'),
            self::STATUS_SUBMITTED => __('invoices::models/purchase_invoices.status.2'),
            // self::STATUS_REPORTED => __('invoices::models/purchase_invoices.status.3'),
            self::STATUS_CLEARED => __('invoices::models/purchase_invoices.status.4'),
            self::STATUS_REJECTED => __('invoices::models/purchase_invoices.status.5'),
            self::STATUS_RETURNED => __('invoices::models/purchase_invoices.status.6'),
            self::STATUS_PARTIALLY_RETURNED => __('invoices::models/purchase_invoices.status.7'),
        ];
    }

    /**
     * Localized type names
     */
    public static function types()
    {
        return [
            self::TYPE_INVOICE => __('invoices::models/purchase_invoices.invoice_types.1'),
            self::TYPE_RETURN => __('invoices::models/purchase_invoices.invoice_types.2'),
            self::TYPE_DEBIT_NOTE => __('invoices::models/purchase_invoices.invoice_types.3'),
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
            self::STATUS_RECEIVED => 'badge badge-info',
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
        return $this->type_inv == self::TYPE_RETURN ? 'badge badge-danger' : 'badge badge-primary';
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
        return $query->where('type_inv', self::TYPE_RETURN);
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
        return __('invoices::models.purchase_invoices.status.' . $this->status);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(InvSupplier::class, 'supplier_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
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
        return $this->belongsTo(PurchaseInvoice::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PurchaseInvoice::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class, 'purchase_invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(PurchaseInvoicePayment::class, 'purchase_invoice_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Invoices\Database\Factories\PurchaseInvoiceFactory::new();
    }
}
