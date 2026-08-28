<?php

namespace App\Http\Controllers;

use App\Exports\GlobalDataExport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function dashboard(): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        return view('dashboard');
    }
    public function UserActivity()
    {
       

        // 1. Get all users in the system
        $allUsers = User::get();

        // 2. Get all activities in the system (default to last 7 days)
        $startDate = request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->endOfDay() : now()->endOfDay();

        $activities = Activity::with('causer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        $activitiesByCauser = $activities->groupBy('causer_id');

        $headers = [
            'المستخدم',
            'المنتجات',
            'الفواتير',
            'المخزون',
            'الحسابات',
            'HR',
            // 'المركبات',
            'الملاحظات',
            'الاقتراحات',
    'المشاكل',
        ];

        $dataExcel = $allUsers->map(function ($u) use ($activitiesByCauser) {
            $userActivities = $activitiesByCauser->get($u->id, collect());

            $counts = [
                'Products' => 0,
                'Invoices' => 0,
                'Stock' => 0,
                'Accounts' => 0,
                'HR' => 0,
                // 'Vehicles' => 0,
                'other' => 0,
            ];

            foreach ($userActivities as $activity) {
                $subjectType = $activity->subject_type;
                if (! $subjectType) {
                    $counts['other']++;
                    continue;
                }

                $className = class_basename($subjectType);
                $fullNamespace = $subjectType;

                if (str_starts_with($fullNamespace, 'Modules\BasicData\\') ||
                    str_starts_with($fullNamespace, 'App\Models\BasicDataApp\\')) {
                    $counts['Products']++;
                } elseif (str_starts_with($fullNamespace, 'Modules\Invoices\\') ||
                    str_starts_with($fullNamespace, 'App\Models\invApp\\')) {
                    $counts['Invoices']++;
                } elseif (str_starts_with($fullNamespace, 'Modules\Store\\') ||
                    str_starts_with($fullNamespace, 'App\Models\StoreApp\\')) {
                    $counts['Stock']++;
                } elseif (str_starts_with($fullNamespace, 'Modules\AccuSoft\\') ||
                    str_starts_with($fullNamespace, 'Modules\Finance\\') ||
                    str_starts_with($fullNamespace, 'App\Models\AccuSoft\\')) {
                    $counts['Accounts']++;
                } elseif (str_starts_with($fullNamespace, 'Modules\HR\\') ||
                    $className === 'Employee' ||
                    $className === 'EmployeeIdentity' ||
                    $className === 'EmployeeBank') {
                    $counts['HR']++;
                } elseif (str_starts_with($fullNamespace, 'Modules\Vehicles\\') ||
                    str_starts_with($fullNamespace, 'Modules\Drivers\\') ||
                    str_starts_with($fullNamespace, 'Modules\Operation\\') ||
                    str_starts_with($fullNamespace, 'App\Models\Vehicles\\')) {
                    // $counts['Vehicles']++;
                    $counts['other']++;
                } else {
                    // Fallback using class name match
                    if (preg_match('/(Product|Category|Unit|ProductSize|ProductUnit)/i', $className)) {
                        $counts['Products']++;
                    } elseif (preg_match('/(Invoice|Quotation|Order|Payment|Supplier|Vendor)/i', $className)) {
                        $counts['Invoices']++;
                    } elseif (preg_match('/(Store|StOpeningBalance|StReceiving|StIssuing|StSettlement|StDamaged|StDirectTransfer|StReservation|Inventory)/i', $className)) {
                        $counts['Stock']++;
                    } elseif (preg_match('/(TaxAccount|Bond|Safe|Bank|Asset|Depreciation|Account)/i', $className)) {
                        $counts['Accounts']++;
                    } elseif (preg_match('/(Employee|Attendance|Holiday|Payroll|Leave|Salary|Vpn)/i', $className)) {
                        $counts['HR']++;
                    } elseif (preg_match('/(Vehicle|Driver|Car|Fleet|Trip|License)/i', $className)) {
                        // $counts['Vehicles']++;
                        $counts['other']++;
                    } else {
                        $counts['other']++;
                    }
                }
            }

            // Build Notes
            $notes = '';
            $latestActivity = $userActivities->first();
            if ($latestActivity) {
                $action = $latestActivity->description;
                $actionTranslations = [
                    'created' => 'إنشاء',
                    'updated' => 'تعديل',
                    'deleted' => 'حذف',
                    'opened' => 'فتح',
                ];
                $translatedAction = $actionTranslations[strtolower($action)] ?? $action;

                $subjectType = $latestActivity->subject_type;
                $moduleName = $subjectType ? class_basename($subjectType) : 'نظام';
                $moduleTranslations = [
                    'User' => 'مستخدم',
                    'Role' => 'صلاحية',
                    'Employee' => 'موظف',
                    'Branch' => 'فرع',
                    'Area' => 'منطقة',
                    'City' => 'مدينة',
                    'Company' => 'شركة',
                    'CompanyContract' => 'عقد شركة',
                    'TaxAccount' => 'حساب ضريبي',
                    'Product' => 'منتج',
                    'Store' => 'مستودع',
                    'InvSupplier' => 'مورد',
                    'SalesInvoice' => 'فاتورة مبيعات',
                    'PurchaseInvoice' => 'فاتورة مشتريات',
                ];
                $translatedModule = $moduleTranslations[$moduleName] ?? $moduleName;
                $time = $latestActivity->created_at ? $latestActivity->created_at->format('Y-m-d H:i') : '';

                $notes = "آخر عملية: $translatedAction في $translatedModule ($time)";
                if ($counts['other'] > 0) {
                    $notes .= " | عمليات أخرى: ".$counts['other'];
                }
            } else {
                $notes = 'لا يوجد نشاط مسجل';
            }

            return [
                'user_name' => $u->name.' ('.($u->email ?? $u->phone).')',
                'products' => $counts['Products'],
                'invoices' => $counts['Invoices'],
                'stock' => $counts['Stock'],
                'accounts' => $counts['Accounts'],
                'hr' => $counts['HR'],
                // 'vehicles' => $counts['Vehicles'],
                'notes' => $notes,
            ];
        })->toArray();

        return Excel::download(new GlobalDataExport($dataExcel, $headers), 'UserActivitySummary.xlsx');
    }
}
