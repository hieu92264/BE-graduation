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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            // Tenant (người thuê) & landlord (chủ trọ)
            $table->foreignId('tenant_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('landlord_user_id')->constrained('users')->cascadeOnDelete();

            // Booking dates (optional, depending on your flow)
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Price agreed between tenant & landlord (offline payment)
            $table->decimal('agreed_price', 12, 2)->default(0);
            $table->string('currency', 10)->default('VND');

            // Commission for admin statistics (cut %)
            $table->decimal('commission_percent', 5, 2)->default(0); // e.g. 5.00 means 5%
            $table->decimal('commission_amount', 12, 2)->default(0);

            $table->enum('status', BookingStatus::values())
                ->default(BookingStatus::PENDING->value)
                ->index();
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['room_id', 'status']);
            $table->index(['tenant_user_id', 'status']);
            $table->index(['landlord_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
