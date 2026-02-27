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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['city_id', 'name']);
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['district_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
    }
};
