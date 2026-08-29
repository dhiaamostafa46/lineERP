<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Repositories\BaseRepository;
use App\Traits\HasBulkActions;
use Illuminate\Http\Request;
use Modules\BasicData\App\Helpers\HasExportActions;

abstract class BasicDataResourceController extends AppBaseController
{
    use HasBulkActions, HasExportActions;

    protected BaseRepository $repository;
    protected string $viewPath;
    protected string $modelTranslation;
    protected string $exportFileName = 'export';
    protected ?string $createRequestClass = null;
    protected ?string $updateRequestClass = null;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Extra data hook for index view
     */
    protected function indexViewData(Request $request): array
    {
        return [];
    }

    /**
     * Extra data hook for create/edit forms
     */
    protected function formViewData(?int $id = null): array
    {
        return [
            'statuses' => method_exists($this->repository, 'statuses') ? $this->repository->statuses() : [],
        ];
    }

    /**
     * Display listing of resource.
     */
    public function index(Request $request)
    {
        $pagination = (int)$request->get('pagination', 10);
        $items = $this->repository->allQuery($request->except('pagination'))->paginate($pagination);

        $viewData = array_merge([
            $this->viewPath => $items,
            'statuses' => method_exists($this->repository, 'statuses') ? $this->repository->statuses() : [],
        ], $this->indexViewData($request));

        return view("basicdata::{$this->viewPath}.index", $viewData);
    }

    /**
     * Show creation form.
     */
    public function create()
    {
        return view("basicdata::{$this->viewPath}.create", $this->formViewData());
    }

    /**
     * Store newly created resource.
     */
    public function store(Request $request)
    {
        if ($this->createRequestClass) {
            app($this->createRequestClass);
        }

        try {
            if (method_exists($this->repository, 'createWithRelations')) {
                $this->repository->createWithRelations($request->all());
            } else {
                $this->repository->create($request->all());
            }

            flash()->success(__('messages.saved', ['model' => __($this->modelTranslation)]));
            return redirect()->route("basicdata.{$this->viewPath}.index");
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __($this->modelTranslation)]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display specified resource.
     */
    public function show($id)
    {
        $item = $this->repository->find($id);

        if (empty($item)) {
            flash()->error(__($this->modelTranslation) . ' ' . __('messages.not_found'));
            return redirect(route("basicdata.{$this->viewPath}.index"));
        }

        $singleVarName = \Illuminate\Support\Str::singular($this->viewPath);
        return view("basicdata::{$this->viewPath}.show", [$singleVarName => $item]);
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $item = $this->repository->find($id);

        if (empty($item)) {
            flash()->error(__($this->modelTranslation) . ' ' . __('messages.not_found'));
            return redirect(route("basicdata.{$this->viewPath}.index"));
        }

        $singleVarName = \Illuminate\Support\Str::singular($this->viewPath);
        $viewData = array_merge([
            $singleVarName => $item,
        ], $this->formViewData((int)$id));

        return view("basicdata::{$this->viewPath}.edit", $viewData);
    }

    /**
     * Update specified resource.
     */
    public function update(Request $request, $id)
    {
        if ($this->updateRequestClass) {
            app($this->updateRequestClass);
        }

        try {
            $item = $this->repository->find($id);

            if (empty($item)) {
                flash()->error(__($this->modelTranslation) . ' ' . __('messages.not_found'));
                return redirect(route("basicdata.{$this->viewPath}.index"));
            }

            if (method_exists($this->repository, 'updateWithRelations')) {
                $this->repository->updateWithRelations($request->all(), (int)$id);
            } else {
                $this->repository->update($request->all(), $id);
            }

            flash()->success(__('messages.updated', ['model' => __($this->modelTranslation)]));
            return redirect()->route("basicdata.{$this->viewPath}.index");
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __($this->modelTranslation)]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove specified resource.
     */
    public function destroy($id)
    {
        try {
            $item = $this->repository->find($id);

            if (empty($item)) {
                flash()->error(__($this->modelTranslation) . ' ' . __('messages.not_found'));
                return redirect(route("basicdata.{$this->viewPath}.index"));
            }

            $this->repository->delete($id);
            flash()->success(__('messages.deleted', ['model' => __($this->modelTranslation)]));
            return redirect()->route("basicdata.{$this->viewPath}.index");
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __($this->modelTranslation)]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
