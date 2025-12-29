<?php

namespace App\Http\Middleware;

use App\Common\Traits\ApiResponseTrait;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = Auth::user();

            $path = $request->path();

            $prefix = config('app.api_prefix');
            if (str_starts_with($path, ltrim($prefix, '/'))) {
                $path = substr($path, strlen(ltrim($prefix, '/')) + 1);
            }

            $code = str_replace('/', '.', $path);

            $permissionCode = strtolower($code);

            if (!$user || !$user->hasPermission($permissionCode)) {
                return $this->failedResponse(
                    'You do not have permission to access this resource.',
                    Response::HTTP_FORBIDDEN,
                    ['permission' => $permissionCode]
                );
            }
            return $next($request);
        } catch (Exception $e) {
            return $this->failedResponse(
                'An error occurred while checking permissions.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['error' => $e->getMessage()]
            );
        }
    }
}
