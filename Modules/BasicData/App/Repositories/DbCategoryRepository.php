<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class DbCategoryRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'status'];

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

        return $query->orderBy('parent_id', 'asc')->orderBy('sort', 'desc');
    }

    public function model(): string
    {
        return Category::class;
    }

    public function statuses(): array
    {
        return Category::statuses();
    }


    public function types(): array
    {
        return Category::types();
    }


    public function listItems($id)
    {
        return Category::findOrFail($id);
    }

    public function header(): array
    {
        return [__('basicdata::models/db_categories.fields.id'), __('basicdata::models/db_categories.fields.name'), __('basicdata::models/db_categories.fields.sort'), __('basicdata::models/db_categories.fields.status'), __('basicdata::models/db_categories.fields.created_at')];
    }

    public function dataExel(): array
    {
        return Category::with('translations')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,

                    'sort' => $category->sort ?? '',
                    'status' => $category->status_text,
                    'created_at' => $category->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('basicdata::models/db_categories.singular');
    }

    public function parentCategories($id = null)
    {
        $query = Category::active();
        if ($id) {
            $query->where('id', '!=', $id);
        }
        return $query->get()->pluck('name', 'id')->prepend(__('lang.none'), '');
    }
}
