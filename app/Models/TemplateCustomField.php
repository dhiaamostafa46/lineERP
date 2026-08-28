<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateCustomField extends Model
{
    protected $table = 'template_custom_fields';

    protected $fillable = [
        'template_id',
        'label',
        'type',
        'default_value',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
