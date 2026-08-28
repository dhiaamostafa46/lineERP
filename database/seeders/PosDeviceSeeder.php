<?php

namespace Database\Seeders;

use App\Models\invApp\InvCustomer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Pos\App\Models\PosDevice;
use Modules\Pos\App\Models\PosPaymentMethod;
use App\Models\StoreApp\Store;
use App\Models\Branch;
use App\Models\AccuSoft\AccountMapping;
use App\Models\User;

class PosDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->shouldSkipSeeding()) {
            return;
        }

        DB::transaction(function () {
            $device = $this->createMainDevice();
            $this->createDefaultPaymentMethods($device);
        });
    }

    /**
     * التحقق مما إذا كان يجب تجاوز عملية الـ Seeding.
     */
    private function shouldSkipSeeding(): bool
    {
        return PosDevice::exists();
    }

    /**
     * إنشاء نقطة البيع الرئيسية.
     */
    private function createMainDevice(): PosDevice
    {
        $branch = Branch::first();
        $store = Store::where('branch_id', $branch?->id)->first() ?? Store::first();
        $defauLCustomer = InvCustomer::where('branch_id', $branch?->id)->first() ?? User::first();
        $attributes = array_merge(
            $this->getBaseDeviceAttributes($store?->id, $branch?->id),
            $this->getDefaultAccounts()
        );

        return PosDevice::create($attributes);
    }

    /**
     * الحصول على خصائص الجهاز الأساسية.
     */
    private function getBaseDeviceAttributes(?int $storeId, ?int $branchId): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => 'Main POS Device (نقطة البيع الرئيسية)',
            'store_id' => $storeId,
            'branch_id' => $branchId,
            
            // الحالة النشطة
            'is_active' => true,
            
            // سياسات الوردية والترحيل
            'auto_journal_entry' => false, // الترحيل المجمع للوردية افتراضياً
            
            // تفعيل الطباعة والدرج
            'auto_print_receipt' => true,
            'print_copies_count' => 1,
            
            // إعدادات الضريبة
            'prices_include_tax' => false,
            'send_to_zatca_phase2' => false,
            
            // سياسات البيع
            'allow_negative_stock' => false,
            'allow_price_modification' => false,
            'allow_discount_modification' => true,
            'show_available_qty' => true,
            'enable_pos_returns' => true,
            
            // خيارات الباركود (إن وجدت في قاعدة البيانات مستقبلاً)
            // 'barcode_type' => 'CODE128',
        ];
    }

    /**
     * الحصول على الحسابات الافتراضية للتوجيه المحاسبي.
     */
    private function getDefaultAccounts(): array
    {
        $defaultCustomer = DB::table('inv_customers')->first();

        return [
            'default_customer_id' => $defaultCustomer?->id,
            'main_safe_account_id' => $this->getMappedOrTypeAccount('cash', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_TREASURY),
            'sales_account_id' => $this->getMappedOrTypeAccount('sales', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_SALES),
            'discount_account_id' => $this->getMappedOrTypeAccount('sales_discount', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_EXPENSE) ?? $this->getMappedOrTypeAccount('sales', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_SALES),
            'shortage_account_id' => $this->getMappedOrTypeAccount('inventory_adjustment_loss', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_EXPENSE),
            'overage_account_id' => $this->getMappedOrTypeAccount('inventory_adjustment_profit', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_REVENUE),
            'vat_account_id' => $this->getMappedOrTypeAccount('sales_tax', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_LIABILITY) ?? $this->getMappedOrTypeAccount('tax', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_LIABILITY),
            'cogs_account_id' => $this->getMappedOrTypeAccount('cogs', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES),
            'inventory_account_id' => $this->getMappedOrTypeAccount('inventory', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_INVENTORY),
        ];
    }

    /**
     * إضافة طرق الدفع الافتراضية للجهاز الممرر.
     */
    private function createDefaultPaymentMethods(PosDevice $device): void
    {
        $cashAccountId = $this->getMappedOrTypeAccount('cash', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_TREASURY);
        $bankAccountId = $this->getMappedOrTypeAccount('bank', \App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_BANK);

        $paymentMethods = [];
        $paymentTypes = PosPaymentMethod::types();

        foreach ($paymentTypes as $type => $name) {
            $accountId = null;

            if ($type === PosPaymentMethod::TYPE_CASH) {
                $accountId = $cashAccountId;
            } elseif (in_array($type, ['bank', 'card', 'transfer'])) {
                $accountId = $bankAccountId;
            }

            if (in_array($type, [PosPaymentMethod::TYPE_CREDIT, 'installment'])) {
                $accountId = null;
            }

            $paymentMethods[] = [
                'name' => $name,
                'type' => $type,
                'account_id' => $accountId,
                'is_active' => true,
                'is_default' => $type === PosPaymentMethod::TYPE_CASH
            ];
        }

        $device->paymentMethods()->createMany($paymentMethods);
    }

    /**
     * Get Mapped Account or fallback to Type
     */
    private function getMappedOrTypeAccount(string $mappingKey, int $accountType): ?int
    {
        return AccountMapping::getAccountId($mappingKey) 
            ?? (\App\Models\AccuSoft\TreeAccounts::where('account_type', $accountType)->where('status', 1)->first()->id ?? null);
    }
}
