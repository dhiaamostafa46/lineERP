<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Models\BasicDataApp\Category;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Requests\CreateDbCategoryRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbCategoryRequest;
use Modules\BasicData\App\Repositories\DbCategoryRepository;

class DbCategoryController extends BasicDataResourceController
{
    protected string $viewPath = 'categories';
    protected string $modelTranslation = 'basicdata::models/db_categories.singular';
    protected string $exportFileName = 'categories';
    protected ?string $createRequestClass = CreateDbCategoryRequest::class;
    protected ?string $updateRequestClass = UpdateDbCategoryRequest::class;

    public function __construct(DbCategoryRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function indexViewData(Request $request): array
    {
        return [
            'types' => $this->repository->types(),
            'parent_categories' => $this->repository->parentCategories(),
            'totalCategoriesCount' => Category::count(),
            'activeCategoriesCount' => Category::where('status', 1)->count(),
            'mainCategoriesCount' => Category::whereNull('parent_id')->count(),
            'subCategoriesCount' => Category::whereNotNull('parent_id')->count(),
        ];
    }

    protected function formViewData(?int $id = null): array
    {
        return [
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
            'parent_categories' => $this->repository->parentCategories($id),
        ];
    }
}
