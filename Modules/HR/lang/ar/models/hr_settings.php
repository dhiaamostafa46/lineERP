<?php

return [
    'singular' => 'اعدادات الموارد البشرية',
    'plural'   => 'اعدادات الموارد البشرية',

    'sections' => [
        'payroll_settings'    => 'إعدادات الرواتب',
        'attendance_settings' => 'إعدادات الحضور والبصمة',
        'leave_settings'      => 'إعدادات الإجازات',
    ],

    'fields'   => [
        'id'                             => '#',
        'delivery_payroll_at'            => 'توقيت تسليم الرواتب',
        'preparing_payroll_at'           => 'توقيت تحضير الرواتب',
        'min_salary'                     => 'الحد الأدنى للراتب',
        'max_off_days'                   => 'الحد الأقصى لعدد أيام الإجازة',
        'currency'                       => 'العملة',
        'created_at'                     => 'تاريخ الإنشاء',
        'updated_at'                     => 'تاريخ التعديل',
        'approval_payroll'               => 'موافقات كشوفات الرواتب',
        'user_id'                        => 'المستخدم',
        'sort'                           => 'الترتيب في الموافقة',
        'is_current'                     => 'هل هو منشئ كشف الرواتب؟',
        'missing_fingerprint_policy'     => 'سياسة البصمة الناقصة',
        'calculate_missing_fingerprint'  => 'احتساب البصمة الناقصة',
        'leave_include_weekend'          => 'الإجازة تشمل العطلة الأسبوعية',
        'leave_include_holidays'         => 'الإجازة تشمل العطلات الرسمية',
    ],

    'missing_fp' => [
        'half_day'   => 'خصم نصف يوم',
        'full_day'   => 'خصم يوم كامل',
        'half_shift' => 'خصم نصف شفت',
        'full_shift' => 'خصم شفت كامل',
        'ignore'     => 'تجاهل',
    ],

    'hints' => [
        'delivery_payroll_hint'      => 'اليوم من الشهر الذي يتم فيه تسليم الرواتب للموظفين',
        'preparing_payroll_hint'     => 'اليوم من الشهر الذي يتم فيه البدء بتحضير كشف الرواتب',
        'min_salary_hint'            => 'الحد الأدنى للراتب المسموح به في النظام',
        'max_off_days_hint'          => 'أقصى عدد من أيام الإجازة المسموحة في السنة',
        'calculate_fingerprint_hint' => 'عند التفعيل، سيتم احتساب خصومات على البصمات الناقصة',
        'missing_fingerprint_hint'   => 'اختر السياسة المتبعة عند تسجيل بصمة ناقصة',
        'leave_weekend_hint'         => 'عند التفعيل، سيتم احتساب أيام العطل الأسبوعية ضمن الإجازة',
        'leave_holidays_hint'        => 'عند التفعيل، سيتم احتساب العطلات الرسمية ضمن الإجازة',
    ],

    'placeholders' => [
        'enter_day'     => 'أدخل اليوم (1-31)',
        'enter_amount'  => 'أدخل المبلغ',
        'enter_days'    => 'أدخل عدد الأيام',
        'select_user'   => 'اختر المستخدم',
        'select_policy' => 'اختر السياسة',
    ],

    'buttons' => [
        'add_approver' => 'إضافة موافق جديد',
    ],

    'messages' => [
        'confirm_delete'            => 'هل أنت متأكد من حذف هذا العنصر؟',
        'at_least_one_approver'     => 'يجب أن يكون هناك موافق واحد على الأقل',
        'settings_updated'          => 'تم تحديث الإعدادات بنجاح',
        'one_current_required'      => 'يجب تحديد منشئ واحد على الأقل لكشف الرواتب',
    ],
];
