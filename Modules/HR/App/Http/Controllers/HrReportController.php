<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrTimeTrack;
use Modules\HR\App\Models\HrTimeTrackDetails;
use Modules\HR\App\Repositories\HrReportRepository;

class HrReportController extends Controller
{
    private $HrReportRepository;

    public function __construct(HrReportRepository $HrReportRepository)
    {
        $this->HrReportRepository = $HrReportRepository;
    }

    public function Expired_identity(Request $request)
    {
        $data['Expired_Identity'] = $this->HrReportRepository->Expired_identity($request);
        $data['employees'] = $this->HrReportRepository->employees();

        return view('hr::report_types.Expired_Identity.index', $data);
    }

    public function Contact(Request $request)
    {
        $data['Contact'] = $this->HrReportRepository->Contact($request);
        $data['employees'] = $this->HrReportRepository->employees();

        return view('hr::report_types.Contact.index', $data);
    }

    public function LeaveHolday(Request $request)
    {
        $data['LeaveHoliday'] = $this->HrReportRepository->LeaveHolday($request);
        $data['employees'] = $this->HrReportRepository->employees();
        return view('hr::report_types.LeaveHolday.index', $data);
    }

    public function LeaveHoldaybalance()
    {
        $data['LeaveHoldaybalance'] = $this->HrReportRepository->LeaveHoldaybalance();
        $data['employees'] = $this->HrReportRepository->employees();

        return view('hr::report_types.LeaveHoldaybalance.index', $data);
    }

    public function rewards(Request $request)
    {
        $data['rewards'] = $this->HrReportRepository->rewards($request);
        $data['employees'] = $this->HrReportRepository->employees();
        return view('hr::report_types.rewards.index', $data);
    }

    public function EndService(Request $request)
    {
        $data['EndService'] = $this->HrReportRepository->EndService($request);
        $data['employees'] = $this->HrReportRepository->employees();
        $data['reason'] = $this->HrReportRepository->reasons();
        return view('hr::report_types.EndService.index', $data);
    }

    public function advances(Request $request)
    {
        $data['advances'] = $this->HrReportRepository->advances($request);
        $data['employees'] = $this->HrReportRepository->employees();
        return view('hr::report_types.advances.index', $data);
    }

    public function Payroll(Request $request)
    {
        $data['Payroll'] = $this->HrReportRepository->Payroll($request);

        return view('hr::report_types.payrolls.index', $data);
    }

    public function Attendance(Request $request)
    {
        $attendanceResult = $this->HrReportRepository->Attendance($request);

        $data['Attendance'] = $attendanceResult['data'];
        $data['start_date'] = $attendanceResult['start_date'];
        $data['end_date'] = $attendanceResult['end_date'];

        $data['employees'] = $this->HrReportRepository->employees();

        // للتجربة فقط
        // dd($data);

        return view('hr::report_types.Attendance.index', $data);
    }

    public function SummaryAttendance(Request $request)
    {
        $data['SummaryAttendance'] = $this->HrReportRepository->SummaryAttendance($request);
        $data['employees'] = $this->HrReportRepository->employees();
        $data['departments'] = $this->HrReportRepository->departmentdata();
        $data['branches'] = $this->HrReportRepository->branches();
        return view('hr::report_types.SummaryAttendance.index', $data);
    }

    public function DeductionAttendance(Request $request)
    {
        $data['DeductionAttendance'] = $this->HrReportRepository->DeductionAttendance($request);
        $data['employees'] = $this->HrReportRepository->employees();
        return view('hr::report_types.DeductionAttendance.index', $data);
    }

    public function handle()
    {
        $today = Carbon::now()->toDateString();
        $currentDay = strtolower(Carbon::now()->format('l'));

        Log::info("🔹 بدء تنفيذ الحضور اليومي: {$today}");

        // استخدم lazy() لمعالجة الموظفين بشكل فردي لتقليل استهلاك الذاكرة
        HrEmployee::with('shift.shifts')
            ->lazy()
            ->each(function ($employee) use ($today, $currentDay) {
                try {
                    $shift = $employee->shift;

                    // 1. جلب بيانات الحضور أولاً
                    $attendanceCollection = $this->getAttendanceDataForEmployee($employee->id, $today, $shift ? $shift->shifts : collect([]));

                    $hasCheckInOrOut = $attendanceCollection->contains(function ($item) {
                        return !is_null($item->first_check_in) || !is_null($item->last_check_out);
                    });

                    // 2. تحديد نوع اليوم بناءً على الحضور والجدول الزمني
                    $isWorkDay = $shift && is_array($shift->work_days) && in_array($currentDay, $shift->work_days);
                    // تحديد نوع اليوم المعفي
                    $isexemptdays = $shift && is_array($shift->exempt_days) && in_array($currentDay, $shift->exempt_days);

                    // التحقق من الإجازات العامة أو المخصصة للموظف
                    $isHoliday = HrHoliday::where(function ($query) use ($employee) {
                        $query->where('employee_id', $employee->id)->orWhereNull('employee_id'); // افتراض أن الإجازات العامة لا ترتبط بموظف
                    })
                        ->where('from_at', '<=', $today)
                        ->where('end_at', '>=', $today)
                        ->where('status', 2)
                        ->exists();

                    $type = HrTimeTrack::TYPE_ABSENT; // افتراضي: غائب في يوم عمل
                    if ($hasCheckInOrOut) {
                        $type = HrTimeTrack::TYPE_PRESENT; // حاضر
                    } else {
                        if ($isHoliday) {
                            $type = HrTimeTrack::TYPE_HOLIDAY; // إجازة
                        } elseif (!$isWorkDay) {
                            $type = HrTimeTrack::TYPE_WEEKEND; // عطلة أسبوعية
                        } elseif ($isexemptdays) {
                            $type = HrTimeTrack::TYPE_EXEMPT;
                        }
                    }

                    if ($employee->id == 5) {
                        dd($hasCheckInOrOut);
                    }

                    // 3. إنشاء أو تحديث سجل التتبع الزمني الرئيسي
                    $hrTimeTrack = HrTimeTrack::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'date' => $today,
                        ],
                        [
                            'day' => $currentDay,
                            'status' => 1, // افترض أن 1 يعني "مكتمل"
                            'type' => $type,
                            'hour' => $shift->work_hours ?? 0, // ساعات العمل المجدولة
                        ],
                    );

                    // 4. إضافة تفاصيل الحضور للشفتات
                    if ($attendanceCollection->isNotEmpty()) {
                        $this->createOrUpdateAttendanceDetails($hrTimeTrack, $attendanceCollection);
                    }
                } catch (\Throwable $e) {
                }
            });
    }

    /**
     * جلب وتجميع بيانات الحضور للموظف في يوم معين.
     */
    private function getAttendanceDataForEmployee($employee_id, $today, $shifts)
    {
        $results = collect();

        if ($shifts && $shifts->isNotEmpty()) {
            foreach ($shifts as $shift) {
                $attendanceData = $this->queryAttendanceData($employee_id, $today, $shift);
                if ($attendanceData) {
                    $results->push($attendanceData);
                } else {
                    // إضافة سجل فارغ للشفت الذي لم يتم تسجيل حضور فيه
                    $results->push(
                        (object) [
                            'date' => $today,
                            'employee_id' => $employee_id,
                            'address' => null,
                            'shift_from' => $shift->from,
                            'shift_to' => $shift->to,
                            'type' => null,
                            'lat' => null,
                            'lon' => null,
                            'first_check_in' => null,
                            'last_check_out' => null,
                            'early_arrival' => null,
                            'min_delay' => null,
                            'min_early_leave' => null,
                            'max_overtime' => null,
                            'total_work_seconds' => 0,
                        ],
                    );
                }
            }
        } else {
            // في حالة عدم وجود شفتات محددة، يتم جلب البيانات المتاحة لليوم
            $extraData = $this->queryAttendanceData($employee_id, $today);
            if ($extraData) {
                $results->push($extraData);
            }
        }
        return $results;
    }

    /**
     * تنفيذ الاستعلام لجلب بيانات الحضور.
     */
    private function queryAttendanceData($employee_id, $today, $shift = null)
    {
        return HrAttendance::select('date', 'employee_id', 'address', 'shift_from', 'shift_to', 'type', 'lat', 'lon', DB::raw('MIN(CASE WHEN kind = 1 THEN check_time END) as first_check_in'), DB::raw('MAX(CASE WHEN kind = 2 THEN check_time END) as last_check_out'), DB::raw('MAX(CASE WHEN kind = 1 THEN early_arrival END) as early_arrival'), DB::raw('MIN(CASE WHEN kind = 1 THEN delay END) as min_delay'), DB::raw('MIN(CASE WHEN kind = 2 THEN early_leave END) as min_early_leave'), DB::raw('MAX(CASE WHEN kind = 2 THEN overtime END) as max_overtime'), DB::raw('TIMESTAMPDIFF(SECOND, MIN(CASE WHEN kind = 1 THEN check_time END), MAX(CASE WHEN kind = 2 THEN check_time END)) as total_work_seconds'))
            ->where('employee_id', $employee_id)
            ->where('date', $today)
            ->when($shift, function ($query) use ($shift) {
                // البحث ضمن نطاق الشفت لضمان دقة البيانات
                return $query->where('shift_from', $shift->from)->where('shift_to', $shift->to);
            })
            ->groupBy('date', 'employee_id', 'address', 'lat', 'lon', 'shift_from', 'shift_to', 'type')
            ->first();
    }

    /**
     * إنشاء أو تحديث تفاصيل الحضور المرتبطة بسجل التتبع الزمني.
     */
    private function createOrUpdateAttendanceDetails($hrTimeTrack, $attendances)
    {
        // حذف التفاصيل القديمة لتجنب التكرار
        $hrTimeTrack->timeTrackDetails()->delete();

        foreach ($attendances as $attendance) {
            $type = 1;
            if ($attendance instanceof \Modules\HR\App\Models\HrAttendance) {
                $type = 2;
            }
            HrTimeTrackDetails::create([
                'hr_time_track_id' => $hrTimeTrack->id,
                'check_time' => $attendance->first_check_in,
                'check_out' => $attendance->last_check_out,
                'shift_from' => $attendance->shift_from,
                'shift_to' => $attendance->shift_to,
                'delay' => $attendance->min_delay ?? 0,
                'early_arrival' => $attendance->early_arrival ?? 0,
                'early_leave' => $attendance->min_early_leave ?? 0,
                'overtime' => $attendance->max_overtime ?? 0,
                'total_work_seconds' => $attendance->total_work_seconds ?? 0,
                'lat' => $attendance->lat,
                'lon' => $attendance->lon,
                'address' => $attendance->address,
                'type' => $type,
            ]);
        }
    }

    public function Fingerprint(Request $request)
    {
        $data['Fingerprint'] = $this->HrReportRepository->Fingerprint($request);
        // dd( $data['Fingerprint'][0]);

        $data['employees'] = $this->HrReportRepository->employees();
        return view('hr::report_types.Fingerprint.index', $data);
    }

    public function AttendanceRecords(Request $request)
    {
        // $this->handle();
        $data['SummaryAttendance'] = $this->HrReportRepository->AttendanceRecords($request);
        $data['employees'] = $this->HrReportRepository->employees();
        $data['types'] = $this->HrReportRepository->types();
        $data['start_date'] = $request->start_date;
        $data['end_date'] = $request->end_date;
        $data['employeesdata'] = $this->HrReportRepository->employeesdata($request);

        return view('hr::report_types.AttendanceRecords.index', $data);
    }

    public function Departments(Request $request)
    {
        $data['Departments'] = $this->HrReportRepository->Departments($request);
        $data['employees'] = $this->HrReportRepository->employees();
        $data['departmentsdata'] = $this->HrReportRepository->departmentsdata();
        return view('hr::report_types.Departments.index', $data);
    }

    public function custodies(Request $request)
    {
        $data['custodies'] = $this->HrReportRepository->custodies($request);
        $data['employees'] = $this->HrReportRepository->employees();
        $data['types'] = $this->HrReportRepository->AssetType();
        return view('hr::report_types.custodies.index', $data);
    }




















    public function Summarypdf(Request $request)
    {

        $organization = $this->HrReportRepository->getOrganizationForPDF();
        // dd($organization );
        $headers = $this->HrReportRepository->headerSummary();
        $dataExcel = $this->HrReportRepository->SummaryAttendance($request);

       $dataExcel = $this->HrReportRepository->SummaryAttendancefixArray(  $dataExcel);
        $name = $this->HrReportRepository->nameSummary();

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;

        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;

        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->keep_table_proportions = true;
        $mpdf->showImageErrors = true;
        $mpdf->curlAllowUnsafeSslRequests = true;

        $mpdf->SetDisplayMode('fullpage');

        $mpdf->list_indent_first_level = 0;
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(view('hr::report_types.pdf.pdf', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name , 'organization' =>$organization]));
        $mpdf->Output();


        //    dd( $request->all() ,  $data['SummaryAttendance'] );
    }




    public function Recordspdf(Request $request)
    {


        $organization = $this->HrReportRepository->getOrganizationForPDF();
       
        
        $headers = $this->HrReportRepository->headerRecord();
        $dataExcel = $this->HrReportRepository->AttendanceRecords($request);
        $dataExcel = $this->HrReportRepository->RecordfixArray(  $dataExcel);
        $name = $this->HrReportRepository->nameRecord();

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;

        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;

        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->keep_table_proportions = true;
        $mpdf->showImageErrors = true; // السماح بإظهار أخطاء الصور لتسهيل حلها
        $mpdf->curlAllowUnsafeSslRequests = true; // السماح بجلب الصور من روابط خارجية أو https

        $mpdf->SetDisplayMode('fullpage');

        $mpdf->list_indent_first_level = 0;
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(view('hr::report_types.pdf.pdfRecord', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name , 'organization' =>$organization]));
        $mpdf->Output();

    }

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     return view('hr::index');
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     return view('hr::create');
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Show the specified resource.
     */
    // public function show($id)
    // {
    //     return view('hr::show');
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit($id)
    // {
    //     return view('hr::edit');
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy($id)
    // {
    //     //
    // }
}
