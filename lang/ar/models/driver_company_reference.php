<?php

return [
    'singular' => 'مرجع السائق للشركة',
    'plural' => 'مراجع السائقين للشركات',
    'fields' => [
        'driver_id' => 'السائق',
        'company_id' => 'الشركة',
        'ref_no' => 'رقم المرجع',
        'status' => 'الحالة',
        'started_at' => 'تاريخ البداية',
        'ended_at' => 'تاريخ النهاية',
        'created_by' => 'أنشئ بواسطة',
        'updated_by' => 'آخر تحديث بواسطة',
    ],
    'statuses' => [
        'active' => 'نشط',
        'completed' => 'مكتمل',
        'suspended' => 'موقوف',
    ],
    'messages' => [
        'no_active_ref' => 'لا يوجد رقم مرجع نشط لهذا السائق.',
    ],
];
