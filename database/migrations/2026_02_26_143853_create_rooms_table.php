<?php

use App\Common\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');

            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('post_type_id')->nullable()->constrained('post_types')->nullOnDelete();

            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete();

            $table->string('title', 255);
            $table->string('slug', 300)->unique();
            $table->string('address', 500)->nullable();

            $table->decimal('price', 12, 2)->default(0)->index();
            $table->decimal('area', 8, 2)->nullable()->index();

            $table->text('description')->nullable();

            $table->enum('booking_status', BookingStatus::values())
                ->default(BookingStatus::PENDING->value)
                ->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
