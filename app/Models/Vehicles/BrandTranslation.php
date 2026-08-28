<?php

namespace App\Models\Vehicles;


use Illuminate\Database\Eloquent\Model;

class BrandTranslation extends Model
{
    protected $table = 'vehicle_brands_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
