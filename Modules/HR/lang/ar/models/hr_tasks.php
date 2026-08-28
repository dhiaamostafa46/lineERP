<?php

return [
    'singular' => 'المهمة',
    'plural' => 'المهام',
    'fields' => [
        'id' => 'الرقم التعريفي',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'done' => 'تاريخ الإنجاز',
        'status' => 'الحالة',
        'flage' => 'النوع',

        'department' => 'القسم',
        'employee_id' => 'الموظف',
        'file' => 'الملف',
        'Group'      => 'المجموعة',

        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'deleted_at' => 'تاريخ الحذف',
    ],
    'details' => [
        'singular' => 'تفاصيل المهمة',
        'plural' => 'تفاصيل المهام',
        'hr_task_id' => 'رقم المهمة',
        'description' => 'الوصف',
        'employee_id' => 'الموظف',
        'userID' => 'رقم المستخدم',
        'file' => 'الملف',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'deleted_at' => 'تاريخ الحذف',
    ],
    'statuses' => [
        'pending' => 'قيد الانتظار',
        'in_progress' => 'تحت المعالجة',
        'processed' => 'تمت المعالجة',
        'closed' => 'مغلقة',
    ],

    'flages' => [
        'department' => 'القسم', // قسم
        'employees' => 'الموظفين', // موظفين
        'Group' => 'مجموعات', // موظفين
    ],
];
