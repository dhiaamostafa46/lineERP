<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Http\Requests\CreateHrPenaltyRequest;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Repositories\HrAttendanceRepository;
use Modules\HR\App\Repositories\HrPenaltyRepository;
use Modules\HR\App\Repositories\HrRewardRepository;
use Modules\HR\Http\Requests\HrAttendanceRequest;
use Modules\HR\App\Models\HrShiftType;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Models\HrSalaryAllowance;

use App\Jobs\ProcessEmployeeTimeTrack;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\App\Imports\HrAttendanceImport;
use Modules\HR\App\Models\HrTimeTrack;

class HrAttendanceController extends Controller
{
    /** @var HrAttendanceRepository $HrAttendanceRepository*/
    private $HrAttendanceRepository;
    private $hrPenaltyRepository;
    private $hrRewardRepository;

    public function __construct(HrAttendanceRepository $HrAttendance, HrPenaltyRepository $hrPenaltyRepository, HrRewardRepository $hrRewardRepository)
    {
        $this->HrAttendanceRepository = $HrAttendance;
        $this->hrPenaltyRepository = $hrPenaltyRepository;

        $this->hrRewardRepository = $hrRewardRepository;
    }

    // private function getAttendanceDataForEmployee($employee_id, $today, $shifts)
    // {
    //     $results = collect();

    //     if ($shifts && $shifts->isNotEmpty()) {
    //         Log::info("🔍 الموظف ID {$employee_id} لديه " . $shifts->count() . ' شفتات');

    //         foreach ($shifts as $shift) {
    //             $attendanceData = $this->queryAttendanceData($employee_id, $today, $shift);

    //             if ($attendanceData) {
    //                 Log::info("✓ وجدت بيانات حضور للشفت {$shift->from} - {$shift->to}");
    //                 $results->push($attendanceData);
    //             } else {
    //                 // إضافة سجل فارغ للشفت
    //                 Log::info("○ لا توجد بيانات حضور للشفت {$shift->from} - {$shift->to}، إضافة سجل فارغ");
    //                 $results->push(
    //                     (object) [
    //                         'date' => $today,
    //                         'employee_id' => $employee_id,
    //                         'address' => null,
    //                         'shift_from' => $shift->from,
    //                         'shift_to' => $shift->to,
    //                         'type' => null,
    //                         'lat' => null,
    //                         'lon' => null,
    //                         'first_check_in' => null,
    //                         'last_check_out' => null,
    //                         'earlyArrival' => null,
    //                         'min_delay' => null,
    //                         'min_early_leave' => null,
    //                         'max_overtime' => null,
    //                         'total_work_seconds' => 0,
    //                     ],
    //                 );
    //             }
    //         }
    //     } else {
    //         Log::info("⚠️ الموظف ID {$employee_id} ليس لديه شفتات محددة");
    //         $extraData = $this->queryAttendanceData($employee_id, $today);
    //         if ($extraData) {
    //             Log::info('✓ وجدت بيانات حضور عامة');
    //             $results->push($extraData);
    //         }
    //     }

    //     return $results;
    // }

    // private function queryAttendanceData($employee_id, $today, $shift = null)
    // {
    //     $query = HrAttendance::select(
    //         'date',
    //         'employee_id',
    //         'shift_from',
    //         'shift_to',
    //         DB::raw('MIN(CASE WHEN kind = 1 THEN check_time END) as first_check_in'),
    //         DB::raw('MAX(CASE WHEN kind = 2 THEN check_time END) as last_check_out'),
    //         DB::raw('MAX(CASE WHEN kind = 1 THEN earlyArrival END) as earlyArrival'),
    //         DB::raw('MIN(CASE WHEN kind = 1 THEN delay END) as min_delay'),
    //         DB::raw('MIN(CASE WHEN kind = 2 THEN early_leave END) as min_early_leave'),
    //         DB::raw('MAX(CASE WHEN kind = 2 THEN overtime END) as max_overtime'),
    //         DB::raw('TIMESTAMPDIFF(SECOND, MIN(CASE WHEN kind = 1 THEN check_time END), MAX(CASE WHEN kind = 2 THEN check_time END)) as total_work_seconds'),
    //         // أخذ العنوان من أول سجل دخول
    //         DB::raw('MAX(CASE WHEN kind = 1 THEN address END) as address'),
    //         DB::raw('MAX(CASE WHEN kind = 1 THEN lat END) as lat'),
    //         DB::raw('MAX(CASE WHEN kind = 1 THEN lon END) as lon'),
    //         DB::raw('MAX(CASE WHEN kind = 1 THEN type END) as type'),
    //     )
    //         ->where('employee_id', $employee_id)
    //         ->where('date', $today);

    //     if ($shift) {
    //         $query->where('shift_from', $shift->from)->where('shift_to', $shift->to);
    //     }

    //     // groupBy فقط بالحقول الأساسية
    //     $data = $query->groupBy('date', 'employee_id', 'shift_from', 'shift_to')->first();
    //     return $data;
    // }

    public function AttendanceMovement(Request $request)
    {
        //           $employee = HrEmployee::where('id', 212)->first();
        //          $shift = $employee->shift;
        //         // 1. جلب بيانات الحضور
        //         $attendanceCollection = $this->getAttendanceDataForEmployee(
        //             $employee->id,
        //             '2025-11-17',
        //             $shift ? $shift->shifts : collect([])
        //         );
        //dd($attendanceCollection);

     //   dd($this->HrAttendanceRepository->SHiftpresence(1));

        $data['attendances'] = $this->HrAttendanceRepository->EmpleyeePresence($request);
        $data['employees'] = $this->HrAttendanceRepository->employees();
        return view('hr::Attendance.index', $data);
    }

    public function AttendanceRewards(Request $request)
    {
        $input = $request->all();
        $reward = $this->hrRewardRepository->create($input);
        $attendances = $this->HrAttendanceRepository->changeType($request);
        return back();
    }




public function actions(Request $request)
{
    $data['SummaryAttendance'] = $this->HrAttendanceRepository->actions($request);
    $data['employees'] = $this->HrAttendanceRepository->employees();
    $data['employeesdata'] = null;

    // Get employee data if filtered by specific employee
    if ($request->filled('employee_id')) {
        $data['employeesdata'] = HrEmployee::find($request->employee_id);
    }

    return view('hr::Attendance.actions', $data);
}

    public function AttendancePenalties(CreateHrPenaltyRequest $request)
    {
        $input = $request->all();


        $timetrack=HrTimeTrack::where('id',$request->timetrack)->update(['process'=>2]);

        $penalty = $this->hrPenaltyRepository->create($input);
        $attendances = $this->HrAttendanceRepository->changeType($request);
        return back();
    }

    /**
     * Display a listing of the Hrattendance.
     */
    public function index(Request $request)
    {
        $data['attendances'] = $this->HrAttendanceRepository->EmpleyeePresence($request);
        $data['statuses'] = $this->HrAttendanceRepository->statuses();
        $data['employees'] = $this->HrAttendanceRepository->employees();

        return view('hr::Attendance.index', $data);
    }
    public function indexByDate(Request $request)
    {
        $data['attendances'] = $this->HrAttendanceRepository->EmployeePresenceSearch($request);
        $data['statuses'] = $this->HrAttendanceRepository->statuses();
        $data['employees'] = $this->HrAttendanceRepository->employees();

        $data['start_date'] = $request->start_date;
        $data['end_date'] = $request->end_date;

        return view('hr::Attendance.index', $data);
    }

    /**
     * Show the form for creating a new Hrattendance.
     */
    public function create(Request $request)
    {
        $user = auth()->user()->employee;
        $employee = $user->id ?? null;
        $branch = $user->branch_id ?? null;

        $department = $user->HrEmployee->department_id ?? null;

        $data['weekdays'] = $this->HrAttendanceRepository->weekdays();
        $data['Places'] = $this->HrAttendanceRepository->Place($employee, $department, $branch);
        return view('hr::Attendance.create', $data);
    }

    public function postAttendancelocation(Request $request)
    {
        
        $HrPlace = HrPlace::findOrFail($request->idplace);

        if (empty($HrPlace)) {
            return response()->json(['message' => __('hr::models/hr_attendances.not_found')]);
        }

        if ($request->type == 1) {
            $msg = $this->HrAttendanceRepository->presence($HrPlace, $request);
            $msg =$msg['message'];
            return response()->json(['message' => $msg]);
        } else {
            $msg = $this->HrAttendanceRepository->checkout($HrPlace, $request);
            $msg =$msg['message'];
            return response()->json(['message' => $msg]);
        }
    }

    /**
     * Store a newly created Hrattendance in storage.
     */
    public function store(HrAttendanceRequest $request)
    {
        $input = $request->all();

        $attendance = $this->HrAttendanceRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_attendances.singular')]));

        return redirect(route('hr.Attendance.index'));
    }

    /**
     * Display the specified Hrattendance.
     */
    public function show($id)
    {
        //dd($id);
        $attendance = $this->HrAttendanceRepository->find($id);

        if (empty($attendance)) {
            flash()->error(__('hr::models/hr_attendances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.Attendance.index'));
        }

        return view('hr::Attendance.show')->with('attendance', $attendance);
    }

    /**
     * Show the form for editing the specified Hrattendance.
     */
    public function edit($id)
    {
        $data['attendance'] = $this->HrAttendanceRepository->find($id);
        if (empty($data['attendance'])) {
            flash()->error(__('hr::models/hr_attendances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.Attendance.index'));
        }

        //    $data['owners'] = $this->HrAttendanceRepository->owners();
        //    $data['parents'] = $this->HrAttendanceRepository->parents();
        //    $data['statuses'] = $this->HrAttendanceRepository->statuses();
        //    $data['types'] = $this->HrAttendanceRepository->types();

        return view('hr::Attendance.edit', $data);
    }

    /**
     * Update the specified Hrattendance in storage.
     */
    public function update($id, HrAttendanceRequest $request)
    {
        $attendance = $this->HrAttendanceRepository->find($id);

        if (empty($attendance)) {
            flash()->error(__('hr::models/hr_attendances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.Attendance.index'));
        }

        $attendance = $this->HrAttendanceRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_attendances.singular')]));

        return redirect(route('hr.Attendance.index'));
    }

    /**
     * Remove the specified Hrattendance from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $attendance = $this->HrAttendanceRepository->find($id);

        if (empty($attendance)) {
            flash()->error(__('hr::models/hr_attendances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.Attendance.index'));
        }

        $this->HrAttendanceRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_attendances.singular')]));

        return redirect(route('hr.Attendance.index'));
    }

    public function OutHoursCakculate()
    {
        // dd('tst');
        //$data['EndServices'] = $this->HrEndServiceRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['employees'] = $this->HrAttendanceRepository->employees();
        //$data['statuses'] = $this->HrEndServiceRepository->statuses();

        return view('hr::Attendance.hoursCalculate', $data);
    }

    public function calculateHours($id, $quantity)
    {
        $emp = HrEmployee::find($id);
        $shift = HrShiftType::find($emp->shift_id);
        $salary = HrSalary::where('employee_id', $id)->first();
        $allowance = HrSalaryAllowance::where('salary_id', $salary->id)->sum('amount');
        $hours = $shift->work_hours;
        $sal = $salary->basic + $allowance;

        $daySal = $sal / 26;
        $hourSal = $daySal / $hours;
        $result = $quantity * $hourSal;
        return ['result' => $result];
        //return view('hr::Attendance.hoursCalculate', $data);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        // إنشاء instance من الـ Import
        $importer = new HrAttendanceImport();

        // تنفيذ الاستيراد
        Excel::import($importer, $request->file('file'));

        // الآن يمكنك الحصول على التواريخ
        $dateRange = $importer->getDateRange();

        // التأكد من وجود تواريخ قبل إرسال الـ Job
        if ($dateRange['start_date'] && $dateRange['end_date']) {
            ProcessEmployeeTimeTrack::dispatch($dateRange)->delay(now()->addMinutes(2));
        }

        flash()->success(__('messages.imported', ['model' => __('hr::models/hr_employees.singular')]));
        return redirect(route('hr.attendance.movement'));
    }

    public function reprocessRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:hr_employees,id',
        ]);

        \Modules\HR\App\Jobs\ReprocessAttendanceJob::dispatch(
            $request->start_date,
            $request->end_date,
            $request->employee_id ? (int) $request->employee_id : null
        );

        flash()->success(__('hr::models/hr_attendances.reprocess_started') ?? 'تم بدء إعادة المعالجة في الخلفية بنجاح');
        return redirect()->back();
    }
}
