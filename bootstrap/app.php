<?php

use App\Common\Constants\Environment;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$routesPath = __DIR__ . '/../routes';
$apiRoutes = array_filter(
    glob($routesPath . '/*.php'),
    fn($file) => basename($file) !== 'web.php'
);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: $apiRoutes,
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
           'jwt.auth' => JwtMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $e) {
            $data = [
                'statusCode' => $response->getStatusCode(),
                'message' => $e->getMessage(),
                'result' => null,
            ];

            if($e instanceof AuthenticationException) {
                $data['statusCode'] = Response::HTTP_UNAUTHORIZED;
            }

            if($e instanceof NotFoundHttpException) {
                $data['message'] = 'Not found.';
            }

            if(config('app.env') == Environment::LOCAL) {
                $data['trace'] = $e->getTrace();
            }

            return response()->json($data, $data['statusCode']);
        });
    })->create();
