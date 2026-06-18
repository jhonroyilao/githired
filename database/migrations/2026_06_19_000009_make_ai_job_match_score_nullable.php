<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ai_job_matches')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('alter table ai_job_matches alter column match_score drop not null');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('ai_job_matches')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('update ai_job_matches set match_score = 0 where match_score is null');
            DB::statement('alter table ai_job_matches alter column match_score set not null');
        }
    }
};
