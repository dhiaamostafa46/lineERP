<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class VPNCheckService
{
    public function isVPN(string $ip, ?Request $request = null): array
    {
        $cacheKey = "vpn-check-{$ip}";

        // محاولة الحصول على البيانات من الـ Cache
        $cached = Cache::get($cacheKey);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        $result = $this->performVPNCheck($ip, $request);

        // حفظ النتيجة في الـ Cache
        Cache::put($cacheKey, $result, 3600);

        return $result;
    }

    private function performVPNCheck(string $ip, ?Request $request = null): array
    {
        try {
            // الحصول على معلومات الجهاز
            $deviceInfo = $this->getDeviceInfo($request);

            // فحص VPN باستخدام عدة مصادر
            $vpnCheck = $this->checkVPNFromAPI($ip);

            return [
                'is_vpn' => $vpnCheck['is_vpn'],
                'device_type' => $deviceInfo['device_type'],
                'is_mobile' => $deviceInfo['is_mobile'],
                'browser' => $deviceInfo['browser'],
                'proxy' => $vpnCheck['proxy'] ?? false,
                'hosting' => $vpnCheck['hosting'] ?? false,
                'threat_level' => $vpnCheck['threat_level'] ?? 'low',
                'error' => false
            ];
        } catch (\Exception $e) {
            \Log::warning("VPN check failed for IP: {$ip}, Error: {$e->getMessage()}");

            return [
                'is_vpn' => false,
                'device_type' => 'unknown',
                'is_mobile' => false,
                'browser' => 'unknown',
                'proxy' => false,
                'hosting' => false,
                'threat_level' => 'low',
                'error' => true
            ];
        }
    }

    private function checkVPNFromAPI(string $ip): array
    {
        $result = [
            'is_vpn' => false,
            'proxy' => false,
            'hosting' => false,
            'threat_level' => 'low'
        ];

        try {
            // طلب من ip-api.com
            $response = Http::timeout(3)
                ->withHeaders([
                    'User-Agent' => 'VPN-Check-Service/1.0'
                ])
                ->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'proxy,hosting,threat,isp,org'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $result['proxy'] = $data['proxy'] ?? false;
                $result['hosting'] = $data['hosting'] ?? false;
                $result['threat_level'] = $data['threat'] ?? 'low';
                $result['is_vpn'] = ($data['proxy'] ?? false) || ($data['hosting'] ?? false);
            }
        } catch (\Exception $e) {
            \Log::debug("IP-API check failed for {$ip}: {$e->getMessage()}");
        }

        return $result;
    }

    private function getDeviceInfo(?Request $request): array
    {
        $userAgent = $request?->userAgent() ?? '';

        $isMobile = $this->detectMobile($userAgent);
        $deviceType = $this->detectDeviceType($userAgent);
        $browser = $this->detectBrowser($userAgent);

        return [
            'is_mobile' => $isMobile,
            'device_type' => $deviceType,
            'browser' => $browser
        ];
    }

    private function detectMobile(string $userAgent): bool
    {
        return (bool) preg_match(
            '/(mobile|android|iphone|ipad|phone|tablet|blackberry|webos|opera mini)/i',
            $userAgent
        );
    }

    private function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/iphone|ipod/i', $userAgent)) {
            return 'iPhone';
        } elseif (preg_match('/ipad/i', $userAgent)) {
            return 'iPad';
        } elseif (preg_match('/android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/windows phone/i', $userAgent)) {
            return 'Windows Phone';
        } elseif (preg_match('/windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/macintosh|mac os/i', $userAgent)) {
            return 'Mac';
        } elseif (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        }

        return 'Unknown';
    }

    private function detectBrowser(string $userAgent): string
    {
        if (preg_match('/chrome/i', $userAgent) && !preg_match('/edge/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/edge|edg/i', $userAgent)) {
            return 'Edge';
        } elseif (preg_match('/opera|opr/i', $userAgent)) {
            return 'Opera';
        }

        return 'Unknown';
    }
}
