<?php

return [
    'singular' => 'عطلة رسمية',
    'plural'   => 'العطلات الرسمية',

    'fields'   => [
        'id'           => 'المعرّف',
        'name'         => 'الاسم',
        'start_date'   => 'تاريخ البدء',
        'end_date'     => 'تاريخ الانتهاء',
        'description'  => 'الوصف',
        'rules'        => 'قواعد التكرار',
        'is_recurring' => 'متكررة',
        'status'       => 'الحالة',
        'type'         => 'النوع',
        'color'        => 'اللون',
        'created_at'   => 'تاريخ الإنشاء',
        'updated_at'   => 'تاريخ التحديث',
    ],

    'types' => [
        'holiday' => 'عطلة',
        'event'   => 'حدث',
    ],
];
