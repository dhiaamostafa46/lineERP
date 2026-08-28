<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocumentDefault extends Model
{
    protected $table = 'template_document_defaults';

    protected $fillable = [
        'template_id',
        'document_type',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
