<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Theme
 * @package App\Models
 * @version December 14, 2022, 4:44 pm UTC
 *
 * @property string $name
 * @property string $panel_body_background
 * @property string $panel_aside_background
 * @property string $panel_aside_color
 * @property string $panel_btn_background
 * @property string $panel_header_color
 * @property string $panel_content_color
 * @property string $panel_btn_color
 * @property string $mobile_body_background
 * @property string $mobile_aside_background
 * @property string $mobile_aside_color
 * @property string $mobile_btn_background
 * @property string $mobile_header_color
 * @property string $mobile_content_color
 * @property string $mobile_btn_color
 */
class Theme extends Model
{
    use SoftDeletes;


    public $table = 'themes';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'panel_body_background',
        'panel_aside_background',
        'panel_aside_color',
        'panel_btn_background',
        'panel_header_color',
        'panel_content_color',
        'panel_btn_color',
        'mobile_body_background',
        'mobile_aside_background',
        'mobile_aside_color',
        'mobile_btn_background',
        'mobile_header_color',
        'mobile_content_color',
        'mobile_btn_color'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                      => 'integer',
        'name'                    => 'string',
        'panel_body_background'   => 'string',
        'panel_aside_background'  => 'string',
        'panel_aside_color'       => 'string',
        'panel_btn_background'    => 'string',
        'panel_header_color'      => 'string',
        'panel_content_color'     => 'string',
        'panel_btn_color'         => 'string',
        'mobile_body_background'  => 'string',
        'mobile_aside_background' => 'string',
        'mobile_aside_color'      => 'string',
        'mobile_btn_background'   => 'string',
        'mobile_header_color'     => 'string',
        'mobile_content_color'    => 'string',
        'mobile_btn_color'        => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name'                    => 'required',
        'panel_body_background'   => 'required',
        'panel_aside_background'  => 'required',
        'panel_aside_color'       => 'required',
        'panel_btn_background'    => 'required',
        'panel_header_color'      => 'required',
        'panel_content_color'     => 'required',
        'panel_btn_color'         => 'required',
        'mobile_body_background'  => 'required',
        'mobile_aside_background' => 'required',
        'mobile_aside_color'      => 'required',
        'mobile_btn_background'   => 'required',
        'mobile_header_color'     => 'required',
        'mobile_content_color'    => 'required',
        'mobile_btn_color'        => 'required'
    ];
}
