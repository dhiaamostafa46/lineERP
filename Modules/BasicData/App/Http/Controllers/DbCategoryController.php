<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\BasicDataApp\Category;
use App\Traits\HasBulkActions;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Controllers\Concerns\HasExportActions;
use Modules\BasicData\App\Http\Requests\CreateDbCategoryRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbCategoryRequest;
use Modules\BasicData\App\Repositories\DbCategoryRepository;

class DbCategoryController extends AppBaseController
{
    use HasBulkActions, HasExportActions;

    protected DbCategoryRepository $repository;
    protected string $exportFileName = 'categories';

    public function __construct(DbCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the Category.
     */
    public function index(Request $request)
    {
        $pagination = (int)$request->get('pagination', 10);
        $categories = $this->repository->allQuery($request->except('pagination'))->paginate($pagination);

        return view('basicdata::categories.index', [
            'categories' => $categories,
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
            'parent_categories' => $this->repository->parentCategories(),
            'totalCategoriesCount' => Category::count(),
            'activeCategoriesCount' => Category::where('status', 1)->count(),
            'mainCategoriesCount' => Category::whereNull('parent_id')->count(),
            'subCategoriesCount' => Category::whereNotNull('parent_id')->count(),
        ]);
    }

    /**
     * Show the form for creating a new Category.
     */
    public function create()
    {
        return view('basicdata::categories.create', [
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
            'parent_categories' => $this->repository->parentCategories(),
        ]);
    }

    /**
     * Store a newly created Category in storage.
     */
    public function store(CreateDbCategoryRequest $request)
    {
        try {
            $this->repository->create($request->all());
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_categories.singular')]));
            return redirect()->route('basicdata.categories.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Category.
     */
    public function show($id)
    {
        $category = $this->repository->find($id);

        if (empty($category)) {
            flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.categories.index'));
        }

        return view('basicdata::categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified Category.
     */
    public function edit($id)
    {
        $category = $this->repository->find($id);

        if (empty($category)) {
            flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.categories.index'));
        }

        return view('basicdata::categories.edit', [
            'category' => $category,
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
            'parent_categories' => $this->repository->parentCategories($id),
        ]);
    }

    /**
     * Update the specified Category in storage.
     */
    public function update(UpdateDbCategoryRequest $request, $id)
    {
        try {
            $category = $this->repository->find($id);

            if (empty($category)) {
                flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.categories.index'));
            }

            $this->repository->update($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_categories.singular')]));
            return redirect()->route('basicdata.categories.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Category from storage.
     */
    public function destroy($id)
    {
        try {
            $category = $this->repository->find($id);

            if (empty($category)) {
                flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.categories.index'));
            }

            $this->repository->delete($id);
            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_categories.singular')]));
            return redirect()->route('basicdata.categories.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
