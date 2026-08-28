<?php

return [
    'singular' => 'صنف رصيد افتتاحي',
    'plural'   => 'أصناف الرصيد الافتتاحي',

    'fields' => [
        'id'                     => 'المعرف',
        'st_opening_balance_id'  => 'رقم',
        'product_id'             => 'المنتج',
        'quantity'               => 'الكمية',
        'unit_cost'              => 'تكلفة الوحدة',
        'expiry_date'            => 'تاريخ انتهاء الصلاحية',
        'batch_number'           => 'رقم الدفعة',
        'total_cost'             => 'التكلفة الإجمالية',
        'notes'                  => 'ملاحظات',
        'created_at'             => 'تاريخ الإنشاء',
        'updated_at'             => 'تاريخ التعديل',

        // الإضافات الجديدة
        'org_id'                 => 'المؤسسة',
        'branch_id'              => 'الفرع',
        'user_id'                => 'المستخدم',
        'store_id'               => 'المخزن',
        'inventory_adjustment_id'=> 'رقم حركة الجرد',
        'unit_id'                => 'الوحدة',
        'system_quantity'        => 'الكمية في النظام',
        'variance'               => 'الفرق',
        'movement_id'            => 'رقم الحركة',
    ],
];

