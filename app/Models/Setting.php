<?php

namespace App\Models;

use App\Helpers\ImageUploaderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes, ImageUploaderTrait;


    public $table = 'settings';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'logo',
        'org_id',
        'fav_icon',
        'name',
        'coming_soon',
        'count_user',
        'actual_user',
    ];

    public static $rules = [
        'logo'              => 'nullable',
        'fav_icon'          => 'nullable',
    ];


    ################################### Appends #####################################

    protected $appends = [
        'logo_original_path',
        'logo_thumbnail_path',
        'fav_icon_original_path',
        'fav_icon_thumbnail_path',
    ];

    // fav_icon
    public function setFavIconAttribute($file)
    {
        try {
            if ($file) {

                $fileName = $this->createFileName($file);

                $this->originalImage($file, $fileName);

                $this->thumbImage($file, $fileName, 100, 100);

                $this->attributes['fav_icon'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['fav_icon'] = $file;
        }
    }

    public function getFavIconOriginalPathAttribute()
    {
        return $this->fav_icon ? asset('uploads/images/original/' . $this->fav_icon) : null;
    }

    public function getFavIconThumbnailPathAttribute()
    {
        return $this->fav_icon ? asset('uploads/images/thumbnail/' . $this->fav_icon) : null;
    }
    // fav_icon

    // logo
    public function setLogoAttribute($file)
    {
        try {
            if ($file) {

                $fileName = $this->createFileName($file);

                $this->originalImage($file, $fileName);

                $this->thumbImage($file, $fileName, 200, 200);

                $this->attributes['logo'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['logo'] = $file;
        }
    }

    public function getLogoOriginalPathAttribute()
    {
        return $this->logo ? asset('uploads/images/original/' . $this->logo) : null;
    }

    public function getLogoThumbnailPathAttribute()
    {
        return $this->logo ? asset('uploads/images/thumbnail/' . $this->logo) : null;
    }
    // logo

}
