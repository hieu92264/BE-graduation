<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (! Schema::hasColumn('rooms', 'availability_status')) {
                $table->enum('availability_status', ['available', 'reserved', 'occupied', 'hidden'])
                    ->default('available')
                    ->after('booking_status')
                    ->index();
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'contact_id')) {
                $table->unsignedBigInteger('contact_id')->nullable()->after('room_id')->index();
            }

            if (! Schema::hasColumn('bookings', 'tenant_name')) {
                $table->string('tenant_name', 255)->nullable()->after('tenant_user_id');
            }

            if (! Schema::hasColumn('bookings', 'tenant_phone')) {
                $table->string('tenant_phone', 50)->nullable()->after('tenant_name');
            }

            if (! Schema::hasColumn('bookings', 'tenant_email')) {
                $table->string('tenant_email', 255)->nullable()->after('tenant_phone');
            }

            if (! Schema::hasColumn('bookings', 'reserved_at')) {
                $table->dateTime('reserved_at')->nullable()->after('note');
            }

            if (! Schema::hasColumn('bookings', 'confirmed_at')) {
                $table->dateTime('confirmed_at')->nullable()->after('reserved_at');
            }

            if (! Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->dateTime('cancelled_at')->nullable()->after('confirmed_at');
            }

            if (! Schema::hasColumn('bookings', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('cancelled_at');
            }
        });

        DB::statement("
            ALTER TABLE bookings
            MODIFY status ENUM(
                'draft',
                'reserved',
                'confirmed',
                'cancelled',
                'completed'
            ) NOT NULL DEFAULT 'draft'
        ");

        DB::statement("
            ALTER TABLE bookings
            MODIFY tenant_user_id BIGINT UNSIGNED NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE bookings
            MODIFY status ENUM(
                'pending',
                'confirmed',
                'available',
                'occupied'
            ) NOT NULL DEFAULT 'pending'
        ");

        DB::statement("
            ALTER TABLE bookings
            MODIFY tenant_user_id BIGINT UNSIGNED NOT NULL
        ");

        Schema::table('bookings', function (Blueprint $table) {
            foreach (
                [
                    'contact_id',
                    'tenant_name',
                    'tenant_phone',
                    'tenant_email',
                    'reserved_at',
                    'confirmed_at',
                    'cancelled_at',
                    'completed_at',
                ] as $column
            ) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'availability_status')) {
                $table->dropColumn('availability_status');
            }
        });
    }
};
