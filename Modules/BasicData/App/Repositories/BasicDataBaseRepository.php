<?php

namespace Modules\BasicData\App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

abstract class BasicDataBaseRepository extends BaseRepository
{
    protected array $fieldSearchable = ['name', 'status'];
    protected ?string $modelTranslation = null;

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): Builder
    {
        return parent::allQuery($search, $skip, $limit);
    }

    public function statuses(): array
    {
        $modelClass = $this->model();
        if (method_exists($modelClass, 'statuses')) {
            return $modelClass::statuses();
        }
        return [
            1 => __('basicdata::lang.active'),
            0 => __('basicdata::lang.inactive'),
        ];
    }

    public function types(): array
    {
        $modelClass = $this->model();
        if (method_exists($modelClass, 'types')) {
            return $modelClass::types();
        }
        return [];
    }

    public function listItems(int $id)
    {
        return $this->model()::findOrFail($id);
    }

    public function name(): string
    {
        return $this->modelTranslation ? __($this->modelTranslation) : class_basename($this->model());
    }

    public function header(): array
    {
        return [];
    }

    public function dataExel(): array
    {
        return [];
    }
}
