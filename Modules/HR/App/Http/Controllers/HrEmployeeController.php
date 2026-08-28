<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\Role;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\HR\App\Exports\HrEmployeesExport;
use Modules\HR\App\Exports\HrFailedEmployeesExport;
use Modules\HR\App\Http\Requests\CreateHrEmployeeRequest;
use Modules\HR\App\Http\Requests\UpdateHrEmployeeRequest;
use Modules\HR\App\Imports\HrEmployeesImport;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrSalaryRepository;

class HrEmployeeController extends AppBaseController
{
    /** @var HrEmployeeRepository */
    private $hrEmployeeRepository;

    /** @var EmployeeRepository */
    private $employeeRepository;

    private $hrSalaryRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(HrEmployeeRepository $hrEmployeeRepo, EmployeeRepository $employeeRepo, UserRepository $userRepo, HrSalaryRepository $hrSalaryRepo)
    {
        $this->hrEmployeeRepository = $hrEmployeeRepo;
        $this->employeeRepository = $employeeRepo;
        $this->userRepository = $userRepo;

        $this->hrSalaryRepository = $hrSalaryRepo;
    }

    /**
     * Display a listing of the HrEmployee.
     */
    public function index()
    {
        return view('hr::employees.index');
    }

    /**
     * Show the form for creating a new HrEmployee.
     */
    public function create()
    {
        $data['employees'] = $this->hrEmployeeRepository->employees();
        $data['branches'] = $this->employeeRepository->branches();
        $data['jobs'] = $this->hrEmployeeRepository->jobs();
        $data['departments'] = $this->hrEmployeeRepository->departments();
        $data['shifts'] = $this->hrEmployeeRepository->shifts();
        $data['genders'] = $this->hrEmployeeRepository->genders();
        $data['maritalStatuses'] = $this->hrEmployeeRepository->maritalStatuses();
        $data['identityTypes'] = $this->hrEmployeeRepository->identityTypes();

        // $data['user_roles'] = $this->hrEmployeeRepository->user_roles();
        $data['user_roles'] = Role::where('name', 'موظف')->first();
        // dd( $data['user_roles']);
        $data['user_statuses'] = $this->hrEmployeeRepository->user_statuses();
        $data['allowances'] = $this->hrSalaryRepository->allowances();
        $data['deducts'] = $this->hrSalaryRepository->deducts();

        // fingerprint_exempt
        $data['fingerprint_exempts'] = $this->hrEmployeeRepository->fingerprintExempts();
        // fingerprint_exempt
        $data['attendance_types'] = $this->hrEmployeeRepository->AttendanceTypes();

        return view('hr::employees.create', $data);
    }

    /**
     * Store a newly created HrEmployee in storage.
     */
    public function store(CreateHrEmployeeRequest $request)
    {
        DB::beginTransaction();

        try {
            $main_employee_inputs = $request->only('full_name', 'username', 'phone', 'email', 'dob', 'address', 'national_address', 'religion', 'gender', 'marital_status', 'number_of_children', 'nationality', 'branch_id');
            $user_input = $request->user;
            $user_input['job_number'] = $request->job_number;
            $user_input['branch_id'] = $request->branch_id;
            $user = $this->userRepository->create($user_input);
            $user->assignRole($request->user['role_id']);

            $main_employee_inputs['user_id'] = $user->id;
            $main_employee = $this->employeeRepository->create($main_employee_inputs);
            $employee_inputs = $request->only('job_id', 'username', 'department_id', 'shift_id', 'max_off_days', 'max_advance', 'job_level', 'specialty', 'start_at', 'license_expired_at', 'Direct_manager', 'job_number', 'vacation_balance', 'attendance_type');
            $employee_inputs['fingerprint_exempt'] = $request->input('fingerprint_exempt') ? 1 : 0;

            $employee_inputs['employee_id'] = $main_employee->id;
            $employee = $this->hrEmployeeRepository->create($employee_inputs);
            $main_employee->identity()->create($request->only('identity_type', 'identity_no', 'insurance_no', 'identity_expired_at', 'insurance_expired_at'));

            $main_employee->bank()->create($request->only('iban', 'bank_name'));
            $salary = $this->hrEmployeeRepository->create_salary($employee->id, $request->only('basic'));

            $this->hrSalaryRepository->create_allowances($request->allowances, $salary->id);
            $this->hrSalaryRepository->create_deducts($request->deducts, $salary->id);

            $Balance = $request->only('max_off_days', 'vacation_balance');

            $Balance['employee_id'] = $main_employee->hrEmployee->id;

            DB::commit(); // 🟢 Commit the transaction

            flash()->success(__('messages.saved', ['model' => __('hr::models/hr_employees.singular')]));

            return redirect(route('hr.employees.index'));
        } catch (\Exception $e) {
            DB::rollBack(); // 🔴 Rollback on error
            // Log the exception for debugging purposes

            flash()->error(__('messages.error', ['model' => __('hr::models/hr_employees.singular')]));

            return redirect(route('hr.employees.index'));
        }
    }

    /**
     * Display the specified HrEmployee.
     */
    public function show($id)
    {
        $data['employee'] = $this->hrEmployeeRepository->find($id);

        if (empty($data['employee'])) {
            flash()->error(__('hr::models/hr_employees.singular').' '.__('messages.not_found'));

            return redirect(route('hr.employees.index'));
        }

        return view('hr::employees.show', $data);
    }

    /**
     * Show the form for editing the specified HrEmployee.
     */
    public function edit($id)
    {
        $data['employee'] = $this->hrEmployeeRepository->find($id);

        if (empty($data['employee'])) {
            flash()->error(__('hr::models/hr_employees.singular').' '.__('messages.not_found'));

            return redirect(route('hr.employees.index'));
        }
        $data['employees'] = $this->hrEmployeeRepository->employees();
        $data['branches'] = $this->employeeRepository->branches();
        $data['jobs'] = $this->hrEmployeeRepository->jobs();
        $data['departments'] = $this->hrEmployeeRepository->departments();
        $data['shifts'] = $this->hrEmployeeRepository->shifts();
        $data['genders'] = $this->hrEmployeeRepository->genders();
        $data['maritalStatuses'] = $this->hrEmployeeRepository->maritalStatuses();
        $data['identityTypes'] = $this->hrEmployeeRepository->identityTypes();
        $data['user_roles'] = $this->hrEmployeeRepository->user_roles();
        // $data['user_roles'] = Role::where('name','موظف')->first();
        $data['user_statuses'] = $this->hrEmployeeRepository->user_statuses();
        $salary = HrSalary::where('employee_id', $data['employee']->id)->firstOrFail();
        $data['allowances'] = $this->hrSalaryRepository->allowances();
        $data['deducts'] = $this->hrSalaryRepository->deducts();
        $data['salary'] = $salary->load('allowances', 'deducts');
        $data['salary_allowances'] = $salary->allowances->pluck('pivot.amount', 'id')->toArray();
        $data['salary_deducts'] = $salary->deducts->pluck('pivot.amount', 'id')->toArray();
        $data['User_data'] = $data['employee']->main_employee->User ?? [];

        // fingerprint_exempt
        $data['fingerprint_exempts'] = $this->hrEmployeeRepository->fingerprintExempts();
        // fingerprint_exempt
        $data['attendance_types'] = $this->hrEmployeeRepository->AttendanceTypes();

        // dd($data['User_data']);
        return view('hr::employees.edit', $data);
    }

    /**
     * Update the specified HrEmployee in storage.
     */
    public function update($id, UpdateHrEmployeeRequest $request)
    {
        // dd($request->all());
        $employee = $this->hrEmployeeRepository->find($id);
        if (empty($employee)) {
            flash()->error(__('hr::models/hr_employees.singular').' '.__('messages.not_found'));

            return redirect(route('hr.employees.index'));
        }

        $main_employee = $this->employeeRepository->update($request->only('full_name', 'username', 'phone', 'email', 'dob', 'address', 'national_address', 'religion', 'gender', 'marital_status', 'number_of_children', 'nationality', 'branch_id'), $employee->employee_id);

        $hr_employee_inputs = $request->only('job_id', 'username', 'department_id', 'shift_id', 'max_off_days', 'max_advance', 'job_level', 'specialty', 'start_at', 'license_expired_at', 'Direct_manager', 'job_number', 'vacation_balance', 'attendance_type');
        $hr_employee_inputs['fingerprint_exempt'] = $request->input('fingerprint_exempt') ? 1 : 0;
        $employee = $this->hrEmployeeRepository->update($hr_employee_inputs, $id);

        $main_employee->identity()->update($request->only(['identity_type', 'identity_no', 'insurance_no', 'identity_expired_at', 'insurance_expired_at']));

        $main_employee->bank()->update($request->only(['iban', 'bank_name']));

        $salary = $this->hrEmployeeRepository->create_salary($employee->id, $request->only('basic'));

        $this->hrSalaryRepository->create_allowances($request->allowances, $salary->id);
        $this->hrSalaryRepository->create_deducts($request->deducts, $salary->id);

        // dd($main_employee->user_id);
        // $data['employee']->main_employee->User
        $user_input = $request->user;
        $user_input['job_number'] = $request->job_number;
        $user_input['branch_id'] = $request->branch_id;
        $this->userRepository->update($user_input, $main_employee->user_id);
        if (! empty($request->role_id)) {
            $employee->user->assignRole($request->role_id);
        }

        $Balance = $request->only('max_off_days', 'vacation_balance');
        $Balance['employee_id'] = $main_employee->id;
        // $this->HrHolidayBalanceRepository->CreateOrUpdate( $Balance);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_employees.singular')]));

        return redirect(route('hr.employees.index'));
    }

    /**
     * Remove the specified HrEmployee from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $employee = $this->hrEmployeeRepository->find($id);

        if (empty($employee)) {
            flash()->error(__('hr::models/hr_employees.singular').' '.__('messages.not_found'));

            return redirect(route('hr.employees.index'));
        }

        $msg = $this->hrEmployeeRepository->DeleteEmp($employee);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_employees.singular')]));

        return redirect(route('hr.employees.index'))->with('msg', $msg);
    }

    /**
     * export the specified HrEmployee.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $list = $this->hrEmployeeRepository->all();

        return Excel::download(new HrEmployeesExport($list), 'employees.xlsx');
    }

    /**
     * export the specified HrEmployee.
     *
     * @return \Illuminate\Http\Response
     */
    public function export_selected(Request $request)
    {
        $request->validate([
            'select_employees' => 'required|array',
        ]);
        $list = $this->hrEmployeeRepository->allQuery([])->whereIn('id', $request->employees)->get();

        return Excel::download(new HrEmployeesExport($list), 'employees.xlsx');
    }

    /**
     * import the specified HrEmployee.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $import = new HrEmployeesImport;
            Excel::import($import, $request->file('file'));

            $failedRows = $import->getFailedRows();
            $errors = $import->getErrors();

            if (! empty($failedRows)) {
                // If there are failures, return the download of failed rows
                return Excel::download(new HrFailedEmployeesExport($failedRows), 'failed_employees_'.now()->format('Y-m-d_H-i-s').'.xlsx');

            }

            if (! empty($errors)) {
                // This covers any errors that might not have captured row data (though we try to capture it)
                return redirect()->back()->with('import_errors', $errors);
            }

            // If import is successful without any errors
            flash()->success('تم استيراد الموظفين بنجاح!');

            return redirect()->back()->with('import_success', 'تم استيراد الموظفين بنجاح!');
        } catch (\Exception $e) {
            flash()->error('حدث خطأ أثناء استيراد الملف: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء استيراد الملف: '.$e->getMessage());
        }
    }
}
