<?php

return [

    /* =========================
     |  General
     ========================= */
    'singular' => 'قيد يومية',
    'plural'   => 'قيود اليومية',
    'balanced'   => 'متوازن',
    'unbalanced' => 'غير متوازن',

    /* =========================
     |  Fields (Journal Entry)
     ========================= */
    'fields' => [
        'id'             => 'المعرف',
        'entry_number'   => 'رقم القيد',
        'entry_date'     => 'تاريخ القيد',
        'reference'      => 'المرجع',
        'description'    => 'الوصف / البيان',
        'entry_type'     => 'نوع القيد',
        'source'         => 'مصدر القيد',
        'total_debit'    => 'إجمالي المدين',
        'total_credit'   => 'إجمالي الدائن',
        'status'         => 'الحالة',
        'balance'        => 'الرصيد',
        'is_locked'      => 'مقفلة',
        'locked_at'      => 'تاريخ القفل',
        'total'          => 'الإجمالي',
        'created_by'     => 'أنشئ بواسطة',
        'created_at'     => 'تاريخ الإنشاء',
        'posted_by'      => 'مرحل بواسطة',
        'posted_at'      => 'تاريخ الترحيل',
        'branch_id'      => 'الفرع',
        'attachment'     => 'المرفق',
        'notes'          => 'ملاحظات',
        'fiscal_year_id' => 'السنة المالية',
        'approved_by'    => 'تمت الموافقة من قبل',
        'account_id'     => 'الحساب',
        'cost_center_id' => 'مركز التكلفة',
        'original_voucher' => 'سند أصلي',
        'amount_in_words' => 'المبلغ كتابة',
        'accountant'     => 'المحاسب',
        'reviewed_by'    => 'المراجعة الداخلية',
        'account_code'   => 'كود الحساب',
    ],

    /* =========================
     |  Entry Sources
     ========================= */
    'sources' => [
        'manual'    => 'قيد عام / يدوي',
        'sales'     => 'المبيعات',
        'purchases' => 'المشتريات',
        'store'     => 'المخزون',
        'vehicles'  => 'المركبات',
        'drivers'   => 'السائقين',
        'hr'        => 'الموارد البشرية',
        'finance'   => 'السندات والمالية',
        'assets'    => 'الأصول الثابتة',
        'pos'       => 'نقاط البيع',
        'closing'   => 'إقفال محاسبي',
    ],

    /* =========================
     |  Entry Types
     ========================= */
    'types' => [
        'manual'     => 'يدوي',
        'auto'       => 'تلقائي',
        'opening'    => 'قيد افتتاحي',
        'closing'    => 'قيد إقفال',
        'depreciation' => 'إهلاك',
        'adjustment' => 'قيد تسوية',
    ],

    /* =========================
     |  Entry Statuses
     ========================= */
    'statuses' => [
        'draft'    => 'مسودة',
        'posted'   => 'مرحل',
        'reversed' => 'معكوس',
        'locked'   => 'مقفل',
        'pending'  => 'معلق',
    ],

    /* =========================
     |  Journal Entry Details
     ========================= */
    'details' => [
        'journal_entry_id' => 'قيد اليومية',
        'tree_account_id'  => 'الحساب',
        'cost_center_id'   => 'مركز التكلفة',
        'debit'            => 'مدين',
        'credit'           => 'دائن',
        'description'      => 'البيان',
        'is_locked'        => 'مقفلة',
        'locked_at'        => 'تاريخ القفل',
        'locked_by'        => 'قفل بواسطة',
    ],

    /* =========================
     |  Validations
     ========================= */
    'validations' => [
        'select_account_first' => 'الرجاء اختيار الحساب في الصف الأخير قبل إضافة صف جديد.',
        'min_two_rows'         => 'يجب أن يحتوي القيد اليومي على سطرين على الأقل.',
        'account_required_all_rows' => 'حقل الحساب مطلوب في جميع السطور.',
        'account_not_found'    => 'الحساب المحدد غير موجود.',
        'cost_center_not_found' => 'مركز التكلفة المحدد غير موجود.',
        'debit_numeric'        => 'يجب أن يكون المبلغ المدين رقماً.',
        'credit_numeric'       => 'يجب أن يكون المبلغ الدائن رقماً.',
        'amount_required'      => 'يجب إدخال قيمة في المدين أو الدائن.',
        'unbalanced_detailed'  => 'القيد غير متوازن. إجمالي المدين: %s، إجمالي الدائن: %s',
        'zero_total_error'     => 'لا يمكن حفظ قيد بإجمالي صفر.',
        'cost_center_required_for_account' => 'الحساب (%s) يتطلب تحديد مركز تكلفة.',
    ],

    'messages' => [
        'min_rows'       => 'الحد الأدنى سطرين',
        'alert'          => 'تنبيه',
        'min_rows_alert' => 'يجب أن يحتوي القيد على سطرين على الأقل',
        'ok'             => 'حسناً',
        'select_account' => 'اختر حساب',
        'optional'       => 'اختياري',
        'searching'      => 'جاري البحث...',
        'no_results'     => 'لا توجد نتائج',
        'loading_more'   => 'جاري تحميل المزيد...',
        'balanced_error' => 'القيد غير متوازن، يجب أن يتساوى إجمالي المدين مع الدائن.',
        'details_required' => 'يجب إضافة سطرين على الأقل في تفاصيل القيد.',
        'cost_center_required' => 'يحتاج مركز تكلفة',
        'cost_center_missing'  => 'يرجى تحديد مركز تكلفة للحسابات التالية: ',
        'row'                  => 'السطر',
        'account_required_for_amount' => 'يجب اختيار حساب عند إدخال مبلغ',
        'no_both_debit_credit' => 'لا يمكن إدخال مدين ودائن في نفس السطر',
        'debit_exceeds'        => 'مدين أكبر',
        'credit_exceeds'       => 'دائن أكبر',
        'debit_extra'          => 'مدين زائد',
        'credit_extra'         => 'دائن زائد',
        'no_amounts_entered'   => 'لم تُدخل أي مبالغ',
        'difference'           => 'الفرق',
        'basic_info'           => 'المعلومات الأساسية',
    ],

    'import' => [
        'title'             => 'استيراد قيود محاسبية',
        'page_heading'      => 'استيراد قيود محاسبية من ملف Excel',
        'breadcrumb'        => 'استيراد',
        'download_template' => 'تحميل نموذج Excel',
        'upload_file'       => 'رفع ملف البيانات',
        'important_notes'   => 'ملاحظات هامة:',
        'note_1'            => 'يرجى التأكد من أن الملف يتبع النموذج المعتمد.',
        'note_2'            => 'يجب أن تكون القيود متوازنة (إجمالي المدين = إجمالي الدائن لكل قيد).',
        'note_3'            => 'يجب أن تكون أكواد الحسابات مطابقة لما هو موجود في دليل الحسابات.',
        'note_4'            => 'في حال وجود أخطاء، سيتم استيراد القيود الصحيحة فقط، وسيتم تزويدك بملف يحتوي على القيود الخاطئة مع سبب الخطأ.',
        'choose_file'       => 'اختر ملف Excel (.xlsx, .xls, .csv)',
        'cancel'            => 'إلغاء',
        'start_import'      => 'بدء الاستيراد',
    ],

];
