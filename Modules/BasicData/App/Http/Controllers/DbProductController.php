<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Models\BasicDataApp\Product;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Requests\CreateDbProductRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbProductRequest;
use Modules\BasicData\App\Repositories\DbProductRepository;

use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\ProductTemplateExport;
use Modules\BasicData\App\Imports\ProductsImport;

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

    public function import(Request $request)
    {
        return view('basicdata::products.import', $this->formViewData());
    }

    public function importsave(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1G');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new ProductsImport();
            Excel::import($import, $request->file('file'));

            flash()->success(__('messages.imported', ['model' => __('basicdata::models/db_products.plural')]));
            return redirect()->route('basicdata.products.index');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            flash()->error(__('crud.import_errors_message'));
            return redirect()->back()->with('failures', $failures);
        } catch (\Exception $e) {
            flash()->error(__('messages.error_importing', ['model' => __('basicdata::models/db_products.plural')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function importTemplate()
    {
        return Excel::download(new ProductTemplateExport(), 'Product_Import_Template.xlsx');
    }
}
