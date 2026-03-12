<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCmsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('username', 'admin')->first();

        $news = [
            [
                'title' => 'Kinh nghiệm tìm phòng trọ an toàn cho sinh viên mới lên thành phố',
                'status' => 'published',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop',
                'content' => 'Nên kiểm tra hợp đồng, điện nước, giờ giấc, an ninh và khoảng cách đến trường hoặc nơi làm việc trước khi quyết định thuê.',
            ],
            [
                'title' => '5 tiêu chí giúp chủ nhà đăng tin cho thuê hiệu quả hơn',
                'status' => 'published',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1200&auto=format&fit=crop',
                'content' => 'Ảnh đẹp, mô tả rõ ràng, thông tin giá minh bạch, vị trí cụ thể và phản hồi nhanh sẽ giúp tỷ lệ chốt khách tốt hơn.',
            ],
            [
                'title' => 'Xu hướng thuê căn hộ mini và studio cho người đi làm trẻ',
                'status' => 'draft',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?q=80&w=1200&auto=format&fit=crop',
                'content' => 'Nhu cầu thuê studio tăng mạnh ở khu vực trung tâm vì phù hợp với người độc thân và cặp đôi trẻ.',
            ],
        ];

        foreach ($news as $item) {
            Content::query()->updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'isactive' => 'Y',
                    'author_user_id' => $admin?->id,
                    'title' => $item['title'],
                    'thumbnail_url' => $item['thumbnail_url'],
                    'content' => $item['content'],
                    'status' => $item['status'],
                    'published_at' => $item['status'] === 'published' ? now()->subDays(rand(1, 10)) : null,
                    'remark' => 'Seeder news',
                    'user_name_created' => 'seeder',
                    'user_name_updated' => 'seeder',
                ]
            );
        }

        $sliders = [
            [
                'title' => 'Tìm phòng trọ phù hợp chỉ trong vài phút',
                'image_url' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=1600&auto=format&fit=crop',
                'link_url' => '/rooms',
                'sort_order' => 1,
            ],
            [
                'title' => 'Tin nổi bật cho chủ nhà cần cho thuê nhanh',
                'image_url' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?q=80&w=1600&auto=format&fit=crop',
                'link_url' => '/rooms?sort=latest',
                'sort_order' => 2,
            ],
            [
                'title' => 'Khám phá phòng trọ, studio, nhà nguyên căn',
                'image_url' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?q=80&w=1600&auto=format&fit=crop',
                'link_url' => '/rooms/featured',
                'sort_order' => 3,
            ],
        ];

        foreach ($sliders as $item) {
            Slider::query()->updateOrCreate(
                ['sort_order' => $item['sort_order']],
                [
                    'isactive' => 'Y',
                    'title' => $item['title'],
                    'image_url' => $item['image_url'],
                    'link_url' => $item['link_url'],
                    'remark' => 'Seeder slider',
                ]
            );
        }
    }
}
