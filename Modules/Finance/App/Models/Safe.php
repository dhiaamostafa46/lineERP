<?php

namespace Modules\Finance\App\Models;

use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Database\Eloquent\Builder;

class Safe extends TreeAccounts
{
    // تحديد موديل الترجمة والمفتاح الأجنبي ليتم استخدام ترجمات شجرة الحسابات
    public $translationModel = \App\Models\AccuSoft\TreeAccountsTranslation::class;

    // تأكد أن هذا الاسم يطابق العمود الموجود في جدول tree_account_translations
    public $translationForeignKey = 'tree_account_id';

    /**
     * Booted
     */
    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope('safes', function (Builder $query) {
            $query->where('account_type', self::ACCOUNT_TYPE_TREASURY);
        });

        static::creating(function ($model) {
            // إجبار القيم الخاصة بالخزينة والصناديق
            $model->account_type = self::ACCOUNT_TYPE_TREASURY;
            $model->type = self::TYPE_DEBIT;
            $model->status = self::STATUS_ACTIVE;

            if (empty($model->code)) {
                $model->code = self::generateCode($model->parent_id);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeMainSafes($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * الوصول إلى الرصيد المخزن في مصفوفة attributes
     */
    public function getBalanceAttribute(): float
    {
        return (float) $this->getMeta('balance', 0);
    }

    /**
     * الشخص المسؤول عن الصندوق (مخزن في JSON)
     */
    public function getResponsiblePersonAttribute()
    {
        return $this->getMeta('responsible_person');
    }

    public function setResponsiblePersonAttribute($value)
    {
        $this->setMeta('responsible_person', $value);
    }

    /**
     * عملة الصندوق (مخزن في JSON)
     */
    public function getCurrencyAttribute()
    {
        return $this->getMeta('currency', 'SAR');
    }

    public function setCurrencyAttribute($value)
    {
        $this->setMeta('currency', $value);
    }

    public function getPaymentMethodAttribute() { return $this->getMeta('payment_method'); }
    public function setPaymentMethodAttribute($value) { $this->setMeta('payment_method', $value); }

    public function getPaymentMethodTextAttribute()
    {
        $methods = config('payment_methods');
        $all = ($methods['cash'] ?? []) + ($methods['bank'] ?? []) + ($methods['other'] ?? []);
        return $all[$this->payment_method] ?? __('Unspecified');
    }

    /**
     * دالة الإيداع في الصندوق
     * ملاحظة: في النظام المكتمل، يجب أن يتم تحديث الرصيد عبر قيود اليومية
     */
    public function deposit(float $amount, string $description = '')
    {
        if ($amount <= 0) {
            throw new \Exception(__('Invalid amount for deposit'));
        }

        $newBalance = $this->balance + $amount;
        $this->setMeta('balance', $newBalance);

        // يمكنك هنا إضافة منطق لإنشاء قيد يومي تلقائي

        return $this->save();
    }

    /**
     * دالة السحب من الصندوق
     */
    public function withdraw(float $amount, string $description = '')
    {
        if ($amount <= 0) {
            throw new \Exception(__('Invalid amount for withdrawal'));
        }

        if ($this->balance < $amount) {
            throw new \Exception(__('Insufficient balance in this safe'));
        }

        $newBalance = $this->balance - $amount;
        $this->setMeta('balance', $newBalance);

        return $this->save();
    }

    public function getIsSafeAttribute()
    {
        return $this->account_type == self::ACCOUNT_TYPE_TREASURY;
    }
}
