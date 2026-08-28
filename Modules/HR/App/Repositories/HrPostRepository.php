<?php

namespace Modules\HR\App\Repositories;

use App\Models\Branch;
use App\Models\Employee;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrPost;

class HrPostRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'title',
        'type',
        'status',
        'flage',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrPost::class;
    }

    public function types(): array
    {
        return HrPost::types();
    }

    public function statuses(): array
    {
        return HrPost::statuses();
    }

    public function flages(): array
    {
        return HrPost::flages();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }

    public function departments(): array
    {
        return HrDepartment::get()->pluck('name', 'id')->toArray();
    }

    public function branches(): array
    {
        return Branch::get()->pluck('name', 'id')->toArray();
    }

    public function publishedForEmployee(Employee $employee, int $limit = 10)
    {
        return HrPost::query()
            ->published()
            ->visibleToEmployee($employee)
            ->ordered()
            ->with(['translations', 'creator'])
            ->limit($limit)
            ->get();
    }
}
