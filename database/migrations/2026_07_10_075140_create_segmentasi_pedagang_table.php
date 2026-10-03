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
        Schema::create('segmentasi_pedagang', function (Blueprint $table) {
            $table->id();
            $table->string('pedagang_id', 36);
            $table->foreignId('ml_model_id')->constrained('ml_model')->onDelete('cascade');
            $table->integer('cluster_index');
            $table->string('label_segmen', 50);
            $table->json('fitur_snapshot')->nullable();
            $table->timestamp('tanggal_analisis')->useCurrent();
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
        Schema::dropIfExists('segmentasi_pedagang');
    }
};
