<?php

return [
    'singular' => 'الوحدة',
    'plural'   => 'الوحدات',

    'fields'   => [
        'id'                 => 'المعرف',
        'name'               => 'اسم الوحدة',
        'org_id'             => 'المؤسسة',
        'conversion_factor'  => 'عامل التحويل',
        'is_base'            => 'وحدة أساسية',
        'status'             => 'الحالة',
        'is_virtual'         => 'النوع',
        'created_at'         => 'تاريخ الإنشاء',
        'updated_at'         => 'تاريخ التعديل',
    ],

    'statuses' => [
        0 => 'غير نشطة',
        1 => 'نشطة',
    ],

    'virtual_statuses' => [
        0 => 'وحدة حقيقية',
        1 => 'وحدة افتراضية',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
        'real'     => 'badge badge-primary',
        'virtual'  => 'badge badge-warning',
    ],
];
