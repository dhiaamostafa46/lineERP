<?php

namespace Tests\Feature;

use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\TaxAccount;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Models\Branch;
use App\Models\invApp\InvCustomer;
use App\Models\invApp\InvSupplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchScopingTest extends TestCase
{
    use RefreshDatabase;

    private $branch1;

    private $branch2;

    private $user1;

    private $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create branches
        $this->branch1 = new Branch;
        $this->branch1->phone = '123456789';
        $this->branch1->translateOrNew('ar')->name = 'فرع الرياض';
        $this->branch1->translateOrNew('ar')->address = 'الرياض';
        $this->branch1->save();

        $this->branch2 = new Branch;
        $this->branch2->phone = '987654321';
        $this->branch2->translateOrNew('ar')->name = 'فرع جدة';
        $this->branch2->translateOrNew('ar')->address = 'جدة';
        $this->branch2->save();

        // Create users belonging to different branches
        $this->user1 = User::create([
            'name' => 'User Branch 1',
            'email' => 'user1@example.com',
            'phone' => '0511111111',
            'password' => 'password',
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $this->branch1->id,
        ]);

        $this->user2 = User::create([
            'name' => 'User Branch 2',
            'email' => 'user2@example.com',
            'phone' => '0522222222',
            'password' => 'password',
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $this->branch2->id,
        ]);
    }

    /**
     * Test automatic branch_id injection on creation for all 8 models.
     */
    public function test_automatic_branch_injection_on_create()
    {
        $this->actingAs($this->user1);

        // 1. TaxAccount
        $tax = new TaxAccount;
        $tax->rate = 15;
        $tax->status = TaxAccount::STATUS_ACTIVE;
        $tax->translateOrNew('ar')->name = 'ضريبة القيمة المضافة';
        $tax->save();
        $this->assertEquals($this->branch1->id, $tax->branch_id);

        // 2. CostCenters
        $costCenter = new CostCenters;
        $costCenter->code = 'CC-01';
        $costCenter->status = CostCenters::STATUS_ACTIVE;
        $costCenter->translateOrNew('ar')->name = 'مركز الرياض';
        $costCenter->save();
        $this->assertEquals($this->branch1->id, $costCenter->branch_id);

        // 3. TreeAccounts
        $treeAccount = new TreeAccounts;
        $treeAccount->code = '1001';
        $treeAccount->account_type = TreeAccounts::ACCOUNT_TYPE_ASSET;
        $treeAccount->type = TreeAccounts::TYPE_DEBIT;
        $treeAccount->status = TreeAccounts::STATUS_ACTIVE;
        $treeAccount->translateOrNew('ar')->name = 'حساب الصندوق';
        $treeAccount->save();
        $this->assertEquals($this->branch1->id, $treeAccount->branch_id);

        // 4. InvCustomer
        $customer = new InvCustomer;
        $customer->phone = '0533333333';
        $customer->email = 'cust@example.com';
        $customer->status = InvCustomer::STATUS_ACTIVE;
        $customer->translateOrNew('ar')->name = 'عميل الرياض';
        $customer->save();
        $this->assertEquals($this->branch1->id, $customer->branch_id);

        // 5. InvSupplier
        $supplier = new InvSupplier;
        $supplier->phone = '0544444444';
        $supplier->email = 'supp@example.com';
        $supplier->status = InvSupplier::STATUS_ACTIVE;
        $supplier->translateOrNew('ar')->name = 'مورد الرياض';
        $supplier->save();
        $this->assertEquals($this->branch1->id, $supplier->branch_id);

        // 6. Category
        $category = new Category;
        $category->status = Category::STATUS_ACTIVE;
        $category->translateOrNew('ar')->name = 'تصنيف الأجهزة';
        $category->save();
        $this->assertEquals($this->branch1->id, $category->branch_id);

        // 7. Unit
        $unit = new Unit;
        $unit->conversion_factor = 1.0;
        $unit->status = Unit::STATUS_ACTIVE;
        $unit->translateOrNew('ar')->name = 'حبة';
        $unit->save();
        $this->assertEquals($this->branch1->id, $unit->branch_id);

        // 8. Product
        $product = new Product;
        $product->category_id = $category->id;
        $product->base_unit_id = $unit->id;
        $product->org_id = 1;
        $product->user_id = $this->user1->id;
        $product->cost_price = 10.0;
        $product->prod_price = 15.0;
        $product->status = Product::STATUS_ACTIVE;
        $product->translateOrNew('ar')->name = 'منتج تجريبي';
        $product->save();
        $this->assertEquals($this->branch1->id, $product->branch_id);
    }

    /**
     * Test global scoping of queries based on user's branch.
     */
    public function test_global_branch_scoping_on_queries()
    {
        // Insert records with direct database query or saving without auth to avoid triggers,
        // or acting as different users.

        // Insert TaxAccounts for branch 1 and branch 2
        $this->actingAs($this->user1);
        $tax1 = new TaxAccount;
        $tax1->rate = 15;
        $tax1->status = TaxAccount::STATUS_ACTIVE;
        $tax1->translateOrNew('ar')->name = 'ضريبة 1';
        $tax1->save();

        $this->actingAs($this->user2);
        $tax2 = new TaxAccount;
        $tax2->rate = 5;
        $tax2->status = TaxAccount::STATUS_ACTIVE;
        $tax2->translateOrNew('ar')->name = 'ضريبة 2';
        $tax2->save();

        // System-wide TaxAccount (branch_id = null)
        $taxSystem = new TaxAccount;
        $taxSystem->rate = 0;
        $taxSystem->status = TaxAccount::STATUS_ACTIVE;
        $taxSystem->branch_id = null;
        $taxSystem->translateOrNew('ar')->name = 'ضريبة صفرية';
        $taxSystem->save();

        // Assert user 1 only sees their branch and system records
        $this->actingAs($this->user1);
        $taxAccountsForUser1 = TaxAccount::all();
        $this->assertCount(2, $taxAccountsForUser1);
        $this->assertTrue($taxAccountsForUser1->contains($tax1));
        $this->assertTrue($taxAccountsForUser1->contains($taxSystem));
        $this->assertFalse($taxAccountsForUser1->contains($tax2));

        // Assert user 2 only sees their branch and system records
        $this->actingAs($this->user2);
        $taxAccountsForUser2 = TaxAccount::all();
        $this->assertCount(2, $taxAccountsForUser2);
        $this->assertTrue($taxAccountsForUser2->contains($tax2));
        $this->assertTrue($taxAccountsForUser2->contains($taxSystem));
        $this->assertFalse($taxAccountsForUser2->contains($tax1));
    }
}
