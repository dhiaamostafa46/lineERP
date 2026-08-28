<?php

return [
    'singular' => 'الباقات',
    'plural' => 'الباقات',
    'description' => 'يتم اعتماد الباقات على عدد المستخدمين',
    'note' => 'ملاحظة',
    'fields' => [
        'id' => '#',
        'users_count' => 'عدد المستخدمين',
        'subscription' => 'إشتراك',
        'from' => 'من',
        'to' => 'الى',
        'price' => 'السعر',
        'user' => 'مستخدمين',
        'SAR' => 'ريال',
        'up' => 'ما فوق ',
        'activate_yearly_subscription' => 'تفعيل اشتراك السنوي',
        'payment' => 'الدفع',
        'user_price' => 'سعر المستخدم',
        'package_expiry_date' => 'تاريخ انتهاء الباقة', // جديد
    ],

    'message' => [
        'Payment_failed' => 'فشل الدفع',
        'Payment_successfully' => 'تمت معالجة الدفع بنجاح',
        'process_failed' => 'فشل في معالجة الدفع',
        'Payment_type' => 'نوع الدفع غير صالح',
    ],
];
