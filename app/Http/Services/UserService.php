<?php

namespace App\Http\Services;

use App\Common\Traits\ApiResponseTrait;
use App\Http\_base\BaseService;
use App\Http\Interfaces\UserServiceInterface;
use App\Models\User;

class UserService extends BaseService implements UserServiceInterface
{
    use ApiResponseTrait;

    protected function getModel(): string
    {
        // TODO: Implement getModel() method.
        return User::class;
    }

    public function getPermissions(string $id): array
    {
        try {
            $user = User::with('permissions:id,code,name,parent_id')->findOrFail((int)$id);

            return [
                'user_id' => $user->id,
                'permission_ids' => $user->permissions->pluck('id')->values(),
                'permissions' => $user->permissions
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function syncPermissions(string $id, array $permission_ids): array
    {
        try {
            $user = User::findOrFail((int)$id);

            $user->permissions()->sync($permission_ids);

            $user->load('permissions:id,code,name,parent_id');

            return [
                'user_id' => $user->id,
                'permission_ids' => $user->permissions->pluck('id')->values()
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
