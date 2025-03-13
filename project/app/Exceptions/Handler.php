<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return $this->handleApiException($request, $e);
            }
        });
    }

    private function handleApiException($request, Throwable $exception)
{
    $statusCode = 500;
    $response = [
        'success' => false,
        'message' => 'Internal Server Error'
    ];

    // Gérer les exceptions spécifiques
    if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
        $statusCode = 401;
        $response['message'] = 'Unauthenticated';
    } elseif ($exception instanceof \Illuminate\Validation\ValidationException) {
        $statusCode = 422;
        $response['message'] = 'Validation Error';
        $response['errors'] = $exception->errors();
    } elseif ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
        $statusCode = 404;
        $response['message'] = 'Resource not found';
    } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
        $statusCode = 404;
        $response['message'] = 'Route not found';
    } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
        $statusCode = 405;
        $response['message'] = 'Method not allowed';
    } elseif ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
        $statusCode = 403;
        $response['message'] = 'Forbidden';
    }

    // Pour les exceptions non gérées spécifiquement, incluez plus de détails en mode développement
    if (config('app.debug')) {
        $response['exception'] = get_class($exception);
        $response['message'] = $exception->getMessage();
        $response['trace'] = $exception->getTrace();
    }

    return response()->json($response, $statusCode);
}
}
