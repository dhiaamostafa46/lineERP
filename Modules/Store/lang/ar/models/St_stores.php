<?php

return [
    'singular' => 'المستودع',
    'plural' => 'المستودعات',

    'fields' => [
        'id' => 'المعرف',
        'name' => 'اسم المستودع',
        'branch_id' => 'الفرع',
        'manager_user_id' => 'المدير المسؤول',
        'org_id' => 'المؤسسة',
        'type' => 'نوع المستودع',
        'location' => 'الموقع',
        'status' => 'الحالة',

        'address' => 'العنوان',
        'is_active' => 'الحالة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التعديل',
    ],

    'statuses' => [
        0 => 'غير نشط',
        1 => 'نشط',
    ],

    'types' => [
        'main' => 'رئيسي',
        'secondary' => 'فرعي',
        'quarantine' => 'حجر صحي',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active' => 'badge badge-success',
        'main' => 'badge badge-primary',
        'secondary' => 'badge badge-warning',
        'quarantine' => 'badge badge-info',
    ],
];
