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
        Schema::create('field_reference', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');
            $table->string('user_name_created', 50)->nullable();
            $table->string('user_code_created', 50)->nullable();
            $table->string('user_name_updated', 50)->nullable();
            $table->string('user_code_updated', 50)->nullable();
            $table->string('field_code', 255);
            $table->string('reference_code', 255);
            $table->string('field_name_VN', 500)->nullable();
            $table->string('field_name_EN', 500)->nullable();
            $table->string('field_name_CN', 500)->nullable();
            $table->string('field_group', 255)->nullable();
            $table->string('field_sort', 255)->nullable();
            $table->string('remark', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_reference');
    }
};
