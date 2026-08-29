<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\BasicDataApp\ServicePoint;
use App\Traits\HasBulkActions;
use Illuminate\Http\Request;
use Modules\BasicData\App\Http\Controllers\Concerns\HasExportActions;
use Modules\BasicData\App\Http\Requests\CreateDbServicePointRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbServicePointRequest;
use Modules\BasicData\App\Repositories\DbServicePointRepository;

class DbServicePointController extends AppBaseController
{
    use HasBulkActions, HasExportActions;

    protected DbServicePointRepository $repository;
    protected string $exportFileName = 'service_points';

    public function __construct(DbServicePointRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the ServicePoint.
     */
    public function index(Request $request)
    {
        $pagination = (int)$request->get('pagination', 10);
        $servicePoints = $this->repository->allQuery($request->except('pagination'))->paginate($pagination);

        return view('basicdata::service_points.index', [
            'servicePoints' => $servicePoints,
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
            'totalPointsCount' => ServicePoint::count(),
            'activePointsCount' => ServicePoint::where('status', 1)->count(),
        ]);
    }

    /**
     * Show the form for creating a new ServicePoint.
     */
    public function create()
    {
        return view('basicdata::service_points.create', [
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
        ]);
    }

    /**
     * Store a newly created ServicePoint in storage.
     */
    public function store(CreateDbServicePointRequest $request)
    {
        try {
            $this->repository->create($request->all());
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_service_points.singular')]));
            return redirect()->route('basicdata.service_points.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_service_points.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified ServicePoint.
     */
    public function show($id)
    {
        $servicePoint = $this->repository->find($id);

        if (empty($servicePoint)) {
            flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.service_points.index'));
        }

        return view('basicdata::service_points.show', compact('servicePoint'));
    }

    /**
     * Show the form for editing the specified ServicePoint.
     */
    public function edit($id)
    {
        $servicePoint = $this->repository->find($id);

        if (empty($servicePoint)) {
            flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.service_points.index'));
        }

        return view('basicdata::service_points.edit', [
            'servicePoint' => $servicePoint,
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
        ]);
    }

    /**
     * Update the specified ServicePoint in storage.
     */
    public function update(UpdateDbServicePointRequest $request, $id)
    {
        try {
            $servicePoint = $this->repository->find($id);

            if (empty($servicePoint)) {
                flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.service_points.index'));
            }

            $this->repository->update($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_service_points.singular')]));
            return redirect()->route('basicdata.service_points.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_service_points.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified ServicePoint from storage.
     */
    public function destroy($id)
    {
        try {
            $servicePoint = $this->repository->find($id);

            if (empty($servicePoint)) {
                flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.service_points.index'));
            }

            $this->repository->delete($id);
            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_service_points.singular')]));
            return redirect()->route('basicdata.service_points.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_service_points.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
