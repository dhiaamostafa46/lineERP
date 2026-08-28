<?php

namespace App\Models\invApp;

use Illuminate\Database\Eloquent\Model;

class InvSupplierTranslation extends Model
{
    protected $table = 'inv_supplier_translations';

    public $fillable = ['name' ];

    public $timestamps = false;
}
