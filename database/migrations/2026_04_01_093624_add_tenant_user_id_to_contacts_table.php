<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'tenant_user_id')) {
                $table->foreignId('tenant_user_id')
                    ->nullable()
                    ->after('owner_user_id')
                    ->constrained('users')
                    ->nullOnDelete()
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'tenant_user_id')) {
                $table->dropConstrainedForeignId('tenant_user_id');
            }
        });
    }
};
