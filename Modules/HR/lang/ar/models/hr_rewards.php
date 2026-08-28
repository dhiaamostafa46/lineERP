<?php

return [
    'singular' => 'مكافأة',
    'plural'   => 'المكافأات',
    'fields'   => [
        'id'                    => '#',
        'employee_id'           => 'الموظف',
        'type'                  => 'نوع المكافأة',
        'amount'                => 'المبلغ',
        'status'                => 'الحالة',
        'over_time'             => 'ساعات اجازة',
        'days_off'              => 'ايام اجازة',
        'start_at'              => 'يبدء في',
        'end_at'                => 'ينتهي في',
        'created_at'            => 'تأريخ الانشاء',
        'updated_at'            => 'تأريخ التعديل',
        'overtime'              => 'ساعات العمل الاضافية',
        'static_amount'         => 'مبلغ ثابت',
        'compensatory_holidays' => 'اجازة تعويضية',
        'in_kind_reward'        => 'مكافأة عينية',
        'note'                  => 'ملاحظات',
        'value'                 => 'القيمة',
        'created'                 => 'تم إنشاء المكافأة',
    ],
    'alerts'=>[
          'created'                 => 'تم إنشاء المكافأة',

    ],

];
