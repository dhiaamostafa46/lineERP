<?php return [
    'singular' => 'سلفة',
    'plural' => 'السلف',
    'fields' => [
        'id' => 'الرقم',
        'employee_id' => 'الموظف',
        'approver_id' => 'المعتمد',
        'payroll_id' => 'كشف الرواتب',
        'description' => 'الوصف',
        'reason' => 'السبب',
        'due_at' => 'تاريخ الاستحقاق',
        'approved_id' => 'تمت الموافقة بواسطة',
        'approved_at' => 'تاريخ الموافقة',
        'status' => 'الحالة',
        'amount' => 'المبلغ',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
        'attachment' => 'المرفق',
        'current_attachment' => 'المرفق الحالي',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'advance_details' => 'تفاصيل السلفة',
    ],

    'select_employee' => 'اختر الموظف',
    'view_attachment' => 'عرض المرفق',
    'action' => 'الإجراء',
    'approver' => 'المعتمد',

    // Installments
    'minimum_one_installment_required' => 'يجب أن يكون هناك قسط شهري واحد على الأقل',
    'invalid_month_format' => 'صيغة الشهر غير صحيحة: :month. الصيغة المطلوبة: YYYY-MM',
    'invalid_amount_for_month' => 'المبلغ غير صحيح للشهر: :month',
    'monthly_payments_total_mismatch' => 'مجموع الأقساط الشهرية لا يساوي مبلغ السلفة. المتوقع: :expected، الفعلي: :actual، الفرق: :difference',

    // Errors
    'error_creating_advance' => 'حدث خطأ أثناء إنشاء السلفة',
    'error_updating_advance' => 'حدث خطأ أثناء تحديث السلفة',
    'cannot_delete_paid_installment' => 'لا يمكن حذف قسط تم دفعه',
    'advance_not_found' => 'السلفة غير موجودة',

    // Success messages
    'advance_created_successfully' => 'تم إنشاء السلفة بنجاح',
    'advance_updated_successfully' => 'تم تحديث السلفة بنجاح',
    'advance_deleted_successfully' => 'تم حذف السلفة بنجاح',

    // Warnings
    'cannot_modify_paid_payments' => 'لا يمكن تعديل الأقساط المدفوعة',
    'some_payments_already_paid' => 'بعض الأقساط تم دفعها بالفعل ولن يتم تعديلها',

    // Display
    'total' => 'الإجمالي',
    'paid' => 'المدفوع',
    'pending' => 'المتبقي',
    'payment_progress' => 'تقدم السداد',
    'no_monthly_payments' => 'لا توجد أقساط شهرية',
    'advance_details' => 'تفاصيل السلفة',
    'installment_details' => 'تفاصيل الأقساط',
];
