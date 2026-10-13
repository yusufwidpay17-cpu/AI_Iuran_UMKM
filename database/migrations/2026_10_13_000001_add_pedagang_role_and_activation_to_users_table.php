<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Modifikasi enum role agar mendukung role 'pedagang'
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'petugas', 'pedagang') NOT NULL DEFAULT 'petugas'");
        } catch (\Exception $e) {}

        // 2. Tambah kolom pedagang_id, status_aktivasi, activation_token, activation_expires_at
        try {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'pedagang_id')) {
                    $table->string('pedagang_id', 36)->nullable()->after('role');
                    $table->index('pedagang_id');
                }
                if (!Schema::hasColumn('users', 'status_aktivasi')) {
                    $table->enum('status_aktivasi', ['aktif', 'pending'])->default('aktif')->after('pedagang_id');
                }
                if (!Schema::hasColumn('users', 'activation_token')) {
                    $table->string('activation_token', 100)->nullable()->after('status_aktivasi');
                }
                if (!Schema::hasColumn('users', 'activation_expires_at')) {
                    $table->timestamp('activation_expires_at')->nullable()->after('activation_token');
                }
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'activation_expires_at')) {
                $table->dropColumn('activation_expires_at');
            }
            if (Schema::hasColumn('users', 'activation_token')) {
                $table->dropColumn('activation_token');
            }
            if (Schema::hasColumn('users', 'status_aktivasi')) {
                $table->dropColumn('status_aktivasi');
            }
            if (Schema::hasColumn('users', 'pedagang_id')) {
                $table->dropColumn('pedagang_id');
            }
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'petugas') NOT NULL DEFAULT 'petugas'");
    }
};
