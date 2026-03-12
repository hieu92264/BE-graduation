<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('post_status', ['pending', 'approved', 'rejected', 'hidden'])
                ->default('pending')
                ->after('booking_status')
                ->index();

            $table->foreignId('moderated_by')
                ->nullable()
                ->after('post_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->after('moderated_by');

            $table->text('moderation_note')
                ->nullable()
                ->after('moderated_at');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropColumn([
                'post_status',
                'moderated_at',
                'moderation_note',
            ]);
        });
    }
};