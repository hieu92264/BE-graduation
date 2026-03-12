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
        ];

        $landlordCodes = [
            // hiện tại landlord module chưa check.permission
            // để trống vẫn đúng với code hiện tại
        ];

        $tenantCodes = [
            // tenant không có quyền admin
        ];

        $map = [
            UserType::ADMIN->value => $adminCodes,
            UserType::LANDLORD->value => $landlordCodes,
            UserType::TENANT->value => $tenantCodes,
        ];

        foreach ($map as $userType => $codes) {
            $permissionIds = Permission::query()
                ->whereIn('code', $codes)
                ->pluck('id')
                ->all();

            foreach ($permissionIds as $permissionId) {
                RolePermissions::query()->updateOrCreate(
                    [
                        'user_type' => $userType,
                        'permission_id' => $permissionId,
                    ],
                    []
                );
            }
        }
    }
}
