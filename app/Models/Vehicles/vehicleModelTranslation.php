<?php

namespace App\Models\Vehicles;


use Illuminate\Database\Eloquent\Model;

class vehicleModelTranslation extends Model
{
    protected $table = 'vehicle_models_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
