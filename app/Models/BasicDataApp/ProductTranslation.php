<?php

namespace App\Models\BasicDataApp;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $table = 'product_translations';

    public $fillable = ['name', 'details'];

    public $timestamps = false;
}
