<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\UserProfileServiceInterface;
use App\Models\UserProfiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function __construct(protected UserProfileServiceInterface $userProfileService)
    {
    }

    public function index()
    {
        $data = $this->userProfileService->getAll();

        return $this->successResponse(
            $data->toArray(),
            'messages.profile.list_success',
            HttpStatus::OK
        );
    }

    public function store(Request $request)
    {
        $data = $this->userProfileService->create($request->all());

        return $this->successResponse($data->toArray(), 'messages.profile.create_success', HttpStatus::CREATED);
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $data = $this->userProfileService->update($id, $request->all());

        return $this->successResponse($data->toArray(), 'messages.profile.update_success', HttpStatus::OK);
    }

    public function destroy(string $id)
    {
        $this->userProfileService->delete($id);

        return $this->successResponse([], 'messages.profile.delete_success', HttpStatus::NO_CONTENT);
    }

    public function me()
    {
        $user = auth('api')->user();
        $user->load('profile');

        return $this->successResponse([
            'user' => $user->only(['id', 'username', 'email', 'locale', 'isactive']),
            'profile' => $user->profile,
        ], 'messages.profile.me_success', HttpStatus::OK);
    }

    public function updateMe(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'avatar_url' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:500'],
            'zalo' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
            'locale' => ['nullable', 'string', 'max:10'],
        ]);

        if (array_key_exists('locale', $data)) {
            $user->locale = $data['locale'];
            $user->save();
        }

        $profile = UserProfiles::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $data['full_name'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'avatar_url' => $data['avatar_url'] ?? null,
                'address' => $data['address'] ?? null,
                'zalo' => $data['zalo'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'remark' => $data['remark'] ?? null,
            ]
        );

        return $this->successResponse([
            'user' => $user->only(['id', 'username', 'email', 'locale', 'isactive']),
            'profile' => $profile,
        ], 'messages.profile.update_me_success', HttpStatus::OK);
    }

    public function changeMyPassword(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return $this->failedResponse('auth.current_password_incorrect', 422);
        }

        $user->password = $data['password'];
        $user->save();

        return $this->successResponse([], 'messages.profile.change_password_success', HttpStatus::OK);
    }
}
