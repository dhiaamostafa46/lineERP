<?php

namespace Modules\Store\App\Models;

use App\Helpers\ImageUploaderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StoreApp\Store;
use Illuminate\Support\Facades\File;

class StSettlement extends Model
{
    use \App\Traits\BelongsToBranch;

    use SoftDeletes, ImageUploaderTrait;

    const STATUS_DRAFT = 1;
    const STATUS_APPROVED = 2;
    const STATUS_CANCELLED = 3;

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function getIsEditableAttribute(): bool
    {
        return $this->status !== self::STATUS_CANCELLED;
    }

    public function getIsDeletableAttribute(): bool
    {
        return $this->status !== self::STATUS_CANCELLED;
    }

    protected $fillable = [
        'org_id', 'branch_id', 'user_id', 'document_number', 'document_date', 
        'store_id', 'status', 'attachment', 'total_items', 'total_quantity', 'total_value',
        'journal_entry_id', 'notes'
    ];

    public function setAttachmentAttribute($file)
    {
        try {
            if ($file) {
                if ($this->attachment) {
                    $this->deleteFile($this->attachment, 'store_settlements');
                }
                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'store_settlements');
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['attachment'] = null;
        }
    }

    public function getAttachmentOriginalPathAttribute()
    {
        if ($this->attachment && File::exists(public_path('uploads/images/store_settlements/' . $this->attachment))) {
            return 'uploads/images/store_settlements/' . $this->attachment;
        }
        return null;
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_original_path ? asset($this->attachment_original_path) : null;
    }

    public function getFileInfoAttribute()
    {
        $path = public_path('uploads/images/store_settlements/' . $this->attachment);
        if (!$this->attachment || !File::exists($path)) {
            return null;
        }
        return [
            'name' => $this->attachment,
            'path' => $path,
            'size' => File::size($path),
            'extension' => File::extension($path),
            'mime' => File::mimeType($path),
            'url' => asset('uploads/images/store_settlements/' . $this->attachment),
        ];
    }

    protected $casts = [
        'document_date' => 'date',
        'total_quantity' => 'decimal:4',
        'total_value' => 'decimal:4',
    ];

    public static function generateDocumentNumber()
    {
        $prefix = 'SET-';
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

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function items()
    {
        return $this->hasMany(StSettlementItem::class, 'settlement_id');
    }

    public static function statuses()
    {
        return [
           self::STATUS_DRAFT => __('lang.draft'),
            self::STATUS_APPROVED => __('lang.approved'),
        ];
    }
}
