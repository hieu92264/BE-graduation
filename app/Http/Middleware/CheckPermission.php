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
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $permissionCode = ''): Response
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return $this->failedResponse('auth.unauthenticated', Response::HTTP_UNAUTHORIZED);
            }

            if (strtolower($user->username ?? '') === 'admin') {
                return $next($request);
            }

            if (! $user->hasPermission($permissionCode)) {
                return $this->failedResponse(
                    'messages.authorization.permission_denied',
                    Response::HTTP_FORBIDDEN,
                    ['permission' => $permissionCode]
                );
            }

            return $next($request);
        } catch (Exception $e) {
            return $this->failedResponse(
                'messages.authorization.permission_check_failed',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['error' => $e->getMessage()]
            );
        }
    }
}
