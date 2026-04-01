<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\AuthServiceInterface;
use App\Http\Requests\DoLoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected AuthServiceInterface $authService)
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
            'Lấy thông tin tài khoản thành công',
            HttpStatus::OK,
            $result
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token') ?? '';
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
        $refreshToken = $request->input('refresh_token') ?? '';
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

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($data);

//        return $this->DataResponse(
//            true,
//            $status === Password::RESET_LINK_SENT ? 'Reset link sent to your email.' : 'Failed to send reset link.',
//            $status === Password::RESET_LINK_SENT ? HttpStatus::OK : HttpStatus::BAD_REQUEST,
//            ['status' => $status]
//        );'
        return response()->json([
            'success' => true,
            'message' => 'Nếu email tồn tại, chúng tôi đã gửi liên kết đặt lại mật khẩu.',
            'status' => $status,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->password = $password;
                $user->setRememberToken(\Str::random(60));
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đặt lại mật khẩu thành công.',
        ]);
    }
}
