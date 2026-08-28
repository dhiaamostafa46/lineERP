<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'email',
        'status',
        'job_number'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function deactivate($id)
    {
        $user = User::find($id);
        $user->status = User::STATUS_INACTIVE;
        $user->save();
    }

    public function activate($id)
    {
        $user = User::find($id);
        $user->status = User::STATUS_ACTIVE;
        $user->save();
    }



    public  function branchs()
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function setting()
    {
        return Setting::first();
    }
    public function model(): string
    {
        return User::class;
    }
    public function statuses(): array
    {
        return User::statuses();
    }
}
