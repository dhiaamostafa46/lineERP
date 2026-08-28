<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;

class HrShift extends Model
{
    public $table = 'hr_shifts';

    public $fillable = [
        'type_id',
        'from',
        'to',
        'is_active'
    ];

    public $timestamps = false;

    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = $value ? 1 : 0;
    }
}
