<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\AppBaseController;
use App\Repositories\RoleRepository;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends AppBaseController
{
    /** @var RoleRepository $roleRepository*/
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the Role.
     */
    public function index(Request $request)
    {
        $roles = $this->roleRepository
            ->allQuery($request->all())
            ->latest()
            ->paginate($request->pagination ?? 5);

        return view('roles.index')->with('roles', $roles);
    }

    /**
     * Show the form for creating a new Role.
     */
    public function create()
    {
        // $permissions = Permission::get()->groupBy('group');

        $excludedPermissions = config('hidepermission.permissions', []);

        $permissions = Permission::whereNotIn('group', $excludedPermissions)->get()->groupBy('group');

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created Role in storage.
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->all();
        $input['guard_name'] = 'web';

         app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = $this->roleRepository->create($input);

        $permissions = $request->permissions ?? [];
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?: [];
        }
        $role->syncPermissions($permissions);

        flash()->success(__('messages.saved', ['model' => __('models/roles.singular')]));

        return redirect(route('roles.index'));
    }

    /**
     * Display the specified Role.
     */
    public function show($id)
    {
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            flash()->error(__('models/roles.singular') . ' ' . __('messages.not_found'));

            return redirect(route('roles.index'));
        }

        return view('roles.show')->with('role', $role);
    }

    /**
     * Show the form for editing the specified Role.
     */
    public function edit($id)
    {
        $role = $this->roleRepository->find($id);
        $excludedPermissions = config('hidepermission.permissions');
        $permissions = Permission::whereNotIn('group', $excludedPermissions)->get()->groupBy('group');

        if (empty($role)) {
            flash()->error(__('models/roles.singular') . ' ' . __('messages.not_found'));

            return redirect(route('roles.index'));
        }
        $permissions_selected = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'permissions_selected'));
    }

    /**
     * Update the specified Role in storage.
     */
    public function update($id, UpdateRoleRequest $request)
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            flash()->error(__('models/roles.singular') . ' ' . __('messages.not_found'));
            return redirect(route('roles.index'));
        }

        $permissions = $request->permissions ?? [];
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?: [];
        }
        // جلب الصلاحيات الموجودة فعلاً
        $existingPermissions = \Spatie\Permission\Models\Permission::whereIn('name', $permissions)->pluck('name')->toArray();

        // تجاهل أي صلاحيات غير موجودة
        $role->syncPermissions($existingPermissions);

        $role = $this->roleRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/roles.singular')]));

        return redirect(route('roles.index'));
    }

    /**
     * Remove the specified Role from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            flash()->error(__('models/roles.singular') . ' ' . __('messages.not_found'));

            return redirect(route('roles.index'));
        }

        $this->roleRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/roles.singular')]));

        return redirect(route('roles.index'));
    }
}
