<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\BasicDataApp\Unit;
use App\Traits\HasBulkActions;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Controllers\Concerns\HasExportActions;
use Modules\BasicData\App\Http\Requests\CreateDbUnitRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbUnitRequest;
use Modules\BasicData\App\Repositories\DbUnitRepository;

class DbUnitController extends AppBaseController
{
    use HasBulkActions, HasExportActions;

    protected DbUnitRepository $repository;
    protected string $exportFileName = 'units';

    public function __construct(DbUnitRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the Unit.
     */
    public function index(Request $request)
    {
        $pagination = (int)$request->get('pagination', 10);
        $units = $this->repository->allQuery($request->except('pagination'))->paginate($pagination);

        return view('basicdata::units.index', [
            'units' => $units,
            'statuses' => $this->repository->statuses(),
            'totalUnitsCount' => Unit::count(),
            'activeUnitsCount' => Unit::where('status', 1)->count(),
        ]);
    }

    /**
     * Show the form for creating a new Unit.
     */
    public function create()
    {
        return view('basicdata::units.create', [
            'statuses' => $this->repository->statuses(),
        ]);
    }

    /**
     * Store a newly created Unit in storage.
     */
    public function store(CreateDbUnitRequest $request)
    {
        try {
            $this->repository->create($request->all());
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_units.singular')]));
            return redirect()->route('basicdata.units.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_units.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Unit.
     */
    public function show($id)
    {
        $unit = $this->repository->find($id);

        if (empty($unit)) {
            flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.units.index'));
        }

        return view('basicdata::units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified Unit.
     */
    public function edit($id)
    {
        $unit = $this->repository->find($id);

        if (empty($unit)) {
            flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.units.index'));
        }

        return view('basicdata::units.edit', [
            'unit' => $unit,
            'statuses' => $this->repository->statuses(),
        ]);
    }

    /**
     * Update the specified Unit in storage.
     */
    public function update(UpdateDbUnitRequest $request, $id)
    {
        try {
            $unit = $this->repository->find($id);

            if (empty($unit)) {
                flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.units.index'));
            }

            $this->repository->update($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_units.singular')]));
            return redirect()->route('basicdata.units.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_units.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Unit from storage.
     */
    public function destroy($id)
    {
        try {
            $unit = $this->repository->find($id);

            if (empty($unit)) {
                flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.units.index'));
            }

            $this->repository->delete($id);
            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_units.singular')]));
            return redirect()->route('basicdata.units.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_units.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
