<?php

namespace Modules\HR\App\Models;

use App\Models\Employee;
use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class HrPost extends Model
{
    use \App\Traits\BelongsToBranch;

    use SoftDeletes, Translatable;

    public $table = 'hr_posts';

    public $translatedAttributes = ['title', 'body'];

    protected $translationForeignKey = 'hr_post_id';

    public $translationModel = HrPostTranslation::class;

    protected $fillable = [
        'type',
        'status',
        'flage',
        'employee_id',
        'department_id',
        'branch_id',
        'published_at',
        'expires_at',
        'is_pinned',
        'image_path',
        'created_by',
    ];

    protected $casts = [
        'employee_id' => 'array',
        'department_id' => 'array',
        'branch_id' => 'array',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_pinned' => 'boolean',
        'type' => 'integer',
        'status' => 'integer',
        'flage' => 'integer',
    ];

    const TYPE_NEWS = 1;

    const TYPE_ANNOUNCEMENT = 2;

    const STATUS_DRAFT = 1;

    const STATUS_PUBLISHED = 2;

    const FLAG_ALL = 1;

    const FLAG_EMPLOYEES = 2;

    const FLAG_DEPARTMENT = 3;

    const FLAG_BRANCHES = 4;

    public static function rules(): array
    {
        $rules = [
            'type' => 'required|integer|in:'.self::TYPE_NEWS.','.self::TYPE_ANNOUNCEMENT,
            'status' => 'required|integer|in:'.self::STATUS_DRAFT.','.self::STATUS_PUBLISHED,
            'flage' => 'required|integer|in:'.self::FLAG_ALL.','.self::FLAG_EMPLOYEES.','.self::FLAG_DEPARTMENT.','.self::FLAG_BRANCHES,
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'is_pinned' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
            'employee_id' => 'nullable|array',
            'employee_id.*' => 'integer|exists:hr_employees,id',
            'department_id' => 'nullable|array',
            'department_id.*' => 'integer|exists:hr_departments,id',
            'branch_id' => 'nullable|array',
            'branch_id.*' => 'integer|exists:branches,id',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.title'] = 'required|string|max:255';
            $rules[$locale.'.body'] = 'required|string';
        }

        return $rules;
    }

    public static function types(): array
    {
        return [
            self::TYPE_NEWS => __('hr::models/hr_posts.types.news'),
            self::TYPE_ANNOUNCEMENT => __('hr::models/hr_posts.types.announcement'),
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => __('hr::models/hr_posts.statuses.draft'),
            self::STATUS_PUBLISHED => __('hr::models/hr_posts.statuses.published'),
        ];
    }

    public static function flages(): array
    {
        return [
            self::FLAG_ALL => __('hr::models/hr_posts.flages.all'),
            self::FLAG_EMPLOYEES => __('hr::models/hr_posts.flages.employees'),
            self::FLAG_DEPARTMENT => __('hr::models/hr_posts.flages.department'),
            self::FLAG_BRANCHES => __('hr::models/hr_posts.flages.branches'),
        ];
    }

    public function getTypeTextAttribute(): string
    {
        return self::types()[$this->type] ?? '';
    }

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function getFlagTextAttribute(): string
    {
        return self::flages()[$this->flage] ?? '';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'badge badge-success',
            self::STATUS_DRAFT => 'badge badge-warning',
            default => 'badge badge-secondary',
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ANNOUNCEMENT => 'badge badge-primary',
            self::TYPE_NEWS => 'badge badge-info',
            default => 'badge badge-secondary',
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');
    }

    public function scopeVisibleToEmployee($query, ?Employee $employee)
    {
        if (! $employee) {
            return $query->whereRaw('1 = 0');
        }

        $hrEmployeeId = $employee->HrEmployee?->id;
        $departmentId = $employee->HrEmployee?->department_id;
        $branchId = $employee->branch_id;

        return $query->where(function ($q) use ($hrEmployeeId, $departmentId, $branchId) {
            $q->where('flage', self::FLAG_ALL);

            $q->orWhere(function ($sub) use ($hrEmployeeId) {
                $sub->where('flage', self::FLAG_EMPLOYEES);
                if ($hrEmployeeId) {
                    $sub->whereJsonContains('employee_id', (string) $hrEmployeeId);
                } else {
                    $sub->whereRaw('1 = 0');
                }
            });

            $q->orWhere(function ($sub) use ($departmentId) {
                $sub->where('flage', self::FLAG_DEPARTMENT);
                if ($departmentId) {
                    $sub->whereJsonContains('department_id', (string) $departmentId);
                } else {
                    $sub->whereRaw('1 = 0');
                }
            });

            $q->orWhere(function ($sub) use ($branchId) {
                $sub->where('flage', self::FLAG_BRANCHES);
                if ($branchId) {
                    $sub->whereJsonContains('branch_id', (string) $branchId);
                } else {
                    $sub->whereRaw('1 = 0');
                }
            });
        });
    }
}
