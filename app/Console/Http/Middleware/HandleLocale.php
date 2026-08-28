<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class HandleLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (auth()->check()) {
        //     session(['locale' => auth()->user()->locale]);
        // }
        // app()->setLocale(session('locale') ?? 'en');
        app()->setLocale('ar');
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        // Enforce English locale for Carbon dates formatting globally
        \Carbon\Carbon::setLocale('en');
        \Illuminate\Support\Carbon::setLocale('en');

        // Automatically convert any Eastern Arabic / Persian digits in request query and post data to Western English digits (0-9)
        if ($request->query->count() > 0) {
            $request->query->replace($this->convertArabicDigits($request->query->all()));
        }

        if ($request->request->count() > 0) {
            $request->request->replace($this->convertArabicDigits($request->request->all()));
        }

        return $next($request);
    }

    /**
     * Convert Eastern Arabic (Indic) and Persian digits to Western English digits.
     */
    protected function convertArabicDigits($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'convertArabicDigits'], $input);
        }

        if (is_string($input)) {
            $arabicDigits = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
            $persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
            $englishDigits = ['0','1','2','3','4','5','6','7','8','9'];

            $input = str_replace($arabicDigits, $englishDigits, $input);
            $input = str_replace($persianDigits, $englishDigits, $input);
        }

        return $input;
    }
}
