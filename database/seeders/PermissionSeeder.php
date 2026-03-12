<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            [
                'code' => 'org',
                'name' => 'Quản lý tổ chức',
                'url' => null,
                'parent_id' => null,
                'isactive' => 'Y',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
            [
                'code' => 'org.master-data',
                'name' => 'Master data',
                'url' => null,
                'parent_id' => null,
                'isactive' => 'Y',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
            [
                'code' => 'org.content',
                'name' => 'Nội dung',
                'url' => null,
                'parent_id' => null,
                'isactive' => 'Y',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
        ];

        foreach ($roots as $row) {
            Permission::query()->updateOrCreate(
                ['code' => $row['code']],
                $row
            );
        }

        $orgId = Permission::query()->where('code', 'org')->value('id');
        $masterDataId = Permission::query()->where('code', 'org.master-data')->value('id');
        $contentId = Permission::query()->where('code', 'org.content')->value('id');

        $children = [
            [
                'code' => 'org.permissions',
                'name' => 'Quản lý quyền',
                'url' => '/organizations/permissions',
                'parent_id' => $orgId,
            ],
            [
                'code' => 'org.users',
                'name' => 'Quản lý user',
                'url' => '/organizations/user',
                'parent_id' => $orgId,
            ],
            [
                'code' => 'org.user-profiles',
                'name' => 'Quản lý hồ sơ user',
                'url' => null,
                'parent_id' => $orgId,
            ],
            [
                'code' => 'org.user-permissions',
                'name' => 'Gán quyền user',
                'url' => '/organizations/user-permissions',
                'parent_id' => $orgId,
            ],
            [
                'code' => 'org.employees',
                'name' => 'Quản lý nhân viên',
                'url' => '/organizations/employees',
                'parent_id' => $orgId,
            ],
            [
                'code' => 'org.categories',
                'name' => 'Quản lý danh mục',
                'url' => '/organizations/categories',
                'parent_id' => $masterDataId,
            ],
            [
                'code' => 'org.post-types',
                'name' => 'Quản lý loại tin',
                'url' => null,
                'parent_id' => $masterDataId,
            ],
            [
                'code' => 'org.sliders',
                'name' => 'Quản lý slider',
                'url' => '/organizations/sliders',
                'parent_id' => $contentId,
            ],
        ];

        foreach ($children as $row) {
            Permission::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'isactive' => 'Y',
                    'name' => $row['name'],
                    'url' => $row['url'],
                    'parent_id' => $row['parent_id'],
                    'user_name_created' => 'seeder',
                    'user_name_updated' => 'seeder',
                ]
            );
        }
    }
}
