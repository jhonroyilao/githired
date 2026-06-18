<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'auth_provider')) {
                $table->string('auth_provider')->default('laravel')->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'external_auth_id')) {
                $table->string('external_auth_id')->nullable()->after('auth_provider');
            }

            $table->index(['auth_provider', 'external_auth_id'], 'users_auth_provider_external_id_idx');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->unique('user_id', 'profiles_user_id_unique');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unique('user_id', 'companies_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique('companies_user_id_unique');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropUnique('profiles_user_id_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_auth_provider_external_id_idx');

            if (Schema::hasColumn('users', 'external_auth_id')) {
                $table->dropColumn('external_auth_id');
            }

            if (Schema::hasColumn('users', 'auth_provider')) {
                $table->dropColumn('auth_provider');
            }
        });
    }
};
