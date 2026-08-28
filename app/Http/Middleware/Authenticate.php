<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the Admin should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            return route('login');
        } else {
            return null;
        }
    }

    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, $guards);

        $user = $request->user();
        if ($user && $user->status != \App\Models\User::STATUS_ACTIVE) {
            \Illuminate\Support\Facades\Auth::logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            flash()->error(__('auth.account_inactive'));

            $this->unauthenticated($request, $guards);
        }
    }
}
