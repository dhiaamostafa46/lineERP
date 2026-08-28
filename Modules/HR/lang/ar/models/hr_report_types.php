<?php

return [
    'singular' => 'نوع التقرير',
    'plural' => 'أنواع التقارير',
    'fields' => [
        'id' => 'Id',
        'name' => 'الإسم',
        'status' => 'الحالة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'leave' => 'إجازة',
    ],

    'additional_reports' => [
        'holidays' => 'الإجازات',
        'financial_reports' => 'تقارير مالية',
        'attendance_reports' => 'تقارير الحضور والانصراف',
        'employee_reports' => 'تقارير الموظفين',
    ],

    'Expired_identity' => ' تقرير الإقامات المنتهية',
    'Expired_identity_table' => [
        'employee_id' => 'رقم الموظف',
        'username' => 'اسم المستخدم',
        'department_name' => 'القسم',
        'job_name' => 'الوظيفة',
        'identity_no' => 'رقم الهوية',
        'identity_expired_at' => 'تاريخ انتهاء الهوية',
        'gender' => 'الجنس',
        'nationality' => 'الجنسية',
        'start_date' => 'تاريخ المباشرة',
        'remaining_days' => 'المتبقية الأيام',
    ],




    'Fingerprint' => 'تقرير البصمات',
     'Fingerprint_table' => [
        'date' => 'التاريخ',
        'employee_id' => 'رقم الوظيفي',
        'employee_name' => 'اسم الموظف',
        'address' => 'العنوان',
        'shift_from' => 'بداية الدوام',
        'shift_to' => 'نهاية الدوام',
        'all_check_times' => 'جميع أوقات التسجيل',
        'total_days' => 'إجمالي الأيام',
        'days_with_punches' => 'أيام بها تسجيل',
        'days_without_punches' => 'أيام بدون تسجيل',
        'total_punches' => 'إجمالي التسجيلات',
        'attendance_percentage' => 'نسبة الحضور',
    ],

    'Contact' => 'تقرير العقود',
    'Contact_table' => [
        'id' => 'Id',
        'employee_id' => 'الموظف',
        'type_id' => 'النوع',
        'file' => 'الملف',
        'start_at' => 'تاريخ البداية',
        'end_at' => 'تاريخ النهاية',
        'qiwa_no' => 'رقم العقد في قوى',
        'name' => 'الإسم',
        'status' => 'الحالة',
        'department' => 'القسم', // إضافة القسم
        'job' => 'الوظيفة', // إضافة الوظيفة
        'salary' => 'الراتب', // إضافة الراتب
        'working_hours' => 'ساعات العمل', // إضافة ساعات العمل
        'attendance' => 'الدوام', // إضافة الدوام
    ],

    'LeaveHolday' => 'تقرير الإجازات',
    'LeaveHoliday_table' => [
        'employee_id' => 'الموظف',
        'status' => 'الحالة',
        'start_at' => 'تاريخ البداية',
        'end_at' => 'تاريخ النهاية',
        'contract' => 'العقد',
        'leave_type' => 'نوع الاجازة',
        'days' => 'الأيام',
        'department' => 'القسم', // القسم
        'job' => 'الوظيفة', // الوظيفة
        'shift' => 'الدوام', // إضافة كلمة الدوام مع توضيح shift
        'direct_manager' => 'المدير المباشر', // إضافة كلمة المدير المباشر
    ],

    'LeaveHoldaybalance' => 'تقرير رصيد الإجازات',
    'LeaveHoldaybalance_table' => [
        'employee_id' => 'الموظف',
        'contract' => 'العقد',
        'leave_type' => 'نوع الإجازة',
        'department' => 'القسم',
        'leave_balance' => 'رصيد الإجازات',
        'join_date' => 'تاريخ الالتحاق',
        'carried_balance' => 'الرصيد المرحل',
        'current_balance' => 'الرصيد الحالي',
        'leave_days' => 'عدد ايام الإجازة السنوية',
        'number_of_years' => 'عدد السنوات', // إضافة عدد السنوات
        'carried_balance_cost' => 'تكلفة الرصيد المرحل', // إضافة تكلفة الرصيد المرحل
        'current_balance_cost' => 'تكلفة الرصيد الحالي', // إضافة تكلفة الرصيد الحالي
    ],

    'rewards' => 'تقرير الخصومات والإضافات',
    'rewards_table' => [
        'employee_id' => 'الموظف',
        'deduction_or_bonus_type' => 'نوع الخصم أو المكافأة',
        'bonus_type' => 'نوع المكافأة',
        'value' => 'القيمة',
        'department' => 'القسم',
        'penalty' => 'الجزاء',
        'bonus' => 'المكافأة',
        'date' => 'التاريخ', // التاريخ
        'amount' => 'المبلغ', // المبلغ
        'description' => 'وصف الخصم', // وصف الخصم
        'due_date' => 'تاريخ الاستحقاق', // تاريخ الاستحقاق
        'status' => 'الحالة', // الحالة
    ],

    'EndService' => 'تقرير  نهاية الخدمة ',
    'EndService_table' => [
        'employee_id' => 'الموظف', // الموظف
        'deduction_or_bonus_type' => 'نوع الخصم أو المكافأة', // نوع الخصم أو المكافأة
        'value' => 'القيمة', // القيمة
        'department' => 'القسم', // القسم
        'penalty' => 'الجزاء', // الجزاء
        'bonus' => 'المكافأة', // المكافأة
        'position' => 'الوظيفية', // الوظيفية
        'join_date' => 'تاريخ الالتحاق', // تاريخ الالتحاق
        'years_of_service' => 'سنوات الخدمة', // سنوات الخدمة
        'leave_days' => 'أيام الإجازة', // أيام الإجازة
        'leave_amount' => 'مبلغ الإجازة', // مبلغ الإجازة
        'end_service_bonus' => 'مكافأة نهاية الخدمة', // مكافأة نهاية الخدمة
        'total' => 'المجموع', // المجموع
        'contract_end_date' => 'تاريخ إنهاء العقد', // تاريخ إنهاء العقد
        'termination_reason' => 'السبب', // السبب
    ],

    'advances' => 'تقرير   السلفيات ',
    'advances_table' => [
        'employee_id' => 'الموظف',
        'department' => 'القسم',
        'job' => 'الوظيفة', // إضافة كلمة الوظيفة
        'salary' => 'الراتب', // إضافة كلمة الراتب
        'amount' => 'المبلغ', // المبلغ
        'max_advance' => 'الحد الأقصى للسلف', // إضافة الحد الأقصى للسلف
        'due_date' => 'تاريخ الاستحقاق', // تاريخ الاستحقاق
        'deduction_date' => 'تاريخ الخصم', // تاريخ الخصم
        'status' => 'الحالة', // الحالة
        'description' => 'وصف', // إضافة كلمة وصف
        'date' => 'التاريخ', // إضافة كلمة وصف
    ],

    'Payroll' => 'تقرير  كشف الرواتب ',
    'Payroll_table' => [
        'date' => 'التاريخ',
        'total' => 'الاجمالي',
        'currency' => 'العملة',
        'status' => 'الحالة',
    ],

    'Attendance' => 'تقرير الحضور والانصراف  ',
    'Attendance_table' => [
        'employee_name' => 'اسم الموظف',
        'attendance_date' => 'تاريخ الحضور',
        'first_record' => 'اول تسجيل',
        'last_record' => 'اخر تسجيل',
        'work_hours' => 'ساعات العمل',
        'actual_work_hours' => 'ساعات العمل الفعلية', // إضافة حقل لساعات العمل الفعلية
        'work_period' => 'فترة الدوام', // إضافة حقل فترة الدوام
        'location' => 'الموقع',
        'late' => 'تاخير',
        'earlyArrival' => 'حضور مبكر',
        'departure' => 'انصراف مبكر',
        'overtime' => 'العمل الاضافي',
    ],
    'SummaryAttendance' => 'تقرير   ملخص الحضور والانصراف ',
    'SummaryAttendance_table' => [
        'name' => 'الاسم',
        'work_hours' => 'ساعات العمل',
        'late' => 'تأخير',
        'earlyArrival' => 'حضور مبكر',
        'departure' => 'انصراف مبكر',
        'overtime_hours' => 'العمل الإضافية',
        'total' => 'المجموع',
        'month' => 'شهر', // إضافة كلمة "شهر"
        'year' => 'سنة', // إضافة كلمة "سنة"
        'exempt_days_count' => 'أيام الإعفاء',
        'attendance_count' => 'أيام الحضور',
        'absence_count' => 'أيام الغياب',
        'vacation_days_count' => 'الإجازات الرسمية',
        'holiday_days_count' => 'الإجازات',
        'procedure' => 'إجراء', // إضافة كلمة "إجراء"
        'from_date' => 'من تاريخ', // إضافة كلمة "من تاريخ"
        'to_date' => 'إلى تاريخ', // إضافة كلمة "إلى تاريخ"
    ],

    'DeductionAttendance' => 'تقرير خصومات الحضور والانصراف ',
    'DeductionAttendance_table' => [
        'employee_name' => 'اسم الموظف',
        'date' => 'التاريخ',
        'late' => 'تاخير',
        'work_period' => 'فترة الدوام',
        'early_departure' => 'انصراف مبكر',
        'earlyArrival' => 'حضور مبكر',
        'work_hours' => 'ساعات العمل',
        'action' => 'الاجراء',
        'movement' => 'الحركات',
        'total' => 'المجموع',
        'overtime' => 'العمل الاضافي',
        'amount' => 'المبلغ',
    ],

    'AttendanceRecords' => 'سجلات الحضور',
    'AttendanceRecords_table' => [
        'employee_name' => 'اسم الموظف',
        'date' => 'التاريخ',
        'first_record' => 'اول تسجيل',
        'last_record' => 'اخر تسجيل',
        'late' => 'تاخير',
        'earlyArrival' => 'حضور مبكر',
        'work_period' => 'فترة الدوام', // إضافة حقل فترة الدوام
        'early_departure' => 'انصراف مبكر',
        'work_hours' => 'ساعات العمل',
        'action' => 'الاجراء',
        'location' => 'الموقع',
        'total' => 'المجموع',
        'fingerprint' => 'البصمة',


        'overtime' => 'العمل الاضافي',
        'month' => 'شهر', // إضافة كلمة "شهر"
        'year' => 'سنة', // إضافة كلمة "سنة"
        'day' => 'اليوم', // إضافة كلمة "سنة"
        'difference' => 'الفارق', // إضافة كلمة الفارق
    ],

    'Departments' => 'تقرير الموظفين',
    'Departmentss_table' => [
        'employee_name' => 'اسم الموظف',
        'department' => 'القسم',
        'mobile' => 'الجوال',
        'email' => 'الايميل',
        'identity' => 'الهوية',
        'identity_expiry' => 'انتهاء الهوية',
        'insurance' => 'التامين',
        'salary' => 'الراتب',
        'start_date' => 'تاريخ المباشر',
        'job_title' => 'المسمى الوظيفي',
    ],

    'custodies' => 'تقرير العهد',
    'custodies_table' => [
        'employee_name' => 'اسم الموظف',
        'department' => 'القسم',
        'custody' => 'العهدة',
        'type' => 'النوع',
        'delivery_date' => 'تاريخ التسليم',
        'description' => 'الوصف',
        'return_date' => 'تاريخ الرجوع', // تاريخ الرجاع
        'original' => 'الأصل', // الاصل
        'asset_type' => 'نوع الأصل', // نوع الاصل
    ],
];
