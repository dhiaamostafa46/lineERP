<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Models\BasicDataApp\ServicePoint;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Requests\CreateDbServicePointRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbServicePointRequest;
use Modules\BasicData\App\Repositories\DbServicePointRepository;

class DbServicePointController extends BasicDataResourceController
{
    protected string $viewPath = 'service_points';
    protected string $modelTranslation = 'basicdata::models/db_service_points.singular';
    protected string $exportFileName = 'service_points';
    protected ?string $createRequestClass = CreateDbServicePointRequest::class;
    protected ?string $updateRequestClass = UpdateDbServicePointRequest::class;

    public function __construct(DbServicePointRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function indexViewData(Request $request): array
    {
        return [
            'types' => method_exists($this->repository, 'types') ? $this->repository->types() : [],
            'totalPointsCount' => ServicePoint::count(),
            'activePointsCount' => ServicePoint::where('status', 1)->count(),
        ];
    }

    protected function formViewData(?int $id = null): array
    {
        return [
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
        ];
    }
}
