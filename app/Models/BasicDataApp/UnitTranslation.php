<?php

namespace App\Models\BasicDataApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitTranslation extends Model
{
    use HasFactory;

    protected $table = 'unit_translations';

    public $timestamps = false;

    protected $fillable = ['name'];
}
