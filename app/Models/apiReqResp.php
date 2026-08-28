<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class apiReqResp extends Model
{
    use SoftDeletes;
    protected $table = 'api_req_resp';
    protected $fillable = [
        'user_id',
        'tenant_id',
        'method',
        'endpoint',
        '_request',
        '_response',
        'status',
        'duration_ms',
    ];



    /**
     * Get the translations for the branch.
     */

    /**
     * Get the user that owns the branch.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public static function rules()
    {
       
    }

}
