<?php

namespace Modules\HR\App\Models;

use App\Helpers\ImageUploaderTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrPayroll;
use Illuminate\Support\Facades\File;
class HrMonthlyPayment extends Model
{
    use SoftDeletes, ImageUploaderTrait;

    public $table = 'hr_monthly_payments';

    // Status
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    // Type
    const TYPE_PENDING = 1;
    const TYPE_REPAID = 2;
    const TYPE_REJECTED = 3;

    public $fillable = ['hr_advance_id', 'employee_id', 'approver_id', 'payroll_id', 'due_at', 'amount', 'status', 'type', 'attachment'];

    protected $casts = [
        'hr_advance_id' => 'integer',
        'employee_id' => 'integer',
        'approver_id' => 'integer',
        'payroll_id' => 'integer',
        'due_at' => 'date',
        'amount' => 'decimal:2',
        'status' => 'integer',
        'type' => 'integer',
    ];

    public static array $rules = [
        'hr_advance_id' => 'required|exists:hr_advances,id',
        'employee_id' => 'required|exists:hr_employees,id',
        'due_at' => 'required|date',
        'amount' => 'required|numeric',
        'status' => 'nullable|in:1,2,3',
        'type' => 'nullable|in:1,2,3',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected'),
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_PENDING => __('hr::models/hr_advances.pending'),
            self::TYPE_REPAID => __('hr::models/hr_advances.paid'),
            self::TYPE_REJECTED => __('lang.rejected'),
        ];
    }

    public function getTypesTextAttribute(): string
    {
        return self::types()[$this->type] ?? '';
    }

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function getTypesBadgeAttribute(): string
    {
        $badges = [
            self::TYPE_PENDING => 'badge badge-warning',
            self::TYPE_REPAID => 'badge badge-success',
            self::TYPE_REJECTED => 'badge badge-danger',
        ];
        return $badges[$this->type] ?? 'badge badge-secondary';
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_REJECTED => 'badge badge-danger',
            self::STATUS_APPROVED => 'badge badge-success',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    public function setAttachmentAttribute($file)
    {
        try {
            // إذا كان الملف نص (يعني اسم الملف القديم) نحتفظ به
            if (is_string($file)) {
                $this->attributes['attachment'] = $file;
                return;
            }

            // إذا كان ملف جديد
            if ($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                // حذف الملف القديم إذا كان موجوداً
                if (isset($this->attributes['attachment']) && $this->attributes['attachment']) {
                    $this->deleteAttachmentFile($this->attributes['attachment']);
                }

                // إنشاء اسم الملف الجديد
                $fileName = $this->createFileName($file);

                // حفظ الملف
                $this->saveAttachmentFile($file, $fileName);

                // حفظ اسم الملف في قاعدة البيانات
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['attachment'] = null;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($advance) {
            if ($advance->attachment) {
                $advance->deleteAttachmentFile($advance->attachment);
            }
        });
    }

    /**
     * الحصول على مسار الملف الكامل
     */
    public function getAttachmentPathAttribute()
    {
        if ($this->attachment && File::exists(public_path('uploads/images/Advances/' . $this->attachment))) {
            return 'uploads/images/Advances/' . $this->attachment;
        }
        return null;
    }

    /**
     * الحصول على رابط الملف
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_path ? asset($this->attachment_path) : null;
    }
    // Relations
    public function advance(): BelongsTo
    {
        return $this->belongsTo(HrAdvance::class, 'hr_advance_id');
    }

    public function scopeOutPayroll($query)
    {
        return $query->whereNull('payroll_id');
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id');
    }
}
