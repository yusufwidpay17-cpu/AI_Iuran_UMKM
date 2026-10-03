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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('pedagang_id', 36);
            $table->string('tarif_id', 36);
            $table->string('petugas_id', 36)->nullable();
            $table->date('tanggal_tagihan');
            $table->decimal('nominal_tagihan', 10, 2);
            $table->decimal('nominal_dibayar', 10, 2)->default(0.00);
            $table->enum('status_bayar', ['lunas', 'belum_bayar', 'bayar_sebagian'])->default('belum_bayar');
            $table->timestamp('waktu_bayar')->nullable();
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris'])->nullable();
            $table->string('no_bukti', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('pedagang_id')->references('id')->on('pedagang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tarif_id')->references('id')->on('tarif')->onUpdate('cascade');
            $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->unique(['pedagang_id', 'tanggal_tagihan'], 'uq_pedagang_tanggal');
            $table->index('tanggal_tagihan', 'idx_tagihan_tanggal');
            $table->index('status_bayar', 'idx_tagihan_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tagihan');
    }
};
