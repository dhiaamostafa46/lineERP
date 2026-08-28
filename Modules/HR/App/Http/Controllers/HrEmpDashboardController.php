<?php

namespace Modules\HR\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\HrEmployeesExport;
use App\Imports\HrEmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\EmployeeRepository;
use App\Http\Controllers\AppBaseController;
use App\Repositories\UserRepository;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Models\HrPenalty;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Http\Requests\CreateHrEmployeeRequest;
use Modules\HR\App\Http\Requests\UpdateHrEmployeeRequest;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrDocument;
use Modules\HR\App\Models\HrEmployee;
use App\Models\Employee;
use App\Models\Organization;
use Modules\HR\App\Models\HrContract;
use Modules\HR\App\Models\HrTask;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Models\HrAbsentRequests;
use Modules\HR\App\Models\HrCustody;
use Modules\HR\App\Models\HrJustification;
use Modules\HR\App\Models\HrTimeTrack;
use Modules\HR\App\Repositories\HrRewardRepository;
use Modules\HR\App\Repositories\HrAttendanceRepository;

class HrEmpDashboardController extends AppBaseController
{
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var EmployeeRepository $employeeRepository*/
    private $employeeRepository;

    /** @var UserRepository $userRepository*/
    private $userRepository;
    /** @var UserRepository $userRepository*/
    private $attendRepository;

    public function __construct(HrEmployeeRepository $hrEmployeeRepo, EmployeeRepository $employeeRepo, UserRepository $userRepo, HrAttendanceRepository $atRepo)
    {
        $this->hrEmployeeRepository = $hrEmployeeRepo;
        $this->employeeRepository = $employeeRepo;
        $this->userRepository = $userRepo;
        $this->attendRepository = $atRepo;
    }

    private function getTask()
    {
        // جلب بيانات الموظف من المستخدم
        $employee = auth()->user()->employee;

        // dd($employee);
        // التأكد من وجود الموظف
        if (!$employee) {
            return collect();
        }

        // الحصول على employee_id و department_id
        $employeeId = $employee->HrEmployee->id; //$employee->id;
        $departmentId = $employee->HrEmployee->department_id ?? null;

        // فحص البيانات للتأكد

        // استعلام لإحضار المهام
        $task = HrTask::where(function ($query) use ($employeeId, $departmentId) {
            // إحضار المهام بناءً على employee_id
            $query->where('employee_id', $employeeId);

            // إحضار المهام بناءً على department_id إذا كانت موجودة
            if ($departmentId) {
                $query->orWhere('department_id', $departmentId);
            }
        })
            ->orWhereIn('group_id', function ($query) use ($employeeId) {
                // جلب معرفات المجموعات التي يكون فيها الموظف
                $query->select('hr_group_id')->from('hr_group_details')->where('employee_id', $employeeId);
            })
            ->get();

        return $task;
    }

    private function ShiftEmp()
    {
        $shifts = auth()->user()->employee->HrEmployee->shift->shifts ?? [];

        $currentTime = now(); // الوقت الحالي
        $currentTimeInSeconds = $currentTime->secondsSinceMidnight(); // تحويل الوقت الحالي إلى ثوانٍ منذ منتصف الليل

        $closestShift = null;
        $smallestDiff = PHP_INT_MAX; // تعيين قيمة كبيرة كبداية

        foreach ($shifts as $shift) {
            // تحويل وقت البداية والنهاية للوردية إلى ثوانٍ منذ منتصف الليل
            $fromTimeInSeconds = \Carbon\Carbon::createFromFormat('H:i:s', $shift->from)->secondsSinceMidnight();
            $toTimeInSeconds = \Carbon\Carbon::createFromFormat('H:i:s', $shift->to)->secondsSinceMidnight();

            // حساب الفرق المطلق بين الوقت الحالي ووقت البداية والنهاية
            $diffFromStart = abs($currentTimeInSeconds - $fromTimeInSeconds);
            $diffFromEnd = abs($currentTimeInSeconds - $toTimeInSeconds);

            // اختيار أقل فرق
            $minDiff = min($diffFromStart, $diffFromEnd);

            if ($minDiff < $smallestDiff) {
                $smallestDiff = $minDiff;
                $closestShift = $shift;
            }
        }

        // استرجاع بيانات الحضور بناءً على الدوام الأقرب
        if ($closestShift) {
            $attendance = HrAttendance::where('employee_id', auth()->user()->employee->id)
                ->where('date', date('Y-m-d'))
                ->where('shift_from', $closestShift->from)
                ->where('shift_to', $closestShift->to)
                ->get();
        } else {
            $attendance = HrAttendance::where('employee_id', auth()->user()->employee->id)
                ->where('date', date('Y-m-d'))
                ->get();
        }

        return $attendance;
    }

     public function index()
    {
        return view('hr::ProfileEmployees.show');
    }

    /**
     * Display a listing of the HrEmployee.
     */
    // public function index()
    // {
    //     ///$x  =Employee::where('id',4)->get();
    //     //dd(auth()->user()->employee);
    //     try {
    //         if (!auth()->user()->employee) {
    //             $data['isEmp'] = false;
    //         }

    //         if (auth()->user()->employee) {
    //             $data['shiftEmployees'] = $this->ShiftEmp();

    //             $x = Employee::where('id', 4)->get();
    //             //dd(auth()->user()->employee);
    //             $data['holidays'] = HrHoliday::where('employee_id', auth()->user()->employee->id)->paginate(10);

    //             $data['advances'] = HrAdvance::where('employee_id', auth()->user()->employee->HrEmployee->id)->paginate(10);
    //             $data['documents'] = HrDocument::where('employee_id', auth()->user()->employee->id)->paginate(10);

    //             $HrEmployee = HrEmployee::where('employee_id', auth()->user()->employee->id)->first();
    //             $data['employee'] = $HrEmployee;
    //             $data['contract'] = HrContract::where('employee_id', auth()->user()->employee->id)->first();
    //             $data['custodies'] = HrCustody::where('employee_id', auth()->user()->employee->id)->get();
    //             $data['penalties'] = HrPenalty::where('employee_id', auth()->user()->employee->id)->paginate(10);
    //             $data['justifications'] = HrJustification::where('employee_id', auth()->user()->employee->id)
    //                 ->orderBy('created_at', 'desc')
    //                 ->paginate(10);

    //             ///dd($data['employee']->salary);
    //             $data['tasks'] = $this->getTask();

    //             $user = auth()->user()->employee;
    //             $employee = auth()->user()->employee->HrEmployee->id ?? null;
    //             $branch = $user->branch_id ?? null;

    //             $department = $user->HrEmployee->department_id ?? null;
    //             $x = new HrAttendanceRepository();
    //             $data['weekdays'] = $x->weekdays();


    //              $data['Places'] = $this->attendRepository->Place($employee, $department, $branch);


    //             if ($HrEmployee->attendance_type == HrEmployee::ATTENDANCE_GEOGRAPHIC || $HrEmployee->attendance_type == HrEmployee::ATTENDANCE_All) {

    //             } else {
    //                 $data['Places'] = collect();
    //             }

    //             $data['absentrequests'] = HrAbsentRequests::where('employee_id', auth()->user()->employee->id)->paginate(10);

    //             //dd($data['tasks']);
    //             return view('hr::my_requests.dashboard', $data);
    //         }
    //         return view('hr::my_requests.dashboard', $data);
    //     } catch (\Exception $e) {

    //           flash()->warning('يرجى انتظار موافقة الادارة');
    //         auth()->logout();
    //     }
    // }

    public function employessSalary()
    {
        //dd(auth()->user());
        $data['employee'] = HrEmployee::where('employee_id', auth()->user()->employee->id)->first();
        $data['Org'] = Organization::first();

        return view('hr::my_requests.employessSalary', $data);
    }
    /**
     * Show the form for creating a new HrEmployee.
     */
    public function create()
    {
        // return view('hr::employees.create', $data);
    }

    public function justificationsEmployee()
    {
        //dd(auth()->user());
        $employee = HrEmployee::where('employee_id', auth()->user()->employee->id)->first();

        $shifts = $employee->shift->shifts ?? [];
        $data['employee'] = $employee;
        $data['shifts'] = $shifts
            ->mapWithKeys(function ($shift) {
                return [$shift->id => $shift->from . ' - ' . $shift->to];
            })
            ->toArray();
        $data['types'] = HrJustification::types();
        return view('hr::my_requests.justificationsEmployee', $data);
    }
    /**
     * Store a newly created HrEmployee in storage.
     */
    public function store(CreateHrEmployeeRequest $request)
    {
        return redirect(route('hr.employees.index'));
    }

    /**
     * Display the specified HrEmployee.
     */
    public function show($id)
    {
        $data['employee'] = $this->hrEmployeeRepository->find($id);

        return view('hr::employees.show', $data);
    }

    /**
     * Show the form for editing the specified HrEmployee.
     */
    public function edit($id)
    {
        $data['employee'] = $this->hrEmployeeRepository->find($id);
        $data['user_roles'] = $this->hrEmployeeRepository->user_roles();
        $data['user_statuses'] = $this->hrEmployeeRepository->user_statuses();
        return view('hr::employees.edit', $data);
    }

    /**
     * Update the specified HrEmployee in storage.
     */
    public function update($id, UpdateHrEmployeeRequest $request)
    {
        return redirect(route('hr.employees.index'));
    }

    /**
     * Remove the specified HrEmployee from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        return redirect(route('hr.employees.index'));
    }

    /**
     * export the specified HrEmployee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        return redirect(route('hr.employees.index'));
    }

    /**
     * export the specified HrEmployee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
}
