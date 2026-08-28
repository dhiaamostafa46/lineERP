<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\App\Http\Controllers\HrAbsentController;
use Modules\HR\App\Http\Controllers\HrAttendanceController;
use Modules\HR\App\Http\Controllers\HrCustodyController;
use Modules\HR\App\Http\Controllers\HrEmpDashboardController;
use Modules\HR\App\Http\Controllers\HrEndServiceController;
use Modules\HR\App\Http\Controllers\HrTaskController;
use Modules\HR\App\Http\Controllers\HrTerminationContractController;
use Modules\HR\App\Http\Controllers\HrTerminationController;
use Modules\HR\App\Http\Controllers\HrTerminationTypeController;
use Modules\HR\App\Http\Controllers\HrTerminationTypeRewardController;
use Modules\HR\App\Http\Controllers\HrTrackerController;
use Modules\HR\App\Http\Controllers\HrTrackerJobController;
use Modules\HR\App\Http\Controllers\HrTrackingApprovalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/AttendanceTranslate', function () {

    // معالجة السجلات وحساب early/late/leave/overtime
    \Modules\HR\App\Models\HrAttendance::chunk(2000, function ($records) {

        foreach ($records as $attendance) {

            $employee = $attendance->employee;
            if (! $employee || ! $employee->shift) {
                continue;
            }

            $shiftParent = $employee->shift;
            $shifts = $shiftParent->shifts ?? [];
            if (count($shifts) == 0) {
                continue;
            }

            $attendanceTime = \Carbon\Carbon::parse($attendance->check_time)->secondsSinceMidnight();

            // إيجاد أقرب وردية
            $closestShift = null;
            $bestDiff = PHP_INT_MAX;
            foreach ($shifts as $shift) {
                $fromSec = \Carbon\Carbon::createFromFormat('H:i:s', $shift->from)->secondsSinceMidnight();
                $toSec = \Carbon\Carbon::createFromFormat('H:i:s', $shift->to)->secondsSinceMidnight();

                $diff = min(abs($attendanceTime - $fromSec), abs($attendanceTime - $toSec));

                if ($diff < $bestDiff) {
                    $bestDiff = $diff;
                    $closestShift = $shift;
                }
            }

            if (! $closestShift) {
                continue;
            }

            $fromSec = \Carbon\Carbon::createFromFormat('H:i:s', $closestShift->from)->secondsSinceMidnight();
            $toSec = \Carbon\Carbon::createFromFormat('H:i:s', $closestShift->to)->secondsSinceMidnight();

            // حساب القيم
            $earlyArrival = $attendanceTime < $fromSec ? $fromSec - $attendanceTime : 0;
            $lateArrival = $attendanceTime > $fromSec ? $attendanceTime - $fromSec : 0;
            $earlyLeave = $attendanceTime < $toSec ? $toSec - $attendanceTime : 0;
            $overtime = $attendanceTime > $toSec ? $attendanceTime - $toSec : 0;

            // تطبيق حدود الإعدادات
            $toSeconds = fn ($v) => (int) $v * 60;

            $earlyArrival = ($earlyArrival > $toSeconds($shiftParent->early_entry ?? 0)) ? 0 : $earlyArrival;
            $lateArrival = ($lateArrival <= $toSeconds($shiftParent->late_entry ?? 0)) ? 0 : $lateArrival;
            $earlyLeave = ($earlyLeave <= $toSeconds($shiftParent->early_exit ?? 0)) ? 0 : $earlyLeave;
            $overtime = ($overtime < $toSeconds($shiftParent->late_exit ?? 0)) ? 0 : $overtime;

            // dd([
            //      'shift_from' => $closestShift->from,
            //     'shift_to' => $closestShift->to,
            //     'early_arrival' => $earlyArrival,
            //     'late_arrival' => $lateArrival,
            //     'early_leave' => $earlyLeave,
            //     'overtime' => $overtime,
            //     'attendance' => $attendance->toArray(),
            //     'employee' => $employee->toArray(),
            // ]

            // );
            // تحديث السجل مباشرة
            $attendance->update([
                'shift_from' => $closestShift->from,
                'shift_to' => $closestShift->to,
                'early_arrival' => $earlyArrival,
                'delay' => $lateArrival,
                'early_leave' => $earlyLeave,
                'overtime' => $overtime,
            ]);
        }
    });

    // تحديث الـ kind و type لجميع السجلات
    \Modules\HR\App\Models\HrAttendance::where('date', '>=', '2025-11-01')
        ->orderBy('employee_id')
        ->orderBy('date')
        ->chunk(2000, function ($attendances) {

            // تجميع حسب الموظف + اليوم + الشفت
            $grouped = $attendances->groupBy(function ($item) {
                return $item->employee_id.'_'.$item->date.'_'.$item->shift_from.'_'.$item->shift_to;
            });

            foreach ($grouped as $records) {

                if ($records->count() == 1) {
                    $records->each(function ($record) {
                        $record->update([
                            'kind' => 1,
                            'type' => 1,
                        ]);
                    });
                } else {
                    $records->each(function ($record, $key) {
                        $record->update([
                            'kind' => $key == 0 ? 1 : 2,
                            'type' => 1,
                        ]);
                    });
                }
            }
        });

    return '✔ Attendance updated successfully';
});

// Route::get('/AttendanceTranslate', function () {
//     \Modules\HR\App\Models\HrAttendance::chunk(2000, function ($records) {
//         foreach ($records as $attendance) {
//             $employee = $attendance->employee;
//             if (!$employee || !$employee->shift) {
//                 continue;
//             }
//             $shiftParent = $employee->shift ?? [];
//             $shifts = $shiftParent->shifts ?? [];
//             if (count($shifts) == 0) {
//                 continue;
//             }

//             // تحويل وقت التسجيل إلى ثواني من بداية اليوم
//             $attendanceTime = \Carbon\Carbon::parse($attendance->check_time)->secondsSinceMidnight();
//             // إيجاد أقرب وردية
//             $closestShift = null;
//             $bestDiff = PHP_INT_MAX;

//             foreach ($shifts as $shift) {
//                 $fromSec = \Carbon\Carbon::createFromFormat('H:i:s', $shift->from)->secondsSinceMidnight();
//                 $toSec = \Carbon\Carbon::createFromFormat('H:i:s', $shift->to)->secondsSinceMidnight();

//                 $diff = min(abs($attendanceTime - $fromSec), abs($attendanceTime - $toSec));

//                 if ($diff < $bestDiff) {
//                     $bestDiff = $diff;
//                     $closestShift = $shift;
//                 }
//             }

//             if (!$closestShift) {
//                 continue;
//             }

//             $fromSec = \Carbon\Carbon::createFromFormat('H:i:s', $closestShift->from)->secondsSinceMidnight();
//             $toSec = \Carbon\Carbon::createFromFormat('H:i:s', $closestShift->to)->secondsSinceMidnight();

//             // حساب القيم
//             $earlyArrival = $attendanceTime < $fromSec ? $fromSec - $attendanceTime : 0;
//             $lateArrival = $attendanceTime > $fromSec ? $attendanceTime - $fromSec : 0;
//             $earlyLeave = $attendanceTime < $toSec ? $toSec - $attendanceTime : 0;
//             $overtime = $attendanceTime > $toSec ? $attendanceTime - $toSec : 0;

//             // تعديل حسب حدود الإعدادات
//             $toSeconds = fn($v) => (int) $v * 60;

//             if ($earlyArrival > $toSeconds($shiftParent->early_entry ?? 0)) {
//                 $earlyArrival = 0;
//             }

//             if ($lateArrival <= $toSeconds($shiftParent->late_entry ?? 0)) {
//                 $lateArrival = 0;
//             }

//             if ($earlyLeave <= $toSeconds($shiftParent->early_exit ?? 0)) {
//                 $earlyLeave = 0;
//             }

//             if ($overtime < $toSeconds($shiftParent->late_exit ?? 0)) {
//                 $overtime = 0;
//             }

//             // ▼ تحديث السجل مباشرة
//             $attendance->update([
//                 'shift_from' => $closestShift->from,
//                 'shift_to' => $closestShift->to,
//                 'early_arrival' => $earlyArrival,
//                 'late_arrival' => $lateArrival,
//                 'early_leave' => $earlyLeave,
//                 'overtime' => $overtime,
//             ]);
//         }
//     });

//     $attendances = \Modules\HR\App\Models\HrAttendance::where('date', '>=', '2025-11-18')->orderBy('date')->get();

//     $grouped = $attendances->groupBy(function ($item) {
//         return $item->employee_id . '_' . $item->date . '_' . $item->shift_from . '_' . $item->shift_to;
//     });

//     foreach ($grouped as $records) {
//         if ($records->count() == 1) {
//             foreach ($records as $record) {
//                 $record->update([
//                     'kind' => 1,
//                     'type' => 1,
//                 ]);
//             }
//         }
//     }

//     return '✔ Attendance updated successfully';
// });

Route::group(['middleware' => ['auth', 'permissionHandler', 'check.vpn', 'check.device']], function () {
    Route::resource('document-types', 'HrDocumentTypeController')->names('document_types');
    Route::resource('contract-types', 'HrContractTypeController')->names('contract_types');
    Route::resource('shift-types', 'HrShiftTypeController')->names('shift_types');
    Route::resource('jobs', 'HrJobController')->names('jobs');
    Route::resource('departments', 'HrDepartmentController')->names('departments');
    Route::resource('contracts', 'HrContractController')->names('contracts');
    Route::resource('documents', 'HrDocumentController')->names('documents');
    Route::resource('employees', 'HrEmployeeController')->names('employees');

    Route::get('employees-export', 'HrEmployeeController@export')->name('employees.export');
    Route::post('employees-import', 'HrEmployeeController@import')->name('employees.import');

    Route::resource('allowances', 'HrAllowanceController')->names('allowances');
    Route::resource('deducts', 'HrDeductController')->names('deducts');
    Route::resource('salaries', 'HrSalaryController')->names('salaries');
    Route::post('salaries/import', [Modules\HR\App\Http\Controllers\HrSalaryController::class, 'importSalaries'])->name('salaries.import');

    Route::resource('report-types', 'HrReportTypeController')
        ->names('report_types')
        ->only(['index', 'edit', 'update']);
    Route::get('report-types-export/{id}', 'HrReportTypeController@export')->name('report_types.export');

    Route::resource('holiday-types', 'HrHolidayTypeController')->names('holiday_types');
    Route::resource('asset-types', 'HrAssetTypeController')->names('asset_types');
    Route::resource('rewards', 'HrRewardController');
    Route::resource('penalties', 'HrPenaltyController');
    Route::resource('assets', 'HrAssetController');
    Route::resource('holidays', 'HrHolidayController');
    Route::resource('advances', 'HrAdvanceController');
    Route::get('Place-export', 'HrPlaceController@export')->name('Place.export');
    Route::post('Place-import', 'HrPlaceController@import')->name('Place.import');
    Route::resource('Place', 'HrPlaceController');
    Route::resource('Task', 'HrTaskController');
    Route::resource('TaskDetails', 'HrTaskDetialsController');
    Route::resource('Attendance', 'HrAttendanceController');
    Route::resource('EndService', 'HrEndServiceController');
    Route::resource('ContractItem', 'HrContractItemController');
    Route::resource('GroupTask', 'HrGroupTaskController');
    Route::resource('Archive', 'HrArchiveController');
    Route::resource('templates', 'HrTemplateController');
    Route::resource('attendance-policies', 'HrAttendancePolicyController');
    Route::resource('CalendarEvents', 'HrCalendarEventController');
    Route::resource('posts', 'HrPostController');

    Route::resource('justifications', 'HrJustificationController')->names('justifications');

    Route::post('justifications/getAttendancesForEmployee', 'HrJustificationController@getAttendancesForEmployee')->name('justifications.getAttendancesForEmployee');
    // archive
    Route::get('Archive/restore/{id}', 'HrArchiveController@restore')->name('Archive.restore');
    Route::get('Archive/penalties/{id}', 'HrArchiveController@penalties')->name('Archive.penalties');
    Route::get('Archive/advances/{id}', 'HrArchiveController@advances')->name('Archive.advances');
    Route::get('Archive/rewards/{id}', 'HrArchiveController@rewards')->name('Archive.rewards');
    Route::get('Archive/custodies/{id}', 'HrArchiveController@custodies')->name('Archive.custodies');
    Route::get('Archive/holidays/{id}', 'HrArchiveController@holidays')->name('Archive.holidays');

    Route::post('post-Attendance-location', [HrAttendanceController::class, 'postAttendancelocation'])->name('hr-Attendance.Attendance-location');

    Route::post('get-Employees-salaries', [HrEndServiceController::class, 'getEmployeessalaries'])->name('hr-get-Employees-salaries');
    Route::post('calculate-eosb', [HrEndServiceController::class, 'calculateEosb'])->name('calculate-eosb');
    // attendance movement
    Route::get('attendance/movement', 'HrAttendanceController@AttendanceMovement')->name('attendance.movement');
    Route::get('attendance/actions', 'HrAttendanceController@actions')->name('attendance.actions');
    Route::post('attendance/penalties', 'HrAttendanceController@AttendancePenalties')->name('attendance.penalties');
    Route::post('attendance/rewards', 'HrAttendanceController@AttendanceRewards')->name('attendance.rewards');
    Route::post('attendance/reprocess-range', [HrAttendanceController::class, 'reprocessRange'])->name('attendance.reprocess_range');

    // approve advance
    Route::get('advances/approve/{id}', 'HrAdvanceController@approve')->name('advances.approve');
    Route::post('advances/update-payment', 'HrAdvanceController@updateMonthlyPayment')->name('advances.update_payment');
    // reject advance
    Route::get('advances/reject/{id}', 'HrAdvanceController@reject')->name('advances.reject');

    Route::resource('custodies', 'HrCustodyController');
    // receive custody
    Route::get('custodies/receive/{id}', [HrCustodyController::class, 'receive'])->name(name: 'custodies.receive');
    Route::get('custodies/Return/{id}', [HrCustodyController::class, 'Return'])->name(name: 'custodies.Return');
    Route::get('custodies/AcceptReturn/{id}', [HrCustodyController::class, 'AcceptReturn'])->name(name: 'custodies.AcceptReturn');
    Route::get('custodies/nonAccept/{id}', [HrCustodyController::class, 'nonAccept'])->name(name: 'custodies.nonAccept');
    // --
    Route::resource('settings', 'HrSettingController')->only(['edit', 'update']);
    Route::resource('payrolls', 'HrPayrollController');
    // Route::resource('payroll.approvals', 'HrPayrollApprovalController');
    // Route::resource('payroll.employees', 'HrPayrollEmployeeController');
    // Route::resource('payroll-employees.transactions', 'HrPayrollTransactionController');

    // get department jobs
    Route::resource('trackers', HrTrackerController::class);
    Route::post('get-department-jobs', [HrTrackerController::class, 'getDepartmentJobs'])->name('hr-trackers.department-jobs');
    // Route::resource('tracker-jobs', HrTrackerJobController::class);
    // Route::resource('tracking-approvals', HrTrackingApprovalController::class);
    Route::resource('termination-types', HrTerminationTypeController::class)->names('termination_types');
    // Route::resource('termination-type-rewards', HrTerminationTypeRewardController::class);
    Route::resource('terminations', HrTerminationController::class)->names('terminations');
    Route::view('my-requests', 'hr::my_requests.index')->name('my-requests.index');
    Route::view('my-requests/create', 'hr::my_requests.create')->name('my-requests.create');
    Route::view('my-requests/createEmp', 'hr::my_requests.advances_create_form')->name('my_requests.createEmp');
    // Route::resource('termination-contracts', HrTerminationContractController::class);

    // Report ALL HR
    Route::get('Report/Expired_Identity', 'HrReportController@Expired_Identity')->name('Report.Expired_Identity');
    Route::get('Report/Contact', 'HrReportController@Contact')->name('Report.Contact');
    Route::get('Report/LeaveHolday', 'HrReportController@LeaveHolday')->name('Report.LeaveHolday');
    Route::get('Report/LeaveHoldaybalance', 'HrReportController@LeaveHoldaybalance')->name('Report.LeaveHoldaybalance');
    Route::get('Report/rewards', 'HrReportController@rewards')->name('Report.rewards');
    Route::get('Report/EndService', 'HrReportController@EndService')->name('Report.EndService');
    Route::get('Report/advances', 'HrReportController@advances')->name('Report.advances');
    Route::get('Report/Payroll', 'HrReportController@Payroll')->name('Report.Payroll');
    Route::get('Report/Fingerprint', 'HrReportController@Fingerprint')->name('Report.Fingerprint');

    Route::get('Report/Attendance', 'HrReportController@Attendance')->name('Report.Attendance');
    Route::get('Report/SummaryAttendance', 'HrReportController@SummaryAttendance')->name('Report.SummaryAttendance');

    Route::get('Report/Summarypdf', 'HrReportController@Summarypdf')->name('Report.Summarypdf');
    Route::get('Report/Recordspdf', 'HrReportController@Recordspdf')->name('Report.Recordspdf');

    Route::get('Report/DeductionAttendance', 'HrReportController@DeductionAttendance')->name('Report.DeductionAttendance');
    Route::get('Report/AttendanceRecords', 'HrReportController@AttendanceRecords')->name('Report.AttendanceRecords');

    Route::get('Report/Departments', 'HrReportController@Departments')->name('Report.Departments');
    Route::get('Report/custodies', 'HrReportController@custodies')->name('Report.custodies');

    // by saeed
    Route::get('AttendanceMove/indexbydate', 'HrAttendanceController@indexByDate')->name('AttendanceMove.indexByDate');

    // Dashboard Routes

    Route::get('employeeDashboard', [HrEmpDashboardController::class, 'index'])->name('empdashboard.index');

    Route::get('employessSalary', [HrEmpDashboardController::class, 'employessSalary'])->name('empdashboard.employessSalary');
    Route::get('justificationsEmployee', [HrEmpDashboardController::class, 'justificationsEmployee'])->name('empdashboard.justificationsEmployee');

    Route::resource('absentrequests', HrAbsentController::class)->names('absentrequests');

    Route::get('/showTask/{id}', [HrTaskController::class, 'showTask'])->name('Task.showTask');
    Route::get('/absentstatus/{id}/{status}', [HrAbsentController::class, 'updatestatus'])->name('absent.updatestatus');
    Route::get('/hoursClaculate', [HrAttendanceController::class, 'OutHoursCakculate'])->name('attend.hourscalculate');

    // Route::get('employees-export', 'HrAttendanceController@export')->name('attendance.export');
    Route::post('attendance-import', 'HrAttendanceController@import')->name('attendance.import');
    Route::get('/calculateHours/{id}/{quantity}', [HrAttendanceController::class, 'calculateHours'])->name('attend.calculateHours');

    // Route::get('templates', [\App\Http\Controllers\Admin\TemplateController::class, 'index'])->name('templates.index');

});
// In Modules/HR/routes/web.php (or your main web routes file)

Route::get('/download/app', function () {

    return 'yes';
});
