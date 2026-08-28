<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // تحقق من وجود وتفعيل موديول HR قبل تحويل الموظف
                if (Module::has('HR') && Module::isEnabled('HR') && $user->emp_flage == 2) {
                    return redirect()->route('hr.empdashboard.index');
                }

                // تحويل المستخدم العادي إلى الصفحة الرئيسية العامة
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
