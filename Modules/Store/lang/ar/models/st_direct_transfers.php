<?php

return [
    'singular' => 'تحويل مخزني ',
    'plural'   => 'التحويلات المخزنية',

    'fields' => [
        'id'             => 'المعرف',
        'org_id'         => 'المنشأة',
        'branch_id'      => 'الفرع',
        'user_id'        => 'المستخدم',
        'document_number'=> 'رقم المستند',
        'document_date'  => 'تاريخ المستند',
        'from_store_id'  => 'من مستودع',
        'to_store_id'    => 'إلى مستودع',
        'status'         => 'الحالة',
        'total_items'    => 'عدد الأصناف',
        'total_quantity' => 'إجمالي الكمية',
        'total_value'    => 'إجمالي القيمة',
        'notes'          => 'ملاحظات',
        'product_name'   => 'اسم الصنف (البند)',
        'received_quantity' => 'الكمية المستلمة',
    ],

    'items' => [
        'product_id'         => 'الصنف',
        'unit_id'            => 'الوحدة',
        'quantity'           => 'الكمية',
        'unit_cost'          => 'تكلفة الوحدة',
        'total_cost'         => 'إجمالي التكلفة',
        'received_quantity'  => 'الكمية المستلمة',
        'notes'              => 'ملاحظات',
    ],

    'status' => [
        'draft' => 'مسودة',
        'in_transit' => 'بضاعة بالطريق',
        'transferred' => 'تم التحويل',
        'destination_draft' => 'مسودة تعميد',
        'completed' => 'مكتمل / معتمد',
        'cancelled' => 'ملغي',
        'partial_approved' => 'تعميد جزئي',
        'returned' => 'مرجع',
        'partial_returned' => 'مرجع جزئي',
    ]
];
