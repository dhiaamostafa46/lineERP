<?php

return [
    'singular' => 'الإشعار',
    'plural' => 'الإشعارات',
    'alerts' => 'التنبيهات',
    'reports' => 'التقارير',
    'total' => 'الإجمالي',
    'unread' => 'غير مقروء',
    'all' => 'الكل',
    'edit' => 'تعديل',
    'view_all' => 'عرض الكل',
    'no_notifications' => 'لا توجد إشعارات',
    'no_unread_notifications' => 'لا توجد إشعارات غير مقروءة',
    'all_caught_up' => 'لقد قرأت جميع الإشعارات',
    'mark_read' => 'تحديد كمقروء',
    'mark_all_read' => 'تحديد الكل كمقروء',
    'all_marked_as_read' => 'تم تحديد كافة الإشعارات كمقروءة بنجاح',
    'notification_marked_read' => 'تم تحديد الإشعار كمقروء',
    'all_notifications_marked_read' => 'تم تحديد جميع الإشعارات كمقروءة',
    'notification_deleted' => 'تم حذف الإشعار',
    'read_notifications_cleared' => 'تم حذف جميع الإشعارات المقروءة',
    'notification_not_found' => 'الإشعار غير موجود',
    'error_occurred' => 'حدث خطأ، يرجى المحاولة مرة أخرى',

    'priority' => [
        'low' => 'منخفض',
        'normal' => 'عادي',
        'high' => 'مرتفع',
        'urgent' => 'عاجل 🚨',
    ],

    'modules' => [
        'hr' => 'الموارد البشرية',
        'vehicles' => 'المركبات والصيانة',
        'invoices' => 'الفواتير والمبيعات',
        'store' => 'المخازن والمتاجر',
        'pos' => 'نقاط البيع',
        'accounting' => 'المحاسبة والمالية',
        'assets' => 'الأصول الثابتة',
        'system' => 'النظام العام',
    ],

    'fields' => [
        'id' => '#',
        'org_id' => 'معرف المنشأة',
        'notification_type' => 'نوع الإشعار',
        'notifiable_id' => 'المستفيد / الموظف',
        'notifiable_type' => 'نوع المتلقي',
        'channel' => 'القناة',
        'status' => 'الحالة',
        'priority' => 'الأولوية',
        'fingerprint' => 'البصمة',
        'read_at' => 'وقت القراءة',
        'extra' => 'بيانات التنبيه',
        'anonymous_notifiable_properties' => 'خصائص المتلقي المجهول',
        'confirmed_at' => 'وقت التأكيد',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    'info' => [
        'date' => 'التاريخ',
        'body' => 'ملاحظات',
        'Advance' => 'مبلغ السلفة',
        'Holiday' => 'الاجازة',
        'Justification' => 'تسوية',
        'expiry_date' => 'تاريخ الانتهاء',
        'license_expiry_date' => 'انتهاء الاستمارة',
        'license_expiration_date' => 'انتهاء الرخصة',
        'current_stock' => 'المخزون الحالي',
        'min_quantity' => 'حد الأمان الأدنى',
        'amount' => 'المبلغ',
    ],

    'status' => [
        'pending' => 'قيد الانتظار',
        'read' => 'تمت القراءة',
        'confirmed' => 'تم التأكيد',
        'cancelled' => 'ملغي',
    ],

    'channel' => [
        'database' => 'النظام',
        'email' => 'البريد الإلكتروني',
        'sms' => 'رسالة SMS',
        'mobile_push' => 'تطبيق الجوال',
    ],

    'type' => [
        // HR
        'iqama_expiry' => 'انتهاء الهوية / الإقامة',
        'insurance_expiry' => 'انتهاء التأمين الطبي',
        'passport_expiry' => 'انتهاء جواز السفر',
        'leave_request' => 'طلب إجازة',
        'advance_request' => 'طلب سلفة',
        'settlement_request' => 'طلب تسوية',
        'request_status' => 'تحديث حالة الطلب',

        // Vehicles
        'vehicle_license_expiry' => 'انتهاء رخصة المركبة',
        'driver_license_expiry' => 'انتهاء رخصة القيادة',
        'maintenance_request' => 'طلب صيانة مركبة',
        'traffic_violation' => 'مخالفة مرورية',

        // Invoices
        'quotation_expired' => 'انتهاء عرض السعر',
        'invoice_due' => 'فاتورة مستحقة السداد',
        'purchase_return_pending' => 'مرتجع شراء معلق',

        // Store
        'low_stock' => 'انخفاض رصيد المخزون',
        'stock_transfer_pending' => 'طلب نقل مخزني معلق',

        // POS
        'pos_session_open' => 'جلسة نقطة بيع مفتوحة',
        'pos_cash_discrepancy' => 'عجز / تباين صندوق POS',

        // Accounting
        'unposted_journal_entry' => 'قيد يومية غير مرحل',

        // Assets
        'asset_maintenance' => 'صيانة أصل ثابت',
        'asset_depreciation' => 'إهلاك أصل ثابت',

        // System
        'system_alert' => 'تنبيه نظام عام',
    ],
];
