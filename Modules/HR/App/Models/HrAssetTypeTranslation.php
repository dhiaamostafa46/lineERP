<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrAssetTypeTranslation extends Model
{
    protected $table = 'hr_asset_type_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
