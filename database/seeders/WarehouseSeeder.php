<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreApp\Store;
use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إضافة المستودع الرئيسي وحسابه المالي إذا لم يكن موجوداً
        if (!Store::exists()) {
            $parentInventoryId = AccountMapping::getAccountId('inventory');
            
            if (!$parentInventoryId) {
                $this->command->error("Parent Account mapping for 'inventory' not found. Please run AccountMappingSeeder first.");
                return;
            }

            DB::transaction(function () use ($parentInventoryId) {
                // إنشاء الحساب المالي للمستودع الرئيسي كحساب فرعي من "المخزين"
                $storeAccount = TreeAccounts::create([
                    'parent_id' => $parentInventoryId,
                    'account_type' => TreeAccounts::ACCOUNT_TYPE_INVENTORY,
                    'type' => TreeAccounts::TYPE_DEBIT,
                    'is_leaf' => true,
                    'status' => TreeAccounts::STATUS_ACTIVE,
                    'code' => TreeAccounts::generateCode($parentInventoryId),
                    'ar' => ['name' => 'المستودع الرئيسي'],
                    'en' => ['name' => 'Main Store'],
                ]);

                // إنشاء كائن المستودع وربطه بالحساب المالي
                Store::create([
                    'ar' => [
                        'name' => 'المستودع الرئيسي', 
                        'address' => 'المنطقة الرئيسية'
                    ],
                    'en' => [
                        'name' => 'Main Store', 
                        'address' => 'Main Area'
                    ],
                    'status' => Store::STATUS_ACTIVE,
                    'type' => Store::TYPE_MAIN,
                    'org_id' => 1,
                    'branch_id' => 1,
                    'tree_account_id' => $storeAccount->id,
                ]);

                $this->command->info('Main Warehouse and its financial account created successfully.');
            });
        } else {
            $this->command->info('Main Warehouse already exists. Skipping...');
        }
    }
}
