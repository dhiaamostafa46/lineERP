<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeIdentity extends Model
{
    use HasFactory,  SoftDeletes;

    const TYPE_IDENTITY = 1;
    const TYPE_RESIDENCE = 2;

    protected $fillable = [
        'employee_id',
        'identity_type',
        'identity_no',
        'insurance_no',
        'identity_expired_at',
        'insurance_expired_at',
    ];

    protected $casts = [
        'identity_expired_at' => 'string',
        'insurance_expired_at' => 'string',
    ];

    public static function types()
    {
        return [
            self::TYPE_IDENTITY => __('lang.identity'),
            self::TYPE_RESIDENCE => __('lang.residence'),
        ];
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->identity_type];
    }
}
