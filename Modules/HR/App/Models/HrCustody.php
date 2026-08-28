<?php

namespace Modules\HR\App\Models;

use Modules\HR\App\Models\HrEmployee;
use App\Helpers\ImageUploaderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrCustody extends Model
{
    use SoftDeletes, ImageUploaderTrait;
    public $table = 'hr_custodies';

    // status
    public const STATUS_PENDING  = 1;
    public const STATUS_RECEIVED = 2;

    public const STATUS_RETURN   = 3;
    public const STATUS_ACCEPT   = 4;

    public $fillable = [
        'employee_id',
        'asset_id',
        'details',
        'file',
        'received_id',
        'received_at',
        'return_at',
        'status'
    ];

    public static array $rules = [
        'employee_id' => 'required',
        'asset_id'    => 'required',
        'details'     => 'required',
        'file'        => 'required',
        'received_id' => 'nullable',
        'received_at' => 'nullable',
        'status'      => 'nullable'
    ];

    // status array
    public static function statuses()
    {
        return [
            self::STATUS_PENDING  => __('lang.pending'),
            self::STATUS_RECEIVED => __('lang.received'),
            self::STATUS_RETURN   => __('lang.return'),
            self::STATUS_ACCEPT   => __('lang.acceptreturn')
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING   => 'badge badge-danger',
            self::STATUS_RECEIVED  => 'badge badge-success',
            self::STATUS_RETURN    => 'badge badge-warning',
            self::STATUS_ACCEPT    => 'badge badge-dark',
        ];
        return $badges[$this->status];
    }

    public function setFileAttribute($file)
    {
        try {
            if ($file) {

                $fileName = $this->createFileName($file);

                $this->saveFile($file, $fileName);

                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['file'] = $file;
        }
    }


    public function getFilePathAttribute()
    {
        return $this->file ? asset('uploads/files/' . $this->file) : null;
    }



    public function asset()
    {
        return $this->belongsTo(HrAsset::class, 'asset_id');
    }

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function receiver()
    {
        return $this->belongsTo(HrEmployee::class, 'received_id');
    }
}
