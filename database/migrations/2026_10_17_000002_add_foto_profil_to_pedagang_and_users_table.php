<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('pedagang') && !Schema::hasColumn('pedagang', 'foto_profil')) {
            Schema::table('pedagang', function (Blueprint $table) {
                $table->mediumText('foto_profil')->nullable()->after('status_legal');
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'foto_profil')) {
            Schema::table('users', function (Blueprint $table) {
                $table->mediumText('foto_profil')->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('pedagang') && Schema::hasColumn('pedagang', 'foto_profil')) {
            Schema::table('pedagang', function (Blueprint $table) {
                $table->dropColumn('foto_profil');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'foto_profil')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('foto_profil');
            });
        }
    }
};
