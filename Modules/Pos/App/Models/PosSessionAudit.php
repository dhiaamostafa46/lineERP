<?php

namespace Modules\Pos\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class PosSessionAudit extends Model
{
    use HasFactory;

    const ACTION_SESSION_OPENED = 'session_opened';
    const ACTION_SESSION_RESUMED_OR_TAKEN_OVER = 'session_resumed_or_taken_over';
    const ACTION_FORCED_LOGOUT_DETECTED = 'forced_logout_detected';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pos_session_id',
        'user_id',
        'device_id',
        'action',
        'ip_address',
        'user_agent'
    ];

    /**
     * علاقات
     */
    public function session()
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function device()
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Static Methods
     */

    /**
     * Scopes
     */
}
