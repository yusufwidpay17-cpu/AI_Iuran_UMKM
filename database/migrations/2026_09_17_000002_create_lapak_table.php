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
        Schema::create('lapak', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_lapak', 20)->unique();
            $table->string('blok_kios', 10)->nullable();
            $table->uuid('pedagang_id')->nullable();
            $table->uuid('tarif_id')->nullable();
            $table->enum('kategori', ['kuliner', 'pakaian', 'elektronik', 'sayuran', 'buah', 'sembako', 'lainnya'])->default('kuliner');
            $table->enum('status_lapak', ['aktif', 'nonaktif', 'kosong'])->default('kosong');
            $table->date('tanggal_mulai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pedagang_id')->references('id')->on('pedagang')->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('lapak');
    }
};
