<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrAsset;
use Modules\HR\App\Models\HrCustody;
use App\Repositories\BaseRepository;

class HrCustodyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'employee_id',
        'asset_id',
        'details',
        'file',
        'received_id',
        'received_at',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrCustody::class;
    }

    // employees
    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }

    // status
    public function statuses()
    {
        return $this->model()::statuses();
    }

    // assets
    public function assets()
    {
        return HrAsset::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    // received
    public function received($id)
    {
        $hrCustody = $this->find($id);
        $hrCustody->received_id = auth()->user()->employee->id;
        $hrCustody->received_at = date('Y-m-d H:i:s');
        $hrCustody->status      = $this->model()::STATUS_RECEIVED;
        $hrCustody->save();
    }


    public function Return($id)
    {
        $hrCustody = $this->find($id);
        $hrCustody->received_id = auth()->user()->employee->id;
        $hrCustody->return_at   = date('Y-m-d H:i:s');
        $hrCustody->status      = $this->model()::STATUS_RETURN;
        $hrCustody->save();
    }


    public function AcceptReturn($id)
    {
        $hrCustody = $this->find($id);
        $hrCustody->accept_id   = auth()->user()->id;
        $hrCustody->Accept_at   = date('Y-m-d H:i:s');
        $hrCustody->status      = $this->model()::STATUS_ACCEPT;
        $hrCustody->save();
    }

    public function nonAccept($id)
    {
        $hrCustody = $this->find($id);
        $hrCustody->Accept_at   = null;
        $hrCustody->status      = $this->model()::STATUS_RECEIVED;
        $hrCustody->save();
    }





}
