<?php

namespace Modules\AccuSoft\App\Repositories;

use App\Models\AccuSoft\CostCenters;
use App\Repositories\BaseRepository;

class AsCostCenterRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'code', 'parent_id', 'level', 'is_leaf', 'status', 'attributes'];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.CostCenter';

        if (auth()->check()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.user_id', auth()->id());
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_by') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.created_by', auth()->id());
            }


        }

        return $query;
    }

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return CostCenters::class;
    }

    public function statuses(): array
    {
        return CostCenters::statuses();
    }

    public function CostCenters()
    {
        return CostCenters::active()->with('translations')->get()->pluck('name', 'id')->toArray();
    }

    public function Ajex()
    {
        return CostCenters::active()->with('translations')->get()->pluck('name', 'id')->toArray();
    }

    public function Root($request)
    {
        return CostCenters::whereNull('parent_id')->with('translations')->get();
    }

    public function listItems($id)
    {
        return CostCenters::findOrFail($id);
    }

    public function header(): array
    {
        return [
            __('accusoft::models/as_cost_centers.fields.code'),
            __('accusoft::models/as_cost_centers.fields.name'),
            __('accusoft::models/as_cost_centers.fields.level'),
            __('accusoft::models/as_cost_centers.fields.status'),
            __('accusoft::models/as_cost_centers.fields.created_at'),
        ];
    }

    public function getHeaders(): array
    {
        return $this->header();
    }

    public function dataExcel(): array
    {
        return CostCenters::with('translations')
            ->get()
            ->map(function ($CostCenters) {
                return [
                    'code' => $CostCenters->code,
                    'name' => $CostCenters->name,
                    'level' => $CostCenters->level,
                    'status' => $CostCenters->status_text,
                    'created_at' => $CostCenters->created_at ? $CostCenters->created_at->format('Y-m-d') : '',
                ];
            })
            ->toArray();
    }

    public function dataExel(): array
    {
        return $this->dataExcel();
    }

    public function name()
    {
        return __('accusoft::models/as_cost_centers.singular');
    }
}
