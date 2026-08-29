<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\Category;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DbCategoryRepository extends BaseRepository
{
    protected array $fieldSearchable = ['name', 'parent_id', 'status', 'type'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): Builder
    {
        return parent::allQuery($search, $skip, $limit);
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

    public function listItems(int $id)
    {
        return Category::findOrFail($id);
    }

    public function header(): array
    {
        return [
            __('basicdata::models/db_categories.fields.id'),
            __('basicdata::models/db_categories.fields.name'),
            __('basicdata::models/db_categories.fields.sort'),
            __('basicdata::models/db_categories.fields.status'),
            __('basicdata::models/db_categories.fields.created_at'),
        ];
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
                    'created_at' => $category->created_at?->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name(): string
    {
        return __('basicdata::models/db_categories.singular');
    }

    public function parentCategories(?int $id = null): array
    {
        $query = Category::where('status', 1)->whereNull('parent_id');
        if ($id) {
            $query->where('id', '!=', $id);
        }
        return $query->get()->pluck('name', 'id')->toArray();
    }
}
