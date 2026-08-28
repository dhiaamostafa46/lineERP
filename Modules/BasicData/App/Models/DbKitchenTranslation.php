<?php

namespace Modules\BasicData\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DbKitchenTranslation extends Model
{
    use HasFactory;

    protected $table = 'db_kitchen_translations';
    public $timestamps = false;
    protected $fillable = ['name'];
}
