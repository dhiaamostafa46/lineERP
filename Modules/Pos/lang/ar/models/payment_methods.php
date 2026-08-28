<?php

return [
    'singular' => 'طريقة دفع',
    'plural' => 'طرق الدفع',

    'types' => [
        'cash' => 'صندوق',
        'card' => 'شبكة',
        'transfer' => 'حوالة',
        'credit' => 'الدفع الآجل',
        'installment' => 'الدفع المتعدد',
    ],

    'fields' => [
        'id' => 'المعرف',
        'name' => 'الاسم',
        'type' => 'النوع',
        'account_id' => 'الحساب',
        'is_active' => 'نشط',
        'status' => 'الحالة',
        'select_account' => 'اختر الحساب',
        'type_cash' => 'نقدي',
        'type_bank' => 'تحويل بنكي',
        'type_credit' => 'آجل',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],
];
