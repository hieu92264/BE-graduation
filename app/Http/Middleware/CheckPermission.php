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
    public function handle(Request $request, Closure $next, string $permissionCode = ''): Response
    {
        try {
            $user = Auth::user();

            if (!$user) return $this->failedResponse('Người dùng chưa được xác thực.', Response::HTTP_UNAUTHORIZED);

            if (strtolower($user->username ?? '') === 'admin') {
                return $next($request);
            }

            if (!$user->hasPermission($permissionCode)) {
                return $this->failedResponse(
                    'Bạn không có quyền truy cập tài nguyên này.',
                    Response::HTTP_FORBIDDEN,
                    ['permission' => $permissionCode]
                );
            }
            return $next($request);
        } catch (Exception $e) {
            return $this->failedResponse(
                'Đã xảy ra lỗi khi kiểm tra quyền.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['error' => $e->getMessage()]
            );
        }
    }
}
