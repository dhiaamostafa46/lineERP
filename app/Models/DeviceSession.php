<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceSession extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'device_sessions';

    protected $fillable = [
        'user_id',
        'org_id',
                
        'device_token',
        'device_serial',
        'device_name',
        'user_agent',
        'ip_address',
        'device_type',
        'browser',
        'os',
        'is_active',
        'last_activity_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    // الثوابت لتعريف الحالة
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * الحالة النصية للجلسة
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    /**
     * Accessor for status_text attribute.
     */
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->is_active] ?? __('lang.inactive');
    }

    /**
     * Accessor for status_badge attribute.
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? 'badge badge-light-success'
            : 'badge badge-light-danger';
    }


    /**
     * علاقة المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * هل الجلسة نشطة؟
     */
    public function isActive(): bool
    {
        return $this->is_active === self::STATUS_ACTIVE;
    }

    /**
     * تفعيل الجلسة
     */
    public function activate(): void
    {
        $this->update(['is_active' => self::STATUS_ACTIVE]);
    }

    /**
     * تعطيل الجلسة
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => self::STATUS_INACTIVE]);
    }
}
