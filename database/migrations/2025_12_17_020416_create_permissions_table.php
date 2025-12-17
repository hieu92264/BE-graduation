<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            $table->char('isactive', 1)->default('Y');

            $table->string('code')->unique();
            $table->string('name');

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('permissions')
                ->nullOnDelete();

            $table->string('url')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->string('user_name_created', 50)->nullable();
            $table->string('user_name_updated', 50)->nullable();

            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
