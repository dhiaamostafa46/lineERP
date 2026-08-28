<?php

return [

    'singular' => 'إعدادات المحاسبة',
    'plural'   => 'إعدادات المحاسبة',

    'sections' => [
        'general'  => 'الإعدادات العامة',
        'journal'  => 'إعدادات القيود اليومية',
        'security' => 'الأمان وقفل الفترات',
    ],

    'fields' => [
        'currency'                   => 'العملة',
        'decimal_places'             => 'المنازل العشرية',
        'journal_prefix'             => 'بادئة رقم القيد',
        'journal_next_number'        => 'رقم القيد التالي',
        'allow_backdated_entries'    => 'السماح بقيود بتاريخ سابق',
        'allow_future_dated_entries' => 'السماح بقيود بتاريخ مستقبلي',
        'lock_period_pwd_enabled'    => 'تفعيل كلمة مرور قفل الفترة',
        'lock_period_pwd'            => 'كلمة المرور',
        'vehicle_auto_post_journal_entries' => 'الترحيل التلقائي لعمليات المركبات',
        'driver_auto_post_journal_entries'  => 'الترحيل التلقائي لعمليات السائقين',
        'store_auto_post_journal_entries'   => 'الترحيل التلقائي لعمليات المخزون',
        'sales_auto_post_journal_entries'   => 'الترحيل التلقائي لعمليات المبيعات',
        'purchase_auto_post_journal_entries' => 'الترحيل التلقائي لعمليات المشتريات',
    ],

    'depreciation' => [
        'straight_line'     => 'القسط الثابت',
        'declining_balance' => 'القسط المتناقص',
    ],

    'frequency' => [
        'monthly'   => 'شهري',
        'quarterly' => 'ربع سنوي',
        'yearly'    => 'سنوي',
    ],

    'labels' => [
        'general_settings'      => 'الإعدادات العامة',
        'journal_settings'      => 'إعدادات القيود',
        'depreciation_settings' => 'إعدادات الإهلاك',
        'tax_settings'          => 'إعدادات الضريبة',
        'advanced_settings'     => 'الإعدادات المتقدمة',
    ],

    'unknown' => 'غير معروف',
];
