<?php

namespace Modules\Finance\App\Models;

use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Database\Eloquent\Builder;

class Bank extends TreeAccounts
{



    /**
     * Booted
     */
    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope('banks', function (Builder $query) {
            $query->where('account_type', self::ACCOUNT_TYPE_BANK);
        });

        static::creating(function ($model) {
            // تعيين القيم الافتراضية الخاصة بالحسابات البنكية عند الإنشاء
            $model->account_type = self::ACCOUNT_TYPE_BANK;
            $model->type = self::TYPE_DEBIT; // البنوك أصول بطبيعة مدينة
            $model->status = self::STATUS_ACTIVE;
            $model->is_leaf = true; // البنوك دائماً حسابات نهائية

            if (empty($model->code)) {
                $model->code = self::generateCode($model->parent_id);
            }
        });
    }

    // تم إزالة scopeActive لأنه موروث من الموديل الأب TreeAccounts

    public function scopeMainBanks($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * الوصول إلى الرصيد المخزن في مصفوفة الـ attributes (JSON)
     */
    public function getBalanceAttribute(): float
    {
        return (float) $this->getMeta('balance', 0);
    }

    /**
     * الحقول المضافة للبنك (مخزنة داخل حقل attributes)
     */
    public function getBankNameAttribute() { return $this->getMeta('bank_name'); }
    public function setBankNameAttribute($value) { $this->setMeta('bank_name', $value); }

    public function getAccountNumberAttribute() { return $this->getMeta('account_number'); }
    public function setAccountNumberAttribute($value) { $this->setMeta('account_number', $value); }

    public function getIbanAttribute() { return $this->getMeta('iban'); }
    public function setIbanAttribute($value) { $this->setMeta('iban', $value); }

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
        // $methods = config('payment_methods');
        // $all = ($methods['cash'] ?? []) + ($methods['bank'] ?? []) + ($methods['other'] ?? []);
        return "ييييييييي";
    }

    /**
     * دالة الإيداع البنكي
     */
    public function deposit(float $amount, string $description = '')
    {
        if ($amount <= 0) {
            throw new \Exception(__('Invalid amount for deposit'));
        }

        $newBalance = $this->balance + $amount;
        $this->setMeta('balance', $newBalance);

        return $this->save();
    }

    /**
     * دالة السحب البنكي
     */
    public function withdraw(float $amount, string $description = '')
    {
        if ($amount <= 0) {
            throw new \Exception(__('Invalid amount for withdrawal'));
        }

        if ($this->balance < $amount) {
            throw new \Exception(__('Insufficient balance in this bank account'));
        }

        $newBalance = $this->balance - $amount;
        $this->setMeta('balance', $newBalance);

        return $this->save();
    }

    /**
     * دالة التحقق من نوع الحساب
     */
    public function getIsBankAttribute()
    {
        return $this->account_type == self::ACCOUNT_TYPE_BANK;
    }
}
