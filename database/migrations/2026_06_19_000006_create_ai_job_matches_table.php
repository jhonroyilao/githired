<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_job_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resume_document_id')->nullable()->constrained('resume_documents')->nullOnDelete();
            $table->decimal('match_score', 5, 2)->nullable();
            $table->json('score_breakdown')->nullable();
            $table->json('matching_skills')->nullable();
            $table->json('missing_skills')->nullable();
            $table->text('explanation')->nullable();
            $table->text('suggested_action')->nullable();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->string('prompt_version')->nullable();
            $table->string('profile_hash')->nullable();
            $table->string('resume_hash')->nullable();
            $table->string('job_hash')->nullable();
            $table->string('generation_status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'ai_job_matches_user_id_idx');
            $table->index('job_listing_id', 'ai_job_matches_job_listing_id_idx');
            $table->unique(
                ['user_id', 'job_listing_id', 'profile_hash', 'resume_hash', 'job_hash'],
                'ai_job_matches_cache_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_job_matches');
    }
};
