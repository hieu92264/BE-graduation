<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'preferred_viewing_time')) {
                $table->string('preferred_viewing_time', 255)->nullable()->after('move_in_date');
            }

            if (! Schema::hasColumn('contacts', 'lost_reason')) {
                $table->text('lost_reason')->nullable()->after('status_note');
            }

            if (! Schema::hasColumn('contacts', 'next_follow_up_at')) {
                $table->dateTime('next_follow_up_at')->nullable()->after('lost_reason');
            }

            if (! Schema::hasColumn('contacts', 'last_contacted_at')) {
                $table->dateTime('last_contacted_at')->nullable()->after('next_follow_up_at');
            }

            if (! Schema::hasColumn('contacts', 'viewing_at')) {
                $table->dateTime('viewing_at')->nullable()->after('last_contacted_at');
            }

            if (! Schema::hasColumn('contacts', 'source')) {
                $table->string('source', 100)->default('room_detail_form')->after('viewing_at');
            }
        });

        DB::statement("
            ALTER TABLE contacts
            MODIFY status ENUM(
                'new',
                'contacted',
                'viewing_scheduled',
                'viewed',
                'negotiating',
                'waiting_decision',
                'won',
                'lost',
                'cancelled'
            ) NOT NULL DEFAULT 'new'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE contacts
            MODIFY status ENUM('new','contacted','successful','unsuccessful')
            NOT NULL DEFAULT 'new'
        ");

        Schema::table('contacts', function (Blueprint $table) {
            foreach (
                [
                    'preferred_viewing_time',
                    'lost_reason',
                    'next_follow_up_at',
                    'last_contacted_at',
                    'viewing_at',
                    'source',
                ] as $column
            ) {
                if (Schema::hasColumn('contacts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
