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
        Schema::create('pendaftaran_pedagang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_calon', 100);
            $table->string('no_ktp', 20);
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_usaha', 100)->nullable();
            $table->enum('kategori_usaha', ['kuliner', 'pakaian', 'elektronik', 'sayuran', 'buah', 'sembako', 'lainnya'])->default('kuliner');
            $table->string('no_kios', 20);
            $table->string('blok_kios', 10)->nullable();
            $table->uuid('tarif_id');
            $table->date('tanggal_daftar');
            $table->enum('status_pendaftaran', ['menunggu_verifikasi', 'disetujui', 'ditolak'])->default('menunggu_verifikasi');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('tarif_id')->references('id')->on('tarif')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pendaftaran_pedagang');
    }
};
