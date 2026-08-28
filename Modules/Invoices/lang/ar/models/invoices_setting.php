<?php

return [
    'singular' => 'الإعدادات',
    'plural' => 'الإعدادات',

    'fields' => [
        'id' => 'المعرف',

        'sales_prefix' => 'بادئة فواتير المبيعات',
        'sales_next_number' => 'رقم المبيعات التالي',
        'sales_auto_post' => 'ترحيل المبيعات تلقائياً',
        'sales_terms' => 'شروط المبيعات',

        'purchase_prefix' => 'بادئة فواتير المشتريات',
        'purchase_next_number' => 'رقم المشتريات التالي',
        'purchase_auto_post' => 'ترحيل المشتريات تلقائياً',
        'purchase_terms' => 'شروط المشتريات',

        'purchase_order_prefix' => 'بادئة أوامر الشراء',
        'purchase_order_next_number' => 'رقم أمر الشراء التالي',

        'sales_return_prefix' => 'بادئة مرتجعات المبيعات',
        'sales_return_next_number' => 'رقم مرتجعات المبيعات التالي',

        'sales_debit_prefix' => 'بادئة الإشعارات المدينة',
        'sales_debit_next_number' => 'رقم الإشعار المدين التالي',

        'purchase_return_prefix' => 'بادئة مرتجعات المشتريات',
        'purchase_return_next_number' => 'رقم مرتجعات المشتريات التالي',

        'quotation_prefix' => 'بادئة عروض الأسعار',
        'quotation_validity_days' => 'مدة صلاحية عرض السعر (بالأيام)',
        'quotation_terms' => 'شروط عروض الأسعار',
        'quotation_next_number' => 'رقم عروض الأسعار التالي',

        'enable_shipping' => 'تفعيل مصاريف الشحن في الفواتير',
        'default_shipping_vat_rate' => 'نسبة ضريبة الشحن الافتراضية (%)',

        'default_vat_rate' => 'نسبة الضريبة الافتراضية (%)',
        'prices_include_vat' => 'الأسعار تشمل الضريبة',
        'vat_title' => 'ضريبة القيمة المضافة',
        'vat_hint_included' => 'الأسعار المدخلة في النظام شاملة للضريبة',
        'vat_hint_excluded' => 'الأسعار المدخلة في النظام غير شاملة للضريبة',

        'zakat_rate' => 'نسبة الزكاة (%)',
        'zakat_calculation_method' => 'طريقة حساب الزكاة',

        'show_logo_in_print' => 'إظهار الشعار في الطباعة',
        'show_product_image' => 'إظهار صورة المنتج في الفاتورة',
        'show_discount_column' => 'إظهار عمود الخصم في الجدول',
        'show_unit_price_after_vat' => 'إظهار سعر الوحدة بعد الضريبة',
        'show_customer_balance' => 'إظهار رصيد العميل',

        'invoice_footer_text' => 'نص تذييل الفاتورة',
        'allow_negative_stock' => 'السماح بالبيع بالسالب (بدون رصيد)',

        // ── ZATCA Phase 2 ───────────────────────────────────────────
        'zatca_settings'              => 'إعدادات الزكاة والضريبة (المرحلة الثانية)',
        'zatca_environment'           => 'بيئة العمل (Environment)',
        'zatca_organization_name'     => 'اسم المنشأة العربي',
        'zatca_organization_unit'     => 'اسم  الفرع  / رقم  المجموعة الضريبية ',
        'zatca_building_number'       => 'رقم المبنى',
        'zatca_street_name'           => 'اسم الشارع',
        'zatca_district_name'         => 'اسم الحي',
        'zatca_city_name'             => 'المدينة',
        'zatca_postal_code'           => 'الرمز البريدي',
        'zatca_country_code'          => 'كود الدول (SA)',
        'zatca_vat_number'            => 'الرقم الضريبي',
        'zatca_vat_name'              => 'الاسم المسجل في الضريبة',
        'zatca_uuid'                  => 'رقم UUID الخاص بالجهاز',
        'zatca_common_name'           => 'الاسم الشائع (Common Name)',
        'zatca_section_desc'          => 'تهيئة بيانات الربط مع هيئة الزكاة والضريبة والجمارك (فاتورة - المرحلة الثانية)',
        'environment_sandbox'         => 'Sandbox (بيئة تجريبية)',
        'environment_simulation'      => 'Simulation (محاكاة)',
        'environment_production'      => 'Production (بيئة الإنتاج)',
        
        // New phase 2 fields
        'zatca_cv'                    => 'السجل التجاري (C.V/C.R)',
        'zatca_activity_classification' => 'تصنيف النشاط',
        'zatca_registered_address'    => 'العنوان المسجل',
        'zatca_otp'                   => 'رمز التفعيل OTP الأول',
        'zatca_otp_confirmation'      => 'رمز التفعيل OTP الثاني',
        'zatca_status'                => 'الحالة',
        
        'zatca_env_production_title'  => 'منصة فاتورة',
        'zatca_env_production_desc'   => 'التهيئة لإصدار الفواتير الإلكترونية<br>وإرسالها بشكل فعلي إلى الهيئة.',
        'zatca_env_simulation_title'  => 'منصة محاكاة فاتورة',
        'zatca_env_simulation_desc'   => 'التهيئة لتجربة الفواتير الإلكترونية<br>وإرسالها بشكل تجريبي إلى منصة<br>محاكاة فاتورة.',
        'zatca_env_sandbox_desc'      => 'بيئة تجريبية خاصة بالمطورين<br>لاختبار الربط البرمجي الأولي<br>(Sandbox Portal).',
        'zatca_invoice_type'          => 'نوع الفاتورة',
        'zatca_invoice_simple'        => 'مبسطة (Simple) - 0100',
        'zatca_invoice_standard'      => 'ضريبية (Standard) - 1000',
        'zatca_invoice_both'          => 'الكل (Both) - 1100',
        'zatca_status_linked'         => 'مرتبط',
        'zatca_status_not_linked'     => 'غير مرتبط',
        'zatca_env_prod_badge'        => 'فاتورة - إنتاجية',
        'zatca_env_sim_badge'         => 'محاكاة - تجريبي',
        'zatca_success_linked'        => 'تم حفظ الإعدادات والربط مع هيئة الزكاة والضريبة والجمارك بنجاح!',
        'zatca_error_link'            => 'فشل الربط مع هيئة الزكاة والضريبة والجمارك: :error',
        'zatca_status_compliance'     => 'اختبارات الامتثال (Compliance)',
        'zatca_status_production'     => 'الربط النهائي (Production)',
        'zatca_error_required'        => 'الحقل [:field] مطلوب لتوليد شهادة التفعيل.',
        'zatca_error_vat_format'      => 'الرقم الضريبي غير صحيح. يجب أن يتكون من 15 رقم ويبدأ وينتهي بالرقم 3.',
        'zatca_error_cr_required'     => 'السجل التجاري (CR) مطلوب وصالح (10 أرقام على الأقل) للربط مع هيئة الزكاة والضريبة والجمارك.',
        'zatca_request_production_btn' => 'طلب شهادة الإنتاج النهائية',
        'zatca_success_production'    => 'تم استلام شهادة الإنتاج النهائية بنجاح. النظام الآن جاهز لإرسال الفواتير الحقيقية.',
        'selected_branch_for_zatca'   => 'الفرع المختار لإعدادات الربط مع هيئة الزكاة والضريبة والجمارك (ZATCA)',
        'current_organization_mode'   => 'وضع التسجيل الحالي للمنشأة:',
        'require_cost_center'         => 'تفعيل مراكز التكلفة (جعلها إلزامية)',
    ],

    'sections' => [
        'general_settings' => 'الإعدادات العامة',
        'general_settings_desc' => 'تكوين النظام والخيارات الأساسية',
        'taxes_and_zakat' => 'الضرائب والزكاة',
        'taxes_settings' => 'إعدادات الضرائب',
        'taxes_and_zakat_desc' => 'إعدادات الضريبة المضافة والزكاة',
        'zakat_settings' => 'إعدادات الزكاة',
        'sales_settings' => 'إعدادات المبيعات',
        'purchase_settings' => 'إعدادات المشتريات',
        'quotation_settings' => 'إعدادات عروض الأسعار',
        'shipping_settings' => 'إعدادات الشحن والتوصيل',
        'zatca_environment_settings' => 'إعدادات البيئة',
        'zatca_basic_fields' => 'الحقول الأساسية',
        'zatca_additional_info' => 'معلومات إضافية',
    ],

    'help' => [
        'default_vat_rate'   => 'نسبة الضريبة الافتراضية المطبقة على الفواتير (0-100%)',
        'prices_include_vat' => 'حدد ما إذا كانت الأسعار المدخلة تشمل الضريبة بالفعل',
    ],

    'options' => [
        'zakat_fixed'     => 'نسبة ثابتة',
        'zakat_net_worth' => 'بناءً على الوعاء الزكوي',
    ],
    'hints' => [
        'single_word'             => 'يجب أن تكون كلمة واحدة بدون مسافات',
        'prices_include_vat_on'   => 'الأسعار تشمل الضريبة:',
        'prices_include_vat_off'  => 'الأسعار لا تشمل الضريبة، سيتم إضافة الضريبة على السعر:',
        'example'                 => 'مثال',
        'price_input'             => 'السعر المُدخل',
        'vat_extracted'           => 'صافي السعر :net — الضريبة :vat',
        'vat_added'               => 'الإجمالي بعد إضافة الضريبة (15%) = :total',
        'cost_center_required'    => 'يجب اختيار مركز التكلفة، حيث تم تفعيله في الإعدادات كحقل إلزامي.',
    ],
];
