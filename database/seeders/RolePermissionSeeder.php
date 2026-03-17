<?php

namespace Database\Seeders;

use App\Common\Enums\UserType;
use App\Models\Permission;
use App\Models\RolePermissions;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminCodes = [
            'org.permissions',
            'org.users',
            'org.user-profiles',
            'org.user-permissions',
            'org.employees',
            'org.categories',
            'org.post-types',
            'org.sliders',
            'org.room-moderation',
            'org.contacts',
            'org.bookings',
            'org.reviews',
        ];

        $landlordCodes = [];

        $this->syncPermissionsForRole(UserType::ADMIN->value, $adminCodes);
        $this->syncPermissionsForRole(UserType::LANDLORD->value, $landlordCodes);
    }

    private function syncPermissionsForRole(string $role, array $codes): void
    {
        $permissionIds = Permission::query()
            ->whereIn('code', $codes)
            ->pluck('id')
            ->all();

        RolePermissions::query()->where('user_type', $role)->delete();

        foreach ($permissionIds as $permissionId) {
            RolePermissions::query()->create([
                'user_type' => $role,
                'permission_id' => $permissionId,
            ]);
        }
    }
}
