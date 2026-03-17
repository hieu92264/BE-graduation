<?php

namespace Database\Seeders;

use App\Common\Enums\UserType;
use App\Common\Enums\WorkStatus;
use App\Models\Employee;
use App\Models\RolePermissions;
use App\Models\User;
use App\Models\UserProfiles;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Quản trị hệ thống',
                    'phone_number' => '0900000001',
                    'address' => 'TP. Hồ Chí Minh',
                    'user_type' => UserType::ADMIN->value,
                    'zalo' => '0900000001',
                    'facebook' => 'https://facebook.com/admin.trotot',
                    'remark' => 'Tài khoản admin mặc định',
                ],
            ],
            [
                'username' => 'staff01',
                'email' => 'staff01@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Nhân viên vận hành 01',
                    'phone_number' => '0900000002',
                    'address' => 'TP. Hồ Chí Minh',
                    'user_type' => UserType::ADMIN->value,
                    'zalo' => '0900000002',
                    'facebook' => null,
                    'remark' => 'Nhân viên quản trị nội dung',
                ],
                'employee' => [
                    'employee_code' => 'EMP001',
                    'full_name' => 'Nhân viên vận hành 01',
                    'phone' => '0900000002',
                    'email' => 'staff01@trotot.vn',
                    'status' => WorkStatus::OFFICIAL->value,
                    'join_date' => now()->subMonths(8)->toDateString(),
                ],
            ],
            [
                'username' => 'staff02',
                'email' => 'staff02@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Nhân viên chăm sóc khách hàng',
                    'phone_number' => '0900000003',
                    'address' => 'Hà Nội',
                    'user_type' => UserType::ADMIN->value,
                    'zalo' => '0900000003',
                    'facebook' => null,
                    'remark' => 'Nhân viên hỗ trợ xử lý contact và lead',
                ],
                'employee' => [
                    'employee_code' => 'EMP002',
                    'full_name' => 'Nhân viên chăm sóc khách hàng',
                    'phone' => '0900000003',
                    'email' => 'staff02@trotot.vn',
                    'status' => WorkStatus::OFFICIAL->value,
                    'join_date' => now()->subMonths(5)->toDateString(),
                ],
            ],
            [
                'username' => 'landlord01',
                'email' => 'landlord01@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Nguyễn Văn Chủ Trọ 1',
                    'phone_number' => '0911111111',
                    'address' => 'Quận 7, TP. Hồ Chí Minh',
                    'user_type' => UserType::LANDLORD->value,
                    'zalo' => '0911111111',
                    'facebook' => 'https://facebook.com/landlord01',
                    'remark' => 'Chủ nhà có nhiều phòng trọ sinh viên',
                ],
            ],
            [
                'username' => 'landlord02',
                'email' => 'landlord02@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Trần Thị Chủ Trọ 2',
                    'phone_number' => '0922222222',
                    'address' => 'Cầu Giấy, Hà Nội',
                    'user_type' => UserType::LANDLORD->value,
                    'zalo' => '0922222222',
                    'facebook' => 'https://facebook.com/landlord02',
                    'remark' => 'Chủ nhà cho thuê phòng khép kín và studio',
                ],
            ],
            [
                'username' => 'tenant01',
                'email' => 'tenant01@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Lê Văn Người Thuê 1',
                    'phone_number' => '0933333333',
                    'address' => 'Thủ Đức, TP. Hồ Chí Minh',
                    'user_type' => UserType::TENANT->value,
                    'zalo' => '0933333333',
                    'facebook' => null,
                    'remark' => 'Sinh viên tìm phòng trọ gần trường',
                ],
            ],
            [
                'username' => 'tenant02',
                'email' => 'tenant02@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Phạm Thị Người Thuê 2',
                    'phone_number' => '0944444444',
                    'address' => 'Thanh Xuân, Hà Nội',
                    'user_type' => UserType::TENANT->value,
                    'zalo' => '0944444444',
                    'facebook' => null,
                    'remark' => 'Người đi làm tìm căn hộ mini',
                ],
            ],
            [
                'username' => 'tenant03',
                'email' => 'tenant03@trotot.vn',
                'password' => '12345678',
                'profile' => [
                    'full_name' => 'Đỗ Minh Thuê 3',
                    'phone_number' => '0955555555',
                    'address' => 'Hải Châu, Đà Nẵng',
                    'user_type' => UserType::TENANT->value,
                    'zalo' => '0955555555',
                    'facebook' => null,
                    'remark' => 'Khách thuê phòng trung tâm thành phố',
                ],
            ],
        ];

        foreach ($users as $item) {
            $user = User::query()->updateOrCreate(
                ['username' => $item['username']],
                [
                    'isactive' => 'Y',
                    'email' => $item['email'],
                    'password' => $item['password'],
                    'locale' => 'vi',
                    'remark' => $item['profile']['remark'] ?? null,
                    'email_verified_at' => now(),
                    'user_name_created' => 'seeder',
                    'user_name_updated' => 'seeder',
                ]
            );

            UserProfiles::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'isactive' => 'Y',
                    'full_name' => $item['profile']['full_name'],
                    'phone_number' => $item['profile']['phone_number'],
                    'avatar_url' => null,
                    'address' => $item['profile']['address'],
                    'zalo' => $item['profile']['zalo'],
                    'facebook' => $item['profile']['facebook'],
                    'user_type' => $item['profile']['user_type'],
                    'remark' => $item['profile']['remark'],
                    'user_name_created' => 'seeder',
                    'user_name_updated' => 'seeder',
                ]
            );

            $permissionIds = RolePermissions::query()
                ->where('user_type', $item['profile']['user_type'])
                ->pluck('permission_id')
                ->all();

            $user->permissions()->sync($permissionIds);

            if (! empty($item['employee'])) {
                Employee::query()->updateOrCreate(
                    ['employee_code' => $item['employee']['employee_code']],
                    [
                        'isactive' => 'Y',
                        'user_id' => $user->id,
                        'full_name' => $item['employee']['full_name'],
                        'phone' => $item['employee']['phone'],
                        'email' => $item['employee']['email'],
                        'dob' => now()->subYears(25)->toDateString(),
                        'avatar_url' => null,
                        'status' => $item['employee']['status'],
                        'join_date' => $item['employee']['join_date'],
                        'terminate_date' => null,
                        'remark' => 'Seeder employee',
                        'user_name_created' => 'seeder',
                        'user_name_updated' => 'seeder',
                    ]
                );
            }
        }
    }
}
