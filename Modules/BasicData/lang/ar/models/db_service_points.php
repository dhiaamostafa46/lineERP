<?php

return [
    'singular' => 'نقطة الخدمة',
    'plural'   => 'نقاط الخدمة',

    'fields'   => [
        'id'         => 'المعرف',
        'name'       => 'الاسم',
        'orgID'      => 'المؤسسة',
        'branchID'   => 'الفرع',
        'userID'     => 'المستخدم',
        'code'       => 'الكود',
        'type'       => 'النوع',
        'status'     => 'الحالة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التعديل',
    ],

    'statuses' => [
        0 => 'غير نشط',
        1 => 'نشط',
    ],

    'types' => [
        1 => 'طاولة',
        2 => 'سيارة',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
    ],
];
