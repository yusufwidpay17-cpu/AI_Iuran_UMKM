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
        // 1. Buat tabel kalender_operasional
        if (!Schema::hasTable('kalender_operasional')) {
            Schema::create('kalender_operasional', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->string('kategori', 50); // libur_nasional_pasar, kegiatan_pesantren, petugas_tugas_lain, penagihan_dihentikan_sementara, keadaan_darurat, lainnya
                $table->text('alasan');
                $table->enum('kebijakan_iuran', ['bebas_iuran', 'kewajiban_tetap_berlaku'])->default('kewajiban_tetap_berlaku');
                $table->enum('status', ['aktif', 'dibatalkan'])->default('aktif');
                $table->string('created_by', 36)->nullable();
                $table->string('updated_by', 36)->nullable();
                $table->text('catatan_audit')->nullable();
                $table->timestamps();

                $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_kalender_tanggal_range');
                $table->index('status', 'idx_kalender_status');
                $table->index('kebijakan_iuran', 'idx_kalender_kebijakan');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            });
        }

        // 2. Perluas enum status_bayar pada tabel tagihan agar mendukung status 'dibatalkan'
        try {
            DB::statement("ALTER TABLE tagihan MODIFY COLUMN status_bayar ENUM('lunas', 'belum_bayar', 'bayar_sebagian', 'dibatalkan') NOT NULL DEFAULT 'belum_bayar'");
        } catch (\Exception $e) {
            // Abaikan jika database tidak mendukung alter enum langsung
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kalender_operasional');
    }
};
