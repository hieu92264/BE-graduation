<?php

namespace Database\Seeders;

use App\Models\PostType;
use Illuminate\Database\Seeder;

class PostTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'isactive' => 'Y',
                'code' => 'THUONG',
                'name' => 'Tin thường',
                'priority' => 0,
                'default_days' => 30,
                'price' => 0,
                'remark' => 'Tin đăng cơ bản, hiển thị theo thời gian tạo mới nhất.',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
            [
                'isactive' => 'Y',
                'code' => 'TIET_KIEM',
                'name' => 'Tin tiết kiệm',
                'priority' => 1,
                'default_days' => 15,
                'price' => 10000,
                'remark' => 'Gói đăng giá rẻ cho chủ trọ cần tiếp cận cơ bản.',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
            [
                'isactive' => 'Y',
                'code' => 'NOI_BAT',
                'name' => 'Tin nổi bật',
                'priority' => 5,
                'default_days' => 7,
                'price' => 30000,
                'remark' => 'Ưu tiên hiển thị cao hơn ở trang chủ và danh sách tìm kiếm.',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
            [
                'isactive' => 'Y',
                'code' => 'VIP',
                'name' => 'Tin VIP',
                'priority' => 10,
                'default_days' => 7,
                'price' => 50000,
                'remark' => 'Ưu tiên hiển thị cao, phù hợp phòng đẹp, vị trí tốt, cần cho thuê nhanh.',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
            [
                'isactive' => 'Y',
                'code' => 'SIEU_VIP',
                'name' => 'Tin siêu VIP',
                'priority' => 20,
                'default_days' => 5,
                'price' => 80000,
                'remark' => 'Ưu tiên hiển thị cao nhất, dùng cho bài đăng cần nổi bật mạnh.',
                'user_name_created' => 'seeder',
                'user_name_updated' => 'seeder',
            ],
        ];

        foreach ($rows as $row) {
            PostType::query()->updateOrCreate(
                ['code' => $row['code']],
                $row
            );
        }
    }
}
