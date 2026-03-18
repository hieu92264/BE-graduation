<?php

namespace App\Http\Services;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\AuthServiceInterface;
use App\Models\Permission;
use App\Models\RefreshToken;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();
            $existingUser = User::where('username', $data['username'])->first();

            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Username exists',
                    'statusCode' => HttpStatus::CONFLICT,
                ];
            }

            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->sendEmailVerificationNotification();

            DB::commit();

            return [
                'success' => true,
                'message' => 'User registered successfully',
                'statusCode' => HttpStatus::CREATED,
                'data' => $user->toArray(),
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'statusCode' => HttpStatus::INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function login(array $data): array
    {
        try {
            $credentials = [
                'username' => $data['username'],
                'password' => $data['password'],
            ];

            $token = Auth::guard('api')->attempt($credentials);

            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không chính xác',
                    'statusCode' => HttpStatus::UNAUTHORIZED,
                ];
            }

            $user = User::query()
                ->where('username', $credentials['username'])
                ->first();

            if (!$user || !$user->hasVerifiedEmail()) {
                Auth::guard('api')->logout();
                return [
                    'success' => false,
                    'message' => 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email để xác nhận.',
                    'statusCode' => HttpStatus::FORBIDDEN,
                ];
            }

            $refreshToken = Str::random(64);

            RefreshToken::create([
                'user_id' => $user->id,
                'token' => $refreshToken,
                'expires_at' => now()->addDays(14),
            ]);

            return $this->respondWithToken($token, $refreshToken);
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $exception->getMessage(),
                'statusCode' => HttpStatus::INTERNAL_SERVER_ERROR,
            ];
        }
    }

    protected function respondWithToken(string $accessToken, string $refreshToken): array
    {
        $ttl = (int)config('jwt.ttl');

        return [
            'success' => true,
            'message' => 'Login successful',
            'statusCode' => HttpStatus::OK,
            'data' => [
                'access_token' => $accessToken,
                'token_type' => 'bearer',
                'expires_in' => $ttl * 60, // giây
                'refresh_token' => $refreshToken,
            ],
        ];
    }

    public function logout(string $refreshToken): array
    {
        try {
            if (!Auth::check()) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated',
                    'statusCode' => HttpStatus::UNAUTHORIZED,
                ];
            }

            if ($refreshToken !== '') {
                RefreshToken::where('token', $refreshToken)->delete();
            }

            Auth::logout();

            return [
                'success' => true,
                'message' => 'Successfully logged out',
                'statusCode' => HttpStatus::OK,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'statusCode' => HttpStatus::INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function refreshToken(string $refreshToken): array
    {
        try {
            $storedToken = RefreshToken::where('token', $refreshToken)->first();

            if (!$storedToken || $storedToken->expires_at->isPast()) {
                if ($storedToken) $storedToken->delete();
                return [
                    'success' => false,
                    'message' => 'Phiên đăng nhập đã hết hạn hoặc không hợp lệ.',
                    'statusCode' => HttpStatus::UNAUTHORIZED,
                ];
            }

            $absoluteExpiration = $storedToken->expires_at;
            $userId = $storedToken->user_id;
            $user = User::query()->find($userId);

            $newAccessToken = auth('api')->login($user);
            $storedToken->delete();

            $newRefreshTokenString = Str::random(64);
            RefreshToken::create([
                'user_id' => $userId,
                'token' => $newRefreshTokenString,
                'expires_at' => $absoluteExpiration,
            ]);


            return $this->respondWithToken($newAccessToken ?? '', $newRefreshTokenString);
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $exception->getMessage(),
                'statusCode' => HttpStatus::INTERNAL_SERVER_ERROR,
            ];
        }
    }

    public function me(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        $user->load(['employee', 'profile']);

        $permissions = ($user->username === 'admin')
            ? Permission::get()
            : $user->permissions;

        return [
            'user' => array_merge(
                $user->only(['id', 'username', 'email', 'isactive', 'locale']),
                ['profile' => $user->profile]
            ),
            'employee' => $user->employee,
            'permissions' => $permissions,
        ];
    }

    public function changeUserPass(int $userId, array $password): array
    {
        return [];
    }
}
