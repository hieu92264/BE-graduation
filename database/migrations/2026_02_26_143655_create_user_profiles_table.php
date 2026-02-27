<?php

use App\Common\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            $table->string('full_name', 150)->nullable();
            $table->string('phone_number', 30)->nullable()->index();
            $table->string('avatar_url', 500)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('zalo', 255)->nullable();
            $table->string('facebook', 255)->nullable();

            $table->enum('user_type', UserType::values())->default('tenant')
                ->default(UserType::TENANT->value)
                ->index();

            $table->string('remark', 500)->nullable();
            $table->string('user_name_created', 50)->nullable();
            $table->string('user_name_updated', 50)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
