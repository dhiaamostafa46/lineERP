<?php

return [
    'singular' => 'الحضور',
    'plural' => 'الحضور',
    'presence' => 'حضور',        // Arabic: Presence
    'absence' => 'غياب',         // Arabic: Absence
    'checkout' => 'انصراف',      // Arabic: Checkout
    'location' => 'الموقع',
    'attendance_actions' => 'إجراءات الحضور',     // Arabic: Location

    'total_days' => 'عدد الأيام', // Arabic: Total Days
    'not_found' => 'المكان غير موجود', // Added text
    'attendance_success' => 'تم تسجيل الحضور بنجاح', // Added text
    'checkout_success' => 'تم تسجيل الانصراف بنجاح', // Added text
    'location_far' => 'مكان الحضور بعيد', // Added text
    'attendance_movement' => 'حركة الدوام', //
    'outactual_work_hours' => 'ساعات العمل خارج الدوام الرسمي', // ساعات العمل خارج الدوام الرسمي
     'entry_time_ended' => 'انتهى وقت الدخول',
    'exit_time_not_started_yet' => 'لم يبدأ وقت الانصراف بعد',

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
        'departure' => 'انصراف مبكر',
        'earlyArrival' => 'حضور مبكر',
        'overtime' => 'عمل الاضافي',
        'movement' => 'الحركات',
        'total' => 'المجموع',
        'apply' => 'تطبيق', // Adding the term 'Apply'
        'ignore' => 'تجاهل',
        'Balanced' => 'متوازن',
    ],


    'fields' => [
        'id' => 'الرمز التعريفي',
        'employee_id' => 'الموظف',
        'employee_name' => 'اسم الموظف',  // Added: Employee Name
        'job_title' => 'الوظيفة',         // Added: Job Title
        'department' => 'القسم',          // Added: Department
        'total_days' => 'عدد الأيام',      // Added: Total Days (again for clarity in fields)
        'day' => 'اليوم',
        'name' => 'الاسم',
        'lat' => 'خط العرض',
        'lon' => 'خط الطول',
        'address' => 'العنوان',
        'status' => 'الحالة',
        'distance' => 'المسافة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'deleted_at' => 'تاريخ الحذف',
        'date' => 'التاريخ',                  // Added: Date
        'check_in_time' => 'وقت الحضور',      // Added: Check-in Time
        'check_out_time' => 'وقت الانصراف',   // Added: Check-out Time
        'delay' => 'التأخير',                  // Added: Delay
        'early_leave' => 'الانصراف المبكر',    // Added: Early Leave
        'work_hours' => 'ساعات العمل',
        'earlyArrival' => 'حضور مبكر',
    ],
    'weekdays' => [
        'sunday' => 'الأحد',
        'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
        'saturday' => 'السبت',
    ],

    'type' => [
        'presence' => 'حضور',
        'checkout' => 'انصراف',
    ],


];
