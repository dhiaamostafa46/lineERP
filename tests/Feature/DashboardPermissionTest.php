<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DashboardPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_dashboard_permission(): void
    {
        $branch = new \App\Models\Branch;
        $branch->phone = '123456789';
        $branch->translateOrNew('ar')->name = 'Test Branch';
        $branch->translateOrNew('ar')->address = 'Test Address';
        $branch->save();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone' => '0500000000',
            'password' => 'password',
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $branch->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(403);

        $permission = Permission::create([
            'name' => 'dashboard',
            'group' => 'dashboard',
            'action' => 'index',
            'action_view' => 'index',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo($permission);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_dashboard_redirects_employee_users(): void
    {
        $branch = new \App\Models\Branch;
        $branch->phone = '123456789';
        $branch->translateOrNew('ar')->name = 'Test Branch';
        $branch->translateOrNew('ar')->address = 'Test Address';
        $branch->save();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone' => '0500000000',
            'password' => 'password',
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $branch->id,
            'emp_flage' => 2,
        ]);

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'dashboard',
            'group' => 'dashboard',
            'action' => 'index',
            'action_view' => 'index',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo($permission);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('hr.empdashboard.index'));
    }

    public function test_user_activity_requires_permission(): void
    {
        $branch = new \App\Models\Branch;
        $branch->phone = '123456789';
        $branch->translateOrNew('ar')->name = 'Test Branch';
        $branch->translateOrNew('ar')->address = 'Test Address';
        $branch->save();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone' => '0500000000',
            'password' => 'password',
            'status' => User::STATUS_ACTIVE,
            'branch_id' => $branch->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('UserActivity'));
        $response->assertStatus(403);

        $permission = Permission::create([
            'name' => 'UserActivity',
            'group' => 'UserActivity',
            'action' => 'index',
            'action_view' => 'index',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo($permission);

        $response = $this->get(route('UserActivity'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
