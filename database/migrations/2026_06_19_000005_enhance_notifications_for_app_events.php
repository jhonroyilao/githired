<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable()->after('link');
            }

            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }

            $table->index('user_id', 'notifications_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_id_idx');

            if (Schema::hasColumn('notifications', 'read_at')) {
                $table->dropColumn('read_at');
            }

            if (Schema::hasColumn('notifications', 'data')) {
                $table->dropColumn('data');
            }
        });
    }
};
