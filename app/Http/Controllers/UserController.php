<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Common\Enums\UserType;
use App\Http\Interfaces\UserServiceInterface;
use App\Models\RolePermissions;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(protected UserServiceInterface $userService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('profile')->orderByDesc('id')->get();
        return $this->successResponse(
            $users->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
//    public function store(Request $request)
//    {
//        $data = $request->validate([
//            'isactive' => ['nullable', 'in:Y,N'],
//            'username' => ['required', 'string', 'max:255'],
//            'email' => ['nullable', 'email', 'max:255'],
//            'password' => ['required', 'string', 'min:6'],
//            'locale' => ['nullable', 'string', 'max:10'],
//            'remark' => ['nullable', 'string'],
//
//            // profile fields
//            'full_name' => ['nullable', 'string', 'max:255'],
//            'phone_number' => ['nullable', 'string', 'max:50'],
//            'avatar_url' => ['nullable', 'string'],
//            'address' => ['nullable', 'string'],
//            'zalo' => ['nullable', 'string'],
//            'facebook' => ['nullable', 'string'],
//            'user_type' => ['required', Rule::in(UserType::values())],
//            'profile_remark' => ['nullable', 'string'],
//        ]);
//
//        $existing = null;
//
//        if (!empty($data['email'])) {
//            $existing = User::withTrashed()->where('email', $data['email'])->first();
//        }
//
//        if (!$existing) {
//            $existing = User::withTrashed()->where('username', $data['username'])->first();
//        }
//
//        $needSendVerify = false;
//
//        if ($existing && $existing->trashed()) {
//            $activeEmailConflict = !empty($data['email'])
//                && User::whereNull('deleted_at')
//                    ->where('email', $data['email'])
//                    ->where('id', '!=', $existing->id)
//                    ->exists();
//
//            $activeUsernameConflict =
//                User::whereNull('deleted_at')
//                    ->where('username', $data['username'])
//                    ->where('id', '!=', $existing->id)
//                    ->exists();
//
//            if ($activeEmailConflict) {
//                return $this->failedResponse('Email already exists.', HttpStatus::UNPROCESSABLE_ENTITY);
//            }
//
//            if ($activeUsernameConflict) {
//                return $this->failedResponse('Username already exists.', HttpStatus::UNPROCESSABLE_ENTITY);
//            }
//
//            $user = DB::transaction(function () use ($existing, $data, &$needSendVerify) {
//                $existing->restore();
//
//                $oldEmail = $existing->email;
//                $newEmail = $data['email'] ?? null;
//
//                $emailChanged = ($oldEmail !== $newEmail);
//
//                $existing->fill([
//                    'isactive' => $data['isactive'] ?? 'Y',
//                    'username' => $data['username'],
//                    'email' => $newEmail,
//                    'password' => $data['password'],
//                    'locale' => $data['locale'] ?? null,
//                    'remark' => $data['remark'] ?? null,
//                ]);
//
//                $existing->email_verified_at = null;
//                $needSendVerify = !empty($newEmail);
//
//                $existing->save();
//
//                $existing->profile()->updateOrCreate(
//                    ['user_id' => $existing->id],
//                    [
//                        'isactive' => $data['isactive'] ?? 'Y',
//                        'full_name' => $data['full_name'] ?? null,
//                        'phone_number' => $data['phone_number'] ?? null,
//                        'avatar_url' => $data['avatar_url'] ?? null,
//                        'address' => $data['address'] ?? null,
//                        'zalo' => $data['zalo'] ?? null,
//                        'facebook' => $data['facebook'] ?? null,
//                        'user_type' => $data['user_type'],
//                        'remark' => $data['profile_remark'] ?? null,
//                    ]
//                );
//
//                return $existing->load('profile');
//            });
//
//            if ($needSendVerify) {
//                $user->sendEmailVerificationNotification();
//            }
//
//            return $this->successResponse(
//                $user->toArray(),
//                'User restored & updated successfully. Verification email sent.',
//                HttpStatus::OK
//            );
//        }
//
//        $activeEmailExists = !empty($data['email'])
//            && User::whereNull('deleted_at')->where('email', $data['email'])->exists();
//
//        $activeUsernameExists =
//            User::whereNull('deleted_at')->where('username', $data['username'])->exists();
//
//        if ($activeEmailExists) {
//            return $this->failedResponse('Email already exists.', HttpStatus::UNPROCESSABLE_ENTITY);
//        }
//
//        if ($activeUsernameExists) {
//            return $this->failedResponse('Username already exists.', HttpStatus::UNPROCESSABLE_ENTITY);
//        }
//
//        $user = DB::transaction(function () use ($data, &$needSendVerify) {
//            $user = User::create([
//                'isactive' => $data['isactive'] ?? 'Y',
//                'username' => $data['username'],
//                'email' => $data['email'] ?? null,
//                'password' => $data['password'],
//                'locale' => $data['locale'] ?? null,
//                'remark' => $data['remark'] ?? null,
//            ]);
//
//            $user->profile()->create([
//                'isactive' => $data['isactive'] ?? 'Y',
//                'full_name' => $data['full_name'] ?? null,
//                'phone_number' => $data['phone_number'] ?? null,
//                'avatar_url' => $data['avatar_url'] ?? null,
//                'address' => $data['address'] ?? null,
//                'zalo' => $data['zalo'] ?? null,
//                'facebook' => $data['facebook'] ?? null,
//                'user_type' => $data['user_type'],
//                'remark' => $data['profile_remark'] ?? null,
//            ]);
//
//            $needSendVerify = !empty($data['email']);
//
//            return $user->load('profile');
//        });
//
//        if ($needSendVerify) {
//            $user->sendEmailVerificationNotification();
//        }
//
//        return $this->successResponse(
//            $user->toArray(),
//            'User created successfully. Verification email sent.',
//            HttpStatus::CREATED
//        );
//    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'isactive' => ['nullable', 'in:Y,N'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'locale' => ['nullable', 'string', 'max:10'],
            'remark' => ['nullable', 'string'],

            // profile fields
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'avatar_url' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'zalo' => ['nullable', 'string'],
            'facebook' => ['nullable', 'string'],
            'user_type' => ['required', Rule::in(UserType::values())],
            'profile_remark' => ['nullable', 'string'],
        ]);

        $existing = null;

        if (!empty($data['email'])) {
            $existing = User::withTrashed()->where('email', $data['email'])->first();
        }

        if (!$existing) {
            $existing = User::withTrashed()->where('username', $data['username'])->first();
        }

        $needSendVerify = false;

        // ===== RESTORE + UPDATE (nếu user đã soft-delete) =====
        if ($existing && $existing->trashed()) {
            $activeEmailConflict = !empty($data['email'])
                && User::whereNull('deleted_at')
                    ->where('email', $data['email'])
                    ->where('id', '!=', $existing->id)
                    ->exists();

            $activeUsernameConflict =
                User::whereNull('deleted_at')
                    ->where('username', $data['username'])
                    ->where('id', '!=', $existing->id)
                    ->exists();

            if ($activeEmailConflict) {
                return $this->failedResponse('Email đã tồn tại.', HttpStatus::UNPROCESSABLE_ENTITY);
            }

            if ($activeUsernameConflict) {
                return $this->failedResponse('Tên đăng nhập đã tồn tại.', HttpStatus::UNPROCESSABLE_ENTITY);
            }

            $user = DB::transaction(function () use ($existing, $data, &$needSendVerify) {
                $existing->restore();

                $newEmail = $data['email'] ?? null;

                $existing->fill([
                    'isactive' => $data['isactive'] ?? 'Y',
                    'username' => $data['username'],
                    'email' => $newEmail,
                    'password' => $data['password'],
                    'locale' => $data['locale'] ?? null,
                    'remark' => $data['remark'] ?? null,
                ]);

                // reset verify
                $existing->email_verified_at = null;
                $needSendVerify = !empty($newEmail);

                $existing->save();

                $existing->profile()->updateOrCreate(
                    ['user_id' => $existing->id],
                    [
                        'isactive' => $data['isactive'] ?? 'Y',
                        'full_name' => $data['full_name'] ?? null,
                        'phone_number' => $data['phone_number'] ?? null,
                        'avatar_url' => $data['avatar_url'] ?? null,
                        'address' => $data['address'] ?? null,
                        'zalo' => $data['zalo'] ?? null,
                        'facebook' => $data['facebook'] ?? null,
                        'user_type' => $data['user_type'],
                        'remark' => $data['profile_remark'] ?? null,
                    ]
                );

                // ===== SYNC permissions theo role_permissions.user_type =====
                // sync() sẽ tự insert/delete (và update pivot nếu có field phụ)
                $permissionIds = RolePermissions::where('user_type', $data['user_type'])
                    ->pluck('permission_id')
                    ->all();
                $existing->permissions()->sync($permissionIds);

                return $existing->load('profile');
            });

            if ($needSendVerify) {
                $user->sendEmailVerificationNotification();
            }

            return $this->successResponse(
                $user->toArray(),
                'Khôi phục và cập nhật người dùng thành công. Email xác thực đã được gửi.',
                HttpStatus::OK
            );
        }

        // ===== CREATE MỚI =====
        $activeEmailExists = !empty($data['email'])
            && User::whereNull('deleted_at')->where('email', $data['email'])->exists();

        $activeUsernameExists =
            User::whereNull('deleted_at')->where('username', $data['username'])->exists();

        if ($activeEmailExists) {
            return $this->failedResponse('Email đã tồn tại.', HttpStatus::UNPROCESSABLE_ENTITY);
        }

        if ($activeUsernameExists) {
            return $this->failedResponse('Tên đăng nhập đã tồn tại.', HttpStatus::UNPROCESSABLE_ENTITY);
        }

        $user = DB::transaction(function () use ($data, &$needSendVerify) {
            $user = User::create([
                'isactive' => $data['isactive'] ?? 'Y',
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'password' => $data['password'],
                'locale' => $data['locale'] ?? null,
                'remark' => $data['remark'] ?? null,
            ]);

            $user->profile()->create([
                'isactive' => $data['isactive'] ?? 'Y',
                'full_name' => $data['full_name'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'avatar_url' => $data['avatar_url'] ?? null,
                'address' => $data['address'] ?? null,
                'zalo' => $data['zalo'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'user_type' => $data['user_type'],
                'remark' => $data['profile_remark'] ?? null,
            ]);

            $needSendVerify = !empty($data['email']);

            // ===== SYNC permissions theo role_permissions.user_type =====
            $permissionIds = RolePermissions::where('user_type', $data['user_type'])
                ->pluck('permission_id')
                ->all();
            $user->permissions()->sync($permissionIds);

            return $user->load('profile');
        });

        if ($needSendVerify) {
            $user->sendEmailVerificationNotification();
        }

        return $this->successResponse(
            $user->toArray(),
            'Tạo người dùng thành công. Email xác thực đã được gửi.',
            HttpStatus::CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
//    public function update(Request $request, $id)
//    {
//        $user = User::with('profile')->findOrFail($id);
//
//        $data = $request->validate([
//            'isactive' => ['nullable', 'in:Y,N'],
//            'username' => [
//                'required', 'string', 'max:255',
//                Rule::unique('users', 'username')
//                    ->ignore($user->id)
//                    ->whereNull('deleted_at'),
//            ],
//            'email' => [
//                'nullable', 'email', 'max:255',
//                Rule::unique('users', 'email')
//                    ->ignore($user->id)
//                    ->whereNull('deleted_at'),
//            ],
//            'password' => ['nullable', 'string', 'min:6'], // update thì optional
//            'locale' => ['nullable', 'string', 'max:10'],
//            'remark' => ['nullable', 'string'],
//
//            'full_name' => ['nullable', 'string', 'max:255'],
//            'phone_number' => ['nullable', 'string', 'max:50'],
//            'avatar_url' => ['nullable', 'string'],
//            'address' => ['nullable', 'string'],
//            'zalo' => ['nullable', 'string'],
//            'facebook' => ['nullable', 'string'],
//            'user_type' => ['required', Rule::in(UserType::values())],
//            'profile_remark' => ['nullable', 'string'],
//        ]);
//
//        $updated = DB::transaction(function () use ($user, $data) {
//            $user->fill([
//                'isactive' => $data['isactive'] ?? $user->isactive,
//                'username' => $data['username'],
//                'email' => $data['email'] ?? null,
//                'locale' => $data['locale'] ?? null,
//                'remark' => $data['remark'] ?? null,
//            ]);
//
//            if (!empty($data['password'])) {
//                $user->password = $data['password'];
//            }
//
//            $user->save();
//
//            $user->profile()->updateOrCreate(
//                ['user_id' => $user->id],
//                [
//                    'isactive' => $data['isactive'] ?? 'Y',
//                    'full_name' => $data['full_name'] ?? null,
//                    'phone_number' => $data['phone_number'] ?? null,
//                    'avatar_url' => $data['avatar_url'] ?? null,
//                    'address' => $data['address'] ?? null,
//                    'zalo' => $data['zalo'] ?? null,
//                    'facebook' => $data['facebook'] ?? null,
//                    'user_type' => $data['user_type'],
//                    'remark' => $data['profile_remark'] ?? null,
//                ]
//            );
//
//            return $user->load('profile');
//        });
//
//        return $this->successResponse($updated->toArray(), 'User updated successfully', HttpStatus::OK);
//    }

    public function update(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

        // lấy user_type cũ (có thể là Enum hoặc string)
        $oldUserType = $user->profile?->user_type?->value ?? $user->profile?->user_type;

        $data = $request->validate([
            'isactive' => ['nullable', 'in:Y,N'],
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique('users', 'username')
                    ->ignore($user->id)
                    ->whereNull('deleted_at'),
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id)
                    ->whereNull('deleted_at'),
            ],
            'password' => ['nullable', 'string', 'min:6'], // update thì optional
            'locale' => ['nullable', 'string', 'max:10'],
            'remark' => ['nullable', 'string'],

            'full_name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'avatar_url' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'zalo' => ['nullable', 'string'],
            'facebook' => ['nullable', 'string'],
            'user_type' => ['required', Rule::in(UserType::values())],
            'profile_remark' => ['nullable', 'string'],
        ]);

        $updated = DB::transaction(function () use ($user, $data, $oldUserType) {
            $user->fill([
                'isactive' => $data['isactive'] ?? $user->isactive,
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'locale' => $data['locale'] ?? null,
                'remark' => $data['remark'] ?? null,
            ]);

            if (!empty($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'isactive' => $data['isactive'] ?? 'Y',
                    'full_name' => $data['full_name'] ?? null,
                    'phone_number' => $data['phone_number'] ?? null,
                    'avatar_url' => $data['avatar_url'] ?? null,
                    'address' => $data['address'] ?? null,
                    'zalo' => $data['zalo'] ?? null,
                    'facebook' => $data['facebook'] ?? null,
                    'user_type' => $data['user_type'],
                    'remark' => $data['profile_remark'] ?? null,
                ]
            );

            // ===== Nếu user_type thay đổi -> SYNC permissions theo role_permissions =====
            if (($oldUserType ?? null) !== ($data['user_type'] ?? null)) {
                $permissionIds = RolePermissions::where('user_type', $data['user_type'])
                    ->pluck('permission_id')
                    ->all();

                $user->permissions()->sync($permissionIds);
            }

            return $user->load('profile');
        });

        return $this->successResponse($updated->toArray(), 'Cập nhật người dùng thành công', HttpStatus::OK);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->userService->delete($id);
        return $this->successResponse([], 'Xóa người dùng thành công', HttpStatus::OK);
    }

    public function getPermissions(string $id): JsonResponse
    {
        $data = $this->userService->getPermissions($id);
        return $this->successResponse($data, 'Lấy danh sách người dùng thành công', HttpStatus::OK);
    }

    public function syncPermissions(Request $request, string $id): JsonResponse
    {
        $data = $this->userService->syncPermissions($id, $request->input('permission_ids', []));
        return $this->successResponse($data, 'Lấy chi tiết người dùng thành công', HttpStatus::OK);
    }
}
