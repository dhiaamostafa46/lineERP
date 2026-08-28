<?php

namespace Modules\HR\App\Http\Controllers;


use Illuminate\Http\Request;
use Modules\HR\App\Models\HrTrackerJob;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrTrackerJobRepository;
use Modules\HR\App\Http\Requests\CreateHrTrackerJobRequest;
use Modules\HR\App\Http\Requests\UpdateHrTrackerJobRequest;

class HrTrackerJobController extends AppBaseController
{
    /** @var HrTrackerJobRepository $hrTrackerJobRepository*/
    private $hrTrackerJobRepository;

    public function __construct(HrTrackerJobRepository $hrTrackerJobRepo)
    {
        $this->hrTrackerJobRepository = $hrTrackerJobRepo;
    }

    /**
     * Display a listing of the HrTrackerJob.
     */
    public function index(Request $request)
    {
        $data['tracker_jobs'] = $this->hrTrackerJobRepository->paginate(10);
        return view('hr::tracker_jobs.index', $data);
    }

    /**
     * Show the form for creating a new HrTrackerJob.
     */
    public function create()
    {
        return view('hr::tracker_jobs.create');
    }

    /**
     * Store a newly created HrTrackerJob in storage.
     */
    public function store(CreateHrTrackerJobRequest $request)
    {
        $input = $request->all();

        $tracker_job = $this->hrTrackerJobRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrTrackerJobs.singular')]));

        return redirect(route('hr.tracking-jobs.index'));
    }

    /**
     * Display the specified HrTrackerJob.
     */
    public function show($id)
    {
        $data['tracker_job'] = $this->hrTrackerJobRepository->find($id);

        if (empty($data['tracker_job'])) {
            flash()->error(__('models/hrTrackerJobs.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-jobs.index'));
        }

        return view('hr::tracker_jobs.show', $data);
    }

    /**
     * Show the form for editing the specified HrTrackerJob.
     */
    public function edit($id)
    {
        $data['tracker_job'] = $this->hrTrackerJobRepository->find($id);

        if (empty($data['tracker_job'])) {
            flash()->error(__('models/hrTrackerJobs.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-jobs.index'));
        }

        return view('hr::tracker_jobs.edit', $data);
    }

    /**
     * Update the specified HrTrackerJob in storage.
     */
    public function update($id, UpdateHrTrackerJobRequest $request)
    {
        $tracker_job = $this->hrTrackerJobRepository->find($id);

        if (empty($tracker_job)) {
            flash()->error(__('models/hrTrackerJobs.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-jobs.index'));
        }

        $tracker_job = $this->hrTrackerJobRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hrTrackerJobs.singular')]));

        return redirect(route('hr.tracking-jobs.index'));
    }

    /**
     * Remove the specified HrTrackerJob from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tracker_job = $this->hrTrackerJobRepository->find($id);

        if (empty($tracker_job)) {
            flash()->error(__('models/hrTrackerJobs.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-jobs.index'));
        }

        $this->hrTrackerJobRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrTrackerJobs.singular')]));

        return redirect(route('hr.tracking-jobs.index'));
    }
}
