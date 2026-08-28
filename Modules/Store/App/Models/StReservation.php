<?php

namespace Modules\Store\App\Models;

use App\Helpers\ImageUploaderTrait;
use App\Models\StoreApp\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class StReservation extends Model
{
    use \App\Traits\BelongsToBranch;

    use SoftDeletes, ImageUploaderTrait;

    protected $table = 'st_reservations';

    protected $fillable = [
        'org_id', 'branch_id', 'user_id', 'document_number', 'document_date', 
        'store_id', 'status', 'attachment', 'total_items', 'total_quantity', 'total_value', 
        'approved_by', 'approved_at', 'returned_by', 'returned_at', 'notes'
    ];

    public function setAttachmentAttribute($file)
    {
        try {
            if ($file) {
                if ($this->attachment) {
                    $this->deleteFile($this->attachment, 'store_reservations');
                }
                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'store_reservations');
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['attachment'] = null;
        }
    }

    public function getAttachmentOriginalPathAttribute()
    {
        if ($this->attachment && File::exists(public_path('uploads/images/store_reservations/' . $this->attachment))) {
            return 'uploads/images/store_reservations/' . $this->attachment;
        }
        return null;
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_original_path ? asset($this->attachment_original_path) : null;
    }

    public function getFileInfoAttribute()
    {
        $path = public_path('uploads/images/store_reservations/' . $this->attachment);
        if (!$this->attachment || !File::exists($path)) {
            return null;
        }
        return [
            'name' => $this->attachment,
            'path' => $path,
            'size' => File::size($path),
            'extension' => File::extension($path),
            'mime' => File::mimeType($path),
            'url' => asset('uploads/images/store_reservations/' . $this->attachment),
        ];
    }

    const STATUS_DRAFT = 1;
    const STATUS_APPROVED = 2; // Reserved
    const STATUS_RETURNED = 3;
    const STATUS_CANCELLED = 4;

    protected $casts = [
        'document_date' => 'date',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'total_quantity' => 'decimal:4',
        'total_value' => 'decimal:4',
    ];

    public static function generateDocumentNumber()
    {
        $prefix = 'RES-';
        $year = date('Y');
        $month = date('m');

        $lastNumber = self::withTrashed()
            ->where('document_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('document_number', 'desc')
            ->value('document_number');

        $sequence = 1;
        if ($lastNumber) {
            $sequence = (int) substr($lastNumber, -4) + 1;
        }

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function statuses($selectableOnly = false)
    {
        $all = [
            self::STATUS_DRAFT => __('lang.draft'),
            self::STATUS_APPROVED => __('store::models/st_reservations.status.reserved') ?? 'محجوز',
            self::STATUS_RETURNED => __('store::models/st_reservations.status.returned') ?? 'مرتجع للمستودع',
            self::STATUS_CANCELLED => __('lang.cancelled'),
        ];

        if ($selectableOnly) {
            return [
                self::STATUS_DRAFT => $all[self::STATUS_DRAFT],
                self::STATUS_APPROVED => $all[self::STATUS_APPROVED],
            ];
        }

        return $all;
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function items()
    {
        return $this->hasMany(StReservationItem::class, 'reservation_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function getIsEditableAttribute()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getIsDeletableAttribute()
    {
        return $this->status === self::STATUS_DRAFT;
    }
    
    public function getCanBeReturnedAttribute()
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
