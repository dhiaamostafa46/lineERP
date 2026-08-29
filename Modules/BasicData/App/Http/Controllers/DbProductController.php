<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\BasicDataApp\Product;
use App\Traits\HasBulkActions;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Controllers\Concerns\HasExportActions;
use Modules\BasicData\App\Http\Requests\CreateDbProductRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbProductRequest;
use Modules\BasicData\App\Repositories\DbProductRepository;

class DbProductController extends AppBaseController
{
    use HasBulkActions, HasExportActions;

    protected DbProductRepository $repository;
    protected string $exportFileName = 'products';

    public function __construct(DbProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the Product.
     */
    public function index(Request $request)
    {
        $pagination = (int)$request->get('pagination', 10);
        $type = (int)$request->get('type', 1);

        $products = $this->repository->allQuery($request->except('pagination'))->paginate($pagination);

        return view('basicdata::products.index', [
            'products' => $products,
            'type' => $type,
            'categories' => $this->repository->categories(),
            'kitchens' => $this->repository->kitchens(),
            'statuses' => $this->repository->statuses(),
            'units' => $this->repository->units(),
            'vats' => $this->repository->vats(),
            'types' => $this->repository->types(),
            'totalProductsCount' => Product::where('type', 1)->count(),
            'totalServicesCount' => Product::where('type', 2)->count(),
            'activeCount' => Product::where('status', 1)->count(),
        ]);
    }

    /**
     * Show the form for creating a new Product.
     */
    public function create(Request $request)
    {
        $type = (int)$request->get('type', 1);

        return view('basicdata::products.create', [
            'type' => $type,
            'categories' => $this->repository->categories(),
            'kitchens' => $this->repository->kitchens(),
            'statuses' => $this->repository->statuses(),
            'units' => $this->repository->units(),
            'vats' => $this->repository->vats(),
            'types' => $this->repository->types(),
        ]);
    }

    /**
     * Store a newly created Product in storage.
     */
    public function store(CreateDbProductRequest $request)
    {
        try {
            $this->repository->createWithRelations($request->all());
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_products.singular')]));
            return redirect()->route('basicdata.products.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_products.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Product.
     */
    public function show($id)
    {
        $product = $this->repository->find($id);

        if (empty($product)) {
            flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.products.index'));
        }

        return view('basicdata::products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified Product.
     */
    public function edit($id)
    {
        $product = $this->repository->find($id);

        if (empty($product)) {
            flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.products.index'));
        }

        return view('basicdata::products.edit', [
            'product' => $product,
            'type' => $product->type ?? 1,
            'categories' => $this->repository->categories(),
            'kitchens' => $this->repository->kitchens(),
            'statuses' => $this->repository->statuses(),
            'units' => $this->repository->units(),
            'vats' => $this->repository->vats(),
            'types' => $this->repository->types(),
        ]);
    }

    /**
     * Update the specified Product in storage.
     */
    public function update(UpdateDbProductRequest $request, $id)
    {
        try {
            $product = $this->repository->find($id);

            if (empty($product)) {
                flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.products.index'));
            }

            $this->repository->updateWithRelations($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_products.singular')]));
            return redirect()->route('basicdata.products.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_products.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Product from storage.
     */
    public function destroy($id)
    {
        try {
            $product = $this->repository->find($id);

            if (empty($product)) {
                flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.products.index'));
            }

            $this->repository->delete($id);
            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_products.singular')]));
            return redirect()->route('basicdata.products.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_products.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
