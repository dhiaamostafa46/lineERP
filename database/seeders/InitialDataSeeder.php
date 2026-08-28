<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccuSoft\TaxAccount;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Unit;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\invApp\InvSupplier;
use App\Models\invApp\InvCustomer;
use App\Models\StoreApp\Store;
use App\Models\AccuSoft\AccountMapping;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $taxes = [
            [
                'ar' => ['name' => 'ضريبة القيمة المضافة شامل 15%'],
                'en' => ['name' => 'Vat Included 15%'],
                'rate' => 15,
                'status' => TaxAccount::STATUS_ACTIVE,
            ],
            [
                'ar' => ['name' => 'معفي من الضريبة'],
                'en' => ['name' => 'Tax Exempt'],
                'rate' => 0,

                'status' => TaxAccount::STATUS_ACTIVE,
            ],
            [
                'ar' => ['name' => 'غير خاضع للضريبة'],
                'en' => ['name' => 'Non-taxable'],
                'rate' => 0,

                'status' => TaxAccount::STATUS_ACTIVE,
            ],
        ];

        foreach ($taxes as $tax) {
            TaxAccount::Create(
                $tax
            );
        }

        // Add Default Categories
        $categories = [
            [
                'ar' => ['name' => 'عام'],
                'en' => ['name' => 'General'],
                'type' => Category::TYPE_Visible,
                'status' => Category::STATUS_ACTIVE,
                'is_virtual' => Category::VIRTUAL_TRUE,
                'user_id' => 1,
                'org_id' => 0,
            ],
            [
                'ar' => ['name' => 'خدمات'],
                'en' => ['name' => 'Services'],
                'type' => Category::TYPE_Visible,
                'status' => Category::STATUS_ACTIVE,
                'is_virtual' => Category::VIRTUAL_TRUE,
                'user_id' => 1,
                'org_id' => 0,
            ],
        ];

        foreach ($categories as $categoryData) {
            $exists = Category::whereTranslation('name', $categoryData['en']['name'], 'en')->exists();
            if (!$exists) {
                Category::create($categoryData);
            }
        }

        // Add Default Units
        $units = [
            [
                'ar' => ['name' => 'حبة'],
                'en' => ['name' => 'Piece'],
                'conversion_factor' => 1,
                'is_base' => 1,
                'status' => Unit::STATUS_ACTIVE,
                'is_virtual' => Unit::VIRTUAL_TRUE,
                'user_id' => 1,
                'org_id' => 0,
            ],
            [
                'ar' => ['name' => 'كرتون'],
                'en' => ['name' => 'Box'],
                'conversion_factor' => 1,
                'is_base' => 1,
                'status' => Unit::STATUS_ACTIVE,
                'is_virtual' => Unit::VIRTUAL_TRUE,
                'user_id' => 1,
                'org_id' => 0,
            ],
            [
                'ar' => ['name' => 'كيلو'],
                'en' => ['name' => 'Kg'],
                'conversion_factor' => 1,
                'is_base' => 1,
                'status' => Unit::STATUS_ACTIVE,
                'is_virtual' => Unit::VIRTUAL_TRUE,
                'user_id' => 1,
                'org_id' => 0,
            ],
            [
                'ar' => ['name' => 'لتر'],
                'en' => ['name' => 'Liter'],
                'conversion_factor' => 1,
                'is_base' => 1,
                'status' => Unit::STATUS_ACTIVE,
                'is_virtual' => Unit::VIRTUAL_TRUE,
                'user_id' => 1,
                'org_id' => 0,
            ],
        ];

        foreach ($units as $unitData) {
            $exists = Unit::whereTranslation('name', $unitData['en']['name'], 'en')->exists();
            if (!$exists) {
                Unit::create($unitData);
            }
        }

        // 1. إضافة مورد نقدي افتراضي وحسابه المالي
        if (!InvSupplier::exists()) {
            $parentSupplierId = AccountMapping::getAccountId('supplier');
            if ($parentSupplierId) {
                $parentAccount = TreeAccounts::find($parentSupplierId);
                $supplierAccount = TreeAccounts::create([
                    'parent_id' => $parentSupplierId,
                    'account_type' => TreeAccounts::ACCOUNT_TYPE_SUPPLIERS,
                    'type' => TreeAccounts::TYPE_CREDIT,
                    'is_leaf' => true,
                    'level' => $parentAccount ? $parentAccount->level + 1 : 1,
                    'status' => TreeAccounts::STATUS_ACTIVE,
                    'code' => TreeAccounts::generateCode($parentSupplierId),
                    'ar' => ['name' => 'مورد نقدي'],
                    'en' => ['name' => 'Cash Supplier'],
                ]);

                InvSupplier::create([
                    'ar' => ['name' => 'مورد نقدي'],
                    'en' => ['name' => 'Cash Supplier'],
                    'status' => 1,
                    'tree_account_id' => $supplierAccount->id,
                    
                    'phone' => '0000000000',
                    'country' => 'SA',
                ]);
            }
        }

        // 2. إضافة عميل نقدي افتراضي وحسابه المالي
        if (class_exists('App\Models\invApp\InvCustomer') && !InvCustomer::exists()) {
            $parentCustomerId = AccountMapping::getAccountId('customer');
            if ($parentCustomerId) {
                $parentAccount = TreeAccounts::find($parentCustomerId);
                $customerAccount = TreeAccounts::create([
                    'parent_id' => $parentCustomerId,
                    'account_type' => TreeAccounts::ACCOUNT_TYPE_CUSTOMERS,
                    'type' => TreeAccounts::TYPE_DEBIT,
                    'is_leaf' => true,
                    'level' => $parentAccount ? $parentAccount->level + 1 : 1,
                    'status' => TreeAccounts::STATUS_ACTIVE,
                    'code' => TreeAccounts::generateCode($parentCustomerId),
                    'ar' => ['name' => 'عميل نقدي'],
                    'en' => ['name' => 'Cash Customer'],
                ]);

                InvCustomer::create([
                    'ar' => ['name' => 'عميل نقدي'],
                    'en' => ['name' => 'Cash Customer'],
                    'status' => 1,
                    'tree_account_id' => $customerAccount->id,

                    'phone' => '0000000000',
                    'country' => 'SA',
                ]);
            }
        }

    }
}
