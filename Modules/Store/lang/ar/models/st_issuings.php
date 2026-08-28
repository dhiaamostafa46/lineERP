<?php

return [
    'singular' => 'صرف مخزني',
    'plural'   => 'الصرف المخزني',

    'fields' => [
        'id'             => 'المعرف',
        'org_id'         => 'المنشأة',
        'branch_id'      => 'الفرع',
        'user_id'        => 'المستخدم',
        'document_number'=> 'رقم المستند',
        'document_date'  => 'تاريخ المستند',
        'store_id'       => 'المستودع',
        'status'         => 'الحالة',
        'total_items'    => 'عدد الأصناف',
        'total_quantity' => 'إجمالي الكمية',
        'total_value'    => 'إجمالي القيمة',
        'notes'          => 'ملاحظات',
        'product_name'   => 'اسم الصنف (البند)',
        'tree_account_id'=> 'الحساب المقابل',
    ],

    'items' => [
        'product_id'         => 'الصنف',
        'unit_id'            => 'الوحدة',
        'quantity'           => 'الكمية',
        'unit_cost'          => 'تكلفة الوحدة',
        'total_cost'         => 'إجمالي التكلفة',
        'notes'              => 'ملاحظات',
    ]
];
