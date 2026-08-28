<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrJob;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrPlace;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrTimeTrack;

class HrAttendanceRepository extends BaseRepository
{
    protected $fieldSearchable = ['employee_id', 'day', 'name', 'lat', 'lon', 'address', 'status', 'distance'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrAttendance::class;
    }

    public function changeType($dataAll)
    {
        $updatedRows = HrAttendance::where('date', $dataAll->date)
            ->where('employee_id', $dataAll->employee_id)
            ->update(['type' => 2]);
    }

    public function EmpleyeePresence($request)
    {
        $employeeId = $request->employee_id;
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $query = HrAttendance::select(
            'date',
            'employee_id',
            'shift_from',
            'shift_to',
            'type',
            DB::raw('MIN(CASE WHEN kind = 1 THEN check_time END) as first_check_in'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN check_time END) as last_check_out'),
            DB::raw('MAX(CASE WHEN kind = 1 THEN early_arrival END) as early_arrival'),
            DB::raw('MIN(CASE WHEN kind = 1 THEN delay END) as delay'),
            DB::raw('MIN(CASE WHEN kind = 2 THEN early_leave END) as early_leave'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN overtime END) as overtime'),
            DB::raw('
            TIMEDIFF(
                MAX(check_time),
                MIN(check_time)
            ) as actual_work_hours
        '),
        );

        // فلتر الموظف إذا كان موجود
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        // فلتر التاريخ
        if ($startDate->isSameDay($endDate)) {
            $query->whereDate('date', $startDate->toDateString());
        } else {
            $query->whereDate('date', '>=', $startDate->toDateString())->whereDate('date', '<=', $endDate->toDateString());
        }

        $attendanceData = $query->groupBy('date', 'employee_id', 'shift_from', 'shift_to', 'type')->orderBy('id', 'DESC')->get();
        return $attendanceData;
    }

    //add be saeed

    public function EmployeePresenceSearch($request)
    {
        $employeeId = $request->employee_id;
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $attendanceData = HrAttendance::select(
            'date',
            'employee_id',

            'shift_from',
            'shift_to',
            'type',
            DB::raw('MIN(CASE WHEN kind = 1 THEN check_time END) as first_check_in'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN check_time END) as last_check_out'),
            DB::raw('MAX(CASE WHEN kind = 1 THEN early_arrival END) as early_arrival'),
            DB::raw('MIN(CASE WHEN kind = 1 THEN delay END) as delay'),
            DB::raw('MIN(CASE WHEN kind = 2 THEN early_leave END) as early_leave'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN overtime END) as overtime'),
            DB::raw('
            TIMEDIFF(
                MAX(check_time),
                MIN(check_time)
            ) as actual_work_hours
        '),
        )
            ->where('employee_id', $employeeId)
            ->when(
                $startDate->isSameDay($endDate),
                function ($query) use ($startDate) {
                    $query->whereDate('date', $startDate->toDateString());
                },
                function ($query) use ($startDate, $endDate) {
                    $query->whereDate('date', '>=', $startDate->toDateString())->whereDate('date', '<=', $endDate->toDateString());
                },
            )
            ->groupBy('date', 'employee_id', 'shift_from', 'shift_to', 'type')
            ->orderBy('date', 'DESC')
            ->get();

        return $attendanceData;
    }

    public function statuses(): array
    {
        return HrAttendance::statuses();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }
    public function weekdays(): array
    {
        return HrPlace::weekdays();
    }

    public function Place($employee_id = null, $department_id = null, $branch_id = null)
    {
        return HrPlace::where(function ($query) use ($employee_id, $department_id, $branch_id) {
            // إذا كان هناك موظف محدد
            if ($employee_id) {
                $query->whereJsonContains('employee_id', (string) $employee_id);
            }

            // إذا كان هناك قسم محدد
            if ($department_id) {
                $query->orWhereJsonContains('department_id', (string) $department_id);
            }

            // إذا كان هناك فرع محدد
            if ($branch_id) {
                $query->orWhereJsonContains('branch_id', (string) $branch_id);
            }

            // الأماكن العامة للجميع (flage = 1)
            $query->orWhere('flage', HrPlace::FLAG_ALL);
        })
            ->where(function ($query) {
                // فلترة على الأيام (اليوم الحالي أو الكل)
                $today = Carbon::now()->dayOfWeek + 1;
                $query
                    ->whereJsonContains('day', (string) $today)
                    ->orWhereJsonContains('day', '0') // assuming "0" = ALL days
                    ->orWhere('day', HrPlace::DAY_ALL);
            })
            ->get();
    }

    // public function Place($employee_id = null, $department_id = null)
    // {
    //     return HrPlace::where(function ($query) use ($employee_id, $department_id) {
    //         $query->where('employee_id', $employee_id)->orWhere('department_id', $department_id)->orWhere('flage', 1);
    //     })
    //         ->where(function ($query) {
    //             $query->where('day', Carbon::now()->dayOfWeek + 1)->orWhere('day', 0);
    //         })
    //         ->get();
    // }

    // public  function SHiftpresence()
    // {
    //     $shifts = auth()->user()->employee->HrEmployee->shift->shifts ?? [];
    //     $currentTime = now(); // الوقت الحالي

    //     $closestShift = null;
    //     $smallestDiff = PHP_INT_MAX; // تعيين قيمة ابتدائية كبيرة
    //     $diffFromStart = null; // تأخير في الحضور
    //     $diffFromEnd = null; // الانصراف المبكر
    //     $overtime = null; // العمل الإضافي
    //     $lateArrival = null; // التأخير
    //     $earlyLeave = null; // الانصراف المبكر

    //     foreach ($shifts as $shift) {

    //         // تحويل أوقات الوردية إلى كائنات DateTime
    //         $fromTime = \Carbon\Carbon::createFromFormat('H:i:s', $shift->from);
    //         $toTime = \Carbon\Carbon::createFromFormat('H:i:s', $shift->to);

    //         // حساب الفرق بين الوقت الحالي ووقت البداية
    //         $tempDiffFromStart = $fromTime->diffInSeconds($currentTime, false); // مع السماح بالقيم السالبة
    //         $tempDiffFromEnd = $toTime->diffInSeconds($currentTime, false); // مع السماح بالقيم السالبة

    //         // التحقق مما إذا كانت الوردية نشطة
    //         if ($shift->is_active == 1) {
    //             // إذا كان الوقت الحالي داخل نطاق الوردية
    //             if ($currentTime->between($fromTime, $toTime)) {
    //                 $closestShift = $shift; // الوقت الحالي داخل الوردية
    //                 // حساب التأخير (إذا بدأ الموظف بعد وقت البداية)
    //                 if ($currentTime->gt($fromTime)) {
    //                     $lateArrival = $currentTime->diffInSeconds($fromTime);
    //                 }
    //                 // حساب الانصراف المبكر (إذا غادر الموظف قبل وقت النهاية)
    //                 if ($currentTime->lt($toTime)) {
    //                     $earlyLeave = $toTime->diffInSeconds($currentTime);
    //                 }
    //                 // لا حساب للعمل الإضافي إذا كان الموظف ضمن وقت الدوام
    //                 $overtime = null;
    //                 break; // الخروج من الحلقة
    //             } elseif ($tempDiffFromStart >= 0 && $tempDiffFromStart < $smallestDiff) {
    //                 // إذا كانت الوردية القادمة
    //                 $smallestDiff = $tempDiffFromStart;
    //                 $closestShift = $shift;
    //                 $diffFromStart = $tempDiffFromStart; // تخزين الفرق الحالي
    //                 $diffFromEnd = null; // لا يمكن تخزينه هنا
    //             } elseif ($tempDiffFromEnd >= 0 && $tempDiffFromEnd < $smallestDiff) {
    //                 // إذا كانت الوردية الماضية
    //                 $smallestDiff = $tempDiffFromEnd;
    //                 $closestShift = $shift;
    //                 $diffFromEnd = $tempDiffFromEnd; // تخزين الفرق الحالي
    //                 $diffFromStart = null; // لا يمكن تخزينه هنا
    //             }
    //         }

    //         // حساب العمل الإضافي فقط إذا تجاوز الوقت الحالي وقت نهاية الوردية ولم يغادر الموظف مبكرًا
    //         if ($currentTime->gt($toTime) && is_null($earlyLeave)) {
    //             $overtime = $currentTime->diffInSeconds($toTime);
    //         }
    //     }

    //     // التحقق من وجود وردية
    //     if ($closestShift) {
    //         return [
    //             'diffFromStart' => secondsToTime($diffFromStart),
    //             'diffFromEnd' => secondsToTime($diffFromEnd),
    //             'overtime' => secondsToTime($overtime), // إضافة العمل الإضافي
    //             'lateArrival' => secondsToTime($lateArrival), // إضافة التأخير
    //             'earlyLeave' => secondsToTime($earlyLeave), // إضافة الانصراف المبكر
    //             'from' => $closestShift->from,
    //             'to' => $closestShift->to,
    //         ];
    //     }

    //     // في حالة عدم وجود وردية
    //     return [
    //         'diffFromStart' => null,
    //         'diffFromEnd' => null,
    //         'overtime' => null,
    //         'lateArrival' => null,
    //         'earlyLeave' => null,
    //         'from' => null,
    //         'to' => null,
    //     ];
    // }

    // public function SHiftpresence()
    // {
    //     $shiftperent = auth()->user()->employee->HrEmployee->shift;
    //     $shifts = $shiftperent->shifts ?? [];
    //     $currentTime = now(); // الوقت الحالي
    //     $currentTimeInSeconds = $currentTime->secondsSinceMidnight(); // تحويل الوقت الحالي إلى ثوانٍ منذ منتصف الليل

    //     $closestShift = null;
    //     $smallestDiff = PHP_INT_MAX; // تعيين قيمة كبيرة كبداية

    //     // المتغيرات الخاصة بمؤشرات الحضور
    //     $early_arrival = null; // القدوم المبكر
    //     $lateArrival = null; // التأخير عند الوصول
    //     $earlyLeave = null; // الانصراف المبكر
    //     $overtime = null; // العمل الإضافي

    //     foreach ($shifts as $shift) {
    //         // تحويل وقت بداية ونهاية الوردية إلى ثوانٍ منذ منتصف الليل
    //         $fromTimeInSeconds = \Carbon\Carbon::createFromFormat('H:i:s', $shift->from)->secondsSinceMidnight();
    //         $toTimeInSeconds = \Carbon\Carbon::createFromFormat('H:i:s', $shift->to)->secondsSinceMidnight();

    //         // حساب الفرق المطلق بين الوقت الحالي ووقت البداية والنهاية
    //         $diffFromStart = abs($currentTimeInSeconds - $fromTimeInSeconds);
    //         $diffFromEnd = abs($currentTimeInSeconds - $toTimeInSeconds);

    //         // اختيار أقل فرق بين البداية والنهاية
    //         $minDiff = min($diffFromStart, $diffFromEnd);

    //         // تحديث الوردية الأقرب إذا وجد فرق أصغر
    //         if ($minDiff < $smallestDiff) {
    //             $smallestDiff = $minDiff;
    //             $closestShift = $shift;

    //             // حساب مؤشرات الحضور بناءً على مقارنة الوقت الحالي مع وقت بداية الوردية
    //             if ($currentTimeInSeconds <= $fromTimeInSeconds) {
    //                 // إذا كان الوقت الحالي قبل بداية الوردية، فهذا يعني حضور مبكر
    //                 $early_arrival = $fromTimeInSeconds - $currentTimeInSeconds;
    //                 $lateArrival = null;
    //             } else {
    //                 // إذا كان الوقت الحالي بعد بداية الوردية، فهذا يعني تأخير
    //                 $lateArrival = $currentTimeInSeconds - $fromTimeInSeconds;
    //                 $early_arrival = null;
    //             }

    //             // حساب مؤشرات نهاية الوردية بناءً على مقارنة الوقت الحالي مع وقت نهاية الوردية
    //             if ($currentTimeInSeconds <= $toTimeInSeconds) {
    //                 // إذا كان الوقت الحالي قبل نهاية الوردية، فهذا يعني إمكانية الانصراف المبكر
    //                 $earlyLeave = $toTimeInSeconds - $currentTimeInSeconds;
    //                 $overtime = null;
    //             } else {
    //                 // إذا كان الوقت الحالي بعد نهاية الوردية، فهذا يعني وجود عمل إضافي (تأخر في الانصراف)
    //                 $overtime = $currentTimeInSeconds - $toTimeInSeconds;
    //                 $earlyLeave = null;
    //             }
    //         }
    //     }

    //     // التحقق من وجود وردية محددة
    //     if ($closestShift) {

    //             $early_arrivalsetting = $early_arrival ; // مقدار القدوم المبكر (بالثواني) أو null إذا لم يكن هناك
    //             $lateArrivalsetting = $lateArrival; // مقدار التأخير عند الوصول (بالثواني) أو null إذا لم يكن هناك
    //             $earlyLeavesetting = $earlyLeave; // مقدار الانصراف المبكر (بالثواني) أو null إذا لم يكن هناك
    //             $overtimesetting = $overtime;

    //         // dd( MinutesToSeconds((int)$shiftperent->entry_end ));

    //         if( $lateArrival !=null){

    //              if (!empty($shiftperent->entry_end) && $lateArrival > MinutesToSeconds($shiftperent->entry_end)) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'انتهت فترة الدخول، لا يمكن تسجيل الحضور الآن.',
    //                 ]);
    //             }else if (!empty($shiftperent->early_entry) && $early_arrival > MinutesToSeconds($shiftperent->early_entry)){

    //                 $early_arrivalsetting =0;
    //             }else if (!empty($shiftperent->late_entry) && $lateArrival > MinutesToSeconds($shiftperent->late_entry)){

    //                 $lateArrivalsetting =0;
    //             }
    //         }elseif($earlyLeave !=null){
    //                 if (!empty($shiftperent->exit_start) && $earlyLeave > MinutesToSeconds($shiftperent->exit_start)) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'لم تبدأ فترة الخروج بعد، لا يمكن تسجيل الانصراف الآن.',
    //                 ]);
    //             }
    //             else if (!empty($shiftperent->early_exit) && $earlyLeave > MinutesToSeconds($shiftperent->early_exit)){

    //                 $early_arrivalsetting =0;
    //             }else if (!empty($shiftperent->late_exit) && $overtime > MinutesToSeconds($shiftperent->late_exit)){

    //                 $lateArrivalsetting =0;
    //             }
    //         }

    //         // if ($type == 1) {

    //         // dd(secondsToTime($early_arrival));

    //         return [
    //             'early_arrival' => $early_arrivalsetting, // مقدار القدوم المبكر (بالثواني) أو null إذا لم يكن هناك
    //             'lateArrival' => $lateArrivalsetting, // مقدار التأخير عند الوصول (بالثواني) أو null إذا لم يكن هناك
    //             'earlyLeave' => $earlyLeavesetting, // مقدار الانصراف المبكر (بالثواني) أو null إذا لم يكن هناك
    //             'overtime' => $overtimesetting, // مقدار العمل الإضافي (بالثواني) أو null إذا لم يكن هناك
    //             'from' => $closestShift->from,
    //             'to' => $closestShift->to,
    //         ];
    //     }

    //     // في حالة عدم وجود وردية يتم إعادة القيم null
    //     return [
    //         'early_arrival' => null,
    //         'lateArrival' => null,
    //         'earlyLeave' => null,
    //         'overtime' => null,
    //         'from' => null,
    //         'to' => null,
    //     ];
    // }

    public function SHiftpresence($type = 1, $employee = null, $checkTime = null)
    {
        if (!$employee) {
            $employee = auth()->user()?->employee?->HrEmployee;
        }
        $shiftParent = $employee?->shift;
        if (!$shiftParent) {
            return ['status' => true, 'early_arrival' => null, 'lateArrival' => null, 'earlyLeave' => null, 'overtime' => null, 'from' => null, 'to' => null];
        }

        $shifts = $shiftParent->shifts ?? [];
        $currentTime = $checkTime ? Carbon::parse($checkTime) : now();
        $currentTimeInSeconds = $currentTime->secondsSinceMidnight();

        $closestShift = null;
        $smallestDiff = PHP_INT_MAX;

        foreach ($shifts as $shift) {
            $fromTimeInSeconds = Carbon::createFromFormat('H:i:s', $shift->from)->secondsSinceMidnight();
            $toTimeInSeconds = Carbon::createFromFormat('H:i:s', $shift->to)->secondsSinceMidnight();

            $diffFromStart = abs($currentTimeInSeconds - $fromTimeInSeconds);
            $diffFromEnd = abs($currentTimeInSeconds - $toTimeInSeconds);
            $minDiff = min($diffFromStart, $diffFromEnd);

            if ($minDiff < $smallestDiff) {
                $smallestDiff = $minDiff;
                $closestShift = $shift;
            }
        }

        if (!$closestShift) {
            return ['status' => true, 'early_arrival' => null, 'lateArrival' => null, 'earlyLeave' => null, 'overtime' => null, 'from' => null, 'to' => null];
        }

        $fromTimeInSeconds = Carbon::createFromFormat('H:i:s', $closestShift->from)->secondsSinceMidnight();
        $toTimeInSeconds = Carbon::createFromFormat('H:i:s', $closestShift->to)->secondsSinceMidnight();

        // حساب المؤشرات الأولية
        $early_arrival = $currentTimeInSeconds < $fromTimeInSeconds ? $fromTimeInSeconds - $currentTimeInSeconds : null;
        $lateArrival = $currentTimeInSeconds > $fromTimeInSeconds ? $currentTimeInSeconds - $fromTimeInSeconds : null;
        $earlyLeave = $currentTimeInSeconds < $toTimeInSeconds ? $toTimeInSeconds - $currentTimeInSeconds : null;
        $overtime = $currentTimeInSeconds > $toTimeInSeconds ? $currentTimeInSeconds - $toTimeInSeconds : null;

        // دالة مساعدة لتحويل الدقائق إلى ثوانٍ
        $toSeconds = fn($minutes) => (int) $minutes * 60;

        // 🔹 التحقق من صلاحية تسجيل الحضور
        if ($lateArrival !== null && $type === 1) {
            if (!empty($shiftParent->entry_end) && $lateArrival > $toSeconds($shiftParent->entry_end)) {
                return ['status' => false, 'message' => __('hr::models/hr_attendances.entry_time_ended')];
            }
        }

        // 🔹 التحقق من صلاحية تسجيل الانصراف
        if ($earlyLeave !== null && $type === 2) {
            if (!empty($shiftParent->exit_start) && $earlyLeave > $toSeconds($shiftParent->exit_start)) {
                return ['status' => false, 'message' => __('hr::models/hr_attendances.exit_time_not_started_yet')];
            }
        }

        // 🔹 تعديل قيم المؤشرات بناءً على الإعدادات
        if ($early_arrival !== null && !empty($shiftParent->early_entry) && $early_arrival > $toSeconds($shiftParent->early_entry)) {
            $early_arrival = 0;
        }

        if ($lateArrival !== null && !empty($shiftParent->late_entry) && $lateArrival <= $toSeconds($shiftParent->late_entry)) {
            $lateArrival = 0;
        }

        if ($earlyLeave !== null && !empty($shiftParent->early_exit) && $earlyLeave <= $toSeconds($shiftParent->early_exit)) {
            $earlyLeave = 0;
        }

        if ($overtime !== null && !empty($shiftParent->late_exit) && $overtime < $toSeconds($shiftParent->late_exit)) {
            $overtime = 0;
        }

        return [
            'status' => true,
            'early_arrival' => $early_arrival, // قدوم مبكر بالثواني
            'lateArrival' => $lateArrival, // تأخير بالثواني
            'earlyLeave' => $earlyLeave, // انصراف مبكر بالثواني
            'overtime' => $overtime, // عمل إضافي بالثواني
            'from' => $closestShift->from,
            'to' => $closestShift->to,
        ];
    }

    public function actions($request)
    {
        $employeeId = $request->employee_id;
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : \Carbon\Carbon::yesterday()->endOfDay();

        $attendanceData = HrTimeTrack::query()
            ->with(['employee', 'timeTrackDetails']) // Load relationships
            ->whereBetween('date', [$startDate, $endDate]);

        // Filter by employee if provided
        if ($request->filled('employee_id')) {
            $attendanceData->where('employee_id', $request->employee_id);
        }

        // Filter by type if provided
        if ($request->filled('type_id')) {
            $attendanceData->where('type', $request->type_id);
        }

        return $attendanceData->orderBy('date', 'desc')->orderBy('employee_id', 'asc')->get();
    }

    public function presence($places, $request)
    {
        $disResult = $this->distance($request->latitude, $request->longitude, $places->lat, $places->lon, 'M');

        // Check if the distance is within the allowed range
        if ((int) $disResult <= $places->distance) {
            $shiftDetails = $this->SHiftpresence(1);

            if (isset($shiftDetails['status']) && $shiftDetails['status'] === true) {
                $attendance = new HrAttendance();
                $attendance->employee_id = auth()->user()->employee->hrEmployee->id ?? 0;
                $attendance->day = date('L');
                $attendance->name = $places->name;
                $attendance->lat = $request->latitude;
                $attendance->lon = $request->longitude;
                $attendance->places_id = $places->id;
                $attendance->address = $places->address;
                $attendance->type = $shiftDetails['from'] == null ? 2 : 1;
                $attendance->check_time = now()->format('H:i:s'); // الوقت الحالي

                $attendance->early_arrival = $shiftDetails['early_arrival'] ?? 0;
                $attendance->delay = $shiftDetails['lateArrival'] ?? 0; // تأخير
                $attendance->early_leave = 0;
                $attendance->overtime = 0;

                $attendance->shift_from = $shiftDetails['from']; // تأخير
                $attendance->shift_to = $shiftDetails['to'];
                $attendance->date = now()->format('Y-m-d');
                $attendance->distance = $disResult;
                $attendance->kind = 1;
                $attendance->save();
            } else {
                $msg = $shiftDetails['message'] ?? __('hr::models/hr_attendances.outactual_work_hours');
                return ['message' => $msg, 'code' => '06'];
                //return $shiftDetails['message'] ?? __('hr::models/hr_attendances.outactual_work_hours');
            }
            return ['message' => __('hr::models/hr_attendances.attendance_success'), 'code' => '00'];
            //return __('hr::models/hr_attendances.attendance_success');
        } else {
            return ['message' => __('hr::models/hr_attendances.location_far'), 'code' => '06'];
            //return __('hr::models/hr_attendances.location_far');
        }
    }

    public function checkout($places, $request)
    {
        $disResult = $this->distance($request->latitude, $request->longitude, $places->lat, $places->lon, 'M');

        if ((int) $disResult <= $places->distance) {
            $shiftDetails = $this->SHiftpresence(2);
            if (isset($shiftDetails['status']) && $shiftDetails['status'] === true) {
                $attendance = new HrAttendance();
                $attendance->employee_id = auth()->user()->employee->hrEmployee->id ?? 0;
                $attendance->day = date('L');
                $attendance->name = $places->name;
                $attendance->lat = $request->latitude;
                $attendance->lon = $request->longitude;
                $attendance->places_id = $places->id;
                $attendance->address = $places->address;
                $attendance->type = $shiftDetails['from'] == null ? 2 : 1;
                $attendance->check_time = now()->format('H:i:s'); // الوقت الحالي

                $attendance->early_arrival = 0;
                $attendance->delay = 0; // تأخير
                $attendance->early_leave = $shiftDetails['earlyLeave'] ?? 0;
                $attendance->overtime = $shiftDetails['overtime'] ?? 0;

                $attendance->shift_from = $shiftDetails['from']; // تأخير
                $attendance->shift_to = $shiftDetails['to'];
                $attendance->date = now()->format('Y-m-d');
                $attendance->distance = $disResult;
                $attendance->kind = 2;
                $attendance->save();

        } else {
                $msg = $shiftDetails['message'] ?? __('hr::models/hr_attendances.outactual_work_hours');
                return ['message' => $msg, 'code' => '06'];
                //return $shiftDetails['message'] ?? __('hr::models/hr_attendances.outactual_work_hours');
            }
            return ['message' => __('hr::models/hr_attendances.checkout_success'), 'code' => '00'];
            //return __('hr::models/hr_attendances.attendance_success');
        } else {
            return ['message' => __('hr::models/hr_attendances.location_far'), 'code' => '06'];
            //return __('hr::models/hr_attendances.location_far');
        }
    }



    private function distance($lat1, $lon1, $lat2, $lon2, $unit = 'M')
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515; // المسافة بالأميال
        $unit = strtoupper($unit);

        if ($unit == 'K') {
            // المسافة بالكيلومترات
            return $miles * 1.609344;
        } elseif ($unit == 'N') {
            // المسافة بالميل البحري
            return $miles * 0.8684;
        } elseif ($unit == 'M') {
            // المسافة بالمتر
            return $miles * 1.609344 * 1000;
        } else {
            // المسافة بالأميال
            return $miles;
        }
    }
}
