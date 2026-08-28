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

class PurchaseOrder extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes, ImageUploaderTrait;

    // Invoice Types
    // أنواع الفواتير
    const TYPE_INVOICE = 1;

    // Statuses
    // حالات الفاتورة
    const STATUS_NEW = 'new';
    const STATUS_APPROVED = 'approved';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_COMPLETED = 'completed';

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
        'return_reason',
        'icv',
        'previous_invoice_hash',
        'qr_code',
        'status',
        'technical_errors',
        'created_by',
        'file',
        'notes'
    ];
    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'datetime',
        'total_exclusive_vat' => 'decimal:4',
        'total_discount' => 'decimal:4',
        'number_discount' => 'decimal:4',
        'total_vat' => 'decimal:4',
        'total_inclusive_vat' => 'decimal:4',
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
                    $this->deleteFile($this->file, 'purchase_orders');
                }

                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'purchase_orders');
                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['file'] = null;
        }
    }

    public function getFileOriginalPathAttribute()
    {
        if ($this->file && File::exists(public_path('uploads/images/purchase_orders/' . $this->file))) {
            return 'uploads/images/purchase_orders/' . $this->file;
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
            self::STATUS_NEW => __('invoices::models/purchase_orders.status.new'),
            self::STATUS_APPROVED => __('invoices::models/purchase_orders.status.approved'),
            self::STATUS_PARTIALLY_RECEIVED => __('invoices::models/purchase_orders.status.partially_received'),
            self::STATUS_COMPLETED => __('invoices::models/purchase_orders.status.completed'),
        ];
    }

     public static function statusesSelect()
    {
        return [
            self::STATUS_NEW => __('invoices::models/purchase_orders.status.new'),
            self::STATUS_APPROVED => __('invoices::models/purchase_orders.status.approved'),
            self::STATUS_PARTIALLY_RECEIVED => __('invoices::models/purchase_orders.status.partially_received'),
            self::STATUS_COMPLETED => __('invoices::models/purchase_orders.status.completed'),
        ];
    }
    //    public static function statuses()
    // {
    //     return [
    //         self::STATUS_DRAFT => __('invoices::models/purchase_invoices.status.draft'),
    //         self::STATUS_RECEIVED => __('invoices::models/purchase_invoices.status.received'),
    //         self::STATUS_PARTIALLY_PAID => __('invoices::models/purchase_invoices.status.partially_paid'),
    //         self::STATUS_PAID => __('invoices::models/purchase_invoices.status.paid'),
    //         self::STATUS_RETURNED => __('invoices::models/purchase_invoices.status.returned'),
    //         self::STATUS_PARTIALLY_RETURNED => __('invoices::models/purchase_invoices.status.partially_returned'),
    //         self::STATUS_REJECTED => __('invoices::models/purchase_invoices.status.rejected'),
    //     ];
    // }

    /**
     * Localized type names
     */
    public static function types()
    {
        return [
            self::TYPE_INVOICE => __('invoices::models/purchase_orders.invoice_types.1'),
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
            self::STATUS_NEW => 'badge badge-secondary',
            self::STATUS_APPROVED => 'badge badge-info',
            self::STATUS_PARTIALLY_RECEIVED => 'badge badge-warning',
            self::STATUS_COMPLETED => 'badge badge-success',
        ];

        return $badges[$this->status] ?? 'badge badge-light';
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type_inv] ?? __('lang.invoice');
    }

    // public function getTypeBadgeAttribute()
    // {
    //     return $this->type_inv == self::TYPE_RETURN ? 'badge badge-danger' : 'badge badge-primary';
    // }

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

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeIsInvoice($query)
    {
        return $query->where('type_inv', self::TYPE_INVOICE);
    }

    // public function scopeIsReturn($query)
    // {
    //     return $query->where('type_inv', self::TYPE_RETURN);
    // }

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
        return __('invoices::models.purchase_orders.status.' . $this->status);
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
        return $this->belongsTo(PurchaseOrder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PurchaseOrder::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function payments()
    {
        return $this->hasMany(PurchaseOrderPayment::class, 'purchase_order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function newFactory()
    {
        // return \Modules\Invoices\Database\Factories\PurchaseOrderFactory::new();
    }
}
