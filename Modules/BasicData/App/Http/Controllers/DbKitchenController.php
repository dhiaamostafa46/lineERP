<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\BasicDataApp\Kitchen;
use App\Traits\HasBulkActions;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Controllers\Concerns\HasExportActions;
use Modules\BasicData\App\Http\Requests\CreateDbKitchenRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbKitchenRequest;
use Modules\BasicData\App\Repositories\DbKitchenRepository;

class DbKitchenController extends AppBaseController
{
    use HasBulkActions, HasExportActions;

    protected DbKitchenRepository $repository;
    protected string $exportFileName = 'kitchens';

    public function __construct(DbKitchenRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the Kitchen.
     */
    public function index(Request $request)
    {
        $pagination = (int)$request->get('pagination', 10);
        $kitchens = $this->repository->allQuery($request->except('pagination'))->paginate($pagination);

        return view('basicdata::kitchens.index', [
            'kitchens' => $kitchens,
            'statuses' => $this->repository->statuses(),
            'totalKitchensCount' => Kitchen::count(),
            'activeKitchensCount' => Kitchen::where('status', 1)->count(),
        ]);
    }

    /**
     * Show the form for creating a new Kitchen.
     */
    public function create()
    {
        return view('basicdata::kitchens.create', [
            'statuses' => $this->repository->statuses(),
        ]);
    }

    /**
     * Store a newly created Kitchen in storage.
     */
    public function store(CreateDbKitchenRequest $request)
    {
        try {
            $this->repository->create($request->all());
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_kitchens.singular')]));
            return redirect()->route('basicdata.kitchens.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_kitchens.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Kitchen.
     */
    public function show($id)
    {
        $kitchen = $this->repository->find($id);

        if (empty($kitchen)) {
            flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.kitchens.index'));
        }

        return view('basicdata::kitchens.show', compact('kitchen'));
    }

    /**
     * Show the form for editing the specified Kitchen.
     */
    public function edit($id)
    {
        $kitchen = $this->repository->find($id);

        if (empty($kitchen)) {
            flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.kitchens.index'));
        }

        return view('basicdata::kitchens.edit', [
            'kitchen' => $kitchen,
            'statuses' => $this->repository->statuses(),
        ]);
    }

    /**
     * Update the specified Kitchen in storage.
     */
    public function update(UpdateDbKitchenRequest $request, $id)
    {
        try {
            $kitchen = $this->repository->find($id);

            if (empty($kitchen)) {
                flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.kitchens.index'));
            }

            $this->repository->update($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_kitchens.singular')]));
            return redirect()->route('basicdata.kitchens.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_kitchens.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Kitchen from storage.
     */
    public function destroy($id)
    {
        try {
            $kitchen = $this->repository->find($id);

            if (empty($kitchen)) {
                flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.kitchens.index'));
            }

            $this->repository->delete($id);
            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_kitchens.singular')]));
            return redirect()->route('basicdata.kitchens.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_kitchens.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
