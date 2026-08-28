<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;

class HrAssetTranslation extends Model
{
    public $table = 'hr_asset_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
