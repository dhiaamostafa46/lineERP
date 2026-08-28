<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // يمكنك إضافة تسجيل الأخطاء في خدمات خارجية مثل Sentry
            // if (app()->bound('sentry')) {
            //     app('sentry')->captureException($e);
            // }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // للطلبات من API - إرجاع JSON
        // if ($request->expectsJson()) {
        if ($request->is('api/*'))
        {
            return $this->handleApiException($request, $exception);
        }

        // للطلبات العادية في بيئة الإنتاج
        if (app()->environment('production')) {
            return $this->handleWebException($request, $exception);
        }

        // في بيئة التطوير - عرض الأخطاء التفصيلية
        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions and return JSON response
     */
    protected function handleApiException($request, Throwable $exception)
    {
        $statusCode = 500;
        $message = 'حدث خطأ في الخادم';
         // get code sent from controller
          $data = $exception->getCode()??0;
          $status = sprintf('%02d',   $data);
        // معالجة أنواع الأخطاء المختلفة
        if ($exception instanceof ValidationException) {
            return response()->json([
                'status_code' => "100",
                'message' => 'بيانات غير صحيحة',
                'errors' => $exception->errors()
            ], 422);
        } elseif ($exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'العنصر المطلوب غير موجود';
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'الصفحة غير موجودة';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $message = 'طريقة الطلب غير مسموحة';
        } elseif ($exception instanceof AuthorizationException) {
            $statusCode = 403;
            $message = 'هذا الإجراء غير مصرح به';
        } elseif ($exception instanceof ThrottleRequestsException) {
            $statusCode = 429;
            $message = 'عدد كبير من الطلبات، يرجى المحاولة لاحقاً';
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'غير مصرح بالوصول';
        } elseif ($exception instanceof TokenMismatchException) {
            $statusCode = 419;
            $message = 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة';
        } elseif ($exception instanceof QueryException && !config('app.debug')) {
            $statusCode = 500;
            $message = 'حدث خطأ في قاعدة البيانات.';
        } elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: $message;
        }else
        {
            return response()->json([
                'status_code' =>$statusCode,
                'message' =>$exception->getMessage() ?: "UnCustomError"
            ], 422);
        }

        // في بيئة التطوير - إضافة تفاصيل الخطأ
        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode
        ];

        if (config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Handle web exceptions and return view response
     */
    protected function handleWebException($request, Throwable $exception)
    {
        $statusCode = 500;
        $view = 'errors.500';

        // معالجة أنواع الأخطاء المختلفة
        if ($exception instanceof ValidationException) {
            return parent::render($request, $exception);
        }

        if ($exception instanceof AuthenticationException) {
            return redirect()->guest(route('login'));
        }

        if ($exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            $view = 'errors.404';
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $view = 'errors.404';
        } elseif ($exception instanceof AuthorizationException) {
            $statusCode = 403;
            $view = 'errors.403';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $view = 'errors.405';
        } elseif ($exception instanceof ThrottleRequestsException) {
            $statusCode = 429;
            $view = 'errors.429';
        } elseif ($exception instanceof TokenMismatchException) {
            $statusCode = 419;
            $view = 'errors.419';
        } elseif ($exception instanceof QueryException) {
            $statusCode = 500;
            $view = 'errors.500';
        } elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $view = "errors.{$statusCode}";
        }

        // التحقق من وجود صفحة الخطأ المخصصة
        if (view()->exists($view)) {
            return response()->view($view, [
                'exception' => $exception,
                'statusCode' => $statusCode
            ], $statusCode);
        }

        // استخدام صفحة خطأ افتراضية
        return response()->view('errors.custom', [
            'exception' => $exception,
            'statusCode' => $statusCode
        ], $statusCode);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بالوصول'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
