<?php

use App\Common\Constants\Environment;
use App\Common\Helpers\TranslationHelper;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckUserType;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\SetApiLocale;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            $apiPrefix = config('app.api_prefix');
            $routesPath = base_path('routes');
            $apiFiles = array_filter(
                glob($routesPath . '/*.php'),
                fn ($file) => ! in_array(basename($file), ['web.php', 'console.php'])
            );

            foreach ($apiFiles as $file) {
                Route::prefix($apiPrefix)
                    ->middleware('api')
                    ->group($file);
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => JwtMiddleware::class,
            'check.permission' => CheckPermission::class,
            'check.user_type' => CheckUserType::class,
        ]);

        $middleware->api(prepend: [
            SetApiLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                app()->setLocale(TranslationHelper::resolveLocale($request));

                $statusCode = ($e instanceof AuthenticationException)
                    ? 401
                    : $response->getStatusCode();

                if ($statusCode === 200) {
                    $statusCode = 500;
                }

                $message = $e->getMessage();

                if ($e instanceof ValidationException) {
                    $message = 'messages.validation.invalid';
                } elseif ($e instanceof AuthenticationException) {
                    $message = 'auth.unauthenticated';
                } elseif ($message === '') {
                    $message = 'messages.common.error';
                }

                $payload = [
                    'message' => TranslationHelper::translate($message),
                    'statusCode' => $statusCode,
                    'data' => null,
                    'path' => $request->path(),
                    'timestamp' => now()->toISOString(),
                ];

                if (config('app.env') === Environment::LOCAL) {
                    $payload['stack'] = 'Exception: ' . get_class($e) . ' in ' . $e->getFile() . ':' . $e->getLine();
                }

                if ($e instanceof ValidationException) {
                    $payload['data'] = $e->errors();
                }

                return response()->json($payload, $statusCode);
            }

            return $response;
        });
    })->create();
