<?php

return [
    'singular' => 'المطبخ',
    'plural'   => 'المطابخ',

    'fields'   => [
        'id'         => 'المعرف',
        'name'       => 'اسم المطبخ',
        'orgID'      => 'المؤسسة',
        'branchID'   => 'الفرع',
        'userID'     => 'المستخدم',
        'barcode'    => 'الباركود',
        'status'     => 'الحالة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التعديل',
    ],

    'statuses' => [
        0 => 'غير نشط',
        1 => 'نشط',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
    ],
];
