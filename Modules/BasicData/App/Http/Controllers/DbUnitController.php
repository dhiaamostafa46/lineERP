<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Models\BasicDataApp\Unit;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Requests\CreateDbUnitRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbUnitRequest;
use Modules\BasicData\App\Repositories\DbUnitRepository;

class DbUnitController extends BasicDataResourceController
{
    protected string $viewPath = 'units';
    protected string $modelTranslation = 'basicdata::models/db_units.singular';
    protected string $exportFileName = 'units';
    protected ?string $createRequestClass = CreateDbUnitRequest::class;
    protected ?string $updateRequestClass = UpdateDbUnitRequest::class;

    public function __construct(DbUnitRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function indexViewData(Request $request): array
    {
        return [
            'totalUnitsCount' => Unit::count(),
            'activeUnitsCount' => Unit::where('status', 1)->count(),
        ];
    }
}
