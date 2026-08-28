<?php

namespace App\Models\AccuSoft;

use Illuminate\Database\Eloquent\Model;

class TreeAccountsTranslation extends Model
{
    /**
     * اسم الجدول - يجب أن يطابق ما في الـ migration
     */
    protected $table = 'tree_account_translations';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * تعطيل timestamps
     * إذا كان الجدول لا يحتوي على created_at و updated_at
     */
    public $timestamps = false;
}
