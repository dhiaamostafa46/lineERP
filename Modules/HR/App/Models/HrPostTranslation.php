<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;

class HrPostTranslation extends Model
{
    public $table = 'hr_post_translations';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'body',
    ];
}
