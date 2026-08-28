<?php

namespace App\Models\StoreApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTranslation extends Model
{
    use HasFactory;

    protected $table = 'store_translations';

    public $timestamps = true;

    protected $fillable = ['name' ,'address'];
}
