<?php

namespace Modules\BasicData\App\Repositories;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Repositories\BaseRepository;
use Modules\BasicData\App\Models\DbServicePoint;

class DbServicePointRepository extends BaseRepository
{
    protected $fieldSearchable = ['orgID', 'branchID', 'userID', 'code', 'type', 'status'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $modelName = class_basename($this->model());
        $permissionPrefix = 'basicdata.' . str_replace('db_', '', \Illuminate\Support\Str::snake(\Illuminate\Support\Str::plural($modelName)));

        if (auth()->check()) {

        }

        return $query;
    }

    public function model(): string
    {
        return DbServicePoint::class;
    }

    public function types(): array
    {
        return DbServicePoint::types();
    }

    public function statuses(): array
    {
        return DbServicePoint::statuses();
    }

    public function listItems($id)
    {
        return DbServicePoint::findOrFail($id);
    }

    public function header(): array
    {
        return [  __('basicdata::models/db_service_points.fields.id'),__('basicdata::models/db_service_points.fields.name'), __('basicdata::models/db_service_points.fields.code'), __('basicdata::models/db_service_points.fields.type'), __('basicdata::models/db_service_points.fields.status'), __('basicdata::models/db_service_points.fields.created_at')];
    }
    public function dataExel(): array
    {
        return DbServicePoint::with('translations')
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'name' => $point->name,
                    'code' => $point->code,
                    'type' => $point->type_text ?? '', // إذا لديك accessor للنوع
                    'status' => $point->status_text,
                    'created_at' => $point->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('basicdata::models/db_service_points.singular');
    }
}
