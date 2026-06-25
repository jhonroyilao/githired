<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('desired_job_type')->nullable()->after('github');
            $table->string('work_preference')->nullable()->after('desired_job_type');
            $table->string('experience_level')->nullable()->after('work_preference');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
do $$
begin
  if not exists (select 1 from pg_constraint where conname = 'profiles_desired_job_type_check') then
    alter table profiles add constraint profiles_desired_job_type_check
      check (desired_job_type is null or desired_job_type in ('full-time', 'part-time', 'contract', 'internship'));
  end if;

  if not exists (select 1 from pg_constraint where conname = 'profiles_work_preference_check') then
    alter table profiles add constraint profiles_work_preference_check
      check (work_preference is null or work_preference in ('remote', 'onsite', 'hybrid'));
  end if;

  if not exists (select 1 from pg_constraint where conname = 'profiles_experience_level_check') then
    alter table profiles add constraint profiles_experience_level_check
      check (experience_level is null or experience_level in ('entry', 'mid', 'senior'));
  end if;
end $$;
SQL);
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('alter table profiles drop constraint if exists profiles_desired_job_type_check');
            DB::statement('alter table profiles drop constraint if exists profiles_work_preference_check');
            DB::statement('alter table profiles drop constraint if exists profiles_experience_level_check');
        }

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'desired_job_type',
                'work_preference',
                'experience_level',
            ]);
        });
    }
};
