<?php

namespace Modules\HR\App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;

class HrAttendancePolicy extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public $table = 'hr_attendance_policies';

    public $translatedAttributes = ['name'];

    protected $fillable = [
        'description',
        'is_automatic',
        'scope',
        'scope_ids',
        'start_date',
        'end_date',
        'status',
        'type',
        'salary_effect',
        'settings',
        'calculation_type',
    ];


    protected $casts = [
        'is_automatic' => 'boolean',
        'scope' => 'integer',
        'scope_ids' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'integer',
        'type' => 'integer',
        'salary_effect' => 'array',
        'settings' => 'array',
        'calculation_type' => 'integer',
    ];

    // Constants for Status
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    const AUTOMATIC_TRUE = 1;
    const AUTOMATIC_FALSE = 0;

    const CALCULATION_TYPE_DAY = 1;
    const CALCULATION_TYPE_HOUR = 2;

    // Constants for Type
    const TYPE_ABSENCE = 1;
    const TYPE_LATE = 2;
    const TYPE_OVERTIME = 4;

    // Constants for Scope
    const SCOPE_EMPLOYEE = 1;
    const SCOPE_DEPARTMENT = 2;
    const SCOPE_JOB = 3;
    const SCOPE_BRANCH = 4;

    public static function rules()
    {
        $rules = [
            'description' => 'nullable|string',
            'is_automatic' => 'boolean',
            'scope' => 'required|integer|in:' . implode(',', array_keys(self::scopes())),
            'scope_ids' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|integer|in:' . implode(',', array_keys(self::statuses())),
            'type' => 'required|integer|in:' . implode(',', array_keys(self::types())),
            'settings' => 'nullable|array',
            'calculation_type' => 'required|integer|in:' . implode(',', array_keys(self::calculationTypes())),
            'salary_effect' => 'nullable|array',
            'salary_effect.basic' => 'nullable|boolean',
            'salary_effect.allowances' => 'nullable|array',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        return $rules;
    }

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public static function automatics()
    {
        return [
            self::AUTOMATIC_FALSE => __('lang.no'),
            self::AUTOMATIC_TRUE => __('lang.yes'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_ABSENCE => __('hr::models/hr_attendance_policies.types.absence'),
            self::TYPE_LATE => __('hr::models/hr_attendance_policies.types.late'),
            self::TYPE_OVERTIME => __('hr::models/hr_attendance_policies.types.overtime'),
        ];
    }

    public static function scopes()
    {
        return [
            self::SCOPE_EMPLOYEE => __('hr::models/hr_attendance_policies.scopes.employee'),
            self::SCOPE_DEPARTMENT => __('hr::models/hr_attendance_policies.scopes.department'),
            self::SCOPE_JOB => __('hr::models/hr_attendance_policies.scopes.job'),
            self::SCOPE_BRANCH => __('hr::models/hr_attendance_policies.scopes.branch'),
        ];
    }

    public static function calculationTypes()
    {
        return [
            self::CALCULATION_TYPE_DAY => __('hr::models/hr_attendance_policies.calculation_types.day'),
            self::CALCULATION_TYPE_HOUR => __('hr::models/hr_attendance_policies.calculation_types.hour'),
        ];
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'badge badge-success',
            self::STATUS_INACTIVE => 'badge badge-danger',
            default => 'badge badge-secondary',
        };
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? '';
    }

    public function getScopeTextAttribute()
    {
        return self::scopes()[$this->scope] ?? '';
    }

    public function getCalculationTypeTextAttribute()
    {
        return self::calculationTypes()[$this->calculation_type] ?? '';
    }

    public function getIsAutomaticTextAttribute()
    {
        return self::automatics()[$this->is_automatic] ?? '';
    }

    // ==================== Accessors للبيانات المنسقة ====================

    /**
     * Get formatted scope IDs as comma-separated string
     */
    public function getFormattedScopeIdsAttribute()
    {
        if (empty($this->scope_ids)) {
            return '-';
        }

        // تحويل المصفوفة المتداخلة إلى مصفوفة بسيطة
        $ids = collect($this->scope_ids)->flatten()->filter()->toArray();

        return implode(', ', $ids);
    }

    /**
     * Get scope IDs as simple array
     */
    public function getScopeIdsListAttribute()
    {
        if (empty($this->scope_ids)) {
            return [];
        }

        return collect($this->scope_ids)->flatten()->filter()->toArray();
    }

    /**
     * Get formatted settings for display
     */
    public function getFormattedSettingsAttribute()
    {
        if (empty($this->settings)) {
            return [];
        }

        $formatted = [];

        // إذا كانت السياسة من نوع Late (التأخير)
        if ($this->type == self::TYPE_LATE && isset($this->settings['delay'])) {
            $formatted['delay'] = [
                '0-15 دقيقة' => $this->settings['delay']['0_15']['daily'] ?? [],
                '15-30 دقيقة' => $this->settings['delay']['15_30']['daily'] ?? [],
                '30-60 دقيقة' => $this->settings['delay']['30_60']['daily'] ?? [],
                'أكثر من 60 دقيقة' => $this->settings['delay']['60_plus']['daily'] ?? [],
                'خروج مبكر 15 دقيقة' => $this->settings['delay']['early_15']['daily'] ?? [],
                'خروج مبكر أكثر من 15 دقيقة' => $this->settings['delay']['early_15_plus']['daily'] ?? [],
            ];
        }

        // إذا كانت السياسة من نوع Absence (الغياب)
        if ($this->type == self::TYPE_ABSENCE && isset($this->settings['absence'])) {
            $formatted['absence'] = $this->settings['absence'];
        }

        // إذا كانت السياسة من نوع Overtime (الوقت الإضافي)
        if ($this->type == self::TYPE_OVERTIME && isset($this->settings['overtime_rate'])) {
            $formatted['overtime_rate'] = $this->settings['overtime_rate'] . '%';
        }

        return $formatted;
    }

    /**
     * Get delay settings in readable format
     */
    public function getDelaySettingsAttribute()
    {
        if ($this->type != self::TYPE_LATE || empty($this->settings['delay'])) {
            return null;
        }

        $delays = [];
        $delayRanges = [
            '0_15' => '0-15 دقيقة',
            '15_30' => '15-30 دقيقة',
            '30_60' => '30-60 دقيقة',
            '60_plus' => 'أكثر من 60 دقيقة',
            'early_15' => 'خروج مبكر 15 دقيقة',
            'early_15_plus' => 'خروج مبكر أكثر من 15 دقيقة',
        ];

        foreach ($delayRanges as $key => $label) {
            if (isset($this->settings['delay'][$key]['daily'])) {
                $delays[$label] = [
                    'المرة الأولى' => $this->settings['delay'][$key]['daily']['first'] ?? '-',
                    'المرة الثانية' => $this->settings['delay'][$key]['daily']['second'] ?? '-',
                    'المرة الثالثة' => $this->settings['delay'][$key]['daily']['third'] ?? '-',
                    'المرة الرابعة' => $this->settings['delay'][$key]['daily']['fourth'] ?? '-',
                ];
            }
        }

        return $delays;
    }

    /**
     * Get absence settings
     */
    public function getAbsenceSettingsAttribute()
    {
        if ($this->type != self::TYPE_ABSENCE || empty($this->settings['absence'])) {
            return null;
        }

        return [
            'المرة الأولى' => $this->settings['absence']['first'] ?? '-',
            'المرة الثانية' => $this->settings['absence']['second'] ?? '-',
            'المرة الثالثة' => $this->settings['absence']['third'] ?? '-',
            'المرة الرابعة' => $this->settings['absence']['fourth'] ?? '-',
        ];
    }

    /**
     * Get overtime rate
     */
    public function getOvertimeRateAttribute()
    {
        if ($this->type != self::TYPE_OVERTIME || empty($this->settings['overtime_rate'])) {
            return null;
        }

        return $this->settings['overtime_rate'] . '%';
    }

    // ==================== Dynamic Relations حسب Scope ====================

    /**
     * Get the related entities based on scope type
     */
    public function getScopeEntitiesAttribute()
    {
        if (empty($this->scope_ids_list)) {
            return collect([]);
        }

        $ids = $this->scope_ids_list;

        return match($this->scope) {
            self::SCOPE_EMPLOYEE => \Modules\HR\App\Models\HrEmployee::whereIn('id', $ids)->get(),
            self::SCOPE_DEPARTMENT => \Modules\HR\App\Models\HrDepartment::whereIn('id', $ids)->get(),
            self::SCOPE_JOB => \Modules\HR\App\Models\HrJob::whereIn('id', $ids)->get(),
            self::SCOPE_BRANCH => Branch::whereIn('id', $ids)->get(),
            default => collect([]),
        };
    }

    /**
     * Get formatted names of scope entities
     */
    public function getScopeEntitiesNamesAttribute()
    {
        $entities = $this->scope_entities;

        if ($entities->isEmpty()) {
            return '-';
        }

        return $entities->pluck('name')->implode(', ');
    }

    /**
     * Get scope entities with IDs for display
     */
    public function getScopeEntitiesWithIdsAttribute()
    {
        $entities = $this->scope_entities;

        if ($entities->isEmpty()) {
            return [];
        }

        return $entities->map(function($entity) {
            return [
                'id' => $entity->id,
                'name' => $entity->name,
            ];
        })->toArray();
    }

    /**
     * Get raw settings value (useful for debugging)
     */
    public function getRawSettingsAttribute()
    {
        return $this->settings;
    }

    /**
     * Get raw scope_ids value (useful for debugging)
     */
    public function getRawScopeIdsAttribute()
    {
        return $this->scope_ids;
    }

    /**
     * Check if entity is in scope
     */
    public function isInScope($entityId): bool
    {
        return in_array($entityId, $this->scope_ids_list);
    }

    /**
     * Get count of scope entities
     */
    public function getScopeEntitiesCountAttribute()
    {
        return count($this->scope_ids_list);
    }

    public function getSalaryEffectBasicAttribute()
    {
        return $this->salary_effect['basic'] ?? false;
    }

    public function getSalaryEffectAllowancesAttribute()
    {
        return $this->salary_effect['allowances'] ?? [];
    }

    public function getSalaryEffectAllowancesModelsAttribute()
    {
        if (empty($this->salary_effect_allowances)) {
            return collect([]);
        }
        return \Modules\HR\App\Models\HrAllowance::whereIn('id', $this->salary_effect_allowances)->get();
    }
}
