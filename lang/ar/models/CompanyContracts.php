<?php

return [
    'singular' => 'عقد شركة',
    'plural' => 'عقود الشركات',
    'fields' => [
        'id' => '#',
        'company' => 'الشركة',
        'company_pricing_type' => 'تسعير الشركة',
        'company_pricing_value' => 'قيمة تسعير الشركة',
        'driver_payment_type' => 'أجر السائق',
        'driver_payment_value' => 'قيمة أجر السائق',
        'settlement_cycle' => 'دورة التسوية',
        'start_date' => 'تاريخ البدء',
        'end_date' => 'تاريخ الانتهاء',
        'status' => 'الحالة',
        'notes' => 'ملاحظات',
        'created_at' => 'تاريخ الإنشاء',
    ],
    'placeholders' => [
        'select_company' => 'اختر الشركة',
    ],
    'company_pricing_types' => [
        'per_order' => 'لكل طلب',
        'percentage' => 'نسبة مئوية',
        'monthly' => 'شهري',
        'custom' => 'مخصص',
    ],
    'driver_payment_types' => [
        'salary' => 'راتب',
        'per_order' => 'لكل طلب',
        'percentage' => 'نسبة مئوية',
    ],
    'settlement_cycles' => [
        'daily' => 'يومي',
        'weekly' => 'أسبوعي',
        'monthly' => 'شهري',
    ],
];
