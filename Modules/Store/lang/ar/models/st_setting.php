
<?php
return [
    'singular' => 'إعدادات المخزون',
    'plural'   => 'إعدادات المخزون',

    'fields' => [
        'org_id' => 'المؤسسة',
        'costing_method' => 'طريقة التكلفة',
        'allow_negative_stock' => 'السماح بالمخزون السالب',
        'auto_calculate_cost' => 'حساب التكلفة تلقائياً',
        'stock_valuation_enabled' => 'تمكين تقييم المخزون',
        'auto_serial_number' => 'إنشاء رقم تسلسلي تلقائي',
        'stock_transfer_prefix' => 'بادئة تحويل المخزون',
        'stocktake_prefix' => 'بادئة الجرد',
        'inventory_account_id' => 'حساب المخزون',
        'cogs_account_id' => 'حساب تكلفة البضاعة المباعة',
        'sales_account_id' => 'حساب المبيعات',
        'track_quantity' => 'تتبع الكمية',
        'track_batch' => 'تتبع الدفعة',
        'track_expiry' => 'تتبع تاريخ الانتهاء',
        'allow_backorders' => 'السماح بالطلبات المؤجلة',
        'lead_time_days' => 'مدة التوريد (أيام)',
        'min_stock' => 'أدنى حد للمخزون',
        'max_stock' => 'أقصى حد للمخزون',
        'reorder_point' => 'نقطة إعادة الطلب',
        'default_transfer_type' => 'نوع التحويل الافتراضي',
    ],
    'types' => [
        1 => 'تحويل مباشر',
        2 => 'تحويل غير مباشر',
    ],
];
