<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Hub\HubApp;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HubIntegrationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropAllTables();
        $this->createTestSchema();

        $branch = new Branch;
        $branch->phone = '0501234567';
        $branch->translateOrNew('ar')->name = 'الفرع الرئيسي';
        $branch->translateOrNew('ar')->address = 'الرياض';
        $branch->save();

        $this->user = User::create([
            'name' => 'Admin Hub Tester',
            'email' => 'admin_test_hub@example.com',
            'phone' => '0509998888',
            'password' => bcrypt('password'),
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $branch->id,
        ]);

        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);
    }

    protected function createTestSchema(): void
    {
        Schema::create('branches', function (Blueprint $table): void {
            $table->id();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branch_translations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->unique(['branch_id', 'locale']);
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('user_type')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('org_id')->nullable()->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->string('group')->nullable();
            $table->string('action')->nullable();
            $table->string('action_view')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function (Blueprint $table): void {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table): void {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table): void {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });

        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('organization_translations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->unique(['organization_id', 'locale']);
        });

        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('fav_icon')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hub_app', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('org_id')->default(1)->index();
            $table->string('app_code')->index();
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->longText('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->string('connection_status')->default('active');
            $table->string('webhook_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();

            $table->unique(['app_code', 'org_id'], 'hub_app_code_org_unique');
        });
    }

    /**
     * Test viewing applications index directly from live manifest endpoint.
     */
    public function test_user_can_view_applications_index(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('applications.index'));

        $response->assertStatus(200);
        $response->assertSee(__('models/applications.plural'));
        $response->assertSee('Salla');
    }

    /**
     * Test getting application details via AJAX for popup modal.
     */
    public function test_user_can_get_application_details_for_modal(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson(route('applications.details', 'salla'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'code',
                'name',
                'category',
                'fields',
                'is_active',
                'webhook_url',
            ],
            'message',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('salla', $response->json('data.code'));
    }

    /**
     * Test viewing application show page for a platform in manifest.
     */
    public function test_user_can_view_application_show_page(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('applications.show', 'salla'));

        $response->assertStatus(200);
        $response->assertSee('Salla');
    }

    /**
     * Test activating application, sending to Hub activate API, and saving to hub_app table.
     */
    public function test_user_can_activate_application_and_save_to_hub_app(): void
    {
        $this->actingAs($this->user);

        $payload = [
            'credentials' => [
                'client_id' => 'salla_client_12345',
                'client_secret' => 'super_secret_salla_99999',
            ],
            'settings' => [
                'auto_sync' => '1',
            ],
            'environment' => 'production',
        ];

        $response = $this->post(route('applications.activate', 'salla'), $payload);

        $response->assertRedirect(route('applications.index'));

        // Assert record is created in hub_app table
        $hubApp = HubApp::where('app_code', 'salla')->first();
        $this->assertNotNull($hubApp);
        $this->assertTrue($hubApp->is_active);

        // Assert credentials in database raw column are encrypted
        $rawCredentials = $hubApp->getRawOriginal('credentials');
        $this->assertNotEquals('salla_client_12345', $rawCredentials);
        $this->assertStringNotContainsString('super_secret_salla_99999', $rawCredentials);

        // Assert decrypted accessor returns correct values
        $decrypted = $hubApp->credentials;
        $this->assertEquals('salla_client_12345', $decrypted['client_id']);
        $this->assertEquals('super_secret_salla_99999', $decrypted['client_secret']);
    }

    /**
     * Test toggling application status via AJAX.
     */
    public function test_user_can_toggle_application_status(): void
    {
        $this->actingAs($this->user);

        HubApp::create([
            'org_id' => 1,
            'app_code' => 'moyasar',
            'name' => 'Moyasar',
            'category' => 'payment_gateway',
            'is_active' => true,
            'connection_status' => 'active',
        ]);

        $response = $this->postJson(route('applications.toggle_status', 'moyasar'), [
            'is_active' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'is_active' => false,
            ],
        ]);

        $hubApp = HubApp::where('app_code', 'moyasar')->first();
        $this->assertFalse($hubApp->is_active);
    }

    /**
     * Test deactivating an application.
     */
    public function test_user_can_deactivate_application(): void
    {
        $this->actingAs($this->user);

        HubApp::create([
            'org_id' => 1,
            'app_code' => 'moyasar',
            'name' => 'Moyasar',
            'category' => 'payment_gateway',
            'is_active' => true,
            'connection_status' => 'active',
        ]);

        $response = $this->postJson(route('applications.deactivate', 'moyasar'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $hubApp = HubApp::where('app_code', 'moyasar')->first();
        $this->assertFalse($hubApp->is_active);
    }

    /**
     * Test masked credentials helper.
     */
    public function test_masked_credentials_helper(): void
    {
        $hubApp = HubApp::create([
            'org_id' => 1,
            'app_code' => 'moyasar',
            'name' => 'Moyasar',
            'credentials' => [
                'publishable_key' => 'pk_live_1234567890abcdef',
                'secret_key' => 'sk_live_9876543210fedcba',
            ],
            'is_active' => true,
        ]);

        $masked = $hubApp->getMaskedCredentials([
            ['key' => 'publishable_key', 'type' => 'text'],
            ['key' => 'secret_key', 'type' => 'password'],
        ]);

        $this->assertArrayHasKey('secret_key', $masked);
        $this->assertStringContainsString('••••••••', $masked['secret_key']);
        $this->assertNotEquals('sk_live_9876543210fedcba', $masked['secret_key']);
    }
}
