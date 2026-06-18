<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_listings', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('rejection_reason');
            }

            if (!Schema::hasColumn('job_listings', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('submitted_at');
            }

            if (!Schema::hasColumn('job_listings', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('job_listings', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('job_listings', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->after('rejected_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('job_listings', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('rejected_by');
            }

            if (!Schema::hasColumn('job_listings', 'deleted_at')) {
                $table->softDeletes()->after('views_count');
            }

            if (!Schema::hasColumn('job_listings', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('job_listings', 'delete_reason')) {
                $table->text('delete_reason')->nullable()->after('deleted_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            if (Schema::hasColumn('job_listings', 'delete_reason')) {
                $table->dropColumn('delete_reason');
            }

            if (Schema::hasColumn('job_listings', 'deleted_by')) {
                $table->dropConstrainedForeignId('deleted_by');
            }

            if (Schema::hasColumn('job_listings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('job_listings', 'closed_at')) {
                $table->dropColumn('closed_at');
            }

            if (Schema::hasColumn('job_listings', 'rejected_by')) {
                $table->dropConstrainedForeignId('rejected_by');
            }

            if (Schema::hasColumn('job_listings', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }

            if (Schema::hasColumn('job_listings', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('job_listings', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('job_listings', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
        });
    }
};
