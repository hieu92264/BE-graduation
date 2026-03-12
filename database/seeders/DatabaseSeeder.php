<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            CategorySeeder::class,
            PostTypeSeeder::class,

            PermissionSeeder::class,
            RolePermissionSeeder::class,
            DemoUserSeeder::class,
            DemoRoomSeeder::class,
            DemoCmsSeeder::class,
        ]);
    }
}
