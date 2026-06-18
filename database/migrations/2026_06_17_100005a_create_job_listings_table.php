<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();      // employer
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('job_categories')->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->string('location');
            $table->string('location_type');    // remote | onsite | hybrid
            $table->string('type');             // full-time | part-time | contract | internship
            $table->string('experience_level'); // entry | mid | senior

            $table->text('description');
            $table->text('requirements');
            $table->json('skills_required')->nullable();

            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->string('salary_currency')->default('PHP');

            $table->enum('status', ['draft', 'pending', 'active', 'closed', 'rejected'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};