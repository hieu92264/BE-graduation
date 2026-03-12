khơir tạo dự án
chạy seeder
php artisan db:seed --class=Database\Seeders\LocationSeeder
php artisan db:seed --class=Database\Seeders\CategorySeeder

composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
