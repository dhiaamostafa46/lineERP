<?php

namespace App\Models\BasicDataApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSizeTranslation extends Model
{
    use HasFactory;

    protected $table = 'product_size_translations';

    public $timestamps = true;

    protected $fillable = ['name'];
}
