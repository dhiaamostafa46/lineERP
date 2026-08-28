<?php

namespace App\Models\AccuSoft;

use Illuminate\Database\Eloquent\Model;

class TaxAccountTranslation extends Model
{
    protected $table = 'tax_account_translations';

    protected $fillable = ['name'];

    public $timestamps = false;
}
