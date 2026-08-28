<?php

namespace Tests\Feature;

use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Models\invApp\InvSupplier;
use App\Models\StoreApp\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Invoices\App\Repositories\PurchaseInvoiceRepository;
use Tests\TestCase;

class InvoiceUnitValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_invoice_throws_validation_error_if_product_has_no_unit()
    {
        // 1. Create a branch
        $branch = new \App\Models\Branch;
        $branch->phone = '123456789';
        $branch->translateOrNew('ar')->name = 'Test Branch';
        $branch->translateOrNew('ar')->address = 'Test Address';
        $branch->save();

        // 2. Create a user belonging to that branch
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone' => '0500000000',
            'password' => 'password',
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $branch->id,
        ]);
        $this->actingAs($user);

        // Create Category
        $category = new Category;
        $category->status = Category::STATUS_ACTIVE;
        $category->branch_id = $branch->id;
        $category->translateOrNew('ar')->name = 'Test Category';
        $category->save();

        // 3. Create product with type Product::TYPE_SALE (1) but NO base_unit_id
        $product = Product::create([
            'org_id' => 1,
            'branch_id' => $branch->id,
            'user_id' => $user->id,
            'barcode' => '1234567890123',
            'status' => 1,
            'type' => 1,
            'have_sizes' => false,
            'cost_price' => 10,
            'prod_price' => 15,
            'category_id' => $category->id,
            'ar' => ['name' => 'Test Product'],
            'en' => ['name' => 'Test Product'],
            'base_unit_id' => null, // no unit
        ]);

        // 4. Create Store
        $store = new Store;
        $store->status = Store::STATUS_ACTIVE;
        $store->branch_id = $branch->id;
        $store->translateOrNew('ar')->name = 'Test Store';
        $store->save();

        // 5. Create Supplier
        $supplier = new InvSupplier;
        $supplier->phone = '0544444444';
        $supplier->email = 'supp@example.com';
        $supplier->status = InvSupplier::STATUS_ACTIVE;
        $supplier->branch_id = $branch->id;
        $supplier->translateOrNew('ar')->name = 'Test Supplier';
        $supplier->save();

        $repository = app(PurchaseInvoiceRepository::class);

        $input = [
            'store_id' => $store->id,
            'supplier_id' => $supplier->id,
            'type_inv' => 1,
            'issue_date' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => 10,
                    'unit' => null, // empty unit name
                    'unit_id' => null, // empty unit id
                ],
            ],
        ];

        $this->expectException(ValidationException::class);

        try {
            $repository->CreatePurchase($input);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('items.0.unit', $e->errors());
            $this->assertArrayHasKey('items.0.unit_id', $e->errors());
            $this->assertEquals(__('invoices::models/sales_invoices.validation.product_no_unit'), $e->errors()['items.0.unit'][0]);
            throw $e;
        }
    }
}
