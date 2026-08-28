<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeIdentity;
use App\Repositories\EmployeeRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends AppBaseController
{
    /** @var EmployeeRepository $employeeRepository*/
    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepo)
    {
        $this->employeeRepository = $employeeRepo;
    }

    /**
     * Display a listing of the Employee.
     */
    public function index(Request $request)
    {
        $employees = $this->employeeRepository->paginate(10);

        return view('employees.index')
            ->with('employees', $employees);
    }

    /**
     * Show the form for creating a new Employee.
     */
    public function create()
    {
        $data['branches']        = $this->employeeRepository->branches();
        $data['genders']         = Employee::genders();
        $data['maritalStatuses'] = Employee::maritalStatuses();
        $data['identityTypes']   = EmployeeIdentity::types();


        return view('employees.create', $data);
    }

    /**
     * Store a newly created Employee in storage.
     */
    public function store(CreateEmployeeRequest $request)
    {
        $input = $request->all();

        $employee = $this->employeeRepository->create($input);

        $employee->identity()->create($input);
        $employee->bank()->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/employees.singular')]));

        return redirect(route('employees.index'));
    }

    /**
     * Display the specified Employee.
     */
    public function show($id)
    {
        $employee = $this->employeeRepository->find($id);

        if (empty($employee)) {
            flash()->error(__('models/employees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('employees.index'));
        }

        return view('employees.show')->with('employee', $employee);
    }

    /**
     * Show the form for editing the specified Employee.
     */
    public function edit($id)
    {
        $employee = $this->employeeRepository->find($id);

        $data['genders']         = Employee::genders();
        $data['maritalStatuses'] = Employee::maritalStatuses();
        $data['identityTypes']   = EmployeeIdentity::types();
        $data['employee']        = $employee;

        if (empty($employee)) {
            flash()->error(__('models/employees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('employees.index'));
        }

        return view('employees.edit', $data);
    }

    /**
     * Update the specified Employee in storage.
     */
    public function update($id, UpdateEmployeeRequest $request)
    {
        $employee = $this->employeeRepository->find($id);

        if (empty($employee)) {
            flash()->error(__('models/employees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('employees.index'));
        }

        $employee = $this->employeeRepository->update($request->all(), $id);

        // dd($request->all());
        $employee->identity()->update($request->only(['identity_type', 'identity_no', 'insurance_no', 'identity_expired_at', 'insurance_expired_at']));
        $employee->bank()->update($request->only(['iban', 'bank_name']));

        flash()->success(__('messages.updated', ['model' => __('models/employees.singular')]));

        return redirect(route('employees.index'));
    }


    

    /**
     * Remove the specified Employee from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $employee = $this->employeeRepository->find($id);

        if (empty($employee)) {
            flash()->error(__('models/employees.singular') . ' ' . __('messages.not_found'));

            return redirect(route('employees.index'));
        }

        $this->employeeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/employees.singular')]));

        return redirect(route('employees.index'));
    }
}
