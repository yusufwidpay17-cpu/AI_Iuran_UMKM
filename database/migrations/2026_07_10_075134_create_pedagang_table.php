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
        Schema::create('pedagang', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('nama_pedagang', 100);
            $table->string('no_ktp', 20)->unique();
            $table->string('no_telepon', 20)->nullable();
            $table->string('nama_usaha', 100)->nullable();
            $table->enum('kategori_usaha', ['kuliner', 'pakaian', 'elektronik', 'sayuran', 'buah', 'sembako', 'lainnya']);
            $table->string('kategori_usaha_detail', 100)->nullable();
            $table->string('no_kios', 20)->unique();
            $table->string('blok_kios', 10)->nullable()->default('');
            $table->date('tanggal_daftar');
            $table->enum('status_pedagang', ['aktif', 'nonaktif', 'sementara_tutup'])->default('aktif');
            $table->enum('status_legal', ['legal', 'ilegal', 'proses_izin'])->default('legal');
            $table->string('foto_ktp', 255)->nullable();
            $table->string('foto_izin_usaha', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->string('tarif_id', 36);
            $table->decimal('saldo_deposit', 10, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('tarif_id')->references('id')->on('tarif')->onUpdate('cascade');
            
            // Indexing for search and filters
            $table->index(['nama_pedagang', 'no_kios', 'nama_usaha'], 'idx_pedagang_search');
            $table->index(['status_pedagang', 'status_legal', 'blok_kios', 'kategori_usaha'], 'idx_pedagang_filter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedagang');
    }
};
