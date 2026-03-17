<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('content');
            $table->tinyInteger('rating')->nullable();
            $table->enum('status', ['pending', 'visible', 'hidden'])->default('pending')->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['room_id', 'status']);
        });

        Schema::create('comment_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('content');
            $table->enum('status', ['visible', 'hidden'])->default('visible')->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['comment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_replies');
        Schema::dropIfExists('comments');
    }
};
