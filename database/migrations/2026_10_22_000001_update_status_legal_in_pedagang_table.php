<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ubah column status_legal menjadi varchar(50) untuk mendukung 'legal' dan 'tutup_sementara'
        DB::statement("ALTER TABLE pedagang MODIFY COLUMN status_legal VARCHAR(50) NOT NULL DEFAULT 'legal'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE pedagang MODIFY COLUMN status_legal ENUM('legal', 'ilegal', 'proses_izin') NOT NULL DEFAULT 'legal'");
    }
};
