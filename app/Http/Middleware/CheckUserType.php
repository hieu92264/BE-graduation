<?php

namespace App\Http\Middleware;

use App\Common\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next, string ...$allowedTypes): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $this->failedResponse('Unauthenticated.', Response::HTTP_UNAUTHORIZED);
        }

        $actualType = $user->profile?->user_type?->value ?? $user->profile?->user_type;

        if (empty($allowedTypes) || in_array((string)$actualType, $allowedTypes, true)) {
            return $next($request);
        }

        return $this->failedResponse(
            'You do not have the correct role to access this resource.',
            Response::HTTP_FORBIDDEN,
            [
                'allowed_types' => $allowedTypes,
                'actual_type' => $actualType,
            ]
        );
    }
}
