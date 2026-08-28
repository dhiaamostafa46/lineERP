<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrPayrollEmployeeRequest;
use Modules\HR\App\Http\Requests\UpdateHrPayrollEmployeeRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrPayrollEmployeeRepository;
use Illuminate\Http\Request;


class HrPayrollEmployeeController extends AppBaseController
{
    /** @var HrPayrollEmployeeRepository $hrPayrollEmployeeRepository*/
    private $hrPayrollEmployeeRepository;

    public function __construct(HrPayrollEmployeeRepository $hrPayrollEmployeeRepo)
    {
        $this->hrPayrollEmployeeRepository = $hrPayrollEmployeeRepo;
    }

    /**
     * Display a listing of the HrPayrollEmployee.
     */
    public function index(Request $request)
    {
        $hrPayrollEmployees = $this->hrPayrollEmployeeRepository->paginate(10);

        return view('hr::payroll_employees.index')
            ->with('hrPayrollEmployees', $hrPayrollEmployees);
    }

    /**
     * Show the form for creating a new HrPayrollEmployee.
     */
    public function create()
    {
        return view('hr::payroll_employees.create');
    }

    /**
     * Store a newly created HrPayrollEmployee in storage.
     */
    public function store(CreateHrPayrollEmployeeRequest $request)
    {
        $input = $request->all();

        $hrPayrollEmployee = $this->hrPayrollEmployeeRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrPayrollEmployees.singular')]));

        return redirect(route('hrPayrollEmployees.index'));
    }

    /**
     * Display the specified HrPayrollEmployee.
     */
    public function show($id)
    {
        $hrPayrollEmployee = $this->hrPayrollEmployeeRepository->find($id);

        if (empty($hrPayrollEmployee)) {
            flash()->error(__('models/hrPayrollEmployees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollEmployees.index'));
        }

        return view('hr::payroll_employees.show')->with('hrPayrollEmployee', $hrPayrollEmployee);
    }

    /**
     * Show the form for editing the specified HrPayrollEmployee.
     */
    public function edit($id)
    {
        $hrPayrollEmployee = $this->hrPayrollEmployeeRepository->find($id);

        if (empty($hrPayrollEmployee)) {
            flash()->error(__('models/hrPayrollEmployees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollEmployees.index'));
        }

        return view('hr::payroll_employees.edit')->with('hrPayrollEmployee', $hrPayrollEmployee);
    }

    /**
     * Update the specified HrPayrollEmployee in storage.
     */
    public function update($id, UpdateHrPayrollEmployeeRequest $request)
    {
        $hrPayrollEmployee = $this->hrPayrollEmployeeRepository->find($id);

        if (empty($hrPayrollEmployee)) {
            flash()->error(__('models/hrPayrollEmployees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollEmployees.index'));
        }

        $hrPayrollEmployee = $this->hrPayrollEmployeeRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hrPayrollEmployees.singular')]));

        return redirect(route('hrPayrollEmployees.index'));
    }

    /**
     * Remove the specified HrPayrollEmployee from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrPayrollEmployee = $this->hrPayrollEmployeeRepository->find($id);

        if (empty($hrPayrollEmployee)) {
            flash()->error(__('models/hrPayrollEmployees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollEmployees.index'));
        }

        $this->hrPayrollEmployeeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrPayrollEmployees.singular')]));

        return redirect(route('hrPayrollEmployees.index'));
    }
}
