<?php

return [

    'singular' => 'المنتج والخدمة',
    'plural'   => 'المنتجات والخدمات',
    'product'  => 'منتج',
    'service'  => 'خدمة',
    'products' => 'المنتجات',
    'services' => 'الخدمات',
    'sizes'    => 'المقاسات',
    'fields'   => [
        'id'                => 'المعرف',
        'name'              => 'اسم المنتج',
        'type'              => 'نوع المنتج',
        'details'           => 'التفاصيل',
        'barcode'           => 'الباركود',
        'min_quantity'      => 'الحد الأدنى',
        'category_id'       => 'الفئة',
        'kitchen_id'        => 'المطبخ',
        'unit_id'           => 'الوحدة',
        'cost_price'        => 'سعر التكلفة',
        'prod_price'        => 'سعر البيع',
        'vat'               => 'الضريبة',
        'calories'          => 'السعرات الحرارية',
        'base_unit_id'      => 'وحدة الأساس',
        'sale_unit_id'      => 'وحدة البيع',
        'purchase_unit_id'  => 'وحدة الشراء',
        'report_unit_id'    => 'وحدة التقرير',
        'img'               => 'الصورة',
        's_from'            => 'العمل من',
        's_to'              => 'العمل إلى',
        'work_days'         => 'أيام العمل',
        'have_sizes'        => 'يحتوي على مقاسات',
        'status'            => 'الحالة',
        'created_at'        => 'تاريخ الإنشاء',
        'service'           => 'خدمة',
        'product'           => 'منتج',
        'updated_at'        => 'تاريخ التعديل',
    ],

    'unit' => [
        'unit_id'           => 'الوحدة',
        'conversion_factor' => 'معامل التحويل',
        'is_base'           => 'وحدة أساسية',
        'average_cost'      => 'متوسط التكلفة',
    ],

    'size' => [
        'product_id'                   => 'المنتج',
        'name'                         => 'اسم المقاس',
        'sale_price'                   => 'سعر البيع',
        'cost_price'                   => 'سعر التكلفة',
        'base_unit_consumption_factor' => 'معامل استهلاك وحدة الأساس',
        'barcode'                      => 'الباركود',
        'status'                       => 'الحالة',
    ],

    'placeholders' => [
        'name'      => 'ادخل اسم المنتج',
        'details'   => 'ادخل تفاصيل المنتج',
        'size_name' => 'ادخل اسم المقاس',
        'barcode'   => 'ادخل الباركود',
    ],

    'sections' => [
        'basic_info'   => 'البيانات الأساسية',
        'other_info'   => 'بيانات اخرى',
        'resturnet_info' => 'بيانات المطعم',
        'vat_info'     => 'بيانات الضريبة',
        'units'        => 'الوحدات',
        'sizes'        => 'المقاسات',
        'add_unit'     => 'إضافة وحدة',
        'add_size'     => 'إضافة مقاس',
    ],

    'import' => [
        'category_name_description' => 'اسم :field',
        'kitchen_name_description'  => 'اسم :field',
        'unit_name_description'     => 'اسم :field',
        'typs_description' => ':field (1 = منتج، 2 = خدمة، 3 = مقاسات)',
    ],

    'import_instructions' => [
        'line1' => 'يجب أن يكون الملف بتنسيق Excel (.xlsx أو .xls).',
        'line2' => 'لضمان عملية استيراد ناجحة، يرجى اتباع تنسيق الأعمدة الموجود في ملف القالب. يمكنك تنزيل القالب من هنا:',
        'line3' => 'يرجى عدم تغيير ترتيب الأعمدة أو عناوينها لضمان نجاح عملية الرفع.',
        'line4' => 'تأكد من أن أسماء الفئات والوحدات والمطابخ موجودة مسبقاً في النظام.',
        'size_hint' => 'للمقاسات: اكتب الاسم كـ (اسم المنتج - المقاس) مثال: (تيشيرت - XL)',
        'unit_mandatory_hint' => 'الوحدة إجبارية لجميع الأنواع بما فيها الخدمات.',
    ],

    'messages' => [
        'min_one_unit' => 'يجب إدخال وحدة واحدة على الأقل للمنتج.',
    ],

];
