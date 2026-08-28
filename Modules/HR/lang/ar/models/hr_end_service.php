

<?php

return [
    'singular' => 'إنهاء التعاقد',
    'plural' => 'إنهاء التعاقد',
    'fields' => [
        'id' => 'Id',
        'name' => 'الإسم',
        'employee' => 'الموظف',
        'end_date' => 'تاريخ الانتهاء',
        'status' => 'الحالة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'description' => 'الوصف',
        'reason' => 'السبب',
        'reward_amount' => ' إستحقاقات نهاية الخدمة',
        'approved' => 'الموافقة',
        'duration' => 'مدة',
        'add' => 'إنهاء تعاقد موظف',
        'total_penalties' => 'إجمالي العقوبات المستحقة',
        'total_advances' => 'إجمالي السلف غير المسددة',
        'total_deducts' => 'الاستقطاعات غير المسددة',
        'net_reward' => 'صافي مكافأة نهاية الخدمة (التصفية)',
    ],

    'reasonList' => [
        'termination_duration_end' => 'انتهاء مدة العقد',
        'termination_unlawful_termination' => 'فسخ العقد من قبل صاحب العمل/ العامل لسبب غير مشروع',
        'termination_article_80' => 'فسخ العقد بموجب المادة (80)',
        'termination_force_majeure' => 'انتهاء العقد بسبب القوة القاهرة',
        'termination_woman_post_delivery' => 'إنهاء المرأة العاملة العقد خلال ثلاثة أشهر من الوضع',
        'termination_woman_post_marriage' => 'إنهاء المرأة العاملة العقد خلال ستة أشهر من عقد القران',
        'termination_article_81' => 'فسخ العقد بموجب المادة (81)',
        'resignation' => 'الاستقالة',
        'termination_agreement' => 'الاتفاق على إنهاءه',
        'termination_worker_disability' => 'عجز العامل',
        'termination_employer_death' => 'وفاة صاحب العمل',
        'termination_worker_death' => 'وفاة العامل',
        'termination_business_transfer' => 'انتقال المنشآت الفردية إلى مالك جديد',
        'termination_retirement' => 'بلوغ سن التقاعد',
        'termination_notice_article_75' => 'إشعار إنهاء خدمات وفق المادة (75)',
        'resignation_trial_period' => 'فسخ خلال فترة التجربة',
    ],

    'employee_messages' => [
        'has_penalties' => 'الموظف لديه عقوبات مرتبطة به.',
        'has_advances' => 'الموظف لديه  سلف مرتبطة به.',
        'has_rewards' => 'الموظف لديه  مكافآت مرتبطة به.',
        'has_commitments' => 'الموظف لديه  عهده مرتبطة به.',
        'has_tasks' => 'الموظف لديه  مهام مرتبطة به.',
        'employee_data_unavailable' => 'بيانات الموظف أو تاريخ التعيين غير متوفرة',
        'cannot_create_has_custodies' => 'لا يمكن إنشاء إنهاء تعاقد: الموظف لديه أصول/عهد لم يتم تسليمها.',
    ],

];
