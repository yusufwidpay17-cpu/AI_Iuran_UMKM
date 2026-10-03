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
        Schema::create('ml_model', function (Blueprint $table) {
            $table->id();
            $table->string('nama_model', 100);
            $table->string('algoritma', 50)->default('K-Means');
            $table->integer('jumlah_cluster');
            $table->json('parameter')->nullable();
            $table->decimal('silhouette_score', 5, 3)->nullable();
            $table->string('path_model_pkl', 255)->nullable();
            $table->string('path_scaler_pkl', 255)->nullable();
            $table->date('periode_data_awal');
            $table->date('periode_data_akhir');
            $table->boolean('is_active')->default(false);
            $table->timestamp('trained_at')->useCurrent();
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
        Schema::dropIfExists('ml_model');
    }
};
