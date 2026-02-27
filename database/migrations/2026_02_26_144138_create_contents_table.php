<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');

            $table->foreignId('author_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title', 255);
            $table->string('slug', 300)->unique();
            $table->string('thumbnail_url', 600)->nullable();
            $table->longText('content')->nullable();
            $table->enum('status', ['draft', 'published', 'hidden'])->default('draft')->index();
            $table->dateTime('published_at')->nullable()->index();

            $table->string('remark', 500)->nullable();
            $table->string('user_name_created', 50)->nullable();
            $table->string('user_name_updated', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');

            $table->string('title', 255)->nullable();
            $table->string('image_url', 600);
            $table->string('link_url', 600)->nullable();
            $table->integer('sort_order')->default(0);

            $table->string('remark', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->string('email', 150)->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('subject', 255)->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'processing', 'done'])->default('new')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('news');
    }
};
