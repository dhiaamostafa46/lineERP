<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrTimeTrackDetails extends Model
{
    use HasFactory;

    protected $table = 'hr_time_track_details';

    protected $fillable = [
        'hr_time_track_id',
        'check_time',
        'early_arrival',
        'delay',
        'early_leave',
        'overtime',
        'shift_from',
        'shift_to',
        'check_out',
        'lat',
        'lon',
        'address',
        'type',
    ];

    // ===== Attendance Types =====
    const TYPE_ABSENT            = 1; // غياب
    const TYPE_PRESENT           = 2; // حضور
    const TYPE_WEEKEND           = 3; // عطلة أسبوعية
    const TYPE_HOLIDAY           = 4; // إجازة
    const TYPE_EXEMPT            = 5; // يوم مستبعد
    const TYPE_JUSTIFICATION     = 6; // تبرير
    const TYPE_OFFICIAL_HOLIDAY  = 7; // عطلة رسمية
    const TYPE_FINGERPRINT       = 8; // بصمة

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
            self::TYPE_JUSTIFICATION    => __('hr::lang.justification'),
            self::TYPE_OFFICIAL_HOLIDAY => __('hr::lang.official_holiday'),
            self::TYPE_FINGERPRINT      => __('hr::lang.fingerprint'),
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
     * Relation
     */
    public function timeTrack()
    {
        return $this->belongsTo(HrTimeTrack::class, 'hr_time_track_id');
    }
}
