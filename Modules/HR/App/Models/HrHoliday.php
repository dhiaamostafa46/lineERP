<?php

namespace Modules\HR\App\Models;

use App\Helpers\ImageUploaderTrait;
use App\Models\User;
use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\App\Models\HrHolidayType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class HrHoliday extends Model
{
    use SoftDeletes, ImageUploaderTrait;

    public $table = 'hr_holidays';

    // Status Constants
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    public $fillable = ['employee_id', 'approver_id', 'status', 'comments', 'attachment', 'type_id', 'from_at', 'end_at'];

    protected $casts = [
        'id' => 'integer',
        'employee_id' => 'integer',
        'status' => 'integer',
        'type_id' => 'integer',
        'from_at' => 'date',
        'end_at' => 'date',
    ];

    public static array $rules = [
        'employee_id' => 'required',
        'type_id' => 'required|exists:hr_holiday_types,id',
        'from_at' => 'required|date',
        'end_at' => 'required|date',
        'status' => 'nullable',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ];

    /**
     * حفظ الملف المرفق
     */
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

                // حفظ الملف في مجلد Holiday
                $this->saveHolidayFile($file, $fileName);

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
        if ($this->attachment && File::exists('uploads/images/Holiday/' . $this->attachment)) {
            return 'uploads/images/Holiday/' . $this->attachment;
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
    public function getAttachmentInfoAttribute()
    {
        $path = 'uploads/images/Holiday/' . $this->attachment;

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

        static::saving(function ($model) {
            if ($model->from_at && $model->end_at) {
                $requested_days = 0;
                $employee = HrEmployee::with('shift')->find($model->employee_id);
                $workingDays = $employee ? optional($employee->shift)->work_days : null;

                $from = \Carbon\Carbon::parse($model->from_at);
                $end = \Carbon\Carbon::parse($model->end_at);

                if (!empty($workingDays) && is_array($workingDays)) {
                    $period = \Carbon\CarbonPeriod::create($from, $end);
                    foreach ($period as $date) {
                        if (in_array(strtolower($date->format('l')), $workingDays)) {
                            $requested_days++;
                        }
                    }
                } else {
                    $requested_days = $from->diffInDays($end) + 1;
                }
                $model->requested_days = $requested_days;
            }
        });

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

    // ==================== Status Methods ====================

    public static function statuses()
    {
        return [
            self::STATUS_PENDING => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-success',
            self::STATUS_REJECTED => 'badge badge-danger',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    // ==================== Relations ====================

    public function type()
    {
        return $this->belongsTo(HrHolidayType::class, 'type_id');
    }

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }


    public function GetBlance()  {
        $holidayType = HrHolidayType::find($this->type_id);
        if (!$holidayType) {
            return 0;
        }

        $holidayBalance = HrHolidayBalance::where('employee_id', $this->employee_id)
            ->where('type_id', $this->type_id)
            ->first();

        if (!$holidayBalance) {
            return 0;
        }

        return $holidayBalance;

    }
}
