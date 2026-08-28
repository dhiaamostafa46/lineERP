<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;


class AssetTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'notes', 'locale'];

    protected $table = 'asset_translations';

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
