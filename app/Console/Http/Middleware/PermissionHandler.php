<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class PermissionHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);

        $routeName = $request->route()->getName();
        if (str_contains($routeName, 'update')) {
            $routeName = str_replace('update', 'edit', $routeName);
        }
        if (str_contains($routeName, 'store')) {
            $routeName = str_replace('store', 'create', $routeName);
        }
        $routeName = str_replace('', '', $routeName);
        $routeName = str_replace('.', ' ', $routeName);

        if (!auth()->user()->can($routeName) && $routeName != 'dashboard') {
            throw UnauthorizedException::forPermissions([$routeName]);
        }
        return $next($request);
    }
}
