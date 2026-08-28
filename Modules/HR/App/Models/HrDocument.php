<?php

namespace Modules\HR\App\Models;

use App\Helpers\ImageUploaderTrait;
use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrDocument extends Model
{
    use SoftDeletes, ImageUploaderTrait;

    public $table = 'hr_documents';

    public $fillable = [
        'employee_id',
        'type_id',
        'file',
        'status'
    ];

    protected $casts = [
        'id'          => 'integer',
        'employee_id' => 'integer',
        'type_id'     => 'integer',
        'file'        => 'string',
        'status'      => 'integer'
    ];

    public static array $rules = [
        // 'employee_id' => 'required',
        'type_id'     => 'required|exists:hr_document_types,id',
        'file'        => 'required|file|max:10240',
        'status'      => 'required'
    ];






    // file
    public function setFileAttribute($file)
    {
        try {
            if ($file) {

                $fileName = $this->createFileName($file);

                $this->SaveFileOriginal($file, $fileName);

                // $this->thumbImage($file, $fileName, 200, 200);

                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['file'] = $file;
        }
    }

    public function getFileOriginalPathAttribute()
    {
        return $this->file ? asset('uploads/files/original/' . $this->file) : null;
    }

    public function getFileThumbnailPathAttribute()
    {
        return $this->file ? asset('uploads/images/thumbnail/' . $this->file) : null;
    }
    // file





    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE   => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE   => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status];
    }



    /**
     * Get the employee that owns the HrDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    /**
     * Get the type that owns the HrDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(HrDocumentType::class, 'type_id');
    }
}
