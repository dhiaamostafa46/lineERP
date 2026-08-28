<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class Language extends Model
{
     use SoftDeletes;    public $table = 'languages';

    public $fillable = [
        'name',
        'locale',
        'status'
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'locale' => 'string',
        'status' => 'integer'
    ];

    public static array $rules = [
        
    ];

    
}
