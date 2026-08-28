<?php

return [
    'singular' => 'مسار الطلب',
    'plural' => 'مسارات الطلبات',
    'fields' => [
        'id' => 'Id',
        'department_id' => 'القسم',
        'type' => 'النوع',
        'status' => 'الحالة',
        'name' => 'إسم المسار',
        'tracker_approvals' => 'الموافقات',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'Updated At',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'in_progress' =>'تحت المعالجة',
        'apptovals' => 'الموافقات',


    ],
    'types' => [
        'holidays' => 'الإجازات',
        'penalties' => 'الجزاءآت',
        'advances' => 'السلفيات',
        'rewards' => 'المكافآت',
        'justifications' => 'التسوية',

    ]
];
