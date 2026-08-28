<?php

return [
    'singular' => 'رصيد افتتاحي',
    'plural' => 'الأرصدة الافتتاحية',

    'pages' => [
        'openingbalance' => 'الأرصدة الافتتاحية',
    ],

    'fields' => [
        'id' => 'المعرف',
        'store_id' => 'المستودع',
        'date' => 'التاريخ',
        'notes' => 'ملاحظات',
        'status' => 'الحالة',
        'total_cost' => 'التكلفة الإجمالية',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التعديل',
        'items' => 'الأصناف',
        'product_id' => 'المنتج',
        'quantity' => 'الكمية',
        'unit_cost' => 'تكلفة الوحدة',
        'expiry_date' => 'تاريخ انتهاء الصلاحية',
        'batch_number' => 'رقم الدفعة',

        'org_id' => 'المؤسسة',
        'branch_id' => 'الفرع',
        'user_id' => 'المستخدم',

        'adjustment_number' => 'رقم التعديل',
        'adjustment_date' => 'تاريخ التعديل',
        'type' => 'نوع التعديل',
        'sub_type' => 'النوع الفرعي',
        'reason' => 'السبب',

        'total_quantity' => 'إجمالي الكمية',
        'total_value' => 'القيمة الإجمالية',
        'created_by' => 'تم الإنشاء بواسطة',
        'approved_by' => 'تمت الموافقة بواسطة',
        'approved_at' => 'تاريخ الموافقة',
       
    ],

    'list_types' => [
        'all' => 'الكل',
        'opening' => 'رصيد افتتاحي',
        'damaged' => 'مخزون تالف',
        'settlement' => 'تسوية مخزون',
        'inventory' => 'جرد مخزون',
        'transfer_out' => 'تحويل صادر',
        'transfer_in' => 'تحويل وارد',
    ],
];
