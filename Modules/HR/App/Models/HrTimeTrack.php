<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrTimeTrack extends Model
{
    use HasFactory;

    protected $table = 'hr_time_tracks';

    protected $fillable = [
        'employee_id',
        'date',
        'day',
        'lat',
        'lon',
        'address',
        'type',
        'status',
        'hour',
        'process',
    ];

    // ===== Attendance Types =====
    const TYPE_ABSENT            = 1; // غياب
    const TYPE_PRESENT           = 2; // حضور
    const TYPE_WEEKEND           = 3; // عطلة أسبوعية
    const TYPE_HOLIDAY           = 4; // إجازة
    const TYPE_EXEMPT            = 5; // يوم مستبعد
    const TYPE_OFFICIAL_HOLIDAY  = 6; // عطلة رسمية

    /**
     * Employee relation
     */
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    /**
     * Types list
     */
    public static function types(): array
    {
        return [
            self::TYPE_ABSENT           => __('hr::lang.absent'),
            self::TYPE_PRESENT          => __('hr::lang.present'),
            self::TYPE_WEEKEND          => __('hr::lang.weekend'),
            self::TYPE_HOLIDAY          => __('hr::lang.holiday'),
            self::TYPE_EXEMPT           => __('hr::lang.exempt'),
            self::TYPE_OFFICIAL_HOLIDAY => __('hr::lang.official_holiday'),
        ];
    }

    /**
     * Get type text
     */
    public function getTypeTextAttribute(): string
    {
        return self::types()[$this->type] ?? '-';
    }

    /**
     * Time track details
     */
    public function timeTrackDetails()
    {
        return $this->hasMany(
            HrTimeTrackDetails::class,
            'hr_time_track_id'
        );
    }
}
