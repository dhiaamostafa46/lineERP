<?php

return [

    /* =========================
     |  عام
     ========================= */
    'singular' => 'تقرير',
    'plural'   => 'التقارير',

    /* =========================
     |  الحقول العامة
     ========================= */
    'fields' => [
        'id'            => 'المعرف',
        'report_type'   => 'نوع التقرير',
        'date_from'     => 'من تاريخ',
        'date_to'       => 'إلى تاريخ',
        'account_id'    => 'الحساب',
        'account_from'  => 'من حساب',
        'account_to'    => 'إلى حساب',
        'cost_center_id'=> 'مركز التكلفة',
        'branch_id'     => 'الفرع',
        'currency'      => 'العملة',
        'posted_only'   => 'قيود مرحلة فقط',
        'created_at'    => 'تاريخ الإنشاء',
        'created_by'    => 'أنشئ بواسطة',
    ],

    /* =========================
     |  أنواع التقارير
     ========================= */
    'types' => [
        'all_reports'                   => 'جميع التقارير',
        'account_statement'             => 'كشف حساب',
        'account_summary'               => 'ملخص حساب',
        'income_statement'              => 'قائمة الدخل',
        'trial_balance'                 => 'ميزان المراجعة',
        'general_ledger'                => 'دفتر الأستاذ',
        'journal_entries'               => 'قيود اليومية',
        'balance_sheet'                 => 'الميزانية العمومية',
        'cash_flow_statement_indirect'  => 'قائمة التدفقات النقدية',
        'cash_flow_statement_direct'    => 'قائمة التدفقات النقدية',
        'tax'                            => 'تقرير الضريبة',
        'cost_center'                   => 'تقرير مركز التكلفة',
        'assets'                        => 'تقرير الأصول',
        'sales'                         => 'تقرير المبيعات',
        'purchases'                     => 'تقرير المشتريات',
        'sales_tax_report'              => 'تقرير ضريبة المبيعات',
        'purchases_tax_report'          => 'تقرير ضريبة المشتريات',
    ],

    /* =========================
     |  أسماء التقارير
     ========================= */
    'reports' => [
        'account_statement'                  => 'كشف حساب',
        'general_ledger'                     => 'دفتر الأستاذ',
        'trial_balance_totals'               => 'ميزان مراجعة المجاميع',
        'trial_balance_balances'             => 'ميزان مراجعة',
        'journal_entries'                    => 'بنود القيود (تفاصيل)',
        'account_total_balance'              => 'إجمالي رصيد الحساب',
        'income_statement'                   => 'قائمة الدخل',
        'balance_sheet'                      => 'الميزانية العمومية',
        'cost_centers_report'                => 'تقرير مراكز التكلفة',
        'cost_centers_calculation_report'    => 'تقرير مراكز التكلفة – حسابات',
        'assets'                             => 'تقرير الأصول',
    ],

    /* =========================
     |  أعمدة الجداول
     ========================= */

    'columns' => [

        // بيانات أساسية
        'id'                 => 'رقم',
        'date'              => 'التاريخ',
        'entry_number'      => 'رقم القيد',
        'reference'         => 'المرجع',
        'description'       => 'البيان',
        'account_code'      => 'رمز الحساب',
        'account_name'      => 'اسم الحساب',
        'account_type'      => 'نوع الحساب',
        'cost_center'       => 'مركز التكلفة',
         'cost_center_name'  => 'اسم مركز التكلفة',
        'cost_center_code'  => 'كود مركز التكلفة',
        'account'           => 'الحساب',

        // الحركات
        'debit'             => 'مدين',
        'credit'            => 'دائن',

        // الأرصدة
        'opening_balance'   => 'الرصيد الافتتاحي',
        'period_balance'    => 'رصيد خلال الفترة',
        'closing_balance'   => 'الرصيد الختامي',
        'balance'           => 'الرصيد',
        'running_balance'   => 'الرصيد التراكمي',

        // المجاميع
        'total_debit'       => 'إجمالي المدين',
        'total_credit'      => 'إجمالي الدائن',
        'total'             => 'الإجمالي',

        // إضافات
        'previous_balance'  => 'الرصيد السابق',
        'current_balance'   => 'الرصيد الحالي',
        'net_balance'       => 'صافي الرصيد',
        'movement'          => 'الحركة',
        'generated_at'      => 'تم الإنشاء في',
       'unposted_pl'     => 'أرباح وخسائر غير مرحلة',



        // الفترات
        'from_date'         => 'من تاريخ',
        'to_date'           => 'إلى تاريخ',
        'period'            => 'الفترة',
        'fiscal_year'       => 'السنة المالية',
    ],

    /* =========================
     |  الفلاتر
     ========================= */
    'filters' => [
        'date_range'        => 'الفترة',
        'date_from'         => 'من تاريخ',
        'date_to'           => 'إلى تاريخ',
        'account'           => 'الحساب',
        'select_account'    => 'اختر الحساب',
        'cost_center'       => 'مركز التكلفة',
        'all_cost_centers'  => 'جميع مراكز التكلفة',
        'all_branch'        => 'جميع  الفروع',
        'branch'            => 'الفرع',
        'currency'          => 'العملة',
        'branchId'          =>'الفرع',
        'entry_type'        => 'نوع القيد',
        'all_types'         => 'كل الأنواع',
        'entry_status'      => 'حالة القيد',
        'fiscalYear'        => 'السنة المالية',
        'current_fiscal_year'=> 'السنة المالية الحالية',
    ],

    /* =========================
     |  حالات القيود
     ========================= */
    'statuses' => [
        'all'       => 'الكل',
        'draft'     => 'مسودة',
        'posted'    => 'مرحل',
        'reversed'  => 'ملغي',
    ],

    /* =========================
     |  الإجراءات
     ========================= */
    'actions' => [
        'generate'  => 'إنشاء التقرير',
        'preview'   => 'معاينة',
        'print'     => 'طباعة',
        'export'    => 'تصدير',
    ],

    /* =========================
     |  الرسائل
     ========================= */
    'messages' => [
        'no_data'                   => 'لا توجد بيانات للعرض حسب المعايير المحددة.',
        'loading'                   => 'جارٍ إنشاء التقرير، يرجى الانتظار...',
        'invalid_date'              => 'نطاق التاريخ المحدد غير صحيح.',
        'select_account'            => 'يرجى اختيار حساب',
        'select_account_description'=> 'قم باختيار حساب من القائمة أعلاه لعرض كشف الحساب الخاص به',
        'no_transactions'           => 'لا توجد حركات للحساب المحدد خلال الفترة المختارة',
    ],

    /* =========================
     |  التسميات
     ========================= */
    'labels' => [
        'new' => 'جديد!',
    ],



    'totals' => [
        'total_revenue' => 'إجمالي الإيرادات',
        'total_expense' => 'إجمالي المصروفات',
        'net_income'    => 'صافي الدخل',
        'total_assets'  => 'إجمالي الأصول',
        'total_liabilities' => 'إجمالي الخصوم',
        'total_equity'  => 'إجمالي حقوق الملكية',
        'total_liabilities_and_equity' => 'إجمالي الخصوم وحقوق الملكية',
        'difference'    => 'الفارق',
    ],

    'cash_flow' => [
        'operating_activities' => 'الأنشطة التشغيلية',
        'investing_activities' => 'الأنشطة الاستثمارية',
        'financing_activities' => 'الأنشطة التمويلية',
        'net_income'           => 'صافي الدخل',
        'depreciation'         => 'إهلاك الأصول الثابتة',
        'receivables'          => 'التغير في المدينين',
        'inventory'            => 'التغير في المخزون',
        'other_assets'         => 'التغير في الأصول المتداولة الأخرى',
        'payables'             => 'التغير في الدائنين',
        'other_liabilities'    => 'التغير في الالتزامات المتداولة الأخرى',
        'fixed_assets'         => 'التغير في الأصول الثابتة',
        'loans'                => 'التغير في القروض',
        'capital'              => 'التغير في رأس المال',
        'net_cash_operating'   => 'صافي التدفقات النقدية من الأنشطة التشغيلية',
        'net_cash_investing'   => 'صافي التدفقات النقدية من الأنشطة الاستثمارية',
        'net_cash_financing'   => 'صافي التدفقات النقدية من الأنشطة التمويلية',
        'net_change_in_cash'   => 'صافي التغير في النقدية',
        'beginning_cash'       => 'النقدية في بداية الفترة',
        'ending_cash'          => 'النقدية في نهاية الفترة',
    ],

];
