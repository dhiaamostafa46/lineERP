<?php

return [
    'singular' => 'تطبيق',
    'plural' => 'التطبيقات والربط البرمجي',
    'title' => 'التطبيقات',
    'hub_hub_title' => 'مركز الربط والتكامل المباشر (Evix Hub)',
    'hub_sync_btn' => 'تحديث من Hub',
    'hub_sync_desc' => 'جلب أحدث المنصات، المخططات، والميزات تلقائياً من hub.evixdev.com',
    'sync_success' => 'تم تحديث قائمة التطبيقات من Hub بنجاح (:count تطبيق متوفر).',

    // Stats
    'stats_total' => 'إجمالي المنصات',
    'stats_active' => 'التطبيقات المفعلة',
    'stats_connected' => 'اتصالات ناجحة',
    'stats_need_config' => 'غير مفعلة',

    // Categories
    'categories' => [
        'all' => 'جميع التطبيقات',
        'ecommerce' => 'المتاجر والتجارة الإلكترونية',
        'payment_gateway' => 'بوابات الدفع الإلكتروني',
        'payments' => 'بوابات الدفع والتحصيل',
        'shipping' => 'الشحن وخدمات التوصيل',
        'delivery' => 'الشحن وخدمات التوصيل',
        'government' => 'المنصات الحكومية والامتثال',
        'messaging' => 'الرسائل والإشعارات',
        'accounting' => 'المحاسبة والفوترة',
        'accounting_tax' => 'المحاسبة والفوترة الإلكترونية (ZATCA)',
        'hr' => 'الموارد البشرية',
        'fleet_tracking' => 'تتبع الأساطيل',
        'internal_engine' => 'المحركات والأنظمة الداخلية',
        'other' => 'تكاملات أخرى',
    ],

    // Statuses
    'statuses' => [
        'all' => 'جميع الحالات',
        'active' => 'مفعل',
        'inactive' => 'غير مفعل',
        'connected' => 'متصل',
        'disconnected' => 'غير متصل',
    ],

    // Actions & Fields
    'fields' => [
        'app_code' => 'كود التطبيق',
        'name' => 'اسم المنصة',
        'category' => 'التصنيف',
        'status' => 'الحالة',
        'environment' => 'بيئة التشغيل',
        'webhook_url' => 'رابط الويب هوك',
    ],

    'configure' => 'إعدادات الربط',
    'activate' => 'تفعيل وربط المنصة',
    'deactivate' => 'تعطيل التطبيق',
    'documentation' => 'دليل التوثيق الرسمي',
    'copy_webhook' => 'نسخ رابط الويب هوك',
    'copied' => 'تم النسخ!',
    'live_mode' => 'الإنتاج الفعلي (Live / Production)',
    'sandbox_mode' => 'بيئة الاختبار التجريبية (Sandbox / Test)',
    'credentials_section' => 'مفاتيح وبيانات المصادقة (API Credentials)',
    'webhook_instructions' => 'قم بإضافة هذا الرابط داخل إعدادات Webhooks في المنصة لاستقبال الأحداث وتحديث العمليات تلقائياً.',
    'last_connected' => 'آخر اتصال: :time',
    'never_connected' => 'لم يتم الاتصال بعد',
];
