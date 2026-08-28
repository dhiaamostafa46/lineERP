<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategoryTranslation extends Model
{
    public $timestamps = false;
    protected $table = 'asset_categories_translations';
    
    protected $fillable = ['name', 'description', 'notes'];
}
