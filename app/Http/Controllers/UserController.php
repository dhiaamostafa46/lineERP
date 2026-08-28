<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     */
    public function index(Request $request)
    {
        $data['users'] = $this->userRepository->allQuery($request->all())->latest()->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->userRepository->statuses();
        $data['setting'] = $this->userRepository->setting();

        return view('users.index', $data);
    }

    /**
     * Show the form for creating a new User.
     */
    public function create()
    {
        $data['roles'] = Role::get()->pluck('name', 'name');
        $data['statuses'] = $this->userRepository->statuses();
        $data['branchs'] = $this->userRepository->branchs();

        return view('users.create', $data);
    }

    /**
     * Store a newly created User in storage.
     */
    public function store(CreateUserRequest $request)
    {

        // $count=User::count();
        // $setting=Setting::first();
        // if( $count  <=  $setting->actual_user){
        //     flash()->error(__('models/users.singular') . ' ' . __('messages.not_found'));
        //     return redirect(route('users.index'));
        // }

        $input = $request->all();
        $input['password'] = 'Evix20';
        $input['org_id'] = 1;

        $user = $this->userRepository->create($input);
        $user->job_number = $user->id;
        $user->save();
        // Employee::create([
        //     'user_id' => $user->id,
        //     'username' => Str::snake($user->name),
        //     'full_name' => $user->name,
        //     'email' => $user->email
        // ]);

        flash()->success(__('messages.saved', ['model' => __('models/users.singular')]));
        $user->assignRole($request->role_id);

        return redirect(route('users.index'));
    }

    /**
     * Display the specified User.
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect(route('users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect(route('users.index'));
        }

        $data['user'] = $user;
        $data['roles'] = Role::get()->pluck('name', 'name');
        $data['statuses'] = $this->userRepository->statuses();
        $data['branchs'] = $this->userRepository->branchs();

        return view('users.edit', $data);
    }

    /**
     * Update the specified User in storage.
     */
    public function update($id, UpdateUserRequest $request)
    {

        $user = $this->userRepository->find($id);

        if (empty($user)) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect(route('users.index'));
        }
        $input = $request->all();
        $input['org_id'] = 1;
        $user = $this->userRepository->update($input, $id);
        $user->syncRoles($request->role_id);
        flash()->success(__('messages.updated', ['model' => __('models/users.singular')]));

        return redirect(route('users.index'));
    }

    public function resetpassword($id)
    {
        // dd( $id);
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect(route('users.index'));
        }

        $user->password = 'Evix20';
        $user->save();
        flash()->success(__('messages.updated', ['model' => __('models/users.singular')]));

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/users.singular')]));

        return redirect(route('users.index'));
    }

    public function deactivate($id)
    {
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect(route('users.index'));
        }

        $this->userRepository->deactivate($id);
        flash()->success(__('messages.deactivated', ['model' => __('models/users.singular')]));

        return redirect(route('users.show', $id));
    }

    public function activate($id)
    {
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect(route('users.index'));
        }
        $this->userRepository->activate($id);
        flash()->success(__('messages.activated', ['model' => __('models/users.singular')]));

        return redirect(route('users.show', $id));
    }
}
