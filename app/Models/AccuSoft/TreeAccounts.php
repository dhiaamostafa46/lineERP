<?php

namespace App\Models\AccuSoft;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreeAccounts extends Model
{
    use  HasFactory, SoftDeletes, Translatable;

    protected $table = 'tree_accounts';

    public $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'code',
        'account_type', // 1=asset, 2=liability, 3=equity, 4=revenue, 5=expense, 6=cost_of_sales
        'parent_id',
        'level',
        'is_leaf',
        'status',
        'is_system',
        'attributes',
        'type', // 1=debit, 2=credit
        'use_cost_center',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    protected $casts = [
        'account_type' => 'integer',
        'level' => 'integer',
        'is_leaf' => 'boolean',
        'status' => 'boolean',
        'is_system' => 'boolean',
        'attributes' => 'array',
        'type' => 'integer',
        'use_cost_center' => 'boolean',
    ];

    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    const TYPE_DEBIT = 1;

    const TYPE_CREDIT = 2;

    const ACCOUNT_TYPE_ASSET = 1;

    const ACCOUNT_TYPE_LIABILITY = 2;

    const ACCOUNT_TYPE_EQUITY = 3;

    const ACCOUNT_TYPE_REVENUE = 4;

    const ACCOUNT_TYPE_EXPENSE = 5;

    const ACCOUNT_TYPE_COST_OF_SALES = 6;

    const ACCOUNT_TYPE_SUPPLIERS = 7;

    const ACCOUNT_TYPE_TREASURY = 8;

    const ACCOUNT_TYPE_BANK = 9;

    const ACCOUNT_TYPE_INVENTORY = 10;

    const ACCOUNT_TYPE_CUSTOMERS = 11;

    const ACCOUNT_TYPE_SALES = 12;

    const ACCOUNT_TYPE_PURCHASES = 13;

    const ACCOUNT_TYPE_FIXED_ASSET = 14;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Automatically determine the exact level based on parent.
            if ($model->parent_id) {
                $parent = self::find($model->parent_id);
                $model->level = $parent ? $parent->level + 1 : 1;
            } else {
                $model->level = 1;
            }
        });

        static::created(function ($model) {
            // Update parent is_leaf flag to false since it just gained a child.
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
                // Automatically determine the exact level based on parent.
                if ($model->parent_id) {
                    $parent = self::find($model->parent_id);
                    $model->level = $parent ? $parent->level + 1 : 1;
                } else {
                    $model->level = 1;
                }

                // Update new parent's is_leaf flag to false since it gains a child.
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
                // Adjust is_leaf for old parent if parent_id changed
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
                throw new \Exception(__('accusoft::messages.cannot_delete_account_has_children'));
            }
            if (\DB::table('journal_entry_details')->where('tree_account_id', $model->id)->exists()) {
                throw new \Exception(__('accusoft::messages.cannot_delete_account_has_journals'));
            }
            // Check other potential tables
            $tablesToCheck = [
                'bonds' => ['fund_account_id', 'contact_account_id'],
                'assets' => ['asset_account_id', 'depreciation_expense_account_id', 'accumulated_depreciation_account_id'],
                'sales_invoices' => ['account_id'],
                'purchase_invoices' => ['account_id'],
                'purchase_orders' => ['account_id'],
                'customers' => ['tree_account_id'],
                'suppliers' => ['tree_account_id'],
                'warehouses' => ['tree_account_id'],
            ];
            foreach ($tablesToCheck as $table => $columns) {
                if (\Schema::hasTable($table)) {
                    foreach ($columns as $column) {
                        if (\Schema::hasColumn($table, $column) && \DB::table($table)->where($column, $model->id)->exists()) {
                            throw new \Exception(__('accusoft::messages.cannot_delete_account_used_in_table', ['table' => $table]));
                        }
                    }
                }
            }
        });

        static::deleted(function ($model) {
            // If the parent no longer has any children, set is_leaf to true.
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
        return $this->belongsTo(TreeAccounts::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TreeAccounts::class, 'parent_id');
    }

    public static function rules($id = null)
    {
        $rules = [
            'code' => 'required|unique:tree_accounts,code,'.$id,
            'account_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14',
            'parent_id' => 'nullable|exists:tree_accounts,id',
            'type' => 'required|integer|in:1,2',
            'status' => 'boolean',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
            $rules[$locale.'.description'] = 'nullable|string';
        }

        return $rules;
    }

    public static function accountTypes()
    {
        return [
            self::ACCOUNT_TYPE_ASSET => __('accusoft::models/as_tree_account.types.asset'),
            self::ACCOUNT_TYPE_LIABILITY => __('accusoft::models/as_tree_account.types.liability'),
            self::ACCOUNT_TYPE_EQUITY => __('accusoft::models/as_tree_account.types.equity'),
            self::ACCOUNT_TYPE_REVENUE => __('accusoft::models/as_tree_account.types.revenue'),
            self::ACCOUNT_TYPE_EXPENSE => __('accusoft::models/as_tree_account.types.expense'),
            self::ACCOUNT_TYPE_COST_OF_SALES => __('accusoft::models/as_tree_account.types.cost_of_sales'),
            self::ACCOUNT_TYPE_SUPPLIERS => __('accusoft::models/as_tree_account.types.suppliers'),
            self::ACCOUNT_TYPE_TREASURY => __('accusoft::models/as_tree_account.types.treasury'),
            self::ACCOUNT_TYPE_BANK => __('accusoft::models/as_tree_account.types.bank'),
            self::ACCOUNT_TYPE_INVENTORY => __('accusoft::models/as_tree_account.types.inventory'),
            self::ACCOUNT_TYPE_CUSTOMERS => __('accusoft::models/as_tree_account.types.customers'),
            self::ACCOUNT_TYPE_SALES => __('accusoft::models/as_tree_account.types.sales'),
            self::ACCOUNT_TYPE_PURCHASES => __('accusoft::models/as_tree_account.types.purchases'),
            self::ACCOUNT_TYPE_FIXED_ASSET => __('accusoft::models/as_tree_account.types.fixed_asset') ?? 'أصول ثابتة',
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_DEBIT => __('accusoft::models/as_tree_account.nature.debit'),
            self::TYPE_CREDIT => __('accusoft::models/as_tree_account.nature.credit'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => __('lang.active'),
            self::STATUS_INACTIVE => __('lang.inactive'),
        ];
    }

    public function getAccountTypeTextAttribute()
    {
        return self::accountTypes()[$this->account_type] ?? __('lang.unknown');
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? __('lang.unknown');
    }

    public function getUseCostCenterTextAttribute()
    {
        return $this->use_cost_center ? __('lang.yes') : __('lang.no');
    }

    public function getAccountNumberAttribute()
    {
        return $this->getMeta('account_number');
    }

    public function getIbanAttribute()
    {
        return $this->getMeta('iban');
    }

    public function getPaymentMethodAttribute()
    {
        return $this->getMeta('payment_method');
    }

    public function setPaymentMethodAttribute($value)
    {
        $this->setMeta('payment_method', $value);
    }

    public function getPaymentMethodTextAttribute()
    {
        $methods = config('payment_methods');
        $all = ($methods['cash'] ?? []) + ($methods['bank'] ?? []) + ($methods['other'] ?? []);

        return $all[$this->payment_method] ?? __('Unspecified');
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
     * @param  string  $key  المفتاح (يمكن استخدام النقطة للوصول المتداخل)
     * @param  mixed  $default  القيمة الافتراضية
     */
    public function getMeta($key, $default = null)
    {
        // نستخدم getAttribute لتجنب التعارض مع خاصية attributes المحمية في الموديل
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
     * دالة لتوليد الكود التالي بناءً على الأب
     *
     * @param  int|null  $parentId
     * @return string|int
     */
    public static function generateCode($parentId = null)
    {
        if (! empty($parentId)) {
            // محاولة البحث بالـ ID أولاً، وإذا لم يوجد نبحث بالكود
            $parent = self::find($parentId) ?? self::where('code', $parentId)->first();
            
            if ($parent) {
                $parentCode = $parent->code;

                // جلب آخر ابن تابع لهذا الأب فقط ويبدأ بكود الأب
                $lastChild = self::withTrashed()
                                 ->where('parent_id', $parent->id)
                                 ->where('code', 'like', $parentCode . '%')
                                 ->orderByRaw('LENGTH(code) desc, code desc')
                                 ->first();

                if ($lastChild) {
                    // استخراج الرقم الذي يلي كود الأب
                    $lastNumber = (int) substr($lastChild->code, strlen($parentCode));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                // دمج كود الأب مع الرقم الجديد بصيغة 01, 02 .. 10, 11
                $nextCode = $parentCode . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
                
                // نضمن ألا يكون الكود المولد محجوزاً لحساب آخر (حتى وإن كان تابعاً لأب آخر بالخطأ)
                while (self::withTrashed()->where('code', (string) $nextCode)->exists()) {
                    $newNumber++;
                    $nextCode = $parentCode . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
                }
                
                return $nextCode;
            }
        }

        // كود افتراضي للجذر
        $maxRootCode = self::withTrashed()
                           ->whereNull('parent_id')
                           ->selectRaw('MAX(CAST(code AS UNSIGNED)) as max_code')
                           ->value('max_code');
                           
        $nextCode = $maxRootCode ? $maxRootCode + 1 : 1;
        
        while (self::withTrashed()->where('code', (string) $nextCode)->exists()) {
            $nextCode++;
        }
        
        return $nextCode;
    }
}

