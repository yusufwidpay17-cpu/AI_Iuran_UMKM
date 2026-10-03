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
        Schema::table('tagihan', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan', 'jenis_iuran')) {
                $table->string('jenis_iuran', 50)->default('Iuran Harian')->after('tarif_id');
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
        Schema::table('tagihan', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan', 'jenis_iuran')) {
                $table->dropColumn('jenis_iuran');
            }
        });
    }
};
