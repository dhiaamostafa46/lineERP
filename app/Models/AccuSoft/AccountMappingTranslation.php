<?php

namespace App\Models\AccuSoft;

use Illuminate\Database\Eloquent\Model;

class AccountMappingTranslation extends Model
{
    protected $table = 'account_mapping_translations';

    protected $fillable = ['name'];

    public $timestamps = false;
}
