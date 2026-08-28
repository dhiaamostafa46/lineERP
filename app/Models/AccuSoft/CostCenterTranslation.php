<?php

namespace App\Models\AccuSoft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenterTranslation extends Model
{
    use HasFactory;

    protected $table = 'cost_center_translations';

    protected $fillable = ['name', 'description'];

    public $timestamps = false;
}
