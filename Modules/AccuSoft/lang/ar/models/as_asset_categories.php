<?php

return [
    'singular' => 'فئة الأصول الثابتة',
    'plural' => 'فئات الأصول الثابتة',
    'fields' => [
        'name' => 'اسم الفئة',
        'asset_account_id' => 'حساب الأصل الثابت',
        'accumulated_depreciation_account_id' => 'حساب مجمع الإهلاك',
        'depreciation_expense_account_id' => 'حساب مصروف الإهلاك',
        'default_depreciation_method' => 'طريقة الإهلاك الافتراضية',
        'default_useful_life' => 'العمر الافتراضي (سنوات)',
        'has_accounting_effect' => 'توليد قيود (لها تأثير محاسبي)',
        'calculation_type' => 'نوع المعالجة',
        'useful_life_type' => 'فترات الإهلاك',
        'created_at' => 'تاريخ الإنشاء',
    ],
    'methods' => [
        'none' => 'لا يوجد',
        'straight_line' => 'القسط الثابت',
        'declining_balance' => 'القسط المتناقص',
        'sum_of_years' => 'مجموع سنوات الاستخدام',
        'units_of_production' => 'وحدات الإنتاج',
    ],
];
