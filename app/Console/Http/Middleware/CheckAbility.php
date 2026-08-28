<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAbility
{
    public function handle($request, Closure $next, $ability)
    {
        if (!$request->user() || !$request->user()->tokenCan($ability)) {
            return response()->json([
                'status_code' => "102",
                'error' => 'Forbidden – Missing required permisson' 
            ], 403);
        }

        return $next($request);
    }
}
