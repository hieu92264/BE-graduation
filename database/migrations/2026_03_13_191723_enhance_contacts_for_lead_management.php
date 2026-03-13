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
            if (! Schema::hasColumn('contacts', 'move_in_date')) {
                $table->date('move_in_date')->nullable()->after('message');
            }

            if (! Schema::hasColumn('contacts', 'status_note')) {
                $table->text('status_note')->nullable()->after('status');
            }

            if (! Schema::hasColumn('contacts', 'handled_by')) {
                $table->unsignedBigInteger('handled_by')->nullable()->after('status_note')->index();
            }

            if (! Schema::hasColumn('contacts', 'handled_at')) {
                $table->dateTime('handled_at')->nullable()->after('handled_by');
            }
        });

        DB::statement("
            ALTER TABLE contacts
            MODIFY status ENUM('new','contacted','successful','unsuccessful')
            NOT NULL DEFAULT 'new'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE contacts
            MODIFY status ENUM('new','processing','done')
            NOT NULL DEFAULT 'new'
        ");

        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'handled_at')) {
                $table->dropColumn('handled_at');
            }

            if (Schema::hasColumn('contacts', 'handled_by')) {
                $table->dropColumn('handled_by');
            }

            if (Schema::hasColumn('contacts', 'status_note')) {
                $table->dropColumn('status_note');
            }

            if (Schema::hasColumn('contacts', 'move_in_date')) {
                $table->dropColumn('move_in_date');
            }
        });
    }
};
