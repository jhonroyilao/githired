<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('headline')->nullable();         // e.g. "Full Stack Developer"
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('resume_path')->nullable();      // storage path to uploaded PDF
            $table->string('avatar_path')->nullable();
            $table->json('skills')->nullable();             // ["Laravel", "Vue", "MySQL"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};