<?php

namespace Modules\HR\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\App\Models\HrJob;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrAttendancePolicy;
use Modules\HR\App\Repositories\HrAttendancePolicyRepository;
use Illuminate\Contracts\Support\Renderable;

class HrAttendancePolicyController extends Controller
{
    private $hrAttendancePolicyRepository;

    public function __construct(HrAttendancePolicyRepository $hrAttendancePolicyRepo)
    {
        $this->hrAttendancePolicyRepository = $hrAttendancePolicyRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $data['policies'] = $this->hrAttendancePolicyRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['types'] = $this->hrAttendancePolicyRepository->types();
        $data['scopes'] = $this->hrAttendancePolicyRepository->scopes();
        $data['statuses'] = $this->hrAttendancePolicyRepository->statuses();
        $data['employees'] = $this->hrAttendancePolicyRepository->employees();
        $data['departments'] = $this->hrAttendancePolicyRepository->departments();

        return view('hr::attendance_policies.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
         $data['salarys'] = $this->hrAttendancePolicyRepository->salarys();
        $data['types'] = $this->hrAttendancePolicyRepository->types();
        $data['scopes'] = $this->hrAttendancePolicyRepository->scopes();
        $data['statuses'] = $this->hrAttendancePolicyRepository->statuses();
        $data['employees'] = $this->hrAttendancePolicyRepository->employees();
        $data['automatic'] = $this->hrAttendancePolicyRepository->automatics();
        $data['calculationType'] = $this->hrAttendancePolicyRepository->calculationTypes();
        $data['departments'] = $this->hrAttendancePolicyRepository->departments();
        $data['jobs'] = $this->hrAttendancePolicyRepository->jobs();
        $data['branches'] = $this->hrAttendancePolicyRepository->Branches();

        return view('hr::attendance_policies.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate(HrAttendancePolicy::rules());


   // dd($request->all());

        $this->hrAttendancePolicyRepository->create($request->all());

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_attendance_policies.singular')]));

        return redirect(route('hr.attendance-policies.index'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data['policy'] = $this->hrAttendancePolicyRepository->find($id);

        if (empty($data['policy'])) {
            flash()->error(__('hr::models/hr_attendance_policies.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.attendance_policies.index'));
        }

        return view('hr::attendance_policies.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data['policy'] = $this->hrAttendancePolicyRepository->find($id);

        if (empty($data['policy'])) {
            flash()->error(__('hr::models/hr_attendance_policies.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.attendance-policies.index'));
        }

        $data['salarys'] = $this->hrAttendancePolicyRepository->salarys();
        $data['types'] = $this->hrAttendancePolicyRepository->types();
        $data['scopes'] = $this->hrAttendancePolicyRepository->scopes();
        $data['statuses'] = $this->hrAttendancePolicyRepository->statuses();
        $data['employees'] = $this->hrAttendancePolicyRepository->employees();
        $data['automatic'] = $this->hrAttendancePolicyRepository->automatics();
        $data['calculationType'] = $this->hrAttendancePolicyRepository->calculationTypes();
        $data['departments'] = $this->hrAttendancePolicyRepository->departments();
        $data['jobs'] = $this->hrAttendancePolicyRepository->jobs();
        $data['branches'] = $this->hrAttendancePolicyRepository->Branches();

        return view('hr::attendance_policies.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate(HrAttendancePolicy::rules());

        $policy = $this->hrAttendancePolicyRepository->find($id);

        if (empty($policy)) {
            flash()->error(__('hr::models/hr_attendance_policies.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.attendance-policies.index'));
        }

        $this->hrAttendancePolicyRepository->update($request->all(), $id);
        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_attendance_policies.singular')]));
        return redirect(route('hr.attendance-policies.index'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $policy = $this->hrAttendancePolicyRepository->find($id);

        if (empty($policy)) {
            flash()->error(__('hr::models/hr_attendance_policies.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.attendance_policies.index'));
        }

        $this->hrAttendancePolicyRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_attendance_policies.singular')]));
        return redirect(route('hr.attendance-policies.index'));
    }
}
