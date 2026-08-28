<?php

namespace Modules\HR\App\Models;

use App\Helpers\ImageUploaderTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Database\Factories\HrJustificationFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\File;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrShift;

class HrJustification extends Model
{
    use SoftDeletes, ImageUploaderTrait;

    public $table = 'hr_justifications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['shift_id', 'employee_id', 'reason', 'type', 'status', 'approved_by', 'approved_at','to_time' ,'from_time', 'request_date', 'approver_id', 'attachment'];

    protected $casts = [
        'id' => 'integer',
        'shift_id' => 'integer',
        'employee_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
        'request_date' => 'date',
        'approver_id' => 'integer',
    ];

    public static array $rules = [
        'shift_id' => 'required',
        'employee_id' => 'required|exists:hr_employees,id',
        'reason' => 'required|string',
        'type' => 'required|in:1,2,3,4', // 1 = late, 2 = early_leave, 3 = absence
        'request_date' => 'required|date',
        'approved_by' => 'nullable|exists:users,id',
        'approver_id' => 'nullable|exists:users,id',
    ];

    public function setAttachmentAttribute($file)
    {
        try {
            if ($file) {
                // حذف الملف القديم إذا كان موجوداً
                if ($this->attachment) {
                    $this->deleteHolidayFile($this->attachment);
                }

                // إنشاء اسم الملف
                $fileName = $this->createFileName($file);

                // حفظ الملف في مجلد Justification
                $this->saveJustificationFile($file, $fileName);

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
        if ($this->attachment && File::exists('uploads/images/Justification/' . $this->attachment)) {
            return 'uploads/images/Justification/' . $this->attachment;
        }
        return null;
    }
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_original_path ? asset($this->attachment_original_path) : null;
    }

    public function getAttachmentInfoAttribute()
    {
        $path = 'uploads/images/Justification/' . $this->attachment;

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
     * حذف الملف المرفق عند حذف السجل
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->attachment) {
                $model->deleteHolidayFile($model->attachment);
            }
        });

        static::forceDeleting(function ($model) {
            if ($model->attachment) {
                $model->deleteHolidayFile($model->attachment);
            }
        });
    }

    // Constants for 'type'
    const TYPE_LATE = 1;
    const TYPE_EARLY_LEAVE = 2;
    const TYPE_ABSENCE = 3;

     const TYPE_PERMISSION = 4;
    public static function types(): array
    {
        return [
            self::TYPE_LATE => __('hr::models/hr_justifications.fields.late'), // Assuming translation keys exist
            self::TYPE_EARLY_LEAVE => __('hr::models/hr_justifications.fields.early_leave'),
            self::TYPE_ABSENCE => __('hr::models/hr_justifications.fields.absence'),
            self::TYPE_PERMISSION => __('hr::models/hr_justifications.fields.permission'),
        ];
    }

    public function getTypeTextAttribute(): string
    {
        return self::types()[$this->type] ?? 'Unknown';
    }

    public function getTypeBadgeAttribute(): string
    {
        $badges = [
            self::TYPE_LATE => 'badge badge-warning',
            self::TYPE_EARLY_LEAVE => 'badge badge-info',
            self::TYPE_ABSENCE => 'badge badge-danger',
        ];
        return $badges[$this->type] ?? 'badge badge-secondary';
    }

    // Constants for 'status'
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected'),
        ];
    }

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-success',
            self::STATUS_REJECTED => 'badge badge-danger',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    // Relationships
    public function HrShift(): BelongsTo
    {
        return $this->belongsTo(HrShift::class, 'shift_id', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
