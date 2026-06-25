<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_status_logs', function (Blueprint $table): void {
            $table->string('changed_by_name')->nullable()->after('changed_by');
            $table->string('changed_by_email')->nullable()->after('changed_by_name');
        });
    }

    public function down(): void
    {
        Schema::table('application_status_logs', function (Blueprint $table): void {
            $table->dropColumn(['changed_by_name', 'changed_by_email']);
        });
    }
};
