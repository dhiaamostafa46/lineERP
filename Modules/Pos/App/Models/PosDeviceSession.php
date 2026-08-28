<?php

namespace Modules\Pos\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pos\Database\Factories\PosDeviceSessionFactory;

class PosDeviceSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_uuid',
        'user_id',
        'token_id',
        'browser_fingerprint',
        'ip_address',
        'user_agent',
        'device_name',
        'operating_system',
        'browser',
        'login_time',
        'logout_time',
        'last_activity',
        'status',
        'created_by'
    ];
    
    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'last_activity' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(PosDevice::class, 'device_uuid', 'uuid');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    protected static function newFactory(): PosDeviceSessionFactory
    {
        //return PosDeviceSessionFactory::new();
    }
}
