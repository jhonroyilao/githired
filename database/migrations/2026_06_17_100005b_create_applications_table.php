<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();          // applicant
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();

            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();

            $table->enum('status', ['pending', 'interview', 'hired', 'rejected'])
                  ->default('pending');

            $table->text('employer_notes')->nullable();
            $table->timestamp('status_updated_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'job_listing_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};