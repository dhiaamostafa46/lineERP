<?php

return [
    // 'singular' => 'قسم المنتج',
    // 'plural'   => 'أقسام المنتجات',

     'singular' => ' التصنيف',
    'plural'   => 'التصنيفات',

    'fields'   => [
        'id'          => 'المعرف',
        'name'        => 'الاسم',
        'org_id'      => 'المؤسسة',
        'user_id'     => 'المستخدم',
        'img'         => 'الصورة',
        'sort'        => 'الترتيب',
        'status'      => 'الحالة',
        'is_virtual'  => 'فئة',
        'parent_id'   => 'التصنيف الأب',
        'type'        => 'استخدامها فى المبيعات',
        'created_at'  => 'تاريخ الإنشاء',
        'updated_at'  => 'تاريخ التعديل',
    ],

    'statuses' => [
        0 => 'غير نشط',
        1 => 'نشط',
    ],

    'virtual_statuses' => [
        0 => 'فئة حقيقية',
        1 => 'فئة افتراضية',
    ],
    'type_statuses' => [
        'Visible' => 'تعرض',
        'Hidden'  => 'إحفاء',
    ],

    

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
        'real'     => 'badge badge-primary',
        'virtual'  => 'badge badge-warning',
    ],
];
