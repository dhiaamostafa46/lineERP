<?php

return [
    'singular' => 'منشور',
    'plural' => 'الأخبار والإعلانات',

    'fields' => [
        'id' => 'المعرف',
        'title' => 'العنوان',
        'body' => 'المحتوى',
        'type' => 'النوع',
        'status' => 'الحالة',
        'flage' => 'الى',
        'employee_id' => 'الموظفون',
        'department_id' => 'الأقسام',
        'branch_id' => 'الفروع',
        'published_at' => 'تاريخ النشر',
        'expires_at' => 'تاريخ الانتهاء',
        'is_pinned' => 'تثبيت في الأعلى',
        'image' => 'صورة الغلاف',
        'created_by' => 'أنشئ بواسطة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    'types' => [
        'news' => 'خبر',
        'announcement' => 'إعلان',
    ],

    'statuses' => [
        'draft' => 'مسودة',
        'published' => 'منشور',
    ],

    'flages' => [
        'all' => 'جميع الموظفين',
        'employees' => 'موظفون محددون',
        'department' => 'أقسام',
        'branches' => 'فروع',
    ],

    'feed_title' => 'الأخبار والإعلانات',
    'no_posts' => 'لا توجد منشورات للعرض.',
    'pinned' => 'مثبت',
    'read_more' => 'اقرأ المزيد',
    'show_less' => 'عرض أقل',
];
