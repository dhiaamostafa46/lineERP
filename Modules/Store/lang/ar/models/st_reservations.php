<?php

return [
    'singular' => 'حجز مخزون',
    'plural' => 'حجز على المخزون',
    'fields' => [
        'id' => 'المعرف',
        'user_id' => 'المستخدم',
        'document_number' => 'رقم المستند',
        'document_date' => 'التاريخ',
        'store_id' => 'المستودع',
        'status' => 'الحالة',
        'total_items' => 'إجمالي الأصناف',
        'total_quantity' => 'إجمالي الكمية',
        'total_value' => 'إجمالي القيمة',
        'notes' => 'ملاحظات',
        'items' => 'الأصناف المحجوزة',
        'product_id' => 'المنتج',
        'unit_id' => 'الوحدة',
        'quantity' => 'الكمية',
        'unit_cost' => 'تكلفة الوحدة',
        'total_cost' => 'التكلفة الإجمالية',
    ],
    'status' => [
        'draft' => 'مسودة',
        'reserved' => 'محجوز',
        'returned' => 'مرتجع للمستودع',
        'cancelled' => 'ملغي',
    ],
    'messages' => [
        'authorized_success' => 'تم تعميد الحجز بنجاح',
        'returned_success' => 'تم إرجاع الكمية للمستودع بنجاح',
    ]
];
