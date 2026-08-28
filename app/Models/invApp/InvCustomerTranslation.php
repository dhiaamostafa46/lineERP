<?php

namespace App\Models\invApp;

use Illuminate\Database\Eloquent\Model;

class InvCustomerTranslation extends Model
{

    protected $table = 'inv_customer_translations';

    public $fillable = ['name' ];
  

    public $timestamps = false;
}
