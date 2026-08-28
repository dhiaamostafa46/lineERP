<?php

namespace Tests\Feature;

use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Models\Branch;
use App\Models\invApp\InvCustomer;
use App\Models\invApp\InvSupplier;
use App\Models\invApp\SalesInvoice;
use App\Models\StoreApp\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\AccuSoft\App\Models\AccountingSettings;
use Modules\Invoices\App\Models\InvoiceSetting;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Repositories\PurchaseInvoiceRepository;
use Modules\Invoices\App\Repositories\SalesInvoiceRepository;
use Tests\TestCase;

class InvoicePendingJournalEntryTest extends TestCase
{
    use DatabaseTransactions;

    protected $branch;
    protected $user;
    protected $fiscalYear;
    protected $store;
    protected $product;
    protected $customer;
    protected $supplier;
    protected $salesAccount;
    protected $customerAccount;
    protected $inventoryAccount;
    protected $supplierAccount;
    protected $vatAccount;
    protected $cogsAccount;
    protected $safeAccount;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Branch
        $this->branch = new Branch();
        $this->branch->phone = '123456789';
        $this->branch->translateOrNew('ar')->name = 'فرع الاختبار';
        $this->branch->translateOrNew('ar')->address = 'عنوان الاختبار';
        $this->branch->save();

        // 2. User
        $this->user = User::create([
            'name' => 'Admin User ' . uniqid(),
            'email' => 'admin_' . uniqid() . '@example.com',
            'phone' => '05000' . rand(10000, 99999),
            'password' => 'password',
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $this->branch->id,
        ]);
        $this->actingAs($this->user);

        // 3. Fiscal Year
        $this->fiscalYear = FiscalYear::firstOrCreate([
            'is_current' => true,
        ], [
            'name' => 'السنة المالية ' . now()->year,
            'start_date' => Carbon::now()->startOfYear()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfYear()->format('Y-m-d'),
            'is_closed' => false,
        ]);

        // 4. Tree Accounts Helper
        $this->salesAccount = $this->createAccount('4101' . rand(10, 99), 4, 2, 'المبيعات');
        AccountMapping::updateOrCreate(['mapping_key' => 'sales'], ['account_id' => $this->salesAccount->id, 'key_name' => 'المبيعات']);

        $this->customerAccount = $this->createAccount('1201' . rand(10, 99), 1, 1, 'حساب العملاء');
        AccountMapping::updateOrCreate(['mapping_key' => 'customer'], ['account_id' => $this->customerAccount->id, 'key_name' => 'حساب العملاء']);

        $this->supplierAccount = $this->createAccount('2101' . rand(10, 99), 2, 2, 'حساب الموردين');
        AccountMapping::updateOrCreate(['mapping_key' => 'supplier'], ['account_id' => $this->supplierAccount->id, 'key_name' => 'حساب الموردين']);

        $this->inventoryAccount = $this->createAccount('1301' . rand(10, 99), 1, 1, 'حساب المخزون');
        AccountMapping::updateOrCreate(['mapping_key' => 'inventory'], ['account_id' => $this->inventoryAccount->id, 'key_name' => 'حساب المخزون']);
        AccountMapping::updateOrCreate(['mapping_key' => 'sales_inventory'], ['account_id' => $this->inventoryAccount->id, 'key_name' => 'مخزون المبيعات']);
        AccountMapping::updateOrCreate(['mapping_key' => 'purchase_inventory'], ['account_id' => $this->inventoryAccount->id, 'key_name' => 'مخزون المشتريات']);

        $this->vatAccount = $this->createAccount('2201' . rand(10, 99), 2, 2, 'ضريبة القيمة المضافة');
        AccountMapping::updateOrCreate(['mapping_key' => 'tax'], ['account_id' => $this->vatAccount->id, 'key_name' => 'ضريبة عامة']);
        AccountMapping::updateOrCreate(['mapping_key' => 'sales_tax'], ['account_id' => $this->vatAccount->id, 'key_name' => 'ضريبة مبيعات']);
        AccountMapping::updateOrCreate(['mapping_key' => 'purchase_tax'], ['account_id' => $this->vatAccount->id, 'key_name' => 'ضريبة مشتريات']);

        $this->cogsAccount = $this->createAccount('5101' . rand(10, 99), 5, 1, 'تكلفة البضاعة المباعة');
        AccountMapping::updateOrCreate(['mapping_key' => 'cogs'], ['account_id' => $this->cogsAccount->id, 'key_name' => 'تكلفة البضاعة المباعة']);

        $this->safeAccount = $this->createAccount('1101' . rand(10, 99), 1, 1, 'الصندوق الرئيسي');
        AccountMapping::updateOrCreate(['mapping_key' => 'main_safe'], ['account_id' => $this->safeAccount->id, 'key_name' => 'الصندوق الرئيسي']);

        // 5. Store
        $this->store = new Store();
        $this->store->status = Store::STATUS_ACTIVE;
        $this->store->branch_id = $this->branch->id;
        $this->store->tree_account_id = $this->inventoryAccount->id;
        $this->store->translateOrNew('ar')->name = 'المستودع الرئيسي';
        $this->store->save();

        // 6. Unit & Category & Product
        $unit = Unit::firstOrCreate(['code' => 'PCS'], [
            'status' => 1,
            'ar' => ['name' => 'قطعة'],
            'en' => ['name' => 'Piece'],
        ]);

        $category = new Category();
        $category->status = Category::STATUS_ACTIVE;
        $category->branch_id = $this->branch->id;
        $category->translateOrNew('ar')->name = 'تصنيف عام ' . uniqid();
        $category->save();

        $this->product = Product::create([
            'org_id' => 1,
            'branch_id' => $this->branch->id,
            'user_id' => $this->user->id,
            'barcode' => 'BAR' . rand(100000, 999999),
            'status' => 1,
            'type' => 1,
            'have_sizes' => false,
            'cost_price' => 100,
            'prod_price' => 150,
            'category_id' => $category->id,
            'base_unit_id' => $unit->id,
            'ar' => ['name' => 'منتج تجريبي'],
            'en' => ['name' => 'Test Product'],
        ]);

        // 7. Customer & Supplier
        $this->customer = new InvCustomer();
        $this->customer->phone = '05' . rand(10000000, 99999999);
        $this->customer->email = 'cust_' . uniqid() . '@example.com';
        $this->customer->status = InvCustomer::STATUS_ACTIVE;
        $this->customer->branch_id = $this->branch->id;
        $this->customer->tree_account_id = $this->customerAccount->id;
        $this->customer->translateOrNew('ar')->name = 'عميل تجريبي';
        $this->customer->save();

        $this->supplier = new InvSupplier();
        $this->supplier->phone = '05' . rand(10000000, 99999999);
        $this->supplier->email = 'supp_' . uniqid() . '@example.com';
        $this->supplier->status = InvSupplier::STATUS_ACTIVE;
        $this->supplier->branch_id = $this->branch->id;
        $this->supplier->tree_account_id = $this->supplierAccount->id;
        $this->supplier->translateOrNew('ar')->name = 'مورد تجريبي';
        $this->supplier->save();
    }

    protected function createAccount(string $code, int $accountType, int $type, string $name): TreeAccounts
    {
        $acc = new TreeAccounts();
        $acc->code = $code;
        $acc->account_type = $accountType;
        $acc->type = $type;
        $acc->is_leaf = true;
        $acc->status = 1;
        $acc->level = 1;
        $acc->translateOrNew('ar')->name = $name;
        $acc->save();
        return $acc;
    }

    public function test_sales_invoice_creates_pending_journal_entry_when_auto_post_is_disabled()
    {
        // Ensure auto-post is disabled
        AccountingSettings::set('sales_auto_post_journal_entries', false);
        InvoiceSetting::updateOrCreate(['id' => 1], ['sales_auto_post' => false]);

        $salesRepo = app(SalesInvoiceRepository::class);

        $input = [
            'customer_id' => $this->customer->id,
            'store_id' => $this->store->id,
            'branch_id' => $this->branch->id,
            'type_inv' => SalesInvoice::TYPE_INVOICE,
            'status' => SalesInvoice::STATUS_SUBMITTED,
            'issue_date' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_name' => 'منتج تجريبي',
                    'quantity' => 2,
                    'unit_price' => 150,
                    'unit' => 'قطعة',
                    'unit_id' => $this->product->base_unit_id,
                    'vat_rate' => 15,
                ],
            ],
            'payments' => [
                [
                    'account_id' => $this->safeAccount->id,
                    'amount' => 100,
                    'payment_method' => 'cash',
                ],
            ],
        ];

        $invoice = $salesRepo->createSales($input);

        $this->assertNotNull($invoice->journal_entry_id);

        // Check main invoice entry is STATUS_PENDING (4)
        $mainEntry = JournalEntry::find($invoice->journal_entry_id);
        $this->assertNotNull($mainEntry);
        $this->assertEquals(JournalEntry::STATUS_PENDING, $mainEntry->status);
        $this->assertNull($mainEntry->posted_at);

        // Check payment entry is STATUS_PENDING (4)
        $paymentEntry = JournalEntry::where('reference_type', \App\Models\invApp\SalesInvoicePayment::class)
            ->where('reference_id', $invoice->payments->first()->id)
            ->first();

        $this->assertNotNull($paymentEntry);
        $this->assertEquals(JournalEntry::STATUS_PENDING, $paymentEntry->status);
    }

    public function test_purchase_invoice_creates_pending_journal_entry_when_auto_post_is_disabled()
    {
        // Ensure auto-post is disabled
        AccountingSettings::set('purchase_auto_post_journal_entries', false);
        InvoiceSetting::updateOrCreate(['id' => 1], ['purchase_auto_post' => false]);

        $purchaseRepo = app(PurchaseInvoiceRepository::class);

        $input = [
            'supplier_id' => $this->supplier->id,
            'store_id' => $this->store->id,
            'branch_id' => $this->branch->id,
            'type_inv' => PurchaseInvoice::TYPE_INVOICE,
            'status' => PurchaseInvoice::STATUS_RECEIVED,
            'issue_date' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_name' => 'منتج تجريبي',
                    'quantity' => 5,
                    'unit_price' => 100,
                    'unit' => 'قطعة',
                    'unit_id' => $this->product->base_unit_id,
                ],
            ],
            'payments' => [
                [
                    'account_id' => $this->safeAccount->id,
                    'amount' => 200,
                    'payment_method' => 'cash',
                ],
            ],
        ];

        $invoice = $purchaseRepo->CreatePurchase($input);

        $this->assertNotNull($invoice->journal_entry_id);

        // Check main purchase entry is STATUS_PENDING (4)
        $mainEntry = JournalEntry::find($invoice->journal_entry_id);
        $this->assertNotNull($mainEntry);
        $this->assertEquals(JournalEntry::STATUS_PENDING, $mainEntry->status);
        $this->assertNull($mainEntry->posted_at);

        // Check payment entry is STATUS_PENDING (4)
        $paymentEntry = JournalEntry::where('reference_type', \Modules\Invoices\App\Models\PurchaseInvoicePayment::class)
            ->where('reference_id', $invoice->payments->first()->id)
            ->first();

        $this->assertNotNull($paymentEntry);
        $this->assertEquals(JournalEntry::STATUS_PENDING, $paymentEntry->status);
    }

    public function test_sales_and_purchase_invoices_auto_post_when_enabled()
    {
        // Enable auto-post
        AccountingSettings::set('sales_auto_post_journal_entries', true);
        AccountingSettings::set('purchase_auto_post_journal_entries', true);
        InvoiceSetting::updateOrCreate(['id' => 1], ['sales_auto_post' => true, 'purchase_auto_post' => true]);

        $salesRepo = app(SalesInvoiceRepository::class);
        $purchaseRepo = app(PurchaseInvoiceRepository::class);

        // 1. Sales
        $salesInvoice = $salesRepo->createSales([
            'customer_id' => $this->customer->id,
            'store_id' => $this->store->id,
            'branch_id' => $this->branch->id,
            'type_inv' => SalesInvoice::TYPE_INVOICE,
            'status' => SalesInvoice::STATUS_SUBMITTED,
            'issue_date' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_name' => 'منتج تجريبي',
                    'quantity' => 1,
                    'unit_price' => 150,
                    'unit' => 'قطعة',
                    'unit_id' => $this->product->base_unit_id,
                ],
            ],
        ]);

        $salesEntry = JournalEntry::find($salesInvoice->journal_entry_id);
        $this->assertNotNull($salesEntry);
        $this->assertEquals(JournalEntry::STATUS_POSTED, $salesEntry->status);
        $this->assertNotNull($salesEntry->posted_at);

        // 2. Purchase
        $purchaseInvoice = $purchaseRepo->CreatePurchase([
            'supplier_id' => $this->supplier->id,
            'store_id' => $this->store->id,
            'branch_id' => $this->branch->id,
            'type_inv' => PurchaseInvoice::TYPE_INVOICE,
            'status' => PurchaseInvoice::STATUS_RECEIVED,
            'issue_date' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_name' => 'منتج تجريبي',
                    'quantity' => 2,
                    'unit_price' => 100,
                    'unit' => 'قطعة',
                    'unit_id' => $this->product->base_unit_id,
                ],
            ],
        ]);

        $purchaseEntry = JournalEntry::find($purchaseInvoice->journal_entry_id);
        $this->assertNotNull($purchaseEntry);
        $this->assertEquals(JournalEntry::STATUS_POSTED, $purchaseEntry->status);
        $this->assertNotNull($purchaseEntry->posted_at);
    }
}
