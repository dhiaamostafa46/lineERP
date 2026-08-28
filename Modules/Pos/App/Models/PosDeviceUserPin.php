<?php

namespace Modules\Pos\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class PosDeviceUserPin extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_id',
        'user_id',
        'pin_hash',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * علاقات
     */
    public function device()
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
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
