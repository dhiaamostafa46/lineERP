<?php

namespace App\Models\AccuSoft;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenters extends Model
{
    use  HasFactory, SoftDeletes, Translatable;

    protected $table = 'cost_centers';

    public $translatedAttributes = ['name', 'description'];

    public $translationModel = CostCenterTranslation::class;

    protected $fillable = [
        'code',
        'parent_id',
        'level',
        'is_leaf',
        'status',
        'attributes',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    protected $casts = [
        'level' => 'integer',
        'is_leaf' => 'boolean',
        'status' => 'boolean',
        'attributes' => 'array',
    ];

    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->parent_id) {
                $parent = self::find($model->parent_id);
                $model->level = $parent ? $parent->level + 1 : 1;
            } else {
                $model->level = 1;
            }
        });

        static::created(function ($model) {
            if ($model->parent_id) {
                $parent = self::find($model->parent_id);
                if ($parent && $parent->is_leaf) {
                    $parent->is_leaf = false;
                    $parent->saveQuietly();
                }
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('parent_id')) {
                if ($model->parent_id) {
                    $parent = self::find($model->parent_id);
                    $model->level = $parent ? $parent->level + 1 : 1;
                } else {
                    $model->level = 1;
                }

                if ($model->parent_id) {
                    $newParent = self::find($model->parent_id);
                    if ($newParent && $newParent->is_leaf) {
                        $newParent->is_leaf = false;
                        $newParent->saveQuietly();
                    }
                }
            }
        });

        static::updated(function ($model) {
            if ($model->wasChanged('parent_id') || $model->wasChanged('code') || $model->wasChanged('level')) {
                if ($model->wasChanged('parent_id')) {
                    $oldParentId = $model->getOriginal('parent_id');
                    if ($oldParentId) {
                        $oldParent = self::find($oldParentId);
                        if ($oldParent && $oldParent->children()->count() === 0) {
                            $oldParent->is_leaf = true;
                            $oldParent->saveQuietly();
                        }
                    }
                }

                // Recursively update descendants
                $oldCode = $model->getOriginal('code');
                $newCode = $model->code;
                $model->updateDescendants($oldCode, $newCode, $model->level);
            }
        });

        static::deleting(function ($model) {
            if ($model->children()->count() > 0) {
                throw new \Exception(__('accusoft::messages.cannot_delete_costcenter_has_children'));
            }
            if (\DB::table('journal_entry_details')->where('cost_center_id', $model->id)->exists()) {
                throw new \Exception(__('accusoft::messages.cannot_delete_costcenter_has_journals'));
            }
        });

        static::deleted(function ($model) {
            if ($model->parent_id) {
                $parent = self::find($model->parent_id);
                if ($parent && $parent->children()->count() === 0) {
                    $parent->is_leaf = true;
                    $parent->saveQuietly();
                }
            }
        });
    }

    public function getDescendantIds()
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantIds());
        }
        return $ids;
    }

    public function updateDescendants($oldCode, $newCode, $parentLevel)
    {
        foreach ($this->children as $child) {
            if ($oldCode !== $newCode && str_starts_with($child->code, $oldCode)) {
                $suffix = substr($child->code, strlen($oldCode));
                $child->code = $newCode . $suffix;
            }
            $child->level = $parentLevel + 1;
            $child->saveQuietly();

            $child->updateDescendants($oldCode, $newCode, $child->level);
        }
    }

    public function parent()
    {
        return $this->belongsTo(CostCenters::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CostCenters::class, 'parent_id');
    }

    public static function rules($id = null)
    {
        $rules = [
            'code' => 'required|string|unique:cost_centers,code,'.$id,
            'parent_id' => 'nullable|exists:cost_centers,id',
            'status' => 'boolean',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
            $rules[$locale.'.description'] = 'nullable|string';
        }

        return $rules;
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => __('lang.active'),
            self::STATUS_INACTIVE => __('lang.inactive'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * الحصول على قيمة من مصفوفة attributes
     *
     * @param  string  $key  المفتاح
     * @param  mixed  $default  القيمة الافتراضية
     */
    public function getMeta($key, $default = null)
    {
        return data_get($this->getAttribute('attributes'), $key, $default);
    }

    /**
     * تعيين قيمة في مصفوفة attributes
     *
     * @param  string  $key  المفتاح
     * @param  mixed  $value  القيمة
     */
    public function setMeta($key, $value)
    {
        $attributes = $this->getAttribute('attributes') ?? [];
        data_set($attributes, $key, $value);
        $this->setAttribute('attributes', $attributes);
    }

    /**
     * توليد كود مراكز التكلفة بالتنسيق CC-XX-XX
     * المستوى الأول: CC-01, CC-02, CC-03
     * المستوى الثاني: CC-01-01, CC-01-02, CC-02-01
     * المستوى الثالث: CC-01-01-01, CC-01-02-01
     *
     * @param  int|null  $parentId  معرف المركز الأب
     * @return string الكود المولد
     */
    public static function generateCode($parentId = null)
    {
        if (! empty($parentId)) {
            // محاولة البحث بالـ ID أولاً، وإذا لم يوجد نبحث بالكود
            $parent = self::find($parentId) ?? self::where('code', $parentId)->first();
            
            if ($parent) {
                $parentCode = $parent->code;

                // جلب آخر ابن تابع لهذا الأب فقط ويبدأ بكود الأب مع الشرطة
                $lastChild = self::withTrashed()
                                 ->where('parent_id', $parent->id)
                                 ->where('code', 'like', $parentCode . '-%')
                                 ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) desc')
                                 ->first();

                if ($lastChild) {
                    // استخراج الرقم الذي يلي كود الأب (نتجاوز طول كود الأب + 1 للشرطة)
                    $lastNumber = (int) substr($lastChild->code, strlen($parentCode) + 1);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                // دمج كود الأب مع الرقم الجديد بصيغة -01, -02
                $nextCode = $parentCode . '-' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
                
                // نضمن ألا يكون الكود المولد محجوزاً لحساب آخر (حتى وإن كان تابعاً لأب آخر بالخطأ)
                while (self::withTrashed()->where('code', (string) $nextCode)->exists()) {
                    $newNumber++;
                    $nextCode = $parentCode . '-' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
                }
                
                return $nextCode;
            }
        }

        // كود افتراضي للجذر: CC-01, CC-02...
        $lastRootChild = self::withTrashed()
                             ->whereNull('parent_id')
                             ->where('code', 'like', 'CC-%')
                             ->orderByRaw('CAST(SUBSTRING(code, 4) AS UNSIGNED) desc')
                             ->first();
                             
        if ($lastRootChild) {
            $lastNumber = (int) substr($lastRootChild->code, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $nextCode = 'CC-' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
        
        while (self::withTrashed()->where('code', (string) $nextCode)->exists()) {
            $newNumber++;
            $nextCode = 'CC-' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
        }
        
        return $nextCode;
    }

    /**
     * الحصول على مستوى المركز بناءً على الكود
     *
     * @return int
     */
    public function getCodeLevel()
    {
        return substr_count($this->code, '-');
    }

    /**
     * التحقق من صحة تنسيق الكود
     *
     * @param  string  $code
     * @return bool
     */
    public static function isValidCodeFormat($code)
    {
        // التحقق من التنسيق: CC-XX أو CC-XX-XX أو CC-XX-XX-XX
        return preg_match('/^CC(-\d{2})+$/', $code);
    }
}
