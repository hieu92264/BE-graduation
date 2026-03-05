<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'code' => 'PHONG_TRO',
                'name' => 'Phòng trọ',
                'slug' => 'phong-tro',
                'sort_order' => 1,
                'isactive' => 'Y',
                'remark' => 'Phòng trọ truyền thống, phòng khép kín, phòng có gác…',
            ],
            [
                'code' => 'NHA_NGUYEN_CAN',
                'name' => 'Nhà nguyên căn',
                'slug' => 'nha-nguyen-can',
                'sort_order' => 2,
                'isactive' => 'Y',
                'remark' => 'Nhà thuê nguyên căn (1–nhiều tầng).',
            ],
            [
                'code' => 'CAN_HO_CHUNG_CU',
                'name' => 'Căn hộ / Chung cư',
                'slug' => 'can-ho-chung-cu',
                'sort_order' => 3,
                'isactive' => 'Y',
                'remark' => 'Căn hộ chung cư (1PN, 2PN…).',
            ],
            [
                'code' => 'STUDIO_MINI',
                'name' => 'Căn hộ mini / Studio',
                'slug' => 'can-ho-mini-studio',
                'sort_order' => 4,
                'isactive' => 'Y',
                'remark' => 'Studio/mini apartment, thường có bếp/khép kín.',
            ],
            [
                'code' => 'O_GHEP',
                'name' => 'Ở ghép',
                'slug' => 'o-ghep',
                'sort_order' => 5,
                'isactive' => 'Y',
                'remark' => 'Tìm người ở chung / share phòng.',
            ],
            [
                'code' => 'KY_TUC_XA',
                'name' => 'Ký túc xá / Dorm',
                'slug' => 'ky-tuc-xa-dorm',
                'sort_order' => 6,
                'isactive' => 'Y',
                'remark' => 'Ký túc xá, phòng dorm (nhiều giường).',
            ],
        ];

        // Upsert theo code để chạy lại không bị nhân đôi
        Category::upsert(
            $rows,
            ['code'],
            ['name', 'slug', 'sort_order', 'isactive', 'remark', 'updated_at']
        );
    }
}
