<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchTranslation extends Model
{
    protected $table = 'branch_translations';

    public $fillable = ['name' ,'address'];

    public $timestamps = false;
}
