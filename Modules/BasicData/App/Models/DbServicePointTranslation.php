<?php

namespace Modules\BasicData\App\Models;

use Illuminate\Database\Eloquent\Model;

class DbServicePointTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
    protected $table = 'db_service_point_translations';
}
