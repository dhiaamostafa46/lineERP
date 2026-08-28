<?php

namespace Modules\HR\App\Repositories;

use App\Models\Branch;
use App\Models\Organization;
use Modules\HR\App\Models\HrJob;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrAssetType;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Models\HrContract;
use Modules\HR\App\Models\HrCustody;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrEndService;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrHolidayType;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Models\HrReward;
use Modules\HR\App\Models\HrTask;
use Modules\HR\App\Models\HrTimeTrack;
use Mohamedsabil83\HijriDate\Hijri;
use MohamedSabil83\LaravelHijrian\Facades\Hijrian;

class HrReportRepository
{
    // public function Contract()
    // {

    //     $Contract =HrEmployee::where()->get();

    //     return "";
    // }

    // public function Contract(string $locale = 'ar'): object
    // {
    //     return DB::table('')->isolateBranch()
    //         ->join('hr_employees as he', 'e.id', '=', 'he.employee_id') // ربط الموظف بالوظيفة
    //         ->join('hr_departments as hdt', 'he.department_id', '=', 'hdt.id') // ربط القسم باستخدام hr_departments
    //         ->join('hr_jobs as hjt', 'he.job_id', '=', 'hjt.id') // ربط الوظيفة باستخدام hr_jobs

    //         // ربط الترجمة مع hr_department_translations بناءً على القسم
    //         ->leftJoin('hr_department_translations as hdt_trans', function ($join) use ($locale) {
    //             $join->on('hdt.id', '=', 'hdt_trans.hr_department_id')->where('hdt_trans.locale', '=', $locale); // الترجمة بناءً على اللغة
    //         })

    //         // ربط الترجمة مع hr_job_translations بناءً على الوظيفة
    //         ->leftJoin('hr_job_translations as hjt_trans', function ($join) use ($locale) {
    //             $join->on('hjt.id', '=', 'hjt_trans.hr_job_id')->where('hjt_trans.locale', '=', $locale); // الترجمة بناءً على اللغة
    //         })

    //         ->leftJoin('employee_identities as ei', 'e.id', '=', 'ei.employee_id') // ربط الهوية
    //         ->select(
    //             'he.id as employee_id',
    //             'e.username',
    //             DB::raw("COALESCE(hdt_trans.name, 'N/A') as department_name"), // جلب اسم القسم المترجم
    //             DB::raw("COALESCE(hjt_trans.name, 'N/A') as job_name"), // جلب اسم الوظيفة المترجمة
    //             'ei.identity_no', // رقم الهوية
    //             'ei.identity_expired_at', // تاريخ انتهاء الهوية
    //         )
    //         ->get();
    // }

    // public function LeaveHolday($request)
    // {
    //     return HrHoliday::where('status', '!=', 1)->get();
    // }

    public function LeaveHolday($request)
    {
        // بدء الاستعلام عن الإجازات التي حالتها ليست "معلقة"
        $query = HrHoliday::where('status', '!=', HrHoliday::STATUS_PENDING);

        // التحقق مما إذا كان `employee_id` موجودًا في $request
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // التحقق من التواريخ `start_date` و `end_date` للبحث في فترة الإجازات
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('from_at', [$request->start_date, $request->end_date])->orWhereBetween('end_at', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('from_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('end_at', '<=', $request->end_date);
        }

        // إرجاع النتائج بعد تطبيق الفلاتر
        return $query->get();
    }

    public function reasons(): array
    {
        return HrEndService::reasons();
    }

    public function LeaveHoldaybalance()
    {
        $employees = HrEmployee::with(['department', 'HolidayBalance'])->get();

        $blanceHilay = [];
        foreach ($employees as $employee) {
            $blanceHilay[] = [
                'name' => $employee->username,
                'department' => $employee->department->name ?? 'N/A',
                'max_off_days' => $employee->max_off_days ?? 0,
                'start_at' => $employee->start_at ?? 'N/A',
                'number_of_years' => $employee->start_at ? \Carbon\Carbon::now()->diffAsCarbonInterval($employee->start_at) : 0,
                'leave_balance' => $employee->HolidayBalance->sum('balance') ?? 0,
                'current_balance' => $employee->HolidayBalance->sum('annual_balance') + $employee->HolidayBalance->sum('previous_balance') - $employee->HolidayBalance->sum('balance') ?? 0, // رصيد الإجازات
            ];
        }
        return $blanceHilay;
    }

    public function rewards($request)
    {
        // استعلام المكافآت
        $rewards = DB::table('')->isolateBranch()
            ->join('hr_employees', 'hr_rewards.employee_id', '=', 'hr_employees.id')
            ->select(
                'hr_rewards.id',
                'hr_rewards.created_at',
                'hr_rewards.employee_id',
                'hr_rewards.amount',
                'hr_rewards.status',
                'hr_rewards.type',
                DB::raw('NULL AS due_date'), // عمود فارغ لـ due_date
                DB::raw('NULL AS description'), // عمود فارغ لـ description
                DB::raw("'reward' AS typeReasc"), // إضافة نوع السجل
                'hr_employees.username AS employee_name', // جلب اسم الموظف
            )
            ->where('hr_rewards.status', '!=', '1');

        // استعلام العقوبات
        $penalties = DB::table('')->isolateBranch()
            ->join('hr_employees', 'hr_penalties.employee_id', '=', 'hr_employees.id')
            ->select(
                'hr_penalties.id',
                'hr_penalties.created_at',
                'hr_penalties.employee_id',
                'hr_penalties.amount',
                'hr_penalties.status',
                DB::raw('NULL AS type'), // عمود فارغ لـ type
                'hr_penalties.due_date', // عمود موجود في hr_penalties
                'hr_penalties.description', // عمود موجود في hr_penalties
                DB::raw("'penalty' AS typeReasc"), // إضافة نوع السجل
                'hr_employees.username AS employee_name', // جلب اسم الموظف
            )
            ->where('hr_penalties.status', '!=', '1');

        // التحقق من المدخلات في $request
        if ($request->filled('employee_id')) {
            $rewards->where('hr_rewards.employee_id', $request->employee_id);
            $penalties->where('hr_penalties.employee_id', $request->employee_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $rewards->whereBetween('hr_rewards.created_at', [$request->start_date, $request->end_date]);
            $penalties->whereBetween('hr_penalties.created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $rewards->where('hr_rewards.created_at', '>=', $request->start_date);
            $penalties->where('hr_penalties.created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $rewards->where('hr_rewards.created_at', '<=', $request->end_date);
            $penalties->where('hr_penalties.created_at', '<=', $request->end_date);
        }

        // دمج النتائج
        $results = $rewards
            ->unionAll($penalties)
            ->orderBy('created_at') // ترتيب النتائج بناءً على التاريخ
            ->get();

        return $results;
    }

    public function AssetType(): array
    {
        return HrAssetType::get()->pluck('name', 'id')->toArray();
    }

    public function departmentdata()
    {
        return HrDepartment::get()->pluck('name', 'id')->toArray();
    }

    public function branches()
    {
        return Branch::get()->pluck('name', 'id')->toArray();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }
    public function advances($request)
    {
        $query = HrAdvance::where('status', '!=', HrAdvance::STATUS_PENDING);

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        return $query->get();
    }

    public function EndService($request)
    {
        $query = HrEndService::where('status', '!=', HrEndService::STATUS_PENDING);

        // التحقق من وجود employee_id في الطلب
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // التحقق من وجود تواريخ بداية ونهاية
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('end', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('end', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('end', '<=', $request->end_date);
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // الحصول على النتائج
        return $query->get();
    }

    public function Contact($request): object
    {
        $query = HrContract::query();

        // التحقق مما إذا كانت start_date و end_date مملوءتين
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // البحث عن السجلات التي يكون start_at أو end_at ما بين start_date و end_date
            $query->where(function ($q) use ($request) {
                $q->whereBetween('start_at', [$request->start_date, $request->end_date])->orWhereBetween('end_at', [$request->start_date, $request->end_date]);
            });
        } elseif ($request->filled('start_date')) {
            // البحث عن السجلات التي تبدأ من start_date فصاعداً
            $query->where('start_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            // البحث عن السجلات التي تكون قبل end_date
            $query->where('end_at', '<=', $request->end_date);
        }

        // التحقق مما إذا كان employee_id مملوءًا
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // إرجاع النتائج بعد تطبيق الشروط
        return $query->get();
    }

    public function Payroll($request)
    {
        $query = HrPayroll::query();

        // التحقق من وجود تواريخ البداية والنهاية
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payroll_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('payroll_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('payroll_date', '<=', $request->end_date);
        }

        // الحصول على النتائج
        return $query->get();
    }

    public function Expired_identity($request, string $locale = 'ar'): object
    {
        $query = DB::table('')->isolateBranch()
            ->join('hr_employees as he', 'e.id', '=', 'he.employee_id')
            ->join('hr_departments as hdt', 'he.department_id', '=', 'hdt.id')
            ->join('hr_jobs as hjt', 'he.job_id', '=', 'hjt.id')
            ->leftJoin('hr_department_translations as hdt_trans', function ($join) use ($locale) {
                $join->on('hdt.id', '=', 'hdt_trans.hr_department_id')->where('hdt_trans.locale', '=', $locale);
            })
            ->leftJoin('hr_job_translations as hjt_trans', function ($join) use ($locale) {
                $join->on('hjt.id', '=', 'hjt_trans.hr_job_id')->where('hjt_trans.locale', '=', $locale);
            })
            ->leftJoin('employee_identities as ei', 'e.id', '=', 'ei.employee_id')
            ->select('he.id as employee_id', 'e.username', DB::raw("COALESCE(hdt_trans.name, 'N/A') as department_name"), DB::raw("COALESCE(hjt_trans.name, 'N/A') as job_name"), 'ei.identity_no', 'ei.identity_expired_at', 'e.gender', 'e.nationality', 'he.start_at');

        if ($request->filled('employee_id')) {
            $query->where('e.id', '=', $request->employee_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('ei.identity_expired_at', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('ei.identity_expired_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('ei.identity_expired_at', '<=', $request->end_date);
        } else {
            $query->whereDate('ei.identity_expired_at', '<=', today());
        }

        $results = $query->get();

        // حساب الأيام المتبقية
        $results->map(function ($item) {
            $item->remaining_days = $this->calculateRemainingDays($item->identity_expired_at);
            return $item;
        });

        return $results;
    }

    /**
     * حساب الأيام المتبقية حتى انتهاء الهوية
     * يكتشف تلقائياً إذا كان التاريخ هجري أو ميلادي
     */
    private function calculateRemainingDays($expiryDate)
    {
        if (!$expiryDate) {
            return null;
        }

        try {
            // محاولة الكشف عن نوع التاريخ من الصيغة
            $isHijri = $this->isHijriDate($expiryDate);

            if ($isHijri) {
                // تحويل التاريخ الهجري إلى ميلادي
                // إذا كان التاريخ بصيغة 1446-05-15 (هجري)
                $expiryCarbon = Hijrian::gregory($expiryDate);
            } else {
                // التاريخ ميلادي
                $expiryCarbon = Carbon::parse($expiryDate);
            }

            $today = Carbon::today();
            $remainingDays = $today->diffInDays($expiryCarbon, false);

            return (int) $remainingDays;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * كشف إذا كان التاريخ هجري أو ميلادي
     * التواريخ الهجرية عادة تبدأ من 1300 إلى 1500
     * التواريخ الميلادية عادة من 1900 إلى 2100
     */
    private function isHijriDate($date)
    {
        try {
            // استخراج السنة من التاريخ
            $dateString = (string) $date;

            // إذا كان التاريخ بصيغة YYYY-MM-DD أو YYYY/MM/DD
            preg_match('/^(\d{4})/', $dateString, $matches);

            if (isset($matches[1])) {
                $year = (int) $matches[1];

                // التواريخ الهجرية تكون بين 1300 و 1500 تقريباً
                // التواريخ الميلادية تكون أكبر من 1900
                return $year >= 1300 && $year <= 1500;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    // public function Attendance($request)
    // {
    //     $attendanceData = HrAttendance::select(
    //         'date',
    //         'employee_id',
    //         'address',
    //         'shift_from',
    //         'shift_to',
    //         DB::raw('MIN(check_time) as first_check_in'),
    //         DB::raw('MAX(check_time) as last_check_out'),
    //         DB::raw('MIN(delay) as min_delay'),
    //         DB::raw('MIN(early_leave) as min_early_leave'),
    //         DB::raw('MAX(overtime) as max_overtime'),
    //         DB::raw('
    //             TIMEDIFF(
    //                 MAX(check_time),
    //                 MIN(check_time)
    //             ) - SEC_TO_TIME(SUM(delay)) + SEC_TO_TIME(SUM(early_leave)) as actual_work_hours
    //         '), // حساب ساعات العمل الفعلية
    //     )
    //         ->groupBy('date', 'employee_id', 'address', 'shift_from', 'shift_to')
    //         ->orderBy('date', 'DESC') // ترتيب النتائج حسب التاريخ
    //         ->get();

    //     return $attendanceData;
    // }

    //   public function Fingerprint($request)
    //     {
    //         // تحديد نطاق التواريخ
    //         $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
    //         $endDate = $request->end_date ?? now()->toDateString();

    //         // جلب جميع الموظفين
    //         $employees = HrEmployee::all();

    //         $results = [];

    //         foreach ($employees as $employee) {
    //             // إنشاء query للبصمات مع اسم الموظف
    //             $query = HrAttendance::select(
    //                 'hr_attendances.date',
    //                 'hr_attendances.employee_id',
    //                 DB::raw("COALESCE(he.username, 'N/A') as employee_name"),
    //                 'hr_attendances.address',
    //                 'hr_attendances.shift_from',
    //                 'hr_attendances.shift_to',
    //                 DB::raw('GROUP_CONCAT(TIME_FORMAT(hr_attendances.check_time, "%h:%i %p") ORDER BY hr_attendances.check_time SEPARATOR "--") as all_check_times'),
    //                 DB::raw('COUNT(hr_attendances.id) as total_punches'),
    //                 DB::raw('TIME_FORMAT(MIN(hr_attendances.check_time), "%h:%i %p") as first_check_in'),
    //                 DB::raw('TIME_FORMAT(MAX(hr_attendances.check_time), "%h:%i %p") as last_check_out'),
    //                 DB::raw('TIMEDIFF(MAX(hr_attendances.check_time), MIN(hr_attendances.check_time)) as time_difference')
    //             )
    //             ->leftJoin('hr_employees as he', 'hr_attendances.employee_id', '=', 'he.id')
    //             ->where('hr_attendances.employee_id', $employee->id)
    //             ->whereBetween('hr_attendances.date', [$startDate, $endDate])
    //             ->groupBy('hr_attendances.date', 'hr_attendances.employee_id', 'he.username', 'hr_attendances.address', 'hr_attendances.shift_from', 'hr_attendances.shift_to')
    //             ->orderBy('hr_attendances.date', 'DESC');

    //             $attendanceRecords = $query->get();

    //             // إذا لم يكن هناك سجلات لهذا الموظف في يوم معين، اعتبره غائبًا
    //             foreach (new \Carbon\CarbonPeriod($startDate, $endDate) as $date) {
    //                 $record = $attendanceRecords->firstWhere('date', $date->toDateString());
    //                 $results[] = [
    //                     'date' => $date->toDateString(),
    //                     'employee_id' => $employee->id,
    //                     'employee_name' => $employee->username,
    //                     'address' => $record->address ?? 'N/A',
    //                     'shift_from' => $record->shift_from ?? 'N/A',
    //                     'shift_to' => $record->shift_to ?? 'N/A',
    //                     'all_check_times' => $record->all_check_times ?? 'غائب',
    //                     'total_punches' => $record->total_punches ?? 0,
    //                     'first_check_in' => $record->first_check_in ?? 'غائب',
    //                     'last_check_out' => $record->last_check_out ?? 'غائب',
    //                     'time_difference' => $record->time_difference ?? 'غائب',
    //                 ];
    //             }
    //         }

    //         return $results;
    //     }

    public function Fingerprint($request)
    {
        // نطاق التواريخ افتراضيًا للشهر الحالي
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->toDateString() : now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->toDateString() : now()->toDateString();

        // تحسين التحقق من count_day
        $count = null;
        if ($request->filled('count_day')) {
            $count = (int) $request->count_day;
        }

        // حساب عدد الأيام في النطاق (شاملة)
        $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        $totalDaysInt = (int) $totalDays;

        // تجميع البصمات حسب الموظف ضمن النطاق
        $sub = DB::table('')->isolateBranch()
            ->select('employee_id', DB::raw('COUNT(*) as total_punches'), DB::raw('COUNT(DISTINCT date) as days_with_punches'))
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('employee_id');

        // جلب جميع الموظفين (بما فيهم من ليس لهم بصمات) وربط التجميع
        $query = DB::table('')->isolateBranch()
            ->leftJoinSub($sub, 'att', function ($join) {
                $join->on('he.id', '=', 'att.employee_id');
            })
            ->select('he.id as employee_id', 'he.job_number as job_number', DB::raw("COALESCE(he.username, 'N/A') as employee_name"), DB::raw('COALESCE(att.total_punches, 0) as total_punches'), DB::raw('COALESCE(att.days_with_punches, 0) as days_with_punches'), DB::raw($totalDaysInt . ' as total_days'), DB::raw($totalDaysInt . ' - COALESCE(att.days_with_punches, 0) as days_without_punches'));

        // فلتر حسب موظف إذا طُلب
        if ($request->filled('employee_id')) {
            $query->where('he.id', $request->employee_id);
        }

        // التصفية حسب عدد الأيام
        if ($count !== null) {
            // معالجة خاصة للصفر
            if ($count == 0) {
                $query->where(function ($q) {
                    $q->whereNull('att.days_with_punches')->orWhere('att.days_with_punches', '=', 0);
                });
            } else {
                $query->where('att.days_with_punches', '=', $count);
            }
        }

        $query->orderByDesc('total_punches');

        return $query->get();
    }

    public function FingerprintSummary($request)
    {
        // نطاق التواريخ افتراضيًا للشهر الحالي
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->toDateString() : now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->toDateString() : now()->endOfMonth()->toDateString();

        // استعلام ملخص البصمات لكل موظف في النطاق
        $query = DB::table('')->isolateBranch()
            ->leftJoin('hr_employees as he', 'ha.employee_id', '=', 'he.id')
            ->select('ha.date', 'ha.employee_id', DB::raw("COALESCE(he.username, 'N/A') as employee_name"), 'ha.address', 'ha.shift_from', 'ha.shift_to', DB::raw('GROUP_CONCAT(TIME_FORMAT(ha.check_time, "%h:%i %p") ORDER BY ha.check_time SEPARATOR "--") as all_check_times'), DB::raw('COUNT(ha.id) as total_punches'), DB::raw('TIME_FORMAT(MIN(ha.check_time), "%h:%i %p") as first_check_in'), DB::raw('TIME_FORMAT(MAX(ha.check_time), "%h:%i %p") as last_check_out'), DB::raw('TIMEDIFF(MAX(ha.check_time), MIN(ha.check_time)) as time_difference'))
            ->whereBetween('ha.date', [$startDate, $endDate])
            ->groupBy('ha.date', 'ha.employee_id', 'he.username', 'ha.address', 'ha.shift_from', 'ha.shift_to')
            ->orderBy('ha.date', 'DESC');

        // فلتر حسب موظف إذا طُلب
        if ($request->filled('employee_id')) {
            $query->where('ha.employee_id', $request->employee_id);
        }
    }

    public function Attendance($request)
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

        return [
            'data' => $attendanceData,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    // public function SummaryAttendance($request)
    // {
    //     // جلب employee_id من الطلب إذا كان موجوداً
    //     $employeeId = $request->employee_id;

    //     // تحويل التواريخ إلى كائنات Carbon إذا كانت موجودة في الطلب
    //     $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() :   \Carbon\Carbon::now()->startOfMonth();
    //     $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay()  : \Carbon\Carbon::now()->endOfMonth();

    //     // جلب بيانات الحضور
    //     $attendanceData = HrTimeTrack::selectRaw(
    //         '
    //         hr_time_tracks.employee_id,
    //         COUNT(CASE WHEN type = 2 THEN 1 END) as present_days,
    //         COUNT(CASE WHEN type = 1 THEN 1 END) as absent_days,
    //         COUNT(CASE WHEN type = 3 THEN 1 END) as vacation_days,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.delay), 0)) as total_delay_minutes,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.early_leave), 0)) as total_early_leave_minutes,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.overtime), 0)) as total_overtime_minutes
    //         ',
    //     )->leftJoin('hr_time_track_details', 'hr_time_tracks.id', '=', 'hr_time_track_details.hr_time_track_id');

    //     // إضافة شرط employee_id إذا كان موجوداً
    //     if (!empty($employeeId)) {
    //         $attendanceData->where('hr_time_tracks.employee_id', $employeeId);
    //     }

    //     // إضافة شرط التواريخ إذا كانت موجودة
    //     if (!empty($startDate) && !empty($endDate)) {
    //         $attendanceData->whereBetween('hr_time_tracks.created_at', [$startDate, $endDate]);
    //     }

    //     // تجميع حسب معرف الموظف وجلب البيانات
    //     $attendanceData = $attendanceData->groupBy('hr_time_tracks.employee_id')->get();

    //     dd($attendanceData[0]);

    //     return $attendanceData;
    // }

    //  public function SummaryAttendance($request)
    // {
    //     // جلب employee_id من الطلب إذا كان موجوداً
    //     $employeeId = $request->employee_id;

    //     // تحويل التواريخ إلى كائنات Carbon أو الشهر الحالي افتراضيًا
    //     $startDate = $request->start_date
    //         ? \Carbon\Carbon::parse($request->start_date)->startOfDay()
    //         : \Carbon\Carbon::now()->startOfMonth();

    //     $endDate = $request->end_date
    //         ? \Carbon\Carbon::parse($request->end_date)->endOfDay()
    //         : \Carbon\Carbon::now()->endOfMonth();

    //     // جلب بيانات الحضور
    //     // $attendanceData = HrTimeTrack::selectRaw('
    //     //         hr_time_tracks.employee_id,
    //     //         SUM(CASE WHEN hr_time_tracks.type = 2 THEN 1 ELSE 0 END) as present_days,
    //     //         SUM(CASE WHEN hr_time_tracks.type = 1 THEN 1 ELSE 0 END) as absent_days,
    //     //         SUM(CASE WHEN hr_time_tracks.type = 3 THEN 1 ELSE 0 END) as vacation_days,
    //     //         SUM(CASE WHEN hr_time_tracks.type = 4 THEN 1 ELSE 0 END) as holiday_days,

    //     //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.delay), 0)) as total_delay,
    //     //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.earlyArrival), 0)) as total_early_arrival,
    //     //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.early_leave), 0)) as total_early_leave,
    //     //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.overtime), 0)) as total_overtime,

    //     //         ? as start_date,
    //     //         ? as end_date
    //     //     ', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
    //     //     ->leftJoin('hr_time_track_details', 'hr_time_tracks.id', '=', 'hr_time_track_details.hr_time_track_id');

    //     // // إضافة شرط employee_id إذا كان موجوداً
    //     // if (!empty($employeeId)) {
    //     //     $attendanceData->where('hr_time_tracks.employee_id', $employeeId);
    //     // }

    //     // // فلترة على الحقل الصحيح (date)
    //     // $attendanceData->whereBetween('hr_time_tracks.date', [$startDate->toDateString(), $endDate->toDateString()]);

    //     // // تجميع حسب الموظف
    //     // $attendanceData = $attendanceData->groupBy('hr_time_tracks.employee_id')->get();

    //     $attendanceData = HrTimeTrack::selectRaw('
    //         hr_time_tracks.employee_id,
    //         SUM(CASE WHEN hr_time_tracks.type = 2 THEN 1 ELSE 0 END) as present_days,
    //         SUM(CASE WHEN hr_time_tracks.type = 1 THEN 1 ELSE 0 END) as absent_days,
    //         SUM(CASE WHEN hr_time_tracks.type = 3 THEN 1 ELSE 0 END) as vacation_days,
    //         SUM(CASE WHEN hr_time_tracks.type = 4 THEN 1 ELSE 0 END) as holiday_days,
    //         ? as start_date,
    //         ? as end_date
    //     ', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
    //     ->whereBetween('hr_time_tracks.date', [$startDate->toDateString(), $endDate->toDateString()])
    //     ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
    //     ->groupBy('employee_id');

    //     dd($attendanceData);

    //     return $attendanceData;
    // }

    public function SummaryAttendance($request)
    {
        $employeeId = $request->employee_id;
        $departmentId = $request->department_id;
        $branchId = $request->branch_id;

        // تحديد التواريخ
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();

        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : \Carbon\Carbon::now()->endOfMonth();

        // الاستعلام
        $attendanceData = HrTimeTrack::selectRaw(
            '
        hr_time_tracks.employee_id,
        hr_employees.department_id,
        COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 2 THEN hr_time_tracks.id END) as present_days,
        COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 1 THEN hr_time_tracks.id END) as absent_days,
        COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 3 THEN hr_time_tracks.id END) as vacation_days,
        COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 4 THEN hr_time_tracks.id END) as holiday_days,
        COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 5 THEN hr_time_tracks.id END) as exempt_days,
        SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.delay), 0)) as total_delay,
        SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.early_arrival), 0)) as total_early_arrival,
        SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.early_leave), 0)) as total_early_leave,
        SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.overtime), 0)) as total_overtime,
        ? as start_date,
        ? as end_date
        ',
            [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')],
        )
            ->leftJoin('hr_employees', 'hr_time_tracks.employee_id', '=', 'hr_employees.id')
            ->leftJoin('employees', 'hr_employees.employee_id', '=', 'employees.id') // << مهم
            ->leftJoin('hr_time_track_details', 'hr_time_tracks.id', '=', 'hr_time_track_details.hr_time_track_id')
            ->whereBetween('hr_time_tracks.date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($employeeId, fn($q) => $q->where('hr_time_tracks.employee_id', $employeeId))
            ->when($departmentId, fn($q) => $q->where('hr_employees.department_id', $departmentId))
            ->when($branchId, fn($q) => $q->where('employees.branch_id', $branchId)) // << تمت المعالجة
            ->groupBy('hr_time_tracks.employee_id', 'hr_employees.department_id')
            ->get();

        return $attendanceData;
    }

    // public function SummaryAttendance($request)
    // {
    //     $employeeId = $request->employee_id;
    //     $departmentId = $request->department_id;
    //     $branchId = $request->branch_id;

    //     // تحديد التواريخ
    //     $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();

    //     $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : \Carbon\Carbon::now()->endOfMonth();

    //     // الاستعلام
    //     $attendanceData = HrTimeTrack::selectRaw(
    //         '
    //         hr_time_tracks.employee_id,
    //         hr_employees.department_id,
    //         COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 2 THEN hr_time_tracks.id END) as present_days,
    //         COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 1 THEN hr_time_tracks.id END) as absent_days,
    //         COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 3 THEN hr_time_tracks.id END) as vacation_days,
    //         COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 4 THEN hr_time_tracks.id END) as holiday_days,
    //         COUNT(DISTINCT CASE WHEN hr_time_tracks.type = 5 THEN hr_time_tracks.id END) as exempt_days,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.delay), 0)) as total_delay,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.earlyArrival), 0)) as total_early_arrival,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.early_leave), 0)) as total_early_leave,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.overtime), 0)) as total_overtime,
    //         ? as start_date,
    //         ? as end_date
    //         ',
    //         [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]
    //     )
    //         ->leftJoin('hr_employees', 'hr_time_tracks.employee_id', '=', 'hr_employees.id')
    //         ->leftJoin('hr_time_track_details', 'hr_time_tracks.id', '=', 'hr_time_track_details.hr_time_track_id')
    //         ->whereBetween('hr_time_tracks.date', [$startDate->toDateString(), $endDate->toDateString()])
    //         ->when($employeeId, fn($q) => $q->where('hr_time_tracks.employee_id', $employeeId))
    //         ->when($departmentId, fn($q) => $q->where('hr_employees.department_id', $departmentId))
    //         ->when($branchId, fn($q) => $q->where('hr_employees.main_employee.branch_id', $branchId))
    //         ->groupBy('hr_time_tracks.employee_id', 'hr_employees.department_id')
    //         ->get();

    //     return $attendanceData;
    // }

    public function AttendanceRecords($request)
    {
        $employeeId = $request->employee_id;
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : \Carbon\Carbon::now()->endOfMonth();

        $attendanceData = HrTimeTrack::where('employee_id', $employeeId) // Add employeeId here
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('type_id')) {
            $attendanceData->where('type', $request->type_id); // Include all relevant types
        }
        return $attendanceData->orderByDesc('date')->get();
    }

    public function employeesdata($request)
    {
        $employeesdata = HrEmployee::findOrFail($request->employee_id);
        return $employeesdata;
    }

    public function departmentsdata()
    {
        return HrDepartment::get()->pluck('name', 'id')->toArray();
    }

    // public function Departments( $request)
    // {

    //     $employees = HrEmployee::with(['main_employee', 'department', 'salary'])
    //     ->get();

    // return $employees;

    // }

    public function Departments($request)
    {
        // تحديد اللغة للاستخدام في الترجمة
        $locale = app()->getLocale();

        // بناء الاستعلام مع الانضمام
        $query = DB::table('')->isolateBranch()
            ->join('hr_employees as he', 'e.id', '=', 'he.employee_id') // ربط الموظف بالوظيفة
            ->join('hr_departments as hdt', 'he.department_id', '=', 'hdt.id') // ربط القسم باستخدام hr_departments
            ->join('hr_jobs as hjt', 'he.job_id', '=', 'hjt.id') // ربط الوظيفة باستخدام hr_jobs
            ->leftJoin('hr_department_translations as hdt_trans', function ($join) use ($locale) {
                $join->on('hdt.id', '=', 'hdt_trans.hr_department_id')->where('hdt_trans.locale', '=', $locale); // الترجمة بناءً على اللغة
            })
            ->leftJoin('hr_job_translations as hjt_trans', function ($join) use ($locale) {
                $join->on('hjt.id', '=', 'hjt_trans.hr_job_id')->where('hjt_trans.locale', '=', $locale); // الترجمة بناءً على اللغة
            })
            ->leftJoin('employee_identities as ei', 'e.id', '=', 'ei.employee_id') // ربط الهوية
            ->leftJoin('hr_salaries as hs', 'he.employee_id', '=', 'hs.employee_id') // ربط جدول الرواتب
            ->select(
                'he.id as employee_id',
                'e.username',
                'e.phone',
                'e.email',
                DB::raw("COALESCE(hdt_trans.name, 'N/A') as department_name"), // جلب اسم القسم المترجم
                DB::raw("COALESCE(hjt_trans.name, 'N/A') as job_name"), // جلب اسم الوظيفة المترجمة
                'ei.identity_no', // رقم الهوية
                'ei.identity_expired_at', // تاريخ انتهاء الهوية
                'ei.insurance_no', // رقم التأمين
                'hs.basic', // الراتب الأساسي
                'hs.day_amount', // راتب اليوم
                'hs.hour_amount', // راتب الساعة
                'he.start_at', // إضافة عمود start_at
            );

        // فلترة حسب employee_id إذا كان موجوداً في الطلب
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('he.employee_id', $request->employee_id);
        }

        // فلترة حسب department_id إذا كان موجوداً في الطلب
        if ($request->has('department_id') && $request->department_id) {
            $query->where('he.department_id', $request->department_id);
        }

        // فلترة حسب start_date (تاريخ انتهاء الهوية) إذا كان موجوداً في الطلب
        if ($request->has('start_date') && $request->start_date) {
            $query->where('ei.identity_expired_at', '>=', $request->start_date);
        }

        // فلترة حسب end_date (تاريخ انتهاء الهوية) إذا كان موجوداً في الطلب
        if ($request->has('end_date') && $request->end_date) {
            $query->where('ei.identity_expired_at', '<=', $request->end_date);
        }

        // تنفيذ الاستعلام النهائي
        $employees = $query->get();

        return $employees; // إرجاع البيانات
    }

    public function custodies($request)
    {
        // تحديد اللغة للاستخدام في الترجمة
        $locale = app()->getLocale();

        // بناء الاستعلام مع الانضمام
        $query = DB::table('')->isolateBranch()
            ->join('hr_employees as he', 'hc.employee_id', '=', 'he.id') // ربط العهدة بالموظف
            ->join('hr_assets as ha', 'hc.asset_id', '=', 'ha.id') // ربط العهدة بالأصل
            ->leftJoin('hr_asset_translations as ha_trans', function ($join) use ($locale) {
                $join->on('ha.id', '=', 'ha_trans.hr_asset_id')->where('ha_trans.locale', '=', $locale); // جلب اسم الأصل المترجم
            })
            ->leftJoin('hr_asset_types as hat', 'ha.type_id', '=', 'hat.id') // ربط نوع الأصل
            ->leftJoin('hr_asset_type_translations as hat_trans', function ($join) use ($locale) {
                $join->on('hat.id', '=', 'hat_trans.hr_asset_type_id')->where('hat_trans.locale', '=', $locale); // جلب نوع الأصل المترجم
            })
            ->leftJoin('hr_departments as hd', 'ha.department_id', '=', 'hd.id') // ربط القسم
            ->leftJoin('hr_department_translations as hd_trans', function ($join) use ($locale) {
                $join->on('hd.id', '=', 'hd_trans.hr_department_id')->where('hd_trans.locale', '=', $locale); // جلب اسم القسم المترجم
            })
            ->select(
                'he.username as employee_name', // اسم الموظف
                DB::raw("COALESCE(hd_trans.name, 'N/A') as department"), // جلب اسم القسم المترجم
                'hc.details as custody', // العهدة
                DB::raw("COALESCE(hc.received_at, 'N/A') as delivery_date"), // تاريخ التسليم
                'hc.details as description', // الوصف
                DB::raw("COALESCE(hc.received_at, 'N/A') as return_date"), // تاريخ الرجوع
                DB::raw("COALESCE(ha_trans.name, 'N/A') as original"), // جلب اسم الأصل المترجم أو القيمة الافتراضية
                DB::raw("COALESCE(hat_trans.name, 'N/A') as asset_type"), // جلب نوع الأصل المترجم أو الاسم الافتراضي
            );

        // فلترة حسب employee_id إذا كان موجوداً في الطلب
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('hc.employee_id', $request->employee_id);
        }

        // فلترة حسب asset_id إذا كان موجوداً في الطلب
        if ($request->has('type_id') && $request->type_id) {
            $query->where('hat.id', $request->type_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('hc.received_at', '>=', $request->start_date);
        }

        // فلترة حسب end_date (تاريخ انتهاء الهوية) إذا كان موجوداً في الطلب
        if ($request->has('end_date') && $request->end_date) {
            $query->where('hc.received_at', '<=', $request->end_date);
        }

        // تنفيذ الاستعلام النهائي
        $custodies = $query->get();

        return $custodies; // ارجع البيانات المستخرجة
    }

    // public function SummaryAttendance()
    // {
    //     // الحصول على بداية ونهاية الشهر الحالي
    //     $startDate = now()->startOfMonth(); // بداية الشهر
    //     $endDate = now()->endOfMonth(); // نهاية الشهر

    //     // حساب السنة والشهر
    //     $currentMonth = now()->month; // شهر الحالي
    //     $currentYear = now()->year; // سنة الحالية

    //     // جلب بيانات الحضور والبيانات المطلوبة لكل الموظفين
    //     $attendanceData = HrTimeTrack::selectRaw('
    //         hr_time_tracks.employee_id,
    //         COUNT(CASE WHEN type = 2 THEN 1 END) as present_days,
    //         COUNT(CASE WHEN type = 1 THEN 1 END) as absent_days,
    //         COUNT(CASE WHEN type = 3 THEN 1 END) as vacation_days,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.delay), 0)) as total_delay_minutes,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.early_leave), 0)) as total_early_leave_minutes,
    //         SEC_TO_TIME(IFNULL(SUM(hr_time_track_details.overtime), 0)) as total_overtime_minutes
    //     ')
    //     ->leftJoin('hr_time_track_details', 'hr_time_tracks.id', '=', 'hr_time_track_details.hr_time_track_id')
    //     ->whereBetween('hr_time_tracks.date', [$startDate, $endDate]) // نطاق التواريخ
    //     ->groupBy('hr_time_tracks.employee_id') // تجميع حسب معرف الموظف
    //     ->get(); // جلب جميع النتائج

    //     // إضافة السنة والشهر لكل سجل في النتائج
    //     foreach ($attendanceData as $data) {
    //         $data->month = $currentMonth; // إضافة الشهر
    //         $data->year = $currentYear; // إضافة السنة
    //     }

    //     return $attendanceData;
    // }

    /**
     * دالة لتحويل الدقائق إلى صيغة ساعات ودقائق
     */

    // public function DeductionAttendance($request)
    // {
    //     $attendanceData = HrAttendance::select(
    //         'date',
    //         'employee_id',
    //         'address',
    //         'shift_from',
    //         'shift_to',
    //         'type',
    //         DB::raw('MIN(check_time) as first_check_in'),
    //         DB::raw('MAX(check_time) as last_check_out'),
    //         DB::raw('MIN(delay) as min_delay'),
    //         DB::raw('MIN(early_leave) as min_early_leave'),
    //         DB::raw('MAX(overtime) as max_overtime'),
    //         DB::raw('
    //             TIMEDIFF(
    //                 MAX(check_time),
    //                 MIN(check_time)
    //             ) - SEC_TO_TIME(SUM(delay)) + SEC_TO_TIME(SUM(early_leave)) as actual_work_hours
    //         '), // حساب ساعات العمل الفعلية
    //     )
    //         ->where('type', 2)
    //         ->groupBy('date', 'employee_id', 'address', 'shift_from', 'shift_to', 'type')
    //         ->orderBy('date', 'DESC') // ترتيب النتائج حسب التاريخ
    //         ->get();

    //     return $attendanceData;
    // }

    public function DeductionAttendance($request)
    {
        $attendanceData = HrAttendance::select(
            'date',
            'employee_id',
            'address',
            'shift_from',
            'shift_to',
            'type',
            DB::raw('MIN(CASE WHEN kind = 1 THEN check_time ELSE NULL END) as first_check_in'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN check_time ELSE NULL END) as last_check_out'),
            DB::raw('MAX(CASE WHEN kind = 1 THEN early_arrival ELSE NULL END) as early_arrival'),
            DB::raw('MIN(CASE WHEN kind = 1 THEN delay ELSE NULL END) as min_delay'),
            DB::raw('MIN(CASE WHEN kind = 2 THEN early_leave ELSE NULL END) as min_early_leave'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN overtime ELSE NULL END) as max_overtime'),
            DB::raw('
            TIMEDIFF(
                MAX(check_time),
                MIN(check_time)
            ) - SEC_TO_TIME(SUM(delay)) + SEC_TO_TIME(SUM(early_leave)) as actual_work_hours
        '), // حساب ساعات العمل الفعلية
        )->where('type', 2); // شرط النوع

        // إضافة شروط employee_id و التواريخ إذا كانت موجودة
        if ($request->has('employee_id') && $request->employee_id) {
            $attendanceData->where('employee_id', $request->employee_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $attendanceData->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->has('start_date') && $request->start_date) {
            $attendanceData->where('date', '>=', $request->start_date);
        } elseif ($request->has('end_date') && $request->end_date) {
            $attendanceData->where('date', '<=', $request->end_date);
        }

        // تجميع البيانات حسب الحقول المطلوبة
        $attendanceData = $attendanceData
            ->groupBy('date', 'employee_id', 'address', 'shift_from', 'shift_to', 'type')
            ->orderBy('date', 'DESC') // ترتيب النتائج حسب التاريخ
            ->get();

        return $attendanceData;
    }

    public function types()
    {
        return HrTimeTrack::types();
    }

    public function headerSummary()
    {
        return [__('hr::models/hr_report_types.SummaryAttendance_table.name'), __('hr::models/hr_report_types.SummaryAttendance_table.from_date'), __('hr::models/hr_report_types.SummaryAttendance_table.to_date'), __('hr::models/hr_report_types.SummaryAttendance_table.attendance_count'), __('hr::models/hr_report_types.SummaryAttendance_table.absence_count'), __('hr::models/hr_report_types.SummaryAttendance_table.holiday_days_count'), __('hr::models/hr_report_types.SummaryAttendance_table.vacation_days_count'), __('hr::models/hr_report_types.SummaryAttendance_table.exempt_days_count'), __('hr::models/hr_report_types.SummaryAttendance_table.earlyArrival'), __('hr::models/hr_report_types.SummaryAttendance_table.late'), __('hr::models/hr_report_types.SummaryAttendance_table.departure'), __('hr::models/hr_report_types.SummaryAttendance_table.overtime_hours')];
    }

    public function nameSummary()
    {
        return __('hr::models/hr_report_types.SummaryAttendance');
    }

    public function organization()
    {
        // Get the first organization with all necessary relationships
        $organization = Organization::with(['translations'])->first();

        // If no organization exists, return default data
        if (!$organization) {
            return [
                'name' => config('app.name', 'EvixHR'),
                'logo' => asset('admin_assets/media/logos/default-logo.png'),
                'seal' => asset('admin_assets/media/logos/stampevix.webp'),
                'signature' => '',
                'CR' => 'N/A',
                'tax_number' => 'N/A',
                'organization_number' => 'N/A',
                'chamber_no' => 'N/A',
                'national_address' => 'N/A',
                'activity' => 'N/A',
            ];
        }

        // Return organization data as array
        return [
            'name' => $organization->name ?? config('app.name', 'EvixHR'),
            'logo' => $organization->logo_original_path ?? '',
            'seal' => $organization->seal_original_path ?? asset('admin_assets/media/logos/stampevix.webp'),
            'signature' => $organization->signature_original_path ?? '',
            'CR' => $organization->CR ?? 'N/A',
            'tax_number' => $organization->tax_number ?? 'N/A',
            'organization_number' => $organization->organization_number ?? 'N/A',
            'insurance_sub_no' => $organization->insurance_sub_no ?? 'N/A',
            'chamber_no' => $organization->chamber_no ?? 'N/A',
            'national_address' => $organization->national_address ?? 'N/A',
            'activity' => $organization->activity ?? 'N/A',
            'status' => $organization->status_text ?? 'N/A',
        ];
    }

    // إذا كنت تريد استخدام البيانات في الـ PDF

    public function SummaryAttendancefixArray($data): array
    {
        return $data
            ->map(function ($report) {
                return [
                    'name' => $report->employee->username ?? 'N/A',

                    'from_date' => $report->start_date ? \Carbon\Carbon::parse($report->start_date)->format('Y-m-d') : 'N/A',

                    'to_date' => $report->end_date ? \Carbon\Carbon::parse($report->end_date)->format('Y-m-d') : 'N/A',

                    'attendance_count' => $report->present_days ?? 0,
                    'absence_count' => $report->absent_days ?? 0,
                    'holiday_days' => $report->holiday_days ?? 0,
                    'vacation_days' => $report->vacation_days ?? 0,
                    'exempt_days' => $report->exempt_days ?? 0,

                    'early_arrival' => substr($report->total_early_arrival ?? '00:00:00', 0, 8),
                    'late' => substr($report->total_delay ?? '00:00:00', 0, 8),
                    'departure' => substr($report->total_early_leave ?? '00:00:00', 0, 8),
                    'overtime' => substr($report->total_overtime ?? '00:00:00', 0, 8),
                ];
            })
            ->toArray();
    }

    public function getOrganizationForPDF()
    {
        $org = $this->organization();

        return [
            'company_name' => $org['name'],
            'logo_path' => $org['logo'],
            'seal_path' => $org['seal'],
            'signature_path' => $org['signature'],
            'cr_number' => $org['CR'],
            'tax_number' => $org['tax_number'],
            'details' => [
                'organization_number' => $org['organization_number'],
                'chamber_no' => $org['chamber_no'],
                'national_address' => $org['national_address'],
                'activity' => $org['activity'],
            ],
        ];
    }
    public function nameRecord()
    {
        return __('hr::models/hr_report_types.AttendanceRecords');
    }

    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------------------------------------------------------------

    public function RecordfixArray($data): array
    {
        if (empty($data)) {
            return [];
        }

        $result = [];

        $employee = $data[0]->employee ?? null;

        $employeeInfo = [
            'employee_name' => $employee->username ?? '',
            'job' => $employee->job->name ?? '',
            'department' => $employee->department->name ?? '',
            'attendance_type' => $employee->attendance_type_text ?? '',
        ];

        foreach ($data as $attendanceRecord) {
            $details = $attendanceRecord->timeTrackDetails ?? [];

            // في حال وجود تفاصيل حضور
            if (count($details) > 0) {
                foreach ($details as $index => $item) {
                    $row = array_merge($this->buildMainRow($attendanceRecord, $employeeInfo, $index, count($details)), $this->buildDetailRow($attendanceRecord, $item));

                    $result[] = $row;
                }
            }
            // في حال عدم وجود تفاصيل
            else {
                $row = array_merge($this->buildMainRow($attendanceRecord, $employeeInfo), [
                    'location' => '',
                    'detail_status' => '',
                    'work_period' => '',
                    'fingerprint' => '',
                    'early_arrival' => '',
                    'late' => '',
                    'early_departure' => '',
                    'overtime' => '',
                    'item_bg_color' => '',
                    'no_details' => true,
                ]);

                $result[] = $row;
            }
        }

        return $result;
    }

    private function buildMainRow($attendanceRecord, array $employeeInfo, int $index = 0, int $rowspan = 0): array
    {
        return [
            'rowspan_count' => $index === 0 ? $rowspan : 0,
            'employee_name' => $index === 0 ? $employeeInfo['employee_name'] : null,
            'job' => $index === 0 ? $employeeInfo['job'] : null,
            'department' => $index === 0 ? $employeeInfo['department'] : null,
            'attendance_type' => $index === 0 ? $employeeInfo['attendance_type'] : null,
            'date' => $index === 0 ? $attendanceRecord->date : null,
            'day' => $index === 0 ? \Carbon\Carbon::parse($attendanceRecord->date)->locale('en')->translatedFormat('l') : null,
            'work_hours' => $index === 0 ? $attendanceRecord->hour : null,
            'status' => $index === 0 ? $attendanceRecord->type_text : null,
            'row_bg_color' => $this->getRowBgColor($attendanceRecord),
        ];
    }

    private function buildDetailRow($attendanceRecord, $item): array
    {
        return [
            'location' => $item->address ?? '',
            'detail_status' => $item->type_text ?? '',
            'work_period' => $this->formatWorkPeriod($item),
            'fingerprint' => ($item->check_time ?? '') . ' - ' . ($item->check_out ?? ''),
            'early_arrival' => secondsToTime($item->early_arrival ?? 0),
            'late' => secondsToTime($item->delay ?? 0),
            'early_departure' => secondsToTime($item->early_leave ?? 0),
            'overtime' => secondsToTime($item->overtime ?? 0),
            'item_bg_color' => $this->getItemBgColor($attendanceRecord, $item),
        ];
    }

    private function getRowBgColor($attendanceRecord): string
    {
        return match ($attendanceRecord->type) {
            1 => '#E42312', // غياب / تأخير
            4 => '#FFFF00', // إجازة
            5 => '#12E42E', // حضور
            default => '',
        };
    }

    private function getItemBgColor($attendanceRecord, $item): string
    {
        if ($item->type == 6) {
            return '#12E42E';
        }

        if ($item->type == 1 && $attendanceRecord->type == 2) {
            return '#E42312';
        }

        return '';
    }

    private function formatWorkPeriod($item): string
    {
        if (empty($item->shift_from) || empty($item->shift_to)) {
            return '';
        }

        return \Carbon\Carbon::parse($item->shift_from)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($item->shift_to)->format('h:i A');
    }

    //     public function RecordfixArray($data): array
    //     {
    //         $result = [];

    //           $employeeName = $data[0]->employee->username ?? '';

    //             $job = $data[0]->employee->job->name ?? '';
    //             $department = $data[0]->employee->department->name ?? '';
    //   $attendance_type = $data[0]->employee->attendance_type_text ?? '';

    //         foreach ($data as $attendanceRecord) {

    //             if (count($attendanceRecord->timeTrackDetails) > 0) {
    //                 foreach ($attendanceRecord->timeTrackDetails as $index => $item) {
    //                     // Determine background color
    //                     $bgColor = '';
    //                     if ($item->type == 1 && $attendanceRecord->type == 2) {
    //                         $bgColor = '#E42312';
    //                     } elseif ($item->type == 6) {
    //                         $bgColor = '#12E42E';
    //                     } elseif ($attendanceRecord->type == 1) {
    //                         $bgColor = '#E42312';
    //                     } elseif ($attendanceRecord->type == 4) {
    //                         $bgColor = '#FFFF00';
    //                     } elseif ($attendanceRecord->type == 5) {
    //                         $bgColor = '#12E42E';
    //                     }

    //                     $itemBgColor = '';
    //                     if ($item->type == 1 && $attendanceRecord->type == 2) {
    //                         $itemBgColor = '#E42312';
    //                     } elseif ($item->type == 6) {
    //                         $itemBgColor = '#12E42E';
    //                     }

    //                     $row = [
    //                         'rowspan_count' => $index === 0 ? count($attendanceRecord->timeTrackDetails) : 0,
    //                         'employee_name' => $index === 0 ? $employeeName : null,
    //                           'job' => $index === 0 ? $job : null,
    //                             'department' => $index === 0 ? $department : null,
    //                              'attendance_type' => $index === 0 ? $attendance_type : null,
    //                         'date' => $index === 0 ? $attendanceRecord->date : null,
    //                         'day' => $index === 0 ? \Carbon\Carbon::parse($attendanceRecord->date)->locale('ar')->translatedFormat('l') : null,
    //                         'work_hours' => $index === 0 ? $attendanceRecord->hour : null,
    //                         'status' => $index === 0 ? $attendanceRecord->type_text : null,
    //                         'location' => $item->address ?? '',
    //                         'detail_status' => $item->type_text ?? '',
    //                         'work_period' => isset($item->shift_from) && isset($item->shift_to) ? \Carbon\Carbon::parse($item->shift_from)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($item->shift_to)->format('h:i A') : '',
    //                         'fingerprint' => ($item->check_time ?? '') . ' - ' . ($item->check_out ?? ''),
    //                         'early_arrival' => secondsToTime($item->early_arrival ?? 0),
    //                         'late' => secondsToTime($item->delay ?? 0),
    //                         'early_departure' => secondsToTime($item->early_leave ?? 0),
    //                         'overtime' => secondsToTime($item->overtime ?? 0),
    //                         'row_bg_color' => $bgColor,
    //                         'item_bg_color' => $itemBgColor,
    //                     ];

    //                     $result[] = $row;
    //                 }
    //             } else {
    //                 // No time tracking details
    //                 $bgColor = '';
    //                 if (in_array($attendanceRecord->type, [3, 4])) {
    //                     $bgColor = '#FFFF00';
    //                 } elseif ($attendanceRecord->type == 5) {
    //                     $bgColor = '#12E42E';
    //                 }

    //                 $row = [
    //                     'rowspan_count' => 0,
    //                     'employee_name' => $employeeName,
    //                     'date' => $attendanceRecord->date,
    //                     'day' => \Carbon\Carbon::parse($attendanceRecord->date)->locale('ar')->translatedFormat('l'),
    //                     'work_hours' => $attendanceRecord->hour,
    //                     'status' => $attendanceRecord->type_text,
    //                     'location' => '',
    //                     'detail_status' => '',
    //                     'work_period' => '',
    //                     'fingerprint' => '',
    //                     'early_arrival' => '',
    //                     'late' => '',
    //                     'early_departure' => '',
    //                     'overtime' => '',
    //                     'row_bg_color' => $bgColor,
    //                     'item_bg_color' => '',
    //                     'no_details' => true,
    //                 ];

    //                 $result[] = $row;
    //             }
    //         }

    //         return $result;
    //     }

    public function headerRecord()
    {
        return [
            // __('hr::models/hr_report_types.AttendanceRecords_table.employee_name'),
            __('hr::models/hr_report_types.AttendanceRecords_table.date'),
            // '', // Day column - no header needed or use a translation key
            __('hr::models/hr_report_types.AttendanceRecords_table.work_hours'),
            __('hr::models/hr_report_types.fields.status'),
            __('hr::models/hr_report_types.AttendanceRecords_table.location'),
            __('hr::models/hr_report_types.fields.status'),
            __('hr::models/hr_report_types.AttendanceRecords_table.work_period'),
            __('hr::models/hr_report_types.AttendanceRecords_table.fingerprint'),
            __('hr::models/hr_report_types.AttendanceRecords_table.earlyArrival'),
            __('hr::models/hr_report_types.AttendanceRecords_table.late'),
            __('hr::models/hr_report_types.AttendanceRecords_table.early_departure'),
            __('hr::models/hr_report_types.AttendanceRecords_table.overtime'),
        ];
    }
}
