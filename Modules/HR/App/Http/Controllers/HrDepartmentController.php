<?php

namespace Modules\HR\App\Http\Controllers;


use App\Models\Employee;
use Modules\HR\App\Models\HrDepartment;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrDepartmentRepository;
use Modules\HR\App\Http\Requests\CreateHrDepartmentRequest;
use Modules\HR\App\Http\Requests\UpdateHrDepartmentRequest;

class HrDepartmentController extends AppBaseController
{
    /** @var HrDepartmentRepository $hrDepartmentRepository*/
    private $hrDepartmentRepository;

    public function __construct(HrDepartmentRepository $hrDepartmentRepo)
    {
        $this->hrDepartmentRepository = $hrDepartmentRepo;
    }

    /**
     * Display a listing of the HrDepartment.
     */
    public function index(Request $request)
    {
        $data['departments'] = $this->hrDepartmentRepository->allQuery($request->except('pagination'))->latest()->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->hrDepartmentRepository->statuses();
        $data['types'] = $this->hrDepartmentRepository->types();
        return view('hr::departments.index', $data);
    }

    /**
     * Show the form for creating a new HrDepartment.
     */
    public function create()
    {
        $data['owners'] = $this->hrDepartmentRepository->owners();
        $data['parents'] = $this->hrDepartmentRepository->parents();
        $data['statuses'] = $this->hrDepartmentRepository->statuses();
        $data['types'] = $this->hrDepartmentRepository->types();
        return view('hr::departments.create', $data);
    }

    /**
     * Store a newly created HrDepartment in storage.
     */
    public function store(CreateHrDepartmentRequest $request)
    {
        $input = $request->all();

        $department = $this->hrDepartmentRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_departments.singular')]));

        return redirect(route('hr.departments.index'));
    }

    /**
     * Display the specified HrDepartment.
     */
    public function show($id)
    {
        $department = $this->hrDepartmentRepository->find($id);

        if (empty($department)) {
            flash()->error(__('hr::models/hr_departments.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.departments.index'));
        }

        return view('hr::departments.show')->with('department', $department);
    }

    /**
     * Show the form for editing the specified HrDepartment.
     */
    public function edit($id)
    {
        $data['department'] = $this->hrDepartmentRepository->find($id);
        if (empty($data['department'])) {
            flash()->error(__('hr::models/hr_departments.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.departments.index'));
        }

        $data['owners'] = $this->hrDepartmentRepository->owners();
        $data['parents'] = $this->hrDepartmentRepository->parents();
        $data['statuses'] = $this->hrDepartmentRepository->statuses();
        $data['types'] = $this->hrDepartmentRepository->types();

        return view('hr::departments.edit', $data);
    }

    /**
     * Update the specified HrDepartment in storage.
     */
    public function update($id, UpdateHrDepartmentRequest $request)
    {
        $department = $this->hrDepartmentRepository->find($id);

        if (empty($department)) {
            flash()->error(__('hr::models/hr_departments.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.departments.index'));
        }

        $department = $this->hrDepartmentRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_departments.singular')]));

        return redirect(route('hr.departments.index'));
    }

    /**
     * Remove the specified HrDepartment from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $department = $this->hrDepartmentRepository->find($id);

        if (empty($department)) {
            flash()->error(__('hr::models/hr_departments.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.departments.index'));
        }

        $this->hrDepartmentRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_departments.singular')]));

        return redirect(route('hr.departments.index'));
    }
}
