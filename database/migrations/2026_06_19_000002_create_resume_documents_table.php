<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->default('application/pdf');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->string('content_hash')->nullable();
            $table->string('extraction_status')->default('pending');
            $table->text('extraction_error')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->index('user_id', 'resume_documents_user_id_idx');
            $table->index('content_hash', 'resume_documents_content_hash_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_documents');
    }
};
