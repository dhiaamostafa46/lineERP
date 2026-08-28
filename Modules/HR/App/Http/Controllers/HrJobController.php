<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrJobRequest;
use Modules\HR\App\Http\Requests\UpdateHrJobRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrJobRepository;
use Illuminate\Http\Request;


class HrJobController extends AppBaseController
{
    /** @var HrJobRepository $hrJobRepository*/
    private $hrJobRepository;

    public function __construct(HrJobRepository $hrJobRepo)
    {
        $this->hrJobRepository = $hrJobRepo;
    }

    /**
     * Display a listing of the HrJob.
     */
    public function index(Request $request)
    {
        $data['jobs'] = $this->hrJobRepository->allQuery($request->except('pagination'))->latest()->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->hrJobRepository->statuses();
        $data['licenses'] = $this->hrJobRepository->licenses();

        return view('hr::jobs.index', $data);
    }

    /**
     * Show the form for creating a new HrJob.
     */
    public function create()
    {
        $data['statuses'] = $this->hrJobRepository->statuses();
        $data['licenses'] = $this->hrJobRepository->licenses();
        return view('hr::jobs.create', $data);
    }

    /**
     * Store a newly created HrJob in storage.
     */
    public function store(CreateHrJobRequest $request)
    {
        $input = $request->all();

        $job = $this->hrJobRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_jobs.singular')]));

        return redirect(route('hr.jobs.index'));
    }

    /**
     * Display the specified HrJob.
     */
    public function show($id)
    {
        $job = $this->hrJobRepository->find($id);

        if (empty($job)) {
            flash()->error(__('hr::models/hr_jobs.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.jobs.index'));
        }

        return view('hr::jobs.show')->with('job', $job);
    }

    /**
     * Show the form for editing the specified HrJob.
     */
    public function edit($id)
    {
        $data['job'] = $job = $this->hrJobRepository->find($id);

        if (empty($job)) {
            flash()->error(__('hr::models/hr_jobs.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.jobs.index'));
        }

        $data['statuses'] = $this->hrJobRepository->statuses();
        $data['licenses'] = $this->hrJobRepository->licenses();
        return view('hr::jobs.edit', $data);
    }

    /**
     * Update the specified HrJob in storage.
     */
    public function update($id, UpdateHrJobRequest $request)
    {
        $job = $this->hrJobRepository->find($id);

        if (empty($job)) {
            flash()->error(__('hr::models/hr_jobs.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.jobs.index'));
        }

        $job = $this->hrJobRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_jobs.singular')]));

        return redirect(route('hr.jobs.index'));
    }

    /**
     * Remove the specified HrJob from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $job = $this->hrJobRepository->find($id);

        if (empty($job)) {
            flash()->error(__('hr::models/hr_jobs.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.jobs.index'));
        }

        $this->hrJobRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_jobs.singular')]));

        return redirect(route('hr.jobs.index'));
    }
}
