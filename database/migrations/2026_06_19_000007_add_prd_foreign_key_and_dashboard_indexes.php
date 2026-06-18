<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->index('user_id', 'job_listings_user_id_idx');
            $table->index('company_id', 'job_listings_company_id_idx');
            $table->index('category_id', 'job_listings_category_id_idx');
            $table->index('approved_by', 'job_listings_approved_by_idx');
            $table->index('rejected_by', 'job_listings_rejected_by_idx');
            $table->index('deleted_by', 'job_listings_deleted_by_idx');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'created_at'], 'applications_user_status_idx');
            $table->index(['job_listing_id', 'status', 'created_at'], 'applications_job_status_idx');
            $table->index('resume_document_id', 'applications_resume_document_id_idx');
        });

        Schema::table('application_status_logs', function (Blueprint $table) {
            $table->index('application_id', 'application_status_logs_application_id_idx');
            $table->index('changed_by', 'application_status_logs_changed_by_idx');
        });

        Schema::table('saved_jobs', function (Blueprint $table) {
            $table->index('user_id', 'saved_jobs_user_id_idx');
            $table->index('job_listing_id', 'saved_jobs_job_listing_id_idx');
        });
    }

    public function down(): void
    {
        $this->dropIndexIfExists('saved_jobs', 'saved_jobs_job_listing_id_idx');
        $this->dropIndexIfExists('saved_jobs', 'saved_jobs_user_id_idx');
        $this->dropIndexIfExists('application_status_logs', 'application_status_logs_changed_by_idx');
        $this->dropIndexIfExists('application_status_logs', 'application_status_logs_application_id_idx');
        $this->dropIndexIfExists('applications', 'applications_resume_document_id_idx');
        $this->dropIndexIfExists('applications', 'applications_job_status_idx');
        $this->dropIndexIfExists('applications', 'applications_user_status_idx');
        $this->dropIndexIfExists('job_listings', 'job_listings_deleted_by_idx');
        $this->dropIndexIfExists('job_listings', 'job_listings_rejected_by_idx');
        $this->dropIndexIfExists('job_listings', 'job_listings_approved_by_idx');
        $this->dropIndexIfExists('job_listings', 'job_listings_category_id_idx');
        $this->dropIndexIfExists('job_listings', 'job_listings_company_id_idx');
        $this->dropIndexIfExists('job_listings', 'job_listings_user_id_idx');
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($index) {
                $table->dropIndex($index);
            });
        } catch (Throwable) {
            // Keep rollback tolerant across local SQLite and Postgres branches.
        }
    }
};
