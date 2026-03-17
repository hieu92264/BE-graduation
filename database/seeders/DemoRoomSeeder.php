<?php

namespace Database\Seeders;

use App\Common\Enums\DealStatus;
use App\Common\Enums\LeadStatus;
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
                'availability_status' => 'available',
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
                'availability_status' => 'occupied',
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
                'booking_status' => 'available',
                'availability_status' => 'available',
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
                'availability_status' => 'available',
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
                'booking_status' => 'available',
                'availability_status' => 'available',
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
                'availability_status' => 'hidden',
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
                    'availability_status' => $item['availability_status'],
                    'post_status' => $item['post_status'],
                    'moderation_note' => match ($item['post_status']) {
                        'approved' => 'Tin hợp lệ, thông tin rõ ràng.',
                        'rejected' => 'Cần bổ sung thông tin và hình ảnh rõ hơn trước khi đăng.',
                        'hidden' => 'Tin tạm ẩn do chủ nhà chưa cập nhật lại thông tin.',
                        default => null,
                    },
                    'moderated_by' => in_array($item['post_status'], ['approved', 'rejected', 'hidden'], true) ? $admin?->id : null,
                    'moderated_at' => in_array($item['post_status'], ['approved', 'rejected', 'hidden'], true)
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

        if ($room1) {
            $commentVisible = Comment::query()->updateOrCreate(
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
                    'comment_id' => $commentVisible->id,
                    'user_id' => $landlord1->id,
                    'content' => 'Cảm ơn bạn đã quan tâm, bên mình luôn hỗ trợ khách thuê tốt nhất.',
                ],
                [
                    'status' => 'visible',
                ]
            );

            Comment::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'user_id' => $tenant2->id,
                    'content' => 'Mình muốn hỏi thêm về giờ giấc và chỗ để xe.',
                ],
                [
                    'rating' => 4,
                    'status' => 'pending',
                ]
            );
        }

        if ($room2) {
            Comment::query()->updateOrCreate(
                [
                    'room_id' => $room2->id,
                    'user_id' => $tenant3->id,
                    'content' => 'Phòng ổn nhưng mình không hợp khu vực này.',
                ],
                [
                    'rating' => 3,
                    'status' => 'hidden',
                ]
            );
        }

        $contactNew = null;
        $contactViewing = null;
        $contactNegotiating = null;
        $contactWon = null;
        $contactLost = null;

        if ($room1) {
            $contactNew = Contact::query()->updateOrCreate(
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
                    'preferred_viewing_time' => 'Chiều thứ 7 sau 17:00',
                    'status' => LeadStatus::NEW->value,
                    'status_note' => 'Lead mới từ form public, cần gọi xác nhận lịch xem phòng.',
                    'source' => 'room_detail_form',
                    'handled_by' => null,
                    'handled_at' => null,
                ]
            );

            $contactViewing = Contact::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'email' => 'lead2@example.com',
                ],
                [
                    'owner_user_id' => $landlord1->id,
                    'name' => 'Trần Minh Tìm Phòng',
                    'phone' => '0988888888',
                    'subject' => 'Hẹn xem phòng buổi tối',
                    'message' => 'Cho mình xem phòng vào tối mai sau giờ làm.',
                    'move_in_date' => now()->addDays(10)->toDateString(),
                    'preferred_viewing_time' => '19:00 - 20:00',
                    'status' => LeadStatus::VIEWING_SCHEDULED->value,
                    'status_note' => 'Đã xác nhận lịch xem phòng với khách.',
                    'viewing_at' => now()->addDays(2)->setTime(19, 0),
                    'next_follow_up_at' => now()->addDays(2)->setTime(21, 0),
                    'source' => 'room_detail_form',
                    'handled_by' => $admin?->id,
                    'handled_at' => now()->subHours(4),
                ]
            );

            $contactNegotiating = Contact::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'email' => 'lead3@example.com',
                ],
                [
                    'owner_user_id' => $landlord1->id,
                    'name' => 'Phạm Hoài Thương Lượng',
                    'phone' => '0911222333',
                    'subject' => 'Có thể giữ phòng vài ngày không?',
                    'message' => 'Em đang cân nhắc và muốn giữ phòng đến đầu tháng sau.',
                    'move_in_date' => now()->addDays(14)->toDateString(),
                    'preferred_viewing_time' => 'Cuối tuần',
                    'status' => LeadStatus::NEGOTIATING->value,
                    'status_note' => 'Khách đang thương lượng giá và thời điểm dọn vào.',
                    'next_follow_up_at' => now()->addDays(1)->setTime(10, 0),
                    'source' => 'room_detail_form',
                    'handled_by' => $admin?->id,
                    'handled_at' => now()->subDay(),
                ]
            );

            $contactLost = Contact::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'email' => 'lead4@example.com',
                ],
                [
                    'owner_user_id' => $landlord1->id,
                    'name' => 'Lê Ngân Sách Thấp',
                    'phone' => '0909888777',
                    'subject' => 'Tìm phòng dưới 3 triệu',
                    'message' => 'Phòng đẹp nhưng hiện tại ngân sách của mình chưa phù hợp.',
                    'move_in_date' => now()->addDays(20)->toDateString(),
                    'preferred_viewing_time' => 'Sau 18:00',
                    'status' => LeadStatus::LOST->value,
                    'status_note' => 'Lead không chốt được.',
                    'lost_reason' => 'Không phù hợp ngân sách của khách.',
                    'source' => 'room_detail_form',
                    'handled_by' => $admin?->id,
                    'handled_at' => now()->subDays(2),
                ]
            );
        }

        if ($room2) {
            $contactWon = Contact::query()->updateOrCreate(
                [
                    'room_id' => $room2->id,
                    'email' => 'lead5@example.com',
                ],
                [
                    'owner_user_id' => $landlord1->id,
                    'name' => 'Lê Thuê Thành Công',
                    'phone' => '0933333333',
                    'subject' => 'Chốt thuê phòng',
                    'message' => 'Em đồng ý thuê phòng và muốn ký hợp đồng luôn.',
                    'move_in_date' => now()->subMonths(1)->toDateString(),
                    'preferred_viewing_time' => 'Đã xem trực tiếp',
                    'status' => LeadStatus::WON->value,
                    'status_note' => 'Khách đã chốt thuê, chuyển sang deal completed.',
                    'source' => 'room_detail_form',
                    'handled_by' => $admin?->id,
                    'handled_at' => now()->subDays(5),
                ]
            );
        }

        if ($room1 && $contactNegotiating) {
            Booking::query()->updateOrCreate(
                [
                    'room_id' => $room1->id,
                    'contact_id' => $contactNegotiating->id,
                ],
                [
                    'tenant_user_id' => null,
                    'tenant_name' => $contactNegotiating->name,
                    'tenant_phone' => $contactNegotiating->phone,
                    'tenant_email' => $contactNegotiating->email,
                    'landlord_user_id' => $landlord1->id,
                    'start_date' => now()->addDays(14)->toDateString(),
                    'end_date' => now()->addMonths(6)->toDateString(),
                    'agreed_price' => 3700000,
                    'currency' => 'VND',
                    'commission_percent' => 5,
                    'commission_amount' => 185000,
                    'status' => DealStatus::DRAFT->value,
                    'note' => 'Deal đang ở bước thương lượng cuối.',
                    'reserved_at' => null,
                    'confirmed_at' => null,
                    'cancelled_at' => null,
                    'completed_at' => null,
                ]
            );
        }

        if ($room2 && $contactWon) {
            Booking::query()->updateOrCreate(
                [
                    'room_id' => $room2->id,
                    'contact_id' => $contactWon->id,
                ],
                [
                    'tenant_user_id' => $tenant1->id,
                    'tenant_name' => $contactWon->name,
                    'tenant_phone' => $contactWon->phone,
                    'tenant_email' => $contactWon->email,
                    'landlord_user_id' => $landlord1->id,
                    'start_date' => now()->subMonths(1)->toDateString(),
                    'end_date' => now()->addMonths(11)->toDateString(),
                    'agreed_price' => 2900000,
                    'currency' => 'VND',
                    'commission_percent' => 5,
                    'commission_amount' => 145000,
                    'status' => DealStatus::COMPLETED->value,
                    'note' => 'Khách đã chuyển vào ở ổn định.',
                    'reserved_at' => now()->subMonths(1)->subDays(3),
                    'confirmed_at' => now()->subMonths(1)->subDays(2),
                    'cancelled_at' => null,
                    'completed_at' => now()->subMonths(1),
                ]
            );
        }

        if ($room3) {
            $room3->update([
                'availability_status' => 'available',
            ]);
        }

        if ($room4) {
            $room4->update([
                'availability_status' => 'available',
            ]);
        }
    }
}
