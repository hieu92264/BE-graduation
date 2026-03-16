<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\City;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Contact;
use App\Models\District;
use App\Models\PostType;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoRoomSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('username', 'admin')->first();

        $landlord1 = User::query()->where('username', 'landlord01')->firstOrFail();
        $landlord2 = User::query()->where('username', 'landlord02')->firstOrFail();

        $tenant1 = User::query()->where('username', 'tenant01')->firstOrFail();
        $tenant2 = User::query()->where('username', 'tenant02')->firstOrFail();
        $tenant3 = User::query()->where('username', 'tenant03')->firstOrFail();

        $phongTro = Category::query()->where('code', 'PHONG_TRO')->first();
        $studio = Category::query()->where('code', 'STUDIO_MINI')->first();
        $nhaNguyenCan = Category::query()->where('code', 'NHA_NGUYEN_CAN')->first();

        $thuong = PostType::query()->where('code', 'THUONG')->first();
        $noiBat = PostType::query()->where('code', 'NOI_BAT')->first();
        $vip = PostType::query()->where('code', 'VIP')->first();

        $hcm = City::query()->where('name', 'like', '%Hồ Chí Minh%')->first() ?? City::query()->first();
        $hn = City::query()->where('name', 'like', '%Hà Nội%')->first() ?? City::query()->skip(1)->first() ?? City::query()->first();
        $dn = City::query()->where('name', 'like', '%Đà Nẵng%')->first() ?? City::query()->skip(2)->first() ?? City::query()->first();

        $hcmDistrict = District::query()->where('city_id', $hcm?->id)->first() ?? District::query()->first();
        $hnDistrict = District::query()->where('city_id', $hn?->id)->first() ?? District::query()->skip(1)->first() ?? District::query()->first();
        $dnDistrict = District::query()->where('city_id', $dn?->id)->first() ?? District::query()->skip(2)->first() ?? District::query()->first();

        $hcmWard = Ward::query()->where('district_id', $hcmDistrict?->id)->first() ?? Ward::query()->first();
        $hnWard = Ward::query()->where('district_id', $hnDistrict?->id)->first() ?? Ward::query()->skip(1)->first() ?? Ward::query()->first();
        $dnWard = Ward::query()->where('district_id', $dnDistrict?->id)->first() ?? Ward::query()->skip(2)->first() ?? Ward::query()->first();

        $rooms = [
            [
                'owner_user_id' => $landlord1->id,
                'category_id' => $phongTro?->id,
                'post_type_id' => $vip?->id,
                'city_id' => $hcm?->id,
                'district_id' => $hcmDistrict?->id,
                'ward_id' => $hcmWard?->id,
                'title' => 'Phòng trọ full nội thất gần ĐH Tôn Đức Thắng',
                'address' => '123 Nguyễn Hữu Thọ',
                'price' => 3800000,
                'area' => 22,
                'description' => 'Phòng sạch đẹp, có máy lạnh, giờ giấc tự do, gần trường đại học và siêu thị.',
                'booking_status' => 'available',
                'post_status' => 'approved',
                'isactive' => 'Y',
            ],
            [
                'owner_user_id' => $landlord1->id,
                'category_id' => $phongTro?->id,
                'post_type_id' => $noiBat?->id,
                'city_id' => $hcm?->id,
                'district_id' => $hcmDistrict?->id,
                'ward_id' => $hcmWard?->id,
                'title' => 'Phòng khép kín giá tốt cho sinh viên quận 7',
                'address' => '55 Trần Xuân Soạn',
                'price' => 2900000,
                'area' => 18,
                'description' => 'Phòng có gác, WC riêng, phù hợp sinh viên hoặc người đi làm.',
                'booking_status' => 'occupied',
                'post_status' => 'approved',
                'isactive' => 'Y',
            ],
            [
                'owner_user_id' => $landlord1->id,
                'category_id' => $studio?->id,
                'post_type_id' => $thuong?->id,
                'city_id' => $hcm?->id,
                'district_id' => $hcmDistrict?->id,
                'ward_id' => $hcmWard?->id,
                'title' => 'Studio mới xây, có bếp riêng, cửa sổ lớn',
                'address' => '88 Lê Văn Lương',
                'price' => 5200000,
                'area' => 28,
                'description' => 'Căn hộ mini phù hợp 1-2 người, an ninh tốt, có chỗ để xe.',
                'booking_status' => 'pending',
                'post_status' => 'pending',
                'isactive' => 'Y',
            ],
            [
                'owner_user_id' => $landlord2->id,
                'category_id' => $studio?->id,
                'post_type_id' => $vip?->id,
                'city_id' => $hn?->id,
                'district_id' => $hnDistrict?->id,
                'ward_id' => $hnWard?->id,
                'title' => 'Căn hộ mini gần Cầu Giấy, full đồ, vào ở ngay',
                'address' => '21 Duy Tân',
                'price' => 5600000,
                'area' => 30,
                'description' => 'Có máy giặt, nóng lạnh, điều hòa, khu vực đông dân cư và tiện đi lại.',
                'booking_status' => 'available',
                'post_status' => 'pending',
                'isactive' => 'Y',
            ],
            [
                'owner_user_id' => $landlord2->id,
                'category_id' => $nhaNguyenCan?->id,
                'post_type_id' => $noiBat?->id,
                'city_id' => $hn?->id,
                'district_id' => $hnDistrict?->id,
                'ward_id' => $hnWard?->id,
                'title' => 'Nhà nguyên căn 2 tầng phù hợp nhóm bạn thuê',
                'address' => '9 Nguyễn Trãi',
                'price' => 12000000,
                'area' => 65,
                'description' => 'Nhà riêng đủ nội thất cơ bản, thích hợp hộ gia đình hoặc nhóm bạn.',
                'booking_status' => 'confirmed',
                'post_status' => 'rejected',
                'isactive' => 'Y',
            ],
            [
                'owner_user_id' => $landlord2->id,
                'category_id' => $phongTro?->id,
                'post_type_id' => $thuong?->id,
                'city_id' => $dn?->id,
                'district_id' => $dnDistrict?->id,
                'ward_id' => $dnWard?->id,
                'title' => 'Phòng trọ trung tâm Đà Nẵng, gần cầu Rồng',
                'address' => '15 Trần Phú',
                'price' => 3200000,
                'area' => 20,
                'description' => 'Phòng đẹp, thoáng, gần trung tâm, phù hợp người đi làm.',
                'booking_status' => 'available',
                'post_status' => 'hidden',
                'isactive' => 'N',
            ],
        ];

        $photoSets = [
            [
                'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1484154218962-a197022b5858?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1494526585095-c41746248156?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1502672023488-70e25813eb80?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'https://images.unsplash.com/photo-1494526585095-c41746248156?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1484154218962-a197022b5858?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1200&auto=format&fit=crop',
            ],
        ];

        $savedRooms = [];

        foreach ($rooms as $index => $item) {
            $slug = Str::slug($item['title']);

            $room = Room::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'isactive' => $item['isactive'],
                    'owner_user_id' => $item['owner_user_id'],
                    'category_id' => $item['category_id'],
                    'post_type_id' => $item['post_type_id'],
                    'city_id' => $item['city_id'],
                    'district_id' => $item['district_id'],
                    'ward_id' => $item['ward_id'],
                    'title' => $item['title'],
                    'address' => $item['address'],
                    'price' => $item['price'],
                    'area' => $item['area'],
                    'description' => $item['description'],
                    'booking_status' => $item['booking_status'],
                    'post_status' => $item['post_status'],
                    'moderation_note' => match ($item['post_status']) {
                        'approved' => 'Tin hợp lệ, thông tin rõ ràng.',
                        'rejected' => 'Cần bổ sung thông tin và hình ảnh rõ hơn trước khi đăng.',
                        'hidden' => 'Tin tạm ẩn do chủ nhà chưa cập nhật lại thông tin.',
                        default => null,
                    },
                    'moderated_at' => in_array($item['post_status'], ['approved', 'rejected', 'hidden'])
                        ? now()->subDays(rand(1, 10))
                        : null,
                ]
            );

            $room->photos()->delete();

            $set = $photoSets[$index % count($photoSets)];
            foreach ($set as $photoIndex => $url) {
                RoomPhoto::query()->create([
                    'room_id' => $room->id,
                    'photo_url' => $url,
                    'is_cover' => $photoIndex === 0,
                    'sort_order' => $photoIndex,
                ]);
            }

            $savedRooms[$index] = $room;
        }

        $room1 = $savedRooms[0] ?? null;
        $room2 = $savedRooms[1] ?? null;
        $room3 = $savedRooms[2] ?? null;
        $room4 = $savedRooms[3] ?? null;
        $room5 = $savedRooms[4] ?? null;
        $room6 = $savedRooms[5] ?? null;

        if ($room1) {
            $comment1 = Comment::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'user_id' => $tenant1->id,
                    'content' => 'Phòng sạch sẽ, chủ nhà hỗ trợ nhiệt tình.',
                ],
                [
                    'rating' => 5,
                    'status' => 'visible',
                ]
            );

            CommentReply::query()->updateOrCreate(
                [
                    'comment_id' => $comment1->id,
                    'user_id' => $landlord1->id,
                    'content' => 'Cảm ơn bạn đã quan tâm, bên mình luôn hỗ trợ khách thuê tốt nhất.',
                ],
                [
                    'status' => 'visible',
                ]
            );
        }

        if ($room4) {
            Comment::query()->updateOrCreate(
                [
                    'room_id' => $room4->id,
                    'user_id' => $tenant2->id,
                    'content' => 'Phòng đẹp, khu vực đi lại thuận tiện.',
                ],
                [
                    'rating' => 4,
                    'status' => 'visible',
                ]
            );
        }

        if ($room1) {
            Contact::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'email' => 'lead1@example.com',
                ],
                [
                    'owner_user_id' => $landlord1->id,
                    'name' => 'Nguyễn Văn Quan Tâm',
                    'phone' => '0977777777',
                    'subject' => 'Xin xem phòng cuối tuần',
                    'message' => 'Mình muốn xem phòng vào chiều thứ 7, còn phòng không ạ?',
                    'move_in_date' => now()->addDays(5)->toDateString(),
                    'status' => 'new',
                    'status_note' => 'Lead mới từ form public, cần gọi xác nhận lịch xem phòng.',
                    'handled_by' => null,
                    'handled_at' => null,
                ]
            );
        }

        if ($room4) {
            Contact::query()->updateOrCreate(
                [
                    'room_id' => $room4->id,
                    'email' => 'lead2@example.com',
                ],
                [
                    'owner_user_id' => $landlord2->id,
                    'name' => 'Trần Minh Tìm Phòng',
                    'phone' => '0988888888',
                    'subject' => 'Hỏi thêm về phí dịch vụ',
                    'message' => 'Cho mình hỏi phòng này đã bao gồm điện nước và internet chưa?',
                    'move_in_date' => now()->addDays(10)->toDateString(),
                    'status' => 'contacted',
                    'status_note' => 'Đã gọi tư vấn, khách đang cân nhắc ngân sách.',
                    'handled_by' => $admin?->id,
                    'handled_at' => now()->subDay(),
                ]
            );
        }

        if ($room2) {
            Contact::query()->updateOrCreate(
                [
                    'room_id' => $room2->id,
                    'email' => 'lead3@example.com',
                ],
                [
                    'owner_user_id' => $landlord1->id,
                    'name' => 'Lê Thuê Thành Công',
                    'phone' => '0911222333',
                    'subject' => 'Chốt cọc phòng',
                    'message' => 'Em đồng ý thuê phòng và muốn giữ chỗ đến đầu tháng sau.',
                    'move_in_date' => now()->addDays(14)->toDateString(),
                    'status' => 'successful',
                    'status_note' => 'Khách đã đặt cọc và hẹn ký hợp đồng thuê.',
                    'handled_by' => $admin?->id,
                    'handled_at' => now()->subDays(2),
                ]
            );
        }

        if ($room4) {
            Contact::query()->updateOrCreate(
                [
                    'room_id' => $room4->id,
                    'email' => 'lead4@example.com',
                ],
                [
                    'owner_user_id' => $landlord2->id,
                    'name' => 'Phạm Ngân Sách Thấp',
                    'phone' => '0909888777',
                    'subject' => 'Tìm phòng dưới 4 triệu',
                    'message' => 'Phòng đẹp nhưng ngân sách của mình chưa phù hợp.',
                    'move_in_date' => now()->addDays(20)->toDateString(),
                    'status' => 'unsuccessful',
                    'status_note' => 'Lead không chốt do vượt ngân sách, có thể remark lại nếu có phòng rẻ hơn.',
                    'handled_by' => $admin?->id,
                    'handled_at' => now()->subDays(3),
                ]
            );
        }

        if ($room2) {
            Booking::query()->updateOrCreate(
                [
                    'room_id' => $room2->id,
                    'tenant_user_id' => $tenant1->id,
                ],
                [
                    'landlord_user_id' => $landlord1->id,
                    'start_date' => now()->subMonths(1)->toDateString(),
                    'end_date' => now()->addMonths(11)->toDateString(),
                    'agreed_price' => 2900000,
                    'currency' => 'VND',
                    'commission_percent' => 5,
                    'commission_amount' => 145000,
                    'status' => 'occupied',
                    'note' => 'Khách đã chuyển vào ở.',
                ]
            );
        }

        if ($room4) {
            Booking::query()->updateOrCreate(
                [
                    'room_id' => $room4->id,
                    'tenant_user_id' => $tenant2->id,
                ],
                [
                    'landlord_user_id' => $landlord2->id,
                    'start_date' => now()->addDays(7)->toDateString(),
                    'end_date' => now()->addMonths(6)->toDateString(),
                    'agreed_price' => 5600000,
                    'currency' => 'VND',
                    'commission_percent' => 5,
                    'commission_amount' => 280000,
                    'status' => 'confirmed',
                    'note' => 'Đã cọc, chờ ngày dọn vào.',
                ]
            );
        }

        if ($room1) {
            Booking::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'tenant_user_id' => $tenant3->id,
                ],
                [
                    'landlord_user_id' => $landlord1->id,
                    'start_date' => null,
                    'end_date' => null,
                    'agreed_price' => 3800000,
                    'currency' => 'VND',
                    'commission_percent' => 5,
                    'commission_amount' => 190000,
                    'status' => 'pending',
                    'note' => 'Khách đang cân nhắc và chưa chốt.',
                ]
            );
        }
    }
}
