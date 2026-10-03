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
        Schema::create('log_pengiriman', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->timestamp('waktu_kirim')->useCurrent();
            $table->string('penerima', 100);
            $table->enum('tipe_channel', ['whatsapp', 'email']);
            $table->enum('status', ['terkirim', 'gagal']);
            $table->text('response_api')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_pengiriman');
    }
};
