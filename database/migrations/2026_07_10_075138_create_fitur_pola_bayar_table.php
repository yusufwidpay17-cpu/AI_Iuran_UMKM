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
        Schema::create('fitur_pola_bayar', function (Blueprint $table) {
            $table->id();
            $table->string('pedagang_id', 36);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->integer('total_hari_tagihan');
            $table->integer('jumlah_lunas');
            $table->integer('jumlah_terlambat');
            $table->integer('jumlah_bayar_sebagian');
            $table->decimal('rata_rata_keterlambatan_hari', 10, 2);
            $table->decimal('rasio_ketepatan_bayar', 5, 2);
            $table->decimal('total_nominal_tagihan', 10, 2);
            $table->decimal('total_nominal_dibayar', 10, 2);
            $table->decimal('total_tunggakan', 10, 2);
            $table->timestamp('dihitung_pada')->useCurrent();
            $table->timestamps();

            $table->foreign('pedagang_id')->references('id')->on('pedagang')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fitur_pola_bayar');
    }
};
