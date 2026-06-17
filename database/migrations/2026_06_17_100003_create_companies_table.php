<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // employer user
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->string('size')->nullable();             // "1-10", "11-50", "51-200", "200+"
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};