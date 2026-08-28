<?php

return [
    'singular' => 'سند',
    'plural'   => 'السندات',

    'fields' => [
        'id'                 => 'المعرف',
        'bond_type'          => 'نوع السند',
        'voucher_number'     => 'رقم السند',
        'reference_number'   => 'الرقم المرجعي',
        'date'               => 'التاريخ',
        'amount'             => 'المبلغ',
        'fund_account_id'    => 'حساب الصندوق / البنك',
        'contact_account_id' => 'حساب الطرف',
        'cost_center_id'     => 'مركز التكلفة',
        'branch_id'          => 'الفرع',
        'description'        => 'الوصف / الملاحظات',
        'status'             => 'الحالة',
        'attachment'         => 'مرفق',
        'journal_entry_id'   => 'رقم القيد المحاسبي',
        'created_by'         => 'تم الإنشاء بواسطة',
        'created_at'         => 'تاريخ الإنشاء',
        'updated_at'         => 'تاريخ التحديث',
        'pay_to_receive_from' => 'إصرفوا بموجبه لـ / استلمنا من',
        'amount_text'        => 'مبلغ وقدره',
        'sar_only'           => 'ريال سعودي لا غير',
        'for_purpose'        => 'وذلك مقابل',
        'accountant'         => 'المحاسب',
        'receiver'           => 'المستلم',
        'manager'            => 'المدير العام',
    ],

    'types' => [
        'payment' => 'سند صرف',
        'receipt' => 'سند قبض',
    ],

    'sections'=>[
        'account_details'=> 'تفاصيل الحسابات',
        'other_info' =>'أخرى',
    ],

    'statuses' => [
        'draft' => 'مسودة',
        'approved' => 'معتمدة',
    ],
];
