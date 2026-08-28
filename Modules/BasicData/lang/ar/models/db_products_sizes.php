<?php

return [
    'singular' => 'المقاس',
    'plural'   => 'المقاسات',

    'fields'   => [
        'id'                           => 'المعرف',
        'name'                         => 'اسم المقاس',
        'product_id'                   => 'المنتج',
        'sale_price'                   => 'سعر البيع',
        'cost_price'                   => 'سعر التكلفة',
        'base_unit_consumption_factor' => 'عامل استهلاك الوحدة الأساسية',
        'barcode'                      => 'الباركود',
        'status'                       => 'الحالة',
        'org_id'                       => 'المنظمة',
        'created_at'                   => 'تاريخ الإنشاء',
        'updated_at'                   => 'تاريخ التحديث',
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
