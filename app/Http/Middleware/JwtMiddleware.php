<?php

namespace App\Http\Middleware;

use App\Common\Constants\HttpStatus;
use App\Common\Traits\ApiResponseTrait;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
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
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                throw new Exception('User not found');
            }

            Auth::setUser($user);
            return $next($request);
        } catch (TokenExpiredException $e) {
            return $this->DataResponse(
                false,
                'Token has expired',
                HttpStatus::UNAUTHORIZED,
                null
            );
        } catch (TokenInvalidException $e) {
            return $this->DataResponse(
                false,
                'Token is invalid',
                HttpStatus::UNAUTHORIZED,
                null
            );
        } catch (Exception $e) {
            return $this->DataResponse(
                false,
                $e->getMessage(),
                HttpStatus::UNAUTHORIZED,
                null
            );
        }
    }
}
