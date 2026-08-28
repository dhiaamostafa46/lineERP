<?php

return [
    'singular' => 'مورد',
    'plural'   => 'الموردين',

    'fields'   => [
        'id'              => 'المعرف',
        'name'            => 'اسم المورد',
        'phone'           => 'رقم الجوال',
        'email'           => 'البريد الإلكتروني',
        'vat_number'      => 'الرقم الضريبي',
        'cr_number'       => 'رقم السجل التجاري',
        'address'         => 'العنوان التفصيلي',

        'country'         => 'الدولة',
        'city'            => 'المدينة',
        'district'        => 'الحي',
        'street'          => 'الشارع',
        'building_number' => 'رقم المبنى',
        'postal_code'     => 'الرمز البريدي',
        'additional_number' => 'الرقم الإضافي',

        'tree_account_id' => 'الحساب المالي المرتبط',
        'branch_id'       => 'الفرع',
        'credit_limit'    => 'الحد الائتماني',
        'status'          => 'الحالة',

        'file'            => 'المرفق',
        'created_at'      => 'تاريخ الإنشاء',
    ],

    'sections' => [
        'contact_info'   => 'بيانات التواصل',
        'tax_info'       => 'البيانات الضريبية',
        'address_info'   => 'بيانات العنوان',
        'financial_info' => 'البيانات المالية',
        'attachments'    => 'المرفقات',
    ],
];
