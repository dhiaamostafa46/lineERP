<?php

return [
    'singular' => 'الدفعة الشهرية',
    'plural'   => 'الدفعات الشهرية',
    'fields'   => [
        'id'                      => 'المعرف',
        'hr_advance_id'           => 'السلفة',
        'employee_id'             => 'الموظف',
        'approver_id'             => 'المعتمد',
        'payroll_id'              => 'كشف الرواتب',
        'due_at'                  => 'تاريخ الاستحقاق',
        'amount'                  => 'المبلغ',
        'type'                    => 'النوع',
        'status'                  => 'الحالة',
        'created_at'              => 'تاريخ الإنشاء',
        'updated_at'              => 'تاريخ التحديث',
        'total'                   => 'الإجمالي',
        'no_monthly_payments'     => 'لم يتم جدولة أي أقساط دفع شهرية لهذه السلفة بعد.',
        'employee_advance_details'=> 'تفاصيل سلفة الموظف',
        'total_advances'          => 'إجمالي السلف المعتمدة',
        'total_paid'              => 'إجمالي المدفوع',
        'total_pending'           => 'إجمالي المعلق',
        'remaining_balance'       => 'الرصيد المتبقي',
    ],
];
