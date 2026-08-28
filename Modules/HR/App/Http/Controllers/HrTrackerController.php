<?php

namespace Modules\HR\App\Http\Controllers;


use Illuminate\Http\Request;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrDepartment;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Models\HrTrackingApproval;
use Modules\HR\App\Repositories\HrTrackerRepository;
use Modules\HR\App\Repositories\HrTrackerJobRepository;
use Modules\HR\App\Http\Requests\CreateHrTrackerRequest;
use Modules\HR\App\Http\Requests\UpdateHrTrackerRequest;

class HrTrackerController extends AppBaseController
{
    /** @var HrTrackerRepository $hrTrackerRepository*/
    private $hrTrackerRepository;
    /** @var HrTrackerJobRepository $hrTrackerRepository*/
    private $hrTrackerJobRepository;

    public function __construct(HrTrackerRepository $hrTrackerRepo, HrTrackerJobRepository $hrTrackerJobRepo)
    {
        $this->hrTrackerRepository = $hrTrackerRepo;
        $this->hrTrackerJobRepository = $hrTrackerJobRepo;
    }

    /**
     * Display a listing of the HrTracker.
     */
    public function index(Request $request)
    {
        $data['trackers'] = $this->hrTrackerRepository->paginate(10);

        return view('hr::trackers.index', $data);
    }

    /**
     * Show the form for creating a new HrTracker.
     */
    public function create()
    {
        $data['types'] = $this->hrTrackerRepository->types();
        $data['statuses'] = $this->hrTrackerRepository->statuses();
        $data['departments'] = $this->hrTrackerRepository->departments();
        $data['users'] = $this->hrTrackerRepository->users();

        return view('hr::trackers.create', $data);
    }

    /**
     * Store a newly created HrTracker in storage.
     */
    public function store(CreateHrTrackerRequest $request)
    {
        $input = $request->all();
        $tracker = $this->hrTrackerRepository->create($input);
        $tracker->jobs()->sync($input['jobs'] ?? []);
        flash()->success(__('messages.saved', ['model' => __('models/hr_trackers.singular')]));
        return redirect(route('hr.trackers.index'));
    }

    /**
     * Display the specified HrTracker.
     */
    public function show($id)
    {
        $tracker = $this->hrTrackerRepository->find($id);

        if (empty($tracker)) {
            flash()->error(__('models/hr_trackers.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.trackers.index'));
        }

        return view('hr::trackers.show',compact('tracker'));
    }

    /**
     * Show the form for editing the specified HrTracker.
     */
    public function edit($id)
    {
        $tracker = $this->hrTrackerRepository->find($id);

        if (empty($tracker)) {
            flash()->error(__('models/hr_trackers.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.trackers.index'));
        }

        $data['types'] = $this->hrTrackerRepository->types();
        $data['statuses'] = $this->hrTrackerRepository->statuses();
        $data['departments'] = $this->hrTrackerRepository->departments();
        $data['users'] = $this->hrTrackerRepository->users();
        $data['approvals'] = $tracker->tracker_approvals;
        $data['tracker'] = $tracker->load('jobs');

        return view('hr::trackers.edit', $data);
    }

    /**
     * Update the specified HrTracker in storage.
     */
    public function update($id, UpdateHrTrackerRequest $request)
    {
        $tracker = $this->hrTrackerRepository->find($id);

        if (empty($tracker)) {
            flash()->error(__('models/hr_trackers.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.trackers.index'));
        }
        $tracker = $this->hrTrackerRepository->update($request->all(), $id);
        $tracker->jobs()->sync($request->jobs);

        flash()->success(__('messages.updated', ['model' => __('models/hr_trackers.singular')]));

        return redirect(route('hr.trackers.index'));
    }

    /**
     * Remove the specified HrTracker from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tracker = $this->hrTrackerRepository->find($id);

        if (empty($tracker)) {
            flash()->error(__('models/hr_trackers.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.trackers.index'));
        }

        $this->hrTrackerRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hr_trackers.singular')]));

        return redirect(route('hr.trackers.index'));
    }


    public function getDepartmentJobs()
    {
        $department = HrDepartment::find(request('department_id'));

        return response()->json($department->jobs);
    }
}
