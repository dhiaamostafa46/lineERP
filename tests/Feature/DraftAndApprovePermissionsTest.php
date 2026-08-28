<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DraftAndApprovePermissionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createPermissionSchema();
    }

    protected function createPermissionSchema(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->string('group')->nullable();
            $table->string('action')->nullable();
            $table->string('action_view')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });
    }

    /**
     * Test that draft and approve routes are registered across all modules (except HR).
     */
    public function test_draft_and_approve_routes_are_registered_for_all_non_hr_modules(): void
    {
        $expectedSampleRoutes = [
            // Invoices Module
            'invoices.sales.draft',
            'invoices.sales.approve',
            'invoices.purchase.draft',
            'invoices.purchase.approve',
            'invoices.purchase_orders.draft',
            'invoices.purchase_orders.approve',
            'invoices.purchase_return.draft',
            'invoices.purchase_return.approve',
            'invoices.sales_return.draft',
            'invoices.sales_return.approve',
            'invoices.sales_debit.draft',
            'invoices.sales_debit.approve',
            'invoices.quotations.draft',
            'invoices.quotations.approve',
            'invoices.customers.draft',
            'invoices.customers.approve',
            'invoices.suppliers.draft',
            'invoices.suppliers.approve',

            // Store Module
            'store.stores.draft',
            'store.stores.approve',
            'store.openingbalance.draft',
            'store.openingbalance.approve',
            'store.damaged.draft',
            'store.damaged.approve',
            'store.reservation.draft',
            'store.reservation.approve',
            'store.receiving.draft',
            'store.receiving.approve',
            'store.issuing.draft',
            'store.issuing.approve',
            'store.direct_transfer.draft',
            'store.direct_transfer.approve',
            'store.settlement.draft',
            'store.settlement.approve',

            // AccuSoft Module
            'accusoft.JournalEntry.draft',
            'accusoft.JournalEntry.approve',
            'accusoft.TreeAccounts.draft',
            'accusoft.TreeAccounts.approve',
            'accusoft.CostCenter.draft',
            'accusoft.CostCenter.approve',
            'accusoft.FiscalYear.draft',
            'accusoft.FiscalYear.approve',
            'accusoft.AccountingSettings.draft',
            'accusoft.AccountingSettings.approve',
            'accusoft.AccountMapping.draft',
            'accusoft.AccountMapping.approve',
            'accusoft.assets.draft',
            'accusoft.assets.approve',
            'accusoft.assetcategories.draft',
            'accusoft.assetcategories.approve',

            // Finance Module
            'fnc.banks.draft',
            'fnc.banks.approve',
            'fnc.safes.draft',
            'fnc.safes.approve',
            'fnc.bonds.draft',
            'fnc.bonds.approve',

            // BasicData Module
            'basicdata.products.draft',
            'basicdata.products.approve',
            'basicdata.units.draft',
            'basicdata.units.approve',
            'basicdata.categories.draft',
            'basicdata.categories.approve',

            // Pos Module
            'pos.devices.draft',
            'pos.devices.approve',

            // Global Web Routes
            'taxaccounts.draft',
            'taxaccounts.approve',
            'users.draft',
            'users.approve',
            'roles.draft',
            'roles.approve',
            'Branches.draft',
            'Branches.approve',
            'Templates.draft',
            'Templates.approve',
            'Organization.draft',
            'Organization.approve',
            'DeviceSessions.draft',
            'DeviceSessions.approve',
            'notifications.draft',
            'notifications.approve',
        ];

        foreach ($expectedSampleRoutes as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "Route [{$routeName}] is not registered."
            );
        }
    }

    /**
     * Test that HR module has ZERO draft permissions and is completely excluded.
     */
    public function test_hr_module_does_not_have_draft_permissions(): void
    {
        Artisan::call('permissions:update');

        $allRoutes = collect(Route::getRoutes())->map(fn ($r) => $r->getName())->filter();

        $hrDraftRoutes = $allRoutes->filter(function ($name) {
            return str_starts_with($name, 'hr.') && str_ends_with($name, '.draft');
        });

        $this->assertCount(0, $hrDraftRoutes, 'HR routes should not contain draft permissions.');

        $hrDraftInDb = DB::table('permissions')
            ->where('action', 'draft')
            ->where('group', 'like', 'hr%')
            ->count();

        $this->assertEquals(0, $hrDraftInDb, 'Database permissions should not have any HR draft permissions.');
    }

    /**
     * Test translations for draft and approve permissions in Arabic and English.
     */
    public function test_permissions_translations_for_draft_and_approve(): void
    {
        app()->setLocale('ar');
        $this->assertEquals('المسودة', __('permission.roles.draft'));
        $this->assertEquals('الاعتماد', __('permission.roles.approve'));

        app()->setLocale('en');
        $this->assertEquals('Draft', __('permission.roles.draft'));
        $this->assertEquals('Approve', __('permission.roles.approve'));
    }

    /**
     * Test that permissions:update correctly creates database records with group and action.
     */
    public function test_permissions_update_command_syncs_draft_and_approve_permissions(): void
    {
        Artisan::call('permissions:update');

        $salesDraft = DB::table('permissions')->where('name', 'invoices.sales.draft')->first();
        $this->assertNotNull($salesDraft);
        $this->assertEquals('invoices_sales', $salesDraft->group);
        $this->assertEquals('draft', $salesDraft->action);

        $salesApprove = DB::table('permissions')->where('name', 'invoices.sales.approve')->first();
        $this->assertNotNull($salesApprove);
        $this->assertEquals('invoices_sales', $salesApprove->group);
        $this->assertEquals('approve', $salesApprove->action);

        $receivingDraft = DB::table('permissions')->where('name', 'store.receiving.draft')->first();
        $this->assertNotNull($receivingDraft);
        $this->assertEquals('store_receiving', $receivingDraft->group);
        $this->assertEquals('draft', $receivingDraft->action);

        $journalApprove = DB::table('permissions')->where('name', 'accusoft.JournalEntry.approve')->first();
        $this->assertNotNull($journalApprove);
        $this->assertEquals('accusoft_JournalEntry', $journalApprove->group);
        $this->assertEquals('approve', $journalApprove->action);
    }

    /**
     * Test that a user can have draft and approve permissions checked independently.
     */
    public function test_role_and_user_permission_checking_for_draft_and_approve(): void
    {
        Artisan::call('permissions:update');

        $role = Role::query()->create(['name' => 'test_draft_role', 'guard_name' => 'web']);
        
        $role->givePermissionTo('invoices.sales.draft');
        $role->givePermissionTo('store.receiving.approve');

        $this->assertTrue($role->hasPermissionTo('invoices.sales.draft'));
        $this->assertFalse($role->hasPermissionTo('invoices.sales.approve'));
        $this->assertTrue($role->hasPermissionTo('store.receiving.approve'));
        $this->assertFalse($role->hasPermissionTo('store.receiving.draft'));

        // Clean up
        $role->delete();
    }
}
