<?php

namespace App\Helpers;

class PlatformLogoHelper
{
    /**
     * Get logo SVG / URL for a specific platform code.
     */
    public static function getLogo(string $code): string
    {
        $code = strtolower(trim($code));

        $logos = [
            'salla' => 'https://assets.salla.cloud/cp/assets/images/logo.svg',
            'zid' => 'https://zid.sa/wp-content/uploads/2021/04/Zid-Logo-01.svg',
            'amazon' => 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg',
            'noon' => 'https://z.nooncdn.com/s/app/com/noon/design-system/logos/noon-logo-en.svg',
            'shopify' => 'https://cdn.worldvectorlogo.com/logos/shopify.svg',
            'woocommerce' => 'https://cdn.worldvectorlogo.com/logos/woocommerce.svg',
            'magento' => 'https://cdn.worldvectorlogo.com/logos/magento.svg',
            'bigcommerce' => 'https://cdn.worldvectorlogo.com/logos/bigcommerce-1.svg',
            'trendyol' => 'https://cdn.worldvectorlogo.com/logos/trendyol-1.svg',
            'ebay' => 'https://cdn.worldvectorlogo.com/logos/ebay.svg',
            'etsy' => 'https://cdn.worldvectorlogo.com/logos/etsy.svg',
            'walmart' => 'https://cdn.worldvectorlogo.com/logos/walmart.svg',

            // Payments
            'moyasar' => 'https://moyasar.com/assets/img/logo-dark.svg',
            'hyperpay' => 'https://www.hyperpay.com/wp-content/uploads/2021/05/hyperpay-logo.svg',
            'tamara' => 'https://cdn.tamara.co/widget-v2/assets/tamara-logo-badge-en.svg',
            'tabby' => 'https://tabby.ai/favicon.svg',
            'paytabs' => 'https://site.paytabs.com/wp-content/uploads/2021/06/paytabs-logo-1.png',
            'stc_pay' => 'https://stcpay.com.sa/assets/images/stcpay_logo.svg',
            'geidea' => 'https://geidea.net/egypt/wp-content/uploads/2021/09/geidea-logo.svg',
            'urway' => 'https://urway.sa/wp-content/uploads/2020/07/urway-logo.png',
            'stripe' => 'https://cdn.worldvectorlogo.com/logos/stripe-4.svg',
            'checkout' => 'https://cdn.worldvectorlogo.com/logos/checkout-com.svg',
            'tap' => 'https://tap.company/favicon.png',
            'paypal' => 'https://cdn.worldvectorlogo.com/logos/paypal-3.svg',
            'amwal' => 'https://amwal.tech/assets/amwal-logo.svg',
            'madfu' => 'https://madfu.com/assets/images/logo.svg',
            'alhamrani_pos' => 'https://alhamrani.com/wp-content/uploads/2020/12/alhamrani-logo.png',

            // Shipping & Delivery
            'spl' => 'https://splonline.com.sa/media/1001/spl-logo.svg',
            'aramex' => 'https://cdn.worldvectorlogo.com/logos/aramex-1.svg',
            'smsa' => 'https://www.smsaexpress.com/assets/images/logo.png',
            'naqel' => 'https://www.naqelexpress.com/assets/images/logo.png',
            'dhl' => 'https://cdn.worldvectorlogo.com/logos/dhl-1.svg',
            'fedex' => 'https://cdn.worldvectorlogo.com/logos/fedex-express-6.svg',
            'ajex' => 'https://ajex.com/images/logo.svg',
            'jtexpress' => 'https://www.jtexpress.sa/assets/images/logo.png',
            'aymakan' => 'https://aymakan.com.sa/assets/images/logo.svg',
            'barq' => 'https://barqfleet.com/assets/images/logo.svg',
            'torod' => 'https://torod.co/assets/img/logo.svg',
            'oto' => 'https://tryoto.com/assets/images/logo.svg',

            // Government & Compliance
            'zatca' => 'https://zatca.gov.sa/_layouts/15/ZATCA/Images/zatca-logo.svg',
            'nafath' => 'https://www.iam.gov.sa/authservice//images/logo_ar.svg',
            'qiwa' => 'https://qiwa.sa/themes/custom/qiwa/logo.svg',
            'muqeem' => 'https://muqeem.sa/img/logo.svg',
            'gosi' => 'https://www.gosi.gov.sa/GOSIOnline/images/gosi-logo.svg',
            'mudad' => 'https://mudad.com.sa/assets/images/logo.svg',
            'saudi_business_center' => 'https://sbc.gov.sa/assets/images/logo.svg',
            'open_data' => 'https://open.data.gov.sa/assets/images/logo.svg',

            // ERP / CRM / Systems
            'odoo' => 'https://cdn.worldvectorlogo.com/logos/odoo.svg',
            'dynamics365' => 'https://cdn.worldvectorlogo.com/logos/microsoft-dynamics-365.svg',
            'unifonic' => 'https://unifonic.com/wp-content/themes/unifonic/assets/images/logo.svg',
            'fleet_tracking' => 'https://cdn-icons-png.flaticon.com/512/854/854878.png',
            'pricing' => 'https://cdn-icons-png.flaticon.com/512/2953/2953363.png',
            'crm' => 'https://cdn-icons-png.flaticon.com/512/3126/3126647.png',
        ];

        return $logos[$code] ?? '';
    }

    /**
     * Get fallback SVG icon with brand colors and initials.
     */
    public static function getBrandBadge(string $name, string $category): string
    {
        $colors = [
            'ecommerce' => ['bg' => '#E8FFF3', 'color' => '#50CD89', 'icon' => 'ki-basket'],
            'payment_gateway' => ['bg' => '#F1FAFF', 'color' => '#009EF7', 'icon' => 'ki-credit-cart'],
            'payments' => ['bg' => '#F1FAFF', 'color' => '#009EF7', 'icon' => 'ki-credit-cart'],
            'shipping' => ['bg' => '#FFF8DD', 'color' => '#FFC700', 'icon' => 'ki-delivery-3'],
            'delivery' => ['bg' => '#FFF8DD', 'color' => '#FFC700', 'icon' => 'ki-delivery-3'],
            'government' => ['bg' => '#FFEEF3', 'color' => '#F1416C', 'icon' => 'ki-shield-tick'],
            'messaging' => ['bg' => '#F8F5FF', 'color' => '#7239EA', 'icon' => 'ki-message-text-2'],
            'accounting' => ['bg' => '#F9F9F9', 'color' => '#181C32', 'icon' => 'ki-bill'],
            'hr' => ['bg' => '#E8FFF3', 'color' => '#50CD89', 'icon' => 'ki-profile-user'],
            'internal_engine' => ['bg' => '#F4F4F4', 'color' => '#5E6278', 'icon' => 'ki-gear'],
        ];

        $palette = $colors[$category] ?? ['bg' => '#F1FAFF', 'color' => '#009EF7', 'icon' => 'ki-cube-2'];

        return '<div class="symbol-label" style="background-color: '.$palette['bg'].'; color: '.$palette['color'].';">
                    <i class="ki-duotone '.$palette['icon'].' fs-2" style="color: '.$palette['color'].';">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                </div>';
    }
}
