<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\Category;

class DbCategoryRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = ['name', 'parent_id', 'status', 'type'];
    protected ?string $modelTranslation = 'basicdata::models/db_categories.singular';

    public function model(): string
    {
        return Category::class;
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

    public function parentCategories(?int $id = null): array
    {
        $query = Category::where('status', 1)->whereNull('parent_id');
        if ($id) {
            $query->where('id', '!=', $id);
        }
        return $query->get()->pluck('name', 'id')->toArray();
    }
}
