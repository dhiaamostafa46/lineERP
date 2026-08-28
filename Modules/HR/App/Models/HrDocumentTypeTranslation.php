<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrDocumentTypeTranslation extends Model
{
    protected $table = 'hr_document_type_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
