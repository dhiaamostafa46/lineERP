<?php

namespace App\Services;

use Illuminate\Http\Request;
use hisorange\BrowserDetect\Parser as Browser;

class DeviceIdentificationService
{
    /**
     * إنشاء بصمة فريدة للجهاز (Device Fingerprint)
     */
    public function getDeviceSerialNumber(Request $request): string
    {
        $userAgent = $request->header('User-Agent', '');

        // معلومات الجهاز من مكتبة browser-detect
        $browserInfo = $this->getBrowserInfo($request);

     
        // جمع المعلومات لزيادة دقة البصمة
        $deviceInfo = implode('|', [$browserInfo['browser_family'] ?? '', $browserInfo['platform_family'] ?? '', $browserInfo['device_type'] ?? '']);
        // إنشاء تجزئة فريدة للجهاز
        return hash('sha256', $deviceInfo);

    }

    public function getBrowserInfo(Request $request)
    {
        $data = [
            // Browser Info
            'user_agent' => Browser::userAgent(),
            'browser_name' => Browser::browserName(),
            'browser_family' => Browser::browserFamily(),
            'browser_version' => Browser::browserVersion(),
            'browser_version_major' => Browser::browserVersionMajor(),
            'browser_version_minor' => Browser::browserVersionMinor(),
            'browser_version_patch' => Browser::browserVersionPatch(),
            'browser_engine' => Browser::browserEngine(),

            // Platform Info
            'platform_name' => Browser::platformName(),
            'platform_family' => Browser::platformFamily(),
            'platform_version' => Browser::platformVersion(),
            'platform_version_major' => Browser::platformVersionMajor(),
            'platform_version_minor' => Browser::platformVersionMinor(),
            'platform_version_patch' => Browser::platformVersionPatch(),

            // Device Info
            'device_type' => Browser::deviceType(), // desktop / mobile / tablet / bot
            'device_family' => Browser::deviceFamily(), // Samsung / Apple / Huawei ...
            'device_model' => Browser::deviceModel(),

            // OS checks
            // 'is_windows' => Browser::isWindows(),
            // 'is_linux' => Browser::isLinux(),
            // 'is_mac' => Browser::isMac(),
            // 'is_android' => Browser::isAndroid(),

            // // Device type checks
            // 'is_mobile' => Browser::isMobile(),
            // 'is_tablet' => Browser::isTablet(),
            // 'is_desktop' => Browser::isDesktop(),
            // 'is_bot' => Browser::isBot(),

            // // Browser checks
            // 'is_chrome' => Browser::isChrome(),
            // 'is_firefox' => Browser::isFirefox(),
            // 'is_opera' => Browser::isOpera(),
            // 'is_safari' => Browser::isSafari(),
            // 'is_ie' => Browser::isIE(),
            // 'is_edge' => Browser::isEdge(),

            // Miscellaneous
            'is_in_app' => Browser::isInApp(),
        ];


        // dd($data);
        return $data;

        // return response()->json($data);
    }

    /**
     * إرجاع معلومات مفصلة عن المتصفح والجهاز
     */
    //     public function getBrowserInfo(Request $request): array
    //     {

    //         Browser::userAgent()	Current visitor's HTTP_USER_AGENT string.	(string)
    // Browser::isMobile()	Is this a mobile device.	(boolean)
    // Browser::isTablet()	Is this a tablet device.	(boolean)
    // Browser::isDesktop()	Is this a desktop computer.	(boolean)
    // Browser::isBot()	Is this a crawler / bot.	(boolean)
    // Browser::deviceType()	Enumerated response for [Mobile, Tablet, Desktop, and Bot]	(string)
    // Browser related functions
    // Browser::browserName()	Browser's human friendly name like Firefox 3.6, Chrome 42.	(string)
    // Browser::browserFamily()	Browser's vendor like Chrome, Firefox, Opera.	(string)
    // Browser::browserVersion()	Browser's human friendly version string.	(string)
    // Browser::browserVersionMajor()	Browser's semantic major version.	(integer)
    // Browser::browserVersionMinor()	Browser's semantic minor version.	(integer)
    // Browser::browserVersionPatch()	Browser's semantic patch version.	(integer)
    // Browser::browserEngine()	Browser's engine like: Blink, WebKit, Gecko.	(string)
    // Operating system related functions
    // Browser::platformName()	Operating system's human friendly name like Windows XP, Mac 10.	(string)
    // Browser::platformFamily()	Operating system's vendor like Linux, Windows, Mac.	(string)
    // Browser::platformVersion()	Operating system's human friendly version like XP, Vista, 10.	(integer)
    // Browser::platformVersionMajor()	Operating system's semantic major version.	(integer)
    // Browser::platformVersionMinor()	Operating system's semantic minor version.	(integer)
    // Browser::platformVersionPatch()	Operating system's semantic patch version.	(integer)
    // Operating system extended functions
    // Browser::isWindows()	Is this a windows operating system.	(boolean)
    // Browser::isLinux()	Is this a linux based operating system.	(boolean)
    // Browser::isMac()	Is this an iOS or Mac based operating system.	(boolean)
    // Browser::isAndroid()	Is this an Android operating system.	(boolean)
    // Device related functions
    // Browser::deviceFamily()	Device's vendor like Samsung, Apple, Huawei.	(string)
    // Browser::deviceModel()	Device's brand name like iPad, iPhone, Nexus.	(string)
    // Browser vendor related functions
    // Browser::isChrome()	Is this a chrome browser.	(boolean)
    // Browser::isFirefox()	Is this a firefox browser.	(boolean)
    // Browser::isOpera()	Is this an opera browser.	(boolean)
    // Browser::isSafari()	Is this a safari browser.	(boolean)
    // Browser::isIE()	Checks if the browser is an some kind of Internet Explorer (or Trident)	(boolean)
    // Browser::isIEVersion()	Compares to a given IE version	(boolean)
    // Browser::isEdge()	Is this a microsoft edge browser.	(boolean)
    // Miscellaneous
    // Browser::isInApp()	Check for browsers rendered inside applications like android webview.
    //        $date= [
    //             'browser'      => Browser::browserName(),
    //             'browser_ver'  => Browser::browserVersion(),
    //             'os'           => Browser::platformName(),
    //             'os_ver'       => Browser::platformVersion(),
    //             'device_type'  => Browser::deviceType(),  // desktop / mobile / tablet
    //             'device_name'  => Browser::deviceFamily(),
    //             'brand'        => Browser::deviceModel(), // الشركة المصنعة (Apple / Samsung...)
    //             'is_mobile'    => Browser::isMobile(),
    //             'is_tablet'    => Browser::isTablet(),
    //             'is_desktop'   => Browser::isDesktop(),
    //             'user_agent'   => $request->userAgent(),
    //         ];

    //         dd(  $date);
    //     }
}
