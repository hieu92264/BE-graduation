<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\IAuthService;
use App\Http\Requests\DoLoginRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected IAuthService $authService)
    {
    }

    public function login(DoLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $result = $this->authService->login($credentials);
        return $this->DataResponse(
            $result['success'],
            $result['message'],
            $result['statusCode'],
            $result['data'] ?? []
        );
    }

    public function me(): JsonResponse
    {
        $result = $this->authService->me();
        return $this->DataResponse(
            true,
            'success',
            HttpStatus::OK,
            $result
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $refreshToken = $request->query('refresh_token') ?? null;
        $result = $this->authService->logout($refreshToken);
        return $this->DataResponse(
            true,
            $result['message'],
            HttpStatus::OK,
            null
        );
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $refreshToken = $request->only('refresh_token') ?? '';
        $result = $this->authService->refreshToken($refreshToken);
        return $this->DataResponse(
            $result['success'],
            $result['message'],
            $result['statusCode'],
            $result['data'] ?? []
        );
    }

    public function register(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->authService->register($data);
        return $this->DataResponse(
            $result['success'],
            $result['message'],
            $result['statusCode'],
            $result['data'] ?? []
        );
    }
}
