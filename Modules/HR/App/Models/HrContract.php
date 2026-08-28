<?php

namespace Modules\HR\App\Models;

use App\Models\User;
use Modules\HR\App\Models\HrEmployee;
use App\Helpers\ImageUploaderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Validation\Rule;

class HrContract extends Model
{
    use SoftDeletes, ImageUploaderTrait;

    public $table = 'hr_contracts';

    public $fillable = [
        'employee_id',
        'type_id',
        'contract_number',
        'file',
        'qiwa',
        'start_date',
        'end_date',
        'signed_date',
        'duration_months',
        'auto_renewable',
        'signatory_company_id',
        'signatory_employee_id',
        'company_signature',
        'employee_signature',
        'additional_data',
        'location',
        'office',
        'data_conatact',
        'termination_terms',
        'accepted_by_employee',
        'accepted_date',
        'approved_by_hr',
        'approved_date',
        'approved_by',
        'status',
        'notes'
    ];

    protected $casts = [
        'id'                    => 'integer',
        'employee_id'           => 'integer',
        'type_id'               => 'integer',
        'start_date'            => 'date',
        'end_date'              => 'date',
        'signed_date'           => 'date',
        'accepted_date'         => 'date',
        'approved_date'         => 'date',
        'duration_months'       => 'integer',
        'auto_renewable'        => 'boolean',
        'accepted_by_employee'  => 'boolean',
        'approved_by_hr'        => 'boolean',
        'additional_data'       => 'array',
        'status'                => 'integer',
    ];

    public static array $rules = [
        'employee_id'       => 'required|exists:hr_employees,id',
        'type_id'           => 'required|exists:hr_contract_types,id',
        'contract_number'   => 'required|string',
        'file'              => 'nullable|file|max:10240',
        'qiwa_no'           => 'nullable|string',
        'start_date'        => 'required|date',
        'end_date'          => 'nullable|date|after:start_date',
    ];

    // File handling
    public function setFileAttribute($file)
    {
        try {
            if ($file && is_file($file)) {
                $fileName = $this->createFileName($file);
                $this->SaveFileOriginal($file, $fileName);
                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['file'] = is_string($file) ? $file : null;
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

    // Status constants
    const STATUS_DRAFT      = 1;
    const STATUS_ACTIVE     = 2;
    const STATUS_EXPIRED    = 3;
    const STATUS_TERMINATED = 4;
    const STATUS_RENEWED    = 5;


      const QIWA_YES    = 1;
     const QIWA_NO    = 0;

    public static function statuses()
    {
        return [
            self::STATUS_DRAFT      => __('hr::models/hr_contracts.statuses.draft'),
            self::STATUS_ACTIVE     => __('hr::models/hr_contracts.statuses.active'),
            self::STATUS_EXPIRED    => __('hr::models/hr_contracts.statuses.expired'),
            self::STATUS_TERMINATED => __('hr::models/hr_contracts.statuses.terminated'),
            self::STATUS_RENEWED    => __('hr::models/hr_contracts.statuses.renewed'),
        ];
    }


      public static function qiwas()
    {
        return [
            self::QIWA_YES      => __('lang.yes'),
            self::QIWA_NO       => __('lang.no'),
        ];
    }

     public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }


    public function getQiwaTextAttribute()
    {
        return self::qiwas()[$this->qiwa] ?? __('lang.unknown');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_DRAFT      => 'badge badge-secondary',
            self::STATUS_ACTIVE     => 'badge badge-success',
            self::STATUS_EXPIRED    => 'badge badge-warning',
            self::STATUS_TERMINATED => 'badge badge-danger',
            self::STATUS_RENEWED    => 'badge badge-info',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED ||
               ($this->end_date && $this->end_date->isPast());
    }

    public function isFullySigned()
    {
        return !empty($this->company_signature) && !empty($this->employee_signature);
    }

    public function isApproved()
    {
        return $this->approved_by_hr && $this->accepted_by_employee;
    }

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(HrContractType::class, 'type_id');
    }

    public function signatoryCompany(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signatory_company_id');
    }

    public function signatoryEmployee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'signatory_employee_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function contractItems(): HasMany
    {
        return $this->hasMany(HrContractitem::class, 'contract_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)
                     ->orWhere(function($q) {
                         $q->whereNotNull('end_date')
                           ->where('end_date', '<', now());
                     });
    }

    public function scopeApproved($query)
    {
        return $query->where('approved_by_hr', true)
                     ->where('accepted_by_employee', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where(function($q) {
            $q->where('approved_by_hr', false)
              ->orWhere('accepted_by_employee', false);
        });
    }
}
