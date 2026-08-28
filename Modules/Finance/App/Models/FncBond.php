<?php

namespace Modules\Finance\App\Models;

use App\Models\Branch;
use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageUploaderTrait;
class FncBond extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes , ImageUploaderTrait;

    // أنواع السندات
    const TYPE_PAYMENT = 1; // صرف
    const TYPE_RECEIPT = 2; // قبض

    // حالات السند
    const STATUS_DRAFT = 1;
    const STATUS_APPROVED = 2;

    protected $fillable = [
        'voucher_number',
        'date',
        'bond_type',
        'amount',
        'reference_number',
        'fund_account_id',
        'contact_account_id',
        'cost_center_id',
        'fiscal_year_id',
        'branch_id',
        'description',
        'status',
        'attachment',
        'journal_entry_id',
        'is_locked',
        'locked_at',
        'locked_by',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'bond_type' => 'integer',
        'status' => 'integer',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];


    public function setAttachmentAttribute($file)
    {
        try {
            if ($file) {
                // حذف الملف القديم إذا كان موجوداً
                if ($this->attachment) {
                    $this->deleteFile($this->attachment, 'bonds');
                }

                // إنشاء اسم الملف
                $fileName = $this->createFileName($file);

                // حفظ الملف في مجلد bonds
                $this->saveFileType($file, $fileName, 'bonds');

                // حفظ اسم الملف في قاعدة البيانات
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['attachment'] = null;
        }
    }

    /**
     * الحصول على مسار الملف الكامل
     */
    public function getAttachmentOriginalPathAttribute()
    {
        if ($this->attachment && File::exists('uploads/images/bonds/' . $this->attachment)) {
            return 'uploads/images/bonds/' . $this->attachment;
        }
        return null;
    }

    /**
     * الحصول على رابط الملف
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_original_path ? asset($this->attachment_original_path) : null;
    }

    /**
     * الحصول على معلومات الملف
     */
    public function getFileInfoAttribute()
    {
        $path = 'uploads/images/bonds/' . $this->attachment;

        if (!$this->attachment || !File::exists($path)) {
            return null;
        }

        return [
            'name' => $this->attachment,
            'path' => $path,
            'size' => File::size($path),
            'extension' => File::extension($path),
            'mime' => File::mimeType($path),
            'url' => asset($path),
        ];
    }

    /**
     *
     * الحساب المالي (خزينة أو بنك)
     */
    public function fundAccount(): BelongsTo
    {
        return $this->belongsTo(TreeAccounts::class, 'fund_account_id');
    }

    /**
     * الحساب المقابل (مورد، عميل، أو مصروف)
     */
    public function contactAccount(): BelongsTo
    {
        return $this->belongsTo(TreeAccounts::class, 'contact_account_id');
    }

    /**
     * مركز التكلفة
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenters::class, 'cost_center_id');
    }

    /**
     * السنة المالية
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    /**
     * الفرع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * القيد المحاسبي المرتبط
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * المستخدم المنشئ
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المستخدم الذي أقفل السند
     */
    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * النطاقات (Scopes) لتسهيل الاستعلام
     */
    public function scopePayments($query)
    {
        return $query->where('bond_type', self::TYPE_PAYMENT);
    }

    public function scopeReceipts($query)
    {
        return $query->where('bond_type', self::TYPE_RECEIPT);
    }

    /**
     * الحصول على مسميات الأنواع والحالات
     */
    public static function types()
    {
        return [
            self::TYPE_PAYMENT => __('finance::models/fnc_bond.types.payment'),
            self::TYPE_RECEIPT => __('finance::models/fnc_bond.types.receipt'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => __('finance::models/fnc_bond.statuses.draft'),
            self::STATUS_APPROVED => __('finance::models/fnc_bond.statuses.approved'),
        ];
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->bond_type] ?? __('lang.unknown');
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }
}
