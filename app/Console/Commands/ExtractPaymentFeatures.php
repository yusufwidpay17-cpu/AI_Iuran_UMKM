<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedagang;
use App\Models\Tagihan;
use App\Models\FiturPolaBayar;
use Carbon\Carbon;

class ExtractPaymentFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-payment-features {--days=60 : Jumlah hari terakhir untuk dianalisis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengekstrak fitur pola pembayaran pedagang dari riwayat tagihan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        if ($days <= 0) {
            $this->error('Jumlah hari harus berupa angka positif.');
            return 1;
        }

        $startDate = Carbon::today()->subDays($days)->format('Y-m-d');
        $endDate = Carbon::today()->format('Y-m-d');

        $this->info("Memulai ekstraksi fitur pola pembayaran dari {$startDate} s/d {$endDate} ({$days} hari terakhir)...");

        $pedagangs = Pedagang::where('status_pedagang', 'aktif')->get();
        if ($pedagangs->isEmpty()) {
            $this->warn('Tidak ada pedagang aktif untuk dianalisis.');
            return 0;
        }

        $batchTime = now();
        $processedCount = 0;

        foreach ($pedagangs as $pdg) {
            $stats = Tagihan::where('pedagang_id', $pdg->id)
                ->whereBetween('tanggal_tagihan', [$startDate, $endDate])
                ->selectRaw("
                    COUNT(*) as total_hari,
                    SUM(CASE WHEN status_bayar = 'lunas' THEN 1 ELSE 0 END) as jml_lunas,
                    SUM(CASE WHEN status_bayar = 'bayar_sebagian' THEN 1 ELSE 0 END) as jml_sebagian,
                    SUM(CASE WHEN status_bayar = 'belum_bayar' THEN 1 ELSE 0 END) as jml_belum,
                    SUM(CASE WHEN status_bayar = 'lunas' AND waktu_bayar IS NOT NULL AND DATE(waktu_bayar) > tanggal_tagihan THEN 1 ELSE 0 END) as jml_terlambat,
                    SUM(
                        CASE 
                            WHEN status_bayar = 'lunas' AND waktu_bayar IS NOT NULL THEN 
                                GREATEST(0, DATEDIFF(waktu_bayar, tanggal_tagihan))
                            WHEN status_bayar = 'bayar_sebagian' AND waktu_bayar IS NOT NULL THEN 
                                GREATEST(0, DATEDIFF(waktu_bayar, tanggal_tagihan))
                            WHEN status_bayar = 'belum_bayar' AND tanggal_tagihan < ? THEN 
                                GREATEST(0, DATEDIFF(?, tanggal_tagihan))
                            ELSE 0 
                        END
                    ) as total_delay_days,
                    SUM(CASE WHEN status_bayar = 'lunas' AND (waktu_bayar IS NULL OR DATE(waktu_bayar) <= tanggal_tagihan) THEN 1 ELSE 0 END) as jml_tepat_waktu,
                    SUM(nominal_tagihan) as total_tagihan,
                    SUM(nominal_dibayar) as total_dibayar
                ", [$endDate, $endDate])
                ->first();

            if ($stats && $stats->total_hari > 0) {
                $totalHari = $stats->total_hari;
                $rasioKetepatan = $stats->jml_tepat_waktu / $totalHari;
                $avgKeterlambatan = $stats->total_delay_days / $totalHari;
                $tunggakan = max(0, $stats->total_tagihan - $stats->total_dibayar);

                FiturPolaBayar::updateOrCreate(
                    [
                        'pedagang_id' => $pdg->id,
                        'periode_awal' => $startDate,
                        'periode_akhir' => $endDate,
                    ],
                    [
                        'total_hari_tagihan' => $totalHari,
                        'jumlah_lunas' => $stats->jml_lunas,
                        'jumlah_terlambat' => $stats->jml_terlambat,
                        'jumlah_bayar_sebagian' => $stats->jml_sebagian,
                        'rata_rata_keterlambatan_hari' => $avgKeterlambatan,
                        'rasio_ketepatan_bayar' => $rasioKetepatan,
                        'total_nominal_tagihan' => $stats->total_tagihan,
                        'total_nominal_dibayar' => $stats->total_dibayar,
                        'total_tunggakan' => $tunggakan,
                        'dihitung_pada' => $batchTime,
                    ]
                );

                $processedCount++;
            }
        }

        $this->info("Ekstraksi berhasil diselesaikan. Berhasil memproses {$processedCount} pedagang.");
        return 0;
    }
}
