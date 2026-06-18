<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        $this->addPostgresConstraints();
        $this->addPostgresSearchAndIndexes();
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('drop index if exists job_listings_active_browse_idx');
        DB::statement('drop index if exists job_listings_pending_idx');
        DB::statement('drop index if exists job_listings_employer_status_idx');
        DB::statement('drop index if exists notifications_unread_idx');
        DB::statement('drop index if exists resume_documents_current_unique');
        DB::statement('drop index if exists job_listings_search_idx');
        DB::statement('drop index if exists profiles_skills_gin_idx');
        DB::statement('drop index if exists job_listings_skills_required_gin_idx');
        DB::statement('drop index if exists users_external_auth_unique');

        if (Schema::hasColumn('job_listings', 'search_vector')) {
            DB::statement('alter table job_listings drop column search_vector');
        }

        DB::statement('alter table job_listings drop constraint if exists job_listings_location_type_check');
        DB::statement('alter table job_listings drop constraint if exists job_listings_type_check');
        DB::statement('alter table job_listings drop constraint if exists job_listings_experience_level_check');
        DB::statement('alter table job_listings drop constraint if exists job_listings_salary_range_check');
        DB::statement('alter table job_listings drop constraint if exists job_listings_salary_currency_check');
        DB::statement('alter table resume_documents drop constraint if exists resume_documents_extraction_status_check');
        DB::statement('alter table ai_job_matches drop constraint if exists ai_job_matches_generation_status_check');
        DB::statement('alter table ai_job_matches drop constraint if exists ai_job_matches_match_score_check');
    }

    private function addPostgresConstraints(): void
    {
        DB::statement(<<<'SQL'
do $$
begin
  if not exists (select 1 from pg_constraint where conname = 'job_listings_location_type_check') then
    alter table job_listings add constraint job_listings_location_type_check
      check (location_type in ('remote', 'onsite', 'hybrid'));
  end if;

  if not exists (select 1 from pg_constraint where conname = 'job_listings_type_check') then
    alter table job_listings add constraint job_listings_type_check
      check (type in ('full-time', 'part-time', 'contract', 'internship'));
  end if;

  if not exists (select 1 from pg_constraint where conname = 'job_listings_experience_level_check') then
    alter table job_listings add constraint job_listings_experience_level_check
      check (experience_level in ('entry', 'mid', 'senior'));
  end if;

  if not exists (select 1 from pg_constraint where conname = 'job_listings_salary_range_check') then
    alter table job_listings add constraint job_listings_salary_range_check
      check (salary_min is null or salary_max is null or salary_min <= salary_max);
  end if;

  if not exists (select 1 from pg_constraint where conname = 'job_listings_salary_currency_check') then
    alter table job_listings add constraint job_listings_salary_currency_check
      check (char_length(salary_currency) = 3);
  end if;

  if not exists (select 1 from pg_constraint where conname = 'resume_documents_extraction_status_check') then
    alter table resume_documents add constraint resume_documents_extraction_status_check
      check (extraction_status in ('pending', 'ready', 'failed'));
  end if;

  if not exists (select 1 from pg_constraint where conname = 'ai_job_matches_generation_status_check') then
    alter table ai_job_matches add constraint ai_job_matches_generation_status_check
      check (generation_status in ('pending', 'ready', 'failed'));
  end if;

  if not exists (select 1 from pg_constraint where conname = 'ai_job_matches_match_score_check') then
    alter table ai_job_matches add constraint ai_job_matches_match_score_check
      check (match_score >= 0 and match_score <= 100);
  end if;
end $$;
SQL);
    }

    private function addPostgresSearchAndIndexes(): void
    {
        DB::statement('create extension if not exists citext');
        DB::statement('alter table users alter column email type citext using email::citext');

        DB::statement('alter table job_listings alter column salary_min type numeric(12,2) using salary_min::numeric');
        DB::statement('alter table job_listings alter column salary_max type numeric(12,2) using salary_max::numeric');

        DB::statement("update profiles set skills = '[]' where skills is null");
        DB::statement("alter table profiles alter column skills type jsonb using coalesce(skills::jsonb, '[]'::jsonb)");
        DB::statement("alter table profiles alter column skills set default '[]'::jsonb");
        DB::statement('alter table profiles alter column skills set not null');

        DB::statement("update job_listings set skills_required = '[]' where skills_required is null");
        DB::statement("alter table job_listings alter column skills_required type jsonb using coalesce(skills_required::jsonb, '[]'::jsonb)");
        DB::statement("alter table job_listings alter column skills_required set default '[]'::jsonb");
        DB::statement('alter table job_listings alter column skills_required set not null');

        DB::statement("update notifications set data = '{}' where data is null");
        DB::statement("alter table notifications alter column data type jsonb using coalesce(data::jsonb, '{}'::jsonb)");
        DB::statement("alter table notifications alter column data set default '{}'::jsonb");
        DB::statement('alter table notifications alter column data set not null');

        DB::statement("alter table ai_job_matches alter column score_breakdown type jsonb using coalesce(score_breakdown::jsonb, '{}'::jsonb)");
        DB::statement("alter table ai_job_matches alter column score_breakdown set default '{}'::jsonb");
        DB::statement('alter table ai_job_matches alter column score_breakdown set not null');
        DB::statement("alter table ai_job_matches alter column matching_skills type jsonb using coalesce(matching_skills::jsonb, '[]'::jsonb)");
        DB::statement("alter table ai_job_matches alter column matching_skills set default '[]'::jsonb");
        DB::statement('alter table ai_job_matches alter column matching_skills set not null');
        DB::statement("alter table ai_job_matches alter column missing_skills type jsonb using coalesce(missing_skills::jsonb, '[]'::jsonb)");
        DB::statement("alter table ai_job_matches alter column missing_skills set default '[]'::jsonb");
        DB::statement('alter table ai_job_matches alter column missing_skills set not null');

        if (!Schema::hasColumn('job_listings', 'search_vector')) {
            DB::statement(<<<'SQL'
alter table job_listings add column search_vector tsvector
generated always as (
  to_tsvector(
    'english',
    coalesce(title, '') || ' ' ||
    coalesce(description, '') || ' ' ||
    coalesce(requirements, '') || ' ' ||
    coalesce(location, '')
  )
) stored
SQL);
        }

        DB::statement("create index if not exists job_listings_active_browse_idx on job_listings (published_at desc, id) where status = 'active' and deleted_at is null");
        DB::statement("create index if not exists job_listings_pending_idx on job_listings (created_at asc) where status = 'pending' and deleted_at is null");
        DB::statement("create index if not exists job_listings_employer_status_idx on job_listings (user_id, status, created_at desc) where deleted_at is null");
        DB::statement('create index if not exists notifications_unread_idx on notifications (user_id, created_at desc) where read_at is null');
        DB::statement('create unique index if not exists resume_documents_current_unique on resume_documents (user_id) where is_current = true');
        DB::statement('create unique index if not exists users_external_auth_unique on users (auth_provider, external_auth_id) where external_auth_id is not null');
        DB::statement('create index if not exists job_listings_search_idx on job_listings using gin (search_vector)');
        DB::statement('create index if not exists profiles_skills_gin_idx on profiles using gin (skills jsonb_path_ops)');
        DB::statement('create index if not exists job_listings_skills_required_gin_idx on job_listings using gin (skills_required jsonb_path_ops)');
    }
};
