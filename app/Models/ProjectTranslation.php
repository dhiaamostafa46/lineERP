<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTranslation extends Model
{
    protected $table = 'project_translations';

    protected $fillable = ['name'];
}
