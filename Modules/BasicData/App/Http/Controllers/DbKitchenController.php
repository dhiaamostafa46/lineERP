<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Models\BasicDataApp\Kitchen;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Requests\CreateDbKitchenRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbKitchenRequest;
use Modules\BasicData\App\Repositories\DbKitchenRepository;

class DbKitchenController extends BasicDataResourceController
{
    protected string $viewPath = 'kitchens';
    protected string $modelTranslation = 'basicdata::models/db_kitchens.singular';
    protected string $exportFileName = 'kitchens';
    protected ?string $createRequestClass = CreateDbKitchenRequest::class;
    protected ?string $updateRequestClass = UpdateDbKitchenRequest::class;

    public function __construct(DbKitchenRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function indexViewData(Request $request): array
    {
        return [
            'totalKitchensCount' => Kitchen::count(),
            'activeKitchensCount' => Kitchen::where('status', 1)->count(),
        ];
    }
}
