<?php

namespace App\Models\AccuSoft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fiscal_years';

    protected $fillable = ['start_date', 'end_date', 'is_current', 'is_closed', 'notes', 'closed_at', 'closed_by', 'closed_periods', 'closure_note', 'pre_closing_balances', 'post_closing_balances'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean', // تم التصحيح من integer إلى boolean
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
        'closed_periods' => 'array',
    ];

    // Constants
    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        $rules = [
            'start_date' => 'required|date|unique:fiscal_years,start_date' . ($id ? ",$id" : ''),
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ];

        return $rules;
    }

    /**
     * Scope: Get current fiscal year
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope: Get open fiscal years
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope: Get closed fiscal years
     */
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Check if a date falls within an open fiscal year (Boolean)
     */
    public static function isDateInOpenFiscalYear($date)
    {
        if (empty($date)) {
            return false;
        }

        return self::whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->where('is_closed', false)->exists();
    }

    /**
     * Check if a date falls within an open fiscal year
     */
    public static function checkDate($date)
    {
        $fiscalYear = self::whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->where('is_closed', false)->first();

        if (!$fiscalYear) {
            throw new \Exception(__('accusoft::general.date_outside_open_fiscal_year'));
        }

        return $fiscalYear;
    }

    /**
     * Get available statuses
     */
    public static function statuses()
    {
        return [
            self::STATUS_CLOSE => __('lang.closely'),
            self::STATUS_OPEN => __('lang.openly'),
        ];
    }

    /**
     * Get status attribute
     */
    public function getStatusAttribute()
    {
        return $this->is_closed ? self::STATUS_CLOSE : self::STATUS_OPEN;
    }

    /**
     * Get status text attribute
     */
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    /**
     * Get status badge class attribute
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_CLOSE => 'badge badge-danger',
            self::STATUS_OPEN => 'badge badge-success',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    /**
     * Get fiscal year name (year from start date)
     */
    public function getNameAttribute()
    {
        return $this->start_date ? $this->start_date->format('Y') : '';
    }

    /**
     * Get full fiscal year display name
     */
    public function getFullNameAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('Y') . ' - ' . $this->end_date->format('Y');
        }
        return '';
    }

    /**
     * Check if fiscal year is open
     */
    public function isOpen()
    {
        return !$this->is_closed;
    }

    /**
     * Check if fiscal year is closed
     */
    public function isClosed()
    {
        return $this->is_closed;
    }

    /**
     * Check if fiscal year is current
     */
    public function isCurrent()
    {
        return $this->is_current;
    }

    public function JournalEntry()
    {
        return $this->hasMany(JournalEntry::class);
    }
    /**
     * Relationship: User who closed the fiscal year
     */
    public function closedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'closed_by');
    }
}
