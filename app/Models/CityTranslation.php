<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
    protected $table = 'city_translations';

    protected $fillable = ['name'];

    public $timestamps = false;
}
