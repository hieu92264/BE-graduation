<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\City;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Contact;
use App\Models\Content;
use App\Models\District;
use App\Models\Employee;
use App\Models\PostType;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\Slider;
use App\Models\User;
use App\Models\UserProfiles;
use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(20)->create();
        foreach ($users as $user) {
            UserProfiles::factory()->create(['user_id' => $user->id]);
        }

        Employee::factory(8)->create([
            'user_id' => $users->random()->id
        ]);

        $cities = City::factory(5)->create();
        $districts = collect();
        foreach ($cities as $c) {
            $districts = $districts->merge(District::factory(4)->create(['city_id' => $c->id]));
        }
        $wards = collect();
        foreach ($districts as $d) {
            $wards = $wards->merge(Ward::factory(4)->create(['district_id' => $d->id]));
        }

        $cats = Category::factory(8)->create();
        $postTypes = PostType::factory(5)->create();

        $rooms = Room::factory(30)->create([
            'owner_user_id' => $users->random()->id,
            'category_id' => $cats->random()->id,
            'post_type_id' => $postTypes->random()->id,
            'city_id' => $cities->random()->id,
            'district_id' => $districts->random()->id,
            'ward_id' => $wards->random()->id,
        ]);

        foreach ($rooms as $r) {
            RoomPhoto::factory()->cover()->create(['room_id' => $r->id]);
            RoomPhoto::factory(rand(2, 6))->create(['room_id' => $r->id]);
        }

        $comments = Comment::factory(80)->create([
            'room_id' => $rooms->random()->id,
            'user_id' => $users->random()->id,
        ]);
        foreach ($comments->random(30) as $c) {
            CommentReply::factory(rand(1, 3))->create([
                'comment_id' => $c->id,
                'user_id' => $users->random()->id,
            ]);
        }

        Booking::factory(40)->create([
            'room_id' => $rooms->random()->id,
            'tenant_user_id' => $users->random()->id,
            'landlord_user_id' => $users->random()->id,
        ]);

        Content::factory(20)->create(['author_user_id' => $users->random()->id]);
        Slider::factory(10)->create();
        Contact::factory(20)->create();
    }
}
