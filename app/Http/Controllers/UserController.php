<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Common\Enums\UserType;
use App\Http\Interfaces\UserServiceInterface;
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'isactive' => ['nullable', 'in:Y,N'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
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

        $user = DB::transaction(function () use ($data) {
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

            return $user->load('profile');
        });

        return $this->successResponse($user->toArray(), 'User created successfully', HttpStatus::CREATED);
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
    public function update(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

        $data = $request->validate([
            'isactive' => ['nullable', 'in:Y,N'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
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

        $updated = DB::transaction(function () use ($user, $data) {
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

            return $user->load('profile');
        });

        return $this->successResponse($updated->toArray(), 'User updated successfully', HttpStatus::OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->userService->delete($id);
        return $this->successResponse([], 'Deleted successfully', HttpStatus::OK);
    }

    public function getPermissions(string $id): JsonResponse
    {
        $data = $this->userService->getPermissions($id);
        return $this->successResponse($data, 'Success', HttpStatus::OK);
    }

    public function syncPermissions(Request $request, string $id): JsonResponse
    {
        $data = $this->userService->syncPermissions($id, $request->input('permission_ids', []));
        return $this->successResponse($data, 'Success', HttpStatus::OK);
    }
}
