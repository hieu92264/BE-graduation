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
        Schema::create('post_types', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');

            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->integer('priority')->default(0)->index();
            $table->integer('default_days')->default(30);
            $table->decimal('price', 12, 2)->default(0);

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
        Schema::dropIfExists('post_types');
    }
};
