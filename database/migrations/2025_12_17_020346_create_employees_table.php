<?php

use App\Common\Enums\WorkStatus;
use App\Models\User;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->char('isactive', 1)->default('Y');

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('employee_code')->unique();
            $table->string('full_name', 100);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();

            $table->string('avatar_url')->nullable();

            $table->enum(
                'status',
                array_column(WorkStatus::cases(), 'value')
            );

            $table->date('join_date')->nullable();
            $table->date('terminate_date')->nullable();

            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->string('user_name_created', 50)->nullable();
            $table->string('user_name_updated', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
