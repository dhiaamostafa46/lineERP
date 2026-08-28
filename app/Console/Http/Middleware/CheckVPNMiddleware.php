<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\VPNCheckService;

class CheckVPNMiddleware
{
    protected $vpnService;

    public function __construct(VPNCheckService $vpnService)
    {
        $this->vpnService = $vpnService;
    }

    public function handle(Request $request, Closure $next)
    {
        // تجاهل التحقق للمسارات المستثناة
        if ($this->shouldSkipCheck($request)) {
            return $next($request);
        }

        $ip = $request->ip();
        $vpnCheck = $this->vpnService->isVPN($ip, $request);

        // إذا كان يستخدم VPN
        if ($vpnCheck['is_vpn']) {

            if (auth()->check()) {
                $user = auth()->user();
                // تسجيل الخروج
                auth()->logout();

                // إفراغ جلسة المستخدم
                $request->session()->flush();
                $request->session()->regenerate();

                flash()->error('تم تسجيل الخروج لأنك تستخدم شبكة VPN. يرجى تعطيل VPN والمحاولة مرة أخرى.');

                return redirect('/login');
            }
        }

        return $next($request);
    }

    /**
     * التحقق من المسارات المستثناة
     */
    protected function shouldSkipCheck(Request $request)
    {
        $excludedRoutes = [
            'login',
            'register',
            'logout',
            'password.*',
            'api.*'
        ];

        foreach ($excludedRoutes as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }
}
