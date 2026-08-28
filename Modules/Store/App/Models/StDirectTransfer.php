<?php
namespace Modules\Store\App\Models;

use App\Helpers\ImageUploaderTrait;
use App\Models\AccuSoft\JournalEntry;
use App\Models\StoreApp\StockMovement;
use App\Models\StoreApp\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class StDirectTransfer extends Model
{
    use \App\Traits\BelongsToBranch;

    use SoftDeletes, ImageUploaderTrait;

    protected $table = 'st_direct_transfers';

    protected $fillable = [
        'org_id', 'branch_id', 'user_id', 'document_number', 'document_date', 
        'from_store_id', 'to_store_id', 'status', 'attachment', 'transfer_type',
        'total_items', 'total_quantity', 'total_value', 
        'approved_by', 'approved_at', 'notes', 'journal_entry_id',
        'journal_entries_ids', 'returned_quantity', 'return_status'
    ];

    public function setAttachmentAttribute($file)
    {
        try {
            if ($file) {
                if ($this->attachment) {
                    $this->deleteFile($this->attachment, 'store_direct_transfers');
                }
                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'store_direct_transfers');
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['attachment'] = null;
        }
    }

    public function getAttachmentOriginalPathAttribute()
    {
        if ($this->attachment && File::exists(public_path('uploads/images/store_direct_transfers/' . $this->attachment))) {
            return 'uploads/images/store_direct_transfers/' . $this->attachment;
        }
        return null;
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_original_path ? asset($this->attachment_original_path) : null;
    }

    public function getFileInfoAttribute()
    {
        $path = public_path('uploads/images/store_direct_transfers/' . $this->attachment);
        if (!$this->attachment || !File::exists($path)) {
            return null;
        }
        return [
            'name' => $this->attachment,
            'path' => $path,
            'size' => File::size($path),
            'extension' => File::extension($path),
            'mime' => File::mimeType($path),
            'url' => asset('uploads/images/store_direct_transfers/' . $this->attachment),
        ];
    }

    public static function generateDocumentNumber()
    {
        $prefix = 'TRF-';
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

    const TYPE_DIRECT = 1;
    const TYPE_INDIRECT = 2;

    const STATUS_DRAFT = 1;
    const STATUS_SOURCE_APPROVED = 2; // In Transit
    const STATUS_DESTINATION_DRAFT = 3;
    const STATUS_DESTINATION_APPROVED = 5;
    const STATUS_CANCELLED = 4;
    const STATUS_PARTIAL_APPROVED = 6;
    const STATUS_RETURNED = 7;
    const STATUS_PARTIAL_RETURNED = 8;

    const RETURN_STATUS_NONE = 0;
    const RETURN_STATUS_PARTIAL = 1;
    const RETURN_STATUS_FULL = 3;

    protected $casts = [
        'document_date' => 'date',
        'approved_at' => 'datetime',
        'total_quantity' => 'decimal:4',
        'total_value' => 'decimal:4',
        'returned_quantity' => 'decimal:4',
        'journal_entries_ids' => 'json',
    ];

    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => __('store::models/st_direct_transfers.status.draft'),
            self::STATUS_SOURCE_APPROVED => __('store::models/st_direct_transfers.status.in_transit') . ' / ' . __('store::models/st_direct_transfers.status.transferred'),
            self::STATUS_DESTINATION_DRAFT => __('store::models/st_direct_transfers.status.destination_draft'),
            self::STATUS_DESTINATION_APPROVED => __('store::models/st_direct_transfers.status.completed'),
            self::STATUS_CANCELLED => __('store::models/st_direct_transfers.status.cancelled'),
            self::STATUS_PARTIAL_APPROVED => __('store::models/st_direct_transfers.status.partial_approved') ?? 'تعميد جزئي',
            self::STATUS_RETURNED => __('store::models/st_direct_transfers.status.returned') ?? 'مرجع',
            self::STATUS_PARTIAL_RETURNED => __('store::models/st_direct_transfers.status.partial_returned') ?? 'مرجع جزئي',
        ];
    }

    public function getStatusTextAttribute()
    {
        $all = self::statuses();
        if ($this->is_direct) {
            if ($this->status == self::STATUS_SOURCE_APPROVED) {
                return __('store::models/st_direct_transfers.status.transferred');
            }
        } else {
            if ($this->status == self::STATUS_SOURCE_APPROVED) {
                return __('store::models/st_direct_transfers.status.in_transit');
            }
        }
        return $all[$this->status] ?? '';
    }

    public function items()
    {
        return $this->hasMany(StDirectTransferItem::class, 'direct_transfer_id');
    }

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function movements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }

    public function getIsDirectAttribute()
    {
        return $this->transfer_type == self::TYPE_DIRECT;
    }

    public function getIsEditableAttribute()
    {
       return $this->status === self::STATUS_DRAFT;
    }

    public function getIsDeletableAttribute()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getCanBeValidatedAttribute()
    {
        return in_array($this->status, [
            self::STATUS_SOURCE_APPROVED,
            self::STATUS_DESTINATION_DRAFT,
            self::STATUS_PARTIAL_APPROVED
        ]);
    }
}
