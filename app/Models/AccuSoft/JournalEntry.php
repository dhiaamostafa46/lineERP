<?php

namespace App\Models\AccuSoft;

use App\Helpers\ImageUploaderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use App\Models\User;

class JournalEntry extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes, ImageUploaderTrait;

    protected $table = 'journal_entries';
    protected $fillable = [
        'entry_number',
        'entry_date',
        'branch_id',
        'description',
        'fiscal_year_id',
        'entry_type',
        'source',
        'attachment',
        'status',
        'total_debit',
        'total_credit',
        'created_by',
        'posted_by',
        'posted_at',
        'is_locked',
        'locked_at',
        'locked_by',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'posted_at' => 'datetime',
        'locked_at' => 'datetime',
        'is_locked' => 'boolean',
        'entry_type' => 'integer',
        'status' => 'integer',
        'source' => 'string',
    ];

    public static function generateEntryNumber(): string
    {
       $setting = \Illuminate\Support\Facades\DB::table('accounting_settings')->lockForUpdate()->first(); // عدلنا self

        $prefix = $setting->journal_prefix;

        $nextId = $setting->journal_next_number;

        // withTrashed() لضمان عدم تكرار الرقم بعد حذف قيد (Soft Delete)
        // withoutGlobalScopes() is CRITICAL here to bypass the BelongsToBranch scope
        $lastEntry = self::withoutGlobalScopes()
            ->withTrashed()
            ->where('entry_number', 'LIKE', $prefix . '-%')
            ->orderByRaw('LENGTH(entry_number) DESC')
            ->orderBy('entry_number', 'DESC')
            ->first();

        if ($lastEntry && str_starts_with($lastEntry->entry_number, $prefix . '-')) {
            $lastNumber = (int) substr($lastEntry->entry_number, strlen($prefix . '-'));
            $nextId = max($nextId, $lastNumber + 1);
        }

        // حلقة تأكيدية لضمان عدم وجود القيد إطلاقاً في قاعدة البيانات (لتفادي أي مشاكل تكرار مفاجئة)
        do {
            $generated = $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            // lockForUpdate is crucial here to bypass the REPEATABLE READ snapshot and see recently committed rows from other transactions
            // withoutGlobalScopes is also needed here
            $exists = self::withoutGlobalScopes()
                ->withTrashed()
                ->where('entry_number', $generated)
                ->lockForUpdate()
                ->exists();
            if ($exists) {
                $nextId++;
            }
        } while ($exists);

        // Update the sequence in settings to avoid large loops in the future
        \Illuminate\Support\Facades\DB::table('accounting_settings')->update(['journal_next_number' => $nextId + 1]);

        return $generated;
    }

    public function setattachmentAttribute($file)
    {
        try {
            if ($file) {
                // حذف الصورة القديمة
                if ($this->attachment) {
                    $this->deleteFile($this->attachment, 'journalentry');
                }

                // إنشاء اسم جديد للصورة
                $fileName = $this->createFileName($file);

                // حفظ الصورة في مجلد categories
                $this->saveFileType($file, $fileName, 'journalentry');

                // حفظ الاسم في قاعدة البيانات
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['attachment'] = null;
        }
    }

    public function getattachmentPathAttribute()
    {
        return $this->attachment ? asset('uploads/images/journalentry/' . $this->attachment) : asset('uploads/images/journalentry/no_img.jpg');
    }

    public function getattachmentThumbPathAttribute()
    {
        return $this->attachment ? asset('uploads/images/journalentry/' . $this->attachment) : asset('uploads/images/journalentry/no_img.jpg');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->source)) {
                $model->source = self::determineSource($model->reference_type, $model->description, $model->entry_type);
            }
        });
    }

    // Constants لأنواع القيود
    const ENTRY_TYPE_MANUAL = 1;
    const TYPE_GENERAL = 1;
    const ENTRY_TYPE_OPENING = 2;
    const ENTRY_TYPE_CLOSING = 3;
    const ENTRY_TYPE_DEPRECIATION = 4;
    const ENTRY_TYPE_ADJUSTMENT = 5;
    const ENTRY_TYPE_AUTO = 6;

    // Constants لمصادر وتصنيفات القيود (Sources)
    const SOURCE_MANUAL = 'manual';
    const SOURCE_SALES = 'sales';
    const SOURCE_PURCHASES = 'purchases';
    const SOURCE_STORE = 'store';
    const SOURCE_VEHICLES = 'vehicles';
    const SOURCE_DRIVERS = 'drivers';
    const SOURCE_HR = 'hr';
    const SOURCE_FINANCE = 'finance';
    const SOURCE_ASSETS = 'assets';
    const SOURCE_POS = 'pos';
    const SOURCE_CLOSING = 'closing';

    // Constants لحالات القيد
    const STATUS_DRAFT = 1;
    const STATUS_POSTED = 2;
    const STATUS_REVERSED = 3;
    const STATUS_PENDING = 4;

    // Relations
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReversed($query)
    {
        return $query->where('status', self::STATUS_REVERSED);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function scopeSource($query, $source)
    {
        if ($source) {
            return $query->where('source', $source);
        }
        return $query;
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPosted(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isReversed(): bool
    {
        return $this->status === self::STATUS_REVERSED;
    }

    public function isBalanced(): bool
    {
        return bccomp($this->total_debit, $this->total_credit, 2) === 0;
    }

    public function calculateTotals(): void
    {
        $this->total_debit = $this->details()->sum('debit');
        $this->total_credit = $this->details()->sum('credit');
    }

    // Source determination logic based on reference, description, or entry type
    public static function determineSource(?string $referenceType = null, ?string $description = null, ?int $entryType = null): string
    {
        // 1. Check entry type first
        if ($entryType !== null) {
            if ($entryType === self::ENTRY_TYPE_CLOSING || $entryType === self::ENTRY_TYPE_OPENING) {
                return self::SOURCE_CLOSING;
            }
            if ($entryType === self::ENTRY_TYPE_DEPRECIATION) {
                return self::SOURCE_ASSETS;
            }
        }

        // 2. Check polymorphic reference type
        if (!empty($referenceType)) {
            // Purchases
            if (
                str_contains($referenceType, 'PurchaseInvoice') ||
                str_contains($referenceType, 'PurchaseReturn') ||
                str_contains($referenceType, 'PurchaseOrder') ||
                str_contains($referenceType, 'Purchase')
            ) {
                return self::SOURCE_PURCHASES;
            }

            // Sales
            if (
                str_contains($referenceType, 'SalesInvoice') ||
                str_contains($referenceType, 'SalesDebit') ||
                str_contains($referenceType, 'SalesReturn') ||
                str_contains($referenceType, 'SalesOrder') ||
                str_contains($referenceType, 'Sales')
            ) {
                return self::SOURCE_SALES;
            }

            // POS
            if (
                str_contains($referenceType, 'PosSession') ||
                str_contains($referenceType, 'PosShift') ||
                str_contains($referenceType, 'PosOrder') ||
                str_contains($referenceType, 'PosInvoice') ||
                str_contains($referenceType, 'Pos')
            ) {
                return self::SOURCE_POS;
            }

            // Store / Inventory
            if (
                str_contains($referenceType, 'Store') ||
                str_contains($referenceType, 'StReceiving') ||
                str_contains($referenceType, 'StIssuing') ||
                str_contains($referenceType, 'StSettlement') ||
                str_contains($referenceType, 'StTransfer') ||
                str_contains($referenceType, 'StockTransfer') ||
                str_contains($referenceType, 'StoreTransfer') ||
                str_contains($referenceType, 'StockMovement') ||
                str_contains($referenceType, 'Stock') ||
                str_contains($referenceType, 'Inventory')
            ) {
                return self::SOURCE_STORE;
            }

            // Vehicles
            if (
                str_contains($referenceType, 'Vehicle') ||
                str_contains($referenceType, 'VehicleExpense') ||
                str_contains($referenceType, 'VehicleMaintenance') ||
                str_contains($referenceType, 'MaintenanceRequest')
            ) {
                return self::SOURCE_VEHICLES;
            }

            // Drivers
            if (
                str_contains($referenceType, 'Driver') ||
                str_contains($referenceType, 'DrLedger') ||
                str_contains($referenceType, 'DrSettlement') ||
                str_contains($referenceType, 'DriverHandover') ||
                str_contains($referenceType, 'DrExpense')
            ) {
                return self::SOURCE_DRIVERS;
            }

            // HR
            if (
                str_contains($referenceType, 'HrPayroll') ||
                str_contains($referenceType, 'HrSalary') ||
                str_contains($referenceType, 'Payroll') ||
                str_contains($referenceType, 'EndService') ||
                str_contains($referenceType, 'Employee') ||
                str_contains($referenceType, 'Hr')
            ) {
                return self::SOURCE_HR;
            }

            // Finance / Bonds
            if (
                str_contains($referenceType, 'FncBond') ||
                str_contains($referenceType, 'Finance') ||
                str_contains($referenceType, 'Bond') ||
                str_contains($referenceType, 'Treasury')
            ) {
                return self::SOURCE_FINANCE;
            }

            // Fixed Assets
            if (
                str_contains($referenceType, 'Asset') ||
                str_contains($referenceType, 'AssetTransaction') ||
                str_contains($referenceType, 'AssetDisposal') ||
                str_contains($referenceType, 'DepreciationRun')
            ) {
                return self::SOURCE_ASSETS;
            }

            // Closing / Opening
            if (
                str_contains($referenceType, 'FiscalYear') ||
                str_contains($referenceType, 'AccountingClosure') ||
                str_contains($referenceType, 'Closure')
            ) {
                return self::SOURCE_CLOSING;
            }
        }

        // 3. Check Description keywords (Arabic & English)
        if (!empty($description)) {
            // Closing / Opening
            if (
                str_contains($description, 'إقفال') ||
                str_contains($description, 'أرصدة افتتاحية') ||
                str_contains($description, 'قيد افتتاحي') ||
                str_contains($description, 'قيد إقفال') ||
                str_contains($description, 'نقل النتيجة') ||
                str_contains($description, 'Income Summary') ||
                str_contains($description, 'أرباح محتجزة') ||
                str_contains($description, 'Closing')
            ) {
                return self::SOURCE_CLOSING;
            }

            // Sales
            if (
                str_contains($description, 'مبيعات') ||
                str_contains($description, 'فاتورة مبيعات') ||
                str_contains($description, 'مرتجع مبيعات') ||
                str_contains($description, 'إشعار مدين') ||
                str_contains($description, 'فاتورة #') ||
                str_contains($description, 'Sales')
            ) {
                return self::SOURCE_SALES;
            }

            // Purchases
            if (
                str_contains($description, 'مشتريات') ||
                str_contains($description, 'فاتورة شراء') ||
                str_contains($description, 'فاتورة مشتريات') ||
                str_contains($description, 'مرتجع مشتريات') ||
                str_contains($description, 'إشعار دائن') ||
                str_contains($description, 'Purchase')
            ) {
                return self::SOURCE_PURCHASES;
            }

            // POS
            if (
                str_contains($description, 'نقطة بيع') ||
                str_contains($description, 'نقاط بيع') ||
                str_contains($description, 'كاشير') ||
                str_contains($description, 'وردية') ||
                str_contains($description, 'جلسة كاشير') ||
                str_contains($description, 'POS')
            ) {
                return self::SOURCE_POS;
            }

            // Store
            if (
                str_contains($description, 'مخزون') ||
                str_contains($description, 'مستودع') ||
                str_contains($description, 'تسوية مخزنية') ||
                str_contains($description, 'تسوية جردية') ||
                str_contains($description, 'إخراج مخزني') ||
                str_contains($description, 'إدخال مخزني') ||
                str_contains($description, 'تحويل مخزني') ||
                str_contains($description, 'جرد') ||
                str_contains($description, 'Inventory') ||
                str_contains($description, 'Stock')
            ) {
                return self::SOURCE_STORE;
            }

            // Vehicles
            if (
                str_contains($description, 'مركبة') ||
                str_contains($description, 'مركبات') ||
                str_contains($description, 'صيانة مركبة') ||
                str_contains($description, 'وقود مركبة') ||
                str_contains($description, 'تأمين مركبة') ||
                str_contains($description, 'ترخيص مركبة') ||
                str_contains($description, 'Vehicle')
            ) {
                return self::SOURCE_VEHICLES;
            }

            // Drivers
            if (
                str_contains($description, 'سائق') ||
                str_contains($description, 'سائقين') ||
                str_contains($description, 'عهدة سائق') ||
                str_contains($description, 'تسوية سائق') ||
                str_contains($description, 'سلفة سائق') ||
                str_contains($description, 'Driver')
            ) {
                return self::SOURCE_DRIVERS;
            }

            // HR
            if (
                str_contains($description, 'رواتب') ||
                str_contains($description, 'مسير رواتب') ||
                str_contains($description, 'كشف رواتب') ||
                str_contains($description, 'نهاية خدمة') ||
                str_contains($description, 'سلفة موظف') ||
                str_contains($description, 'موظف') ||
                str_contains($description, 'موظفين') ||
                str_contains($description, 'Payroll') ||
                str_contains($description, 'Salary')
            ) {
                return self::SOURCE_HR;
            }

            // Finance
            if (
                str_contains($description, 'سند قبض') ||
                str_contains($description, 'سند صرف') ||
                str_contains($description, 'سند') ||
                str_contains($description, 'تحويل بنكي') ||
                str_contains($description, 'خزينة') ||
                str_contains($description, 'صندوق') ||
                str_contains($description, 'Bond')
            ) {
                return self::SOURCE_FINANCE;
            }

            // Assets
            if (
                str_contains($description, 'أصل ثابت') ||
                str_contains($description, 'أصول ثابتة') ||
                str_contains($description, 'شراء أصل') ||
                str_contains($description, 'إهلاك') ||
                str_contains($description, 'استبعاد أصل') ||
                str_contains($description, 'Asset') ||
                str_contains($description, 'Depreciation')
            ) {
                return self::SOURCE_ASSETS;
            }
        }

        return self::SOURCE_MANUAL;
    }

    // Accessor لمصادر القيود (Sources)
    public static function sources(): array
    {
        return [
            self::SOURCE_MANUAL => __('accusoft::models/as_journal_entries.sources.manual') ?: 'قيد عام / يدوي',
            self::SOURCE_SALES => __('accusoft::models/as_journal_entries.sources.sales') ?: 'المبيعات',
            self::SOURCE_PURCHASES => __('accusoft::models/as_journal_entries.sources.purchases') ?: 'المشتريات',
            self::SOURCE_STORE => __('accusoft::models/as_journal_entries.sources.store') ?: 'المخزون',
            self::SOURCE_VEHICLES => __('accusoft::models/as_journal_entries.sources.vehicles') ?: 'المركبات',
            self::SOURCE_DRIVERS => __('accusoft::models/as_journal_entries.sources.drivers') ?: 'السائقين',
            self::SOURCE_HR => __('accusoft::models/as_journal_entries.sources.hr') ?: 'الموارد البشرية',
            self::SOURCE_FINANCE => __('accusoft::models/as_journal_entries.sources.finance') ?: 'السندات والمالية',
            self::SOURCE_ASSETS => __('accusoft::models/as_journal_entries.sources.assets') ?: 'الأصول الثابتة',
            self::SOURCE_POS => __('accusoft::models/as_journal_entries.sources.pos') ?: 'نقاط البيع',
            self::SOURCE_CLOSING => __('accusoft::models/as_journal_entries.sources.closing') ?: 'إقفال محاسبي',
        ];
    }

    // Accessor للحصول على نوع القيد كنص
    public static function types()
    {
        return [
            self::ENTRY_TYPE_MANUAL => __('accusoft::models/as_journal_entries.types.manual'),
            self::ENTRY_TYPE_OPENING => __('accusoft::models/as_journal_entries.types.opening'),
            self::ENTRY_TYPE_CLOSING => __('accusoft::models/as_journal_entries.types.closing'),
            self::ENTRY_TYPE_DEPRECIATION => __('accusoft::models/as_journal_entries.types.depreciation'),
            self::ENTRY_TYPE_ADJUSTMENT => __('accusoft::models/as_journal_entries.types.adjustment'),
            self::ENTRY_TYPE_AUTO => __('accusoft::models/as_journal_entries.types.auto'),
        ];
    }

    public static function typesList()
    {
        return [
            self::ENTRY_TYPE_MANUAL => __('accusoft::models/as_journal_entries.types.manual'),
            self::ENTRY_TYPE_OPENING => __('accusoft::models/as_journal_entries.types.opening'),
        ];
    }

    // Accessor للحصول على الحالة كنص
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => __('accusoft::models/as_journal_entries.statuses.draft'),
            self::STATUS_POSTED => __('accusoft::models/as_journal_entries.statuses.posted'),
            self::STATUS_REVERSED => __('accusoft::models/as_journal_entries.statuses.reversed'),
            self::STATUS_PENDING => __('accusoft::models/as_journal_entries.statuses.pending'),
        ];
    }

    public static function statuseslist()
    {
        return [
            self::STATUS_DRAFT => __('accusoft::models/as_journal_entries.statuses.draft'),
            self::STATUS_POSTED => __('accusoft::models/as_journal_entries.statuses.posted'),
        ];
    }

    public function gettypeTextAttribute()
    {
        return self::types()[$this->entry_type] ?? __('lang.unknown');
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    public function getSourceTextAttribute()
    {
        return self::sources()[$this->source] ?? self::sources()[self::SOURCE_MANUAL] ?? $this->source;
    }

    public function getSourceBadgeClassAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_SALES => 'badge-light-success',
            self::SOURCE_PURCHASES => 'badge-light-warning',
            self::SOURCE_STORE => 'badge-light-info',
            self::SOURCE_VEHICLES => 'badge-light-primary',
            self::SOURCE_DRIVERS => 'badge-light-danger',
            self::SOURCE_HR => 'badge-light-success',
            self::SOURCE_FINANCE => 'badge-light-primary',
            self::SOURCE_ASSETS => 'badge-light-info',
            self::SOURCE_POS => 'badge-light-secondary',
            self::SOURCE_CLOSING => 'badge-light-dark',
            default => 'badge-light-dark',
        };
    }
}
