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
        Schema::table('pedagang', function (Blueprint $table) {
            if (!Schema::hasColumn('pedagang', 'alamat_lengkap')) {
                $table->text('alamat_lengkap')->nullable()->after('no_telepon');
            }
            if (!Schema::hasColumn('pedagang', 'rt')) {
                $table->string('rt', 10)->nullable()->after('alamat_lengkap');
            }
            if (!Schema::hasColumn('pedagang', 'rw')) {
                $table->string('rw', 10)->nullable()->after('rt');
            }
            if (!Schema::hasColumn('pedagang', 'desa_kelurahan')) {
                $table->string('desa_kelurahan', 100)->nullable()->after('rw');
            }
            if (!Schema::hasColumn('pedagang', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable()->after('desa_kelurahan');
            }
            if (!Schema::hasColumn('pedagang', 'kabupaten_kota')) {
                $table->string('kabupaten_kota', 100)->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('pedagang', 'provinsi')) {
                $table->string('provinsi', 100)->nullable()->after('kabupaten_kota');
            }
            if (!Schema::hasColumn('pedagang', 'kode_pos')) {
                $table->string('kode_pos', 10)->nullable()->after('provinsi');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedagang', function (Blueprint $table) {
            $columns = [
                'alamat_lengkap',
                'rt',
                'rw',
                'desa_kelurahan',
                'kecamatan',
                'kabupaten_kota',
                'provinsi',
                'kode_pos'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('pedagang', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
