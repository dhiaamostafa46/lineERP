<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationTranslation extends Model
{

    protected $table = 'organization_translations';

    public $fillable = ['name' ,'address'];

    public $timestamps = false;


}
