<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'resume_document_id')) {
                $table->foreignId('resume_document_id')
                    ->nullable()
                    ->after('resume_path')
                    ->constrained('resume_documents')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'resume_document_id')) {
                $table->dropConstrainedForeignId('resume_document_id');
            }
        });
    }
};
