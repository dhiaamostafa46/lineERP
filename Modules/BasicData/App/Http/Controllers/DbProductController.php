<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Models\BasicDataApp\Product;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Requests\CreateDbProductRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbProductRequest;
use Modules\BasicData\App\Repositories\DbProductRepository;

class DbProductController extends BasicDataResourceController
{
    protected string $viewPath = 'products';
    protected string $modelTranslation = 'basicdata::models/db_products.singular';
    protected string $exportFileName = 'products';
    protected ?string $createRequestClass = CreateDbProductRequest::class;
    protected ?string $updateRequestClass = UpdateDbProductRequest::class;

    public function __construct(DbProductRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function indexViewData(Request $request): array
    {
        $type = (int)$request->get('type', 1);

        return [
            'type' => $type,
            'isService' => $type === 2,
            'categories' => $this->repository->categories(),
            'kitchens' => $this->repository->kitchens(),
            'units' => $this->repository->units(),
            'vats' => $this->repository->vats(),
            'types' => $this->repository->types(),
            'statuses' => $this->repository->statuses(),
            'totalCount' => Product::where('type', $type)->count(),
            'activeCount' => Product::where('type', $type)->where('status', 1)->count(),
            'inactiveCount' => Product::where('type', $type)->where('status', 0)->count(),
            'totalCategoriesCount' => count($this->repository->categories()),
        ];
    }

    protected function formViewData(?int $id = null): array
    {
        $type = (int)request()->get('type', 1);

        return [
            'type' => $type,
            'isService' => $type === 2,
            'categories' => $this->repository->categories(),
            'kitchens' => $this->repository->kitchens(),
            'statuses' => $this->repository->statuses(),
            'units' => $this->repository->units(),
            'vats' => $this->repository->vats(),
            'types' => $this->repository->types(),
        ];
    }
}
