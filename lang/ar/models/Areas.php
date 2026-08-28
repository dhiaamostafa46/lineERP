<?php

return [
    'singular' => 'منطقة',
    'plural' => 'المناطق',
    'fields' => [
        'id' => '#',
        'code' => 'الرمز',
        'name' => 'الاسم',
        'status' => 'الحالة',
        'cities_count' => 'عدد المدن',
        'created_at' => 'تاريخ الإنشاء',
    ],
    'messages' => [
        'cannot_delete_has_cities' => 'لا يمكن حذف المنطقة لوجود مدن مرتبطة بها.',
    ],
];
