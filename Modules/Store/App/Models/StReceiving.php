<?php
namespace Modules\Store\App\Models;

use App\Helpers\ImageUploaderTrait;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\StoreApp\StockMovement;
use App\Models\StoreApp\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class StReceiving extends Model
{
    use \App\Traits\BelongsToBranch;

    use SoftDeletes, ImageUploaderTrait;

    protected $table = 'st_receivings';

    protected $fillable = [
        'org_id', 'branch_id', 'user_id', 'document_number', 'document_date', 
        'store_id', 'tree_account_id', 'reference_type', 'reference_id', 'status', 'attachment',
        'total_items', 'total_quantity', 'total_value', 
        'approved_by', 'approved_at', 'notes', 'journal_entry_id'
    ];

    public function setAttachmentAttribute($file)
    {
        try {
            if ($file) {
                if ($this->attachment) {
                    $this->deleteFile($this->attachment, 'store_receivings');
                }
                $fileName = $this->createFileName($file);
                $this->saveFileType($file, $fileName, 'store_receivings');
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['attachment'] = null;
        }
    }

    public function getAttachmentOriginalPathAttribute()
    {
        if ($this->attachment && File::exists(public_path('uploads/images/store_receivings/' . $this->attachment))) {
            return 'uploads/images/store_receivings/' . $this->attachment;
        }
        return null;
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_original_path ? asset($this->attachment_original_path) : null;
    }

    public function getFileInfoAttribute()
    {
        $path = public_path('uploads/images/store_receivings/' . $this->attachment);
        if (!$this->attachment || !File::exists($path)) {
            return null;
        }
        return [
            'name' => $this->attachment,
            'path' => $path,
            'size' => File::size($path),
            'extension' => File::extension($path),
            'mime' => File::mimeType($path),
            'url' => asset('uploads/images/store_receivings/' . $this->attachment),
        ];
    }

    public function account()
    {
        return $this->belongsTo(TreeAccounts::class, 'tree_account_id');
    }

    public static function generateDocumentNumber()
    {
        $prefix = 'REC-';
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

    const STATUS_DRAFT = 1;
    const STATUS_APPROVED = 2;
    const STATUS_CANCELLED = 4;

    protected $casts = [
        'document_date' => 'date',
        'approved_at' => 'datetime',
        'total_quantity' => 'decimal:4',
        'total_value' => 'decimal:4',
    ];

    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => __('lang.draft'),
            self::STATUS_APPROVED => __('lang.approved'),
            // self::STATUS_CANCELLED => __('lang.cancelled'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function items()
    {
        return $this->hasMany(StReceivingItem::class, 'receiving_id');
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

    public function movements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function getIsEditableAttribute()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getIsDeletableAttribute()
    {
        return $this->status === self::STATUS_DRAFT;
    }
}
