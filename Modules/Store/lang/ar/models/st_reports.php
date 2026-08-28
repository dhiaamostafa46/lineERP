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
        'cash_flow_statement_indirect'  => 'قائمة التدفقات النقدية (غير مباشرة)',
        'cash_flow_statement_direct'    => 'قائمة التدفقات النقدية (مباشرة)',
        'tax'                            => 'تقرير الضريبة',
        'cost_center'                   => 'تقرير مركز التكلفة',
        'assets'                        => 'تقرير الأصول',
        'sales'                         => 'تقرير المبيعات',
        'purchases'                     => 'تقرير المشتريات',
        'sales_tax_report'              => 'تقرير ضريبة المبيعات',
        'purchases_tax_report'          => 'تقرير ضريبة المشتريات',
        // تقارير المخزون
        'stock_movement'                => 'حركة المخزون',
        'stock_balance'                 => 'رصيد المخزون',
        'inventory_valuation'           => 'تقييم المخزون',
        'low_stock'                     => 'المخزون المنخفض',
        'inventory_count'               => 'جرد المخزون',
        'pending_stock'                 => 'المخزون المعلق / المحجوز',
    ],

    /* =========================
     |  أسماء التقارير
     ========================= */
    'reports' => [
        'account_statement'                  => 'كشف حساب',
        'general_ledger'                     => 'دفتر الأستاذ',
        'trial_balance_totals'               => 'ميزان مراجعة المجاميع',
        'trial_balance_balances'             => 'ميزان مراجعة',
        'account_total_balance'              => 'إجمالي رصيد الحساب',
        'income_statement'                   => 'قائمة الدخل',
        'balance_sheet'                      => 'الميزانية العمومية',
        'cost_centers_report'                => 'تقرير مراكز التكلفة',
        'cost_centers_calculation_report'    => 'تقرير مراكز التكلفة – حسابات',
    ],

    /* =========================
     |  أعمدة الجداول
     ========================= */

    'columns' => [

        // بيانات أساسية
        'id'                   => 'رقم',
        'date'                 => 'التاريخ',
        'entry_number'         => 'رقم القيد',
        'reference'            => 'المرجع',
        'description'          => 'البيان',
        'account_code'         => 'رمز الحساب',
        'account_name'         => 'اسم الحساب',
        'account_type'         => 'نوع الحساب',
        'cost_center'          => 'مركز التكلفة',
        'cost_center_name'     => 'اسم مركز التكلفة',
        'cost_center_code'     => 'كود مركز التكلفة',
        'account'              => 'الحساب',

        // الحركات المحاسبية
        'debit'                => 'مدين',
        'credit'               => 'دائن',

        // الأرصدة
        'opening_balance'      => 'الرصيد الافتتاحي',
        'period_balance'       => 'رصيد خلال الفترة',
        'closing_balance'      => 'الرصيد الختامي',
        'balance'              => 'الرصيد',
        'running_balance'      => 'الرصيد التراكمي',

        // المجاميع
        'total_debit'          => 'إجمالي المدين',
        'total_credit'         => 'إجمالي الدائن',
        'total'                => 'الإجمالي',

        // إضافات محاسبية
        'previous_balance'     => 'الرصيد السابق',
        'current_balance'      => 'الرصيد الحالي',
        'net_balance'          => 'صافي الرصيد',
        'movement'             => 'الحركة',
        'generated_at'         => 'تم الإنشاء في',
        'unposted_pl'          => 'أرباح وخسائر غير مرحلة',

        // الفترات
        'from_date'            => 'من تاريخ',
        'to_date'              => 'إلى تاريخ',
        'period'               => 'الفترة',
        'fiscal_year'          => 'السنة المالية',

        // أعمدة تقارير المخزون
        'movement_number'      => 'رقم الحركة',
        'movement_type'        => 'نوع الحركة',
        'product'              => 'المنتج',
        'category'             => 'التصنيف',
        'store'                => 'المخزن',
        'quantity'             => 'الكمية',
        'unit'                 => 'الوحدة',
        'unit_cost'            => 'تكلفة الوحدة',
        'total_cost'           => 'الإجمالي',
        'total_value'          => 'القيمة الإجمالية',
        'total_quantity'       => 'الكمية الإجمالية',
        'current_quantity'     => 'الكمية الحالية',
        'reserved_quantity'    => 'الكمية المحجوزة',
        'available_quantity'   => 'الكمية المتاحة',
        'average_cost'         => 'متوسط التكلفة',
        'min_quantity'         => 'الحد الأدنى',
        'reorder_point'        => 'نقطة إعادة الطلب',
        'shortage'             => 'العجز',
        'pending_quantity'     => 'الكمية المعلقة',
        'from_store'           => 'من مخزن',
        'to_store'             => 'إلى مخزن',
        'status'               => 'الحالة',
        'opening_quantity'     => 'الرصيد الافتتاحي',
        'qty_in'               => 'الوارد',
        'qty_out'              => 'الصادر',
        'last_movement'        => 'آخر حركة',
        'book_quantity'        => 'الكمية الدفترية',
        'actual_quantity'      => 'الكمية الفعلية',
        'difference'           => 'الفرق',
    ],

    /* =========================
     |  الفلاتر
     ========================= */
    'filters' => [
        'date_range'          => 'الفترة',
        'date_from'           => 'من تاريخ',
        'date_to'             => 'إلى تاريخ',
        'account'             => 'الحساب',
        'select_account'      => 'اختر الحساب',
        'cost_center'         => 'مركز التكلفة',
        'all_cost_centers'    => 'جميع مراكز التكلفة',
        'all_branch'          => 'جميع الفروع',
        'branch'              => 'الفرع',
        'currency'            => 'العملة',
        'branchId'            => 'الفرع',
        'entry_status'        => 'حالة القيد',
        'fiscalYear'          => 'السنة المالية',
        'current_fiscal_year' => 'السنة المالية الحالية',
        // فلاتر المخزون
        'store'               => 'المخزن',
        'all_stores'          => 'جميع المخازن',
        'product'             => 'المنتج',
        'all_products'        => 'جميع المنتجات',
        'movement_type'       => 'نوع الحركة',
        'all_types'           => 'جميع الأنواع',
        'from_date'           => 'من تاريخ',
        'to_date'             => 'إلى تاريخ',
        'category'            => 'التصنيف',
        'all_categories'      => 'جميع التصنيفات',
        'as_of_date'          => 'حتى تاريخ',
        'product_name'        => 'اسم المنتج',
        'search_by_product_name' => 'اسم المنتج بالكامل أو جزء منه',
        'product_display'     => 'عرض المنتجات',
        'all_with_zero'       => 'الأصناف مع الصفرية',
        'without_zero'        => 'الأصناف بدون الصفرية',
        'zero_only'           => 'الأصناف الصفرية فقط',
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
        'no_data'                    => 'لا توجد بيانات للعرض حسب المعايير المحددة.',
        'loading'                    => 'جارٍ إنشاء التقرير، يرجى الانتظار...',
        'invalid_date'               => 'نطاق التاريخ المحدد غير صحيح.',
        'select_account'             => 'يرجى اختيار حساب',
        'select_account_description' => 'قم باختيار حساب من القائمة أعلاه لعرض كشف الحساب الخاص به',
        'no_transactions'            => 'لا توجد حركات للحساب المحدد خلال الفترة المختارة',
        // رسائل تقارير المخزون
        'select_filters'             => 'يرجى تحديد الفلاتر وضغط بحث لعرض التقرير',
        'low_stock_warning'          => 'تحذير: يوجد :count صنف منخفض المخزون يحتاج إلى إعادة طلب',
        'no_low_stock'               => 'جميع الأصناف ضمن الحد المسموح به',
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
    ],

    /* =========================
     |  حالات المخزون
     ========================= */
    'status' => [
        'normal' => 'طبيعي',
        'low'    => 'منخفض',
        'empty'  => 'نافد',
    ],

];
