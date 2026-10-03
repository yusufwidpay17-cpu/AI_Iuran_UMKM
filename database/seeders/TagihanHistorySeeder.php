<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedagang;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TagihanHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks to safely truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Tagihan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $activePedagang = Pedagang::where('status_pedagang', 'aktif')->with('tarif')->get();
        $petugasList = ['usr-002', 'usr-003']; // Ahmad & Budi

        // Range of 60 days
        $startDate = Carbon::today()->subDays(60);
        $endDate = Carbon::today();

        $billCount = 0;

        foreach ($activePedagang as $pdg) {
            // Determine profile by merchant ID or name
            $profile = 'cukup_rajin'; // Default
            if (in_array($pdg->id, ['pdg-001', 'pdg-002'])) {
                $profile = 'rajin';
            } elseif ($pdg->id === 'pdg-004') {
                $profile = 'berisiko';
            }

            for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
                $tanggal = $date->format('Y-m-d');
                $nominal = $pdg->tarif->nominal_harian;
                $petugasId = $petugasList[array_rand($petugasList)];
                $id = 'tgh-sim-' . (++$billCount);

                $statusBayar = 'belum_bayar';
                $nominalDibayar = 0.00;
                $waktuBayar = null;
                $metodeBayar = null;
                $noBukti = null;
                $catatan = '';

                $rand = rand(1, 100);

                if ($profile === 'rajin') {
                    // Profile: Rajin (98% same day, 2% late by 1 day)
                    if ($rand <= 98) {
                        $statusBayar = 'lunas';
                        $nominalDibayar = $nominal;
                        $waktuBayar = $tanggal . ' ' . sprintf('%02d:%02d:00', rand(8, 12), rand(0, 59));
                        $metodeBayar = ['tunai', 'qris', 'transfer'][rand(0, 2)];
                        $noBukti = 'STK-' . $date->format('Ymd') . '-' . rand(100, 999);
                        $catatan = 'Lunas tepat waktu';
                    } else {
                        // 1 day late
                        $statusBayar = 'lunas';
                        $nominalDibayar = $nominal;
                        $payDate = clone $date;
                        $payDate->addDay();
                        $waktuBayar = $payDate->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', rand(9, 15), rand(0, 59));
                        $metodeBayar = ['tunai', 'qris', 'transfer'][rand(0, 2)];
                        $noBukti = 'STK-' . $payDate->format('Ymd') . '-' . rand(100, 999);
                        $catatan = 'Lunas (terlambat 1 hari)';
                    }
                } elseif ($profile === 'cukup_rajin') {
                    // Profile: Cukup Rajin
                    // - 50% paid on time
                    // - 30% paid late by 2 to 5 days
                    // - 10% paid partially (50% paid)
                    // - 10% unpaid
                    if ($rand <= 50) {
                        $statusBayar = 'lunas';
                        $nominalDibayar = $nominal;
                        $waktuBayar = $tanggal . ' ' . sprintf('%02d:%02d:00', rand(8, 13), rand(0, 59));
                        $metodeBayar = ['tunai', 'qris', 'transfer'][rand(0, 2)];
                        $noBukti = 'STK-' . $date->format('Ymd') . '-' . rand(100, 999);
                        $catatan = 'Lunas tepat waktu';
                    } elseif ($rand <= 80) {
                        // Late by 2 to 5 days
                        $statusBayar = 'lunas';
                        $nominalDibayar = $nominal;
                        $delay = rand(2, 5);
                        $payDate = clone $date;
                        $payDate->addDays($delay);
                        
                        // Ensure we don't pay in the future relative to today
                        if ($payDate->lte($endDate)) {
                            $waktuBayar = $payDate->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', rand(9, 16), rand(0, 59));
                            $noBukti = 'STK-' . $payDate->format('Ymd') . '-' . rand(100, 999);
                            $metodeBayar = ['tunai', 'qris', 'transfer'][rand(0, 2)];
                            $catatan = "Lunas (terlambat $delay hari)";
                        } else {
                            // If paydate would be in the future, count as unpaid/belum_bayar for now
                            $statusBayar = 'belum_bayar';
                            $nominalDibayar = 0.00;
                            $catatan = 'Belum dibayar (jatuh tempo)';
                        }
                    } elseif ($rand <= 90) {
                        // Paid partially (50%)
                        $statusBayar = 'bayar_sebagian';
                        $nominalDibayar = $nominal * 0.50;
                        $delay = rand(1, 3);
                        $payDate = clone $date;
                        $payDate->addDays($delay);
                        
                        if ($payDate->lte($endDate)) {
                            $waktuBayar = $payDate->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', rand(10, 16), rand(0, 59));
                            $metodeBayar = 'tunai';
                        }
                        $catatan = 'Dibayar sebagian';
                    } else {
                        // Unpaid
                        $statusBayar = 'belum_bayar';
                        $nominalDibayar = 0.00;
                        $catatan = 'Belum dibayar';
                    }
                } else {
                    // Profile: Berisiko
                    // - 20% paid on time
                    // - 20% paid very late (7 to 15 days)
                    // - 25% paid partially (30% paid)
                    // - 35% unpaid
                    if ($rand <= 20) {
                        $statusBayar = 'lunas';
                        $nominalDibayar = $nominal;
                        $waktuBayar = $tanggal . ' ' . sprintf('%02d:%02d:00', rand(8, 14), rand(0, 59));
                        $metodeBayar = ['tunai', 'qris'][rand(0, 1)];
                        $noBukti = 'STK-' . $date->format('Ymd') . '-' . rand(100, 999);
                        $catatan = 'Lunas tepat waktu';
                    } elseif ($rand <= 40) {
                        // Very late: 7 to 15 days
                        $statusBayar = 'lunas';
                        $nominalDibayar = $nominal;
                        $delay = rand(7, 15);
                        $payDate = clone $date;
                        $payDate->addDays($delay);

                        if ($payDate->lte($endDate)) {
                            $waktuBayar = $payDate->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', rand(9, 16), rand(0, 59));
                            $noBukti = 'STK-' . $payDate->format('Ymd') . '-' . rand(100, 999);
                            $metodeBayar = 'tunai';
                            $catatan = "Lunas terlambat ($delay hari)";
                        } else {
                            $statusBayar = 'belum_bayar';
                            $nominalDibayar = 0.00;
                            $catatan = 'Menunggak lama';
                        }
                    } elseif ($rand <= 65) {
                        // Paid partially (30%)
                        $statusBayar = 'bayar_sebagian';
                        $nominalDibayar = $nominal * 0.30;
                        $delay = rand(3, 7);
                        $payDate = clone $date;
                        $payDate->addDays($delay);

                        if ($payDate->lte($endDate)) {
                            $waktuBayar = $payDate->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', rand(11, 15), rand(0, 59));
                            $metodeBayar = 'tunai';
                        }
                        $catatan = 'Dibayar sebagian kecil';
                    } else {
                        // Unpaid
                        $statusBayar = 'belum_bayar';
                        $nominalDibayar = 0.00;
                        $catatan = 'Tunggakan';
                    }
                }

                Tagihan::create([
                    'id' => $id,
                    'pedagang_id' => $pdg->id,
                    'tarif_id' => $pdg->tarif_id,
                    'petugas_id' => $petugasId,
                    'tanggal_tagihan' => $tanggal,
                    'nominal_tagihan' => $nominal,
                    'nominal_dibayar' => $nominalDibayar,
                    'status_bayar' => $statusBayar,
                    'waktu_bayar' => $waktuBayar,
                    'metode_bayar' => $metodeBayar,
                    'no_bukti' => $noBukti,
                    'catatan' => $catatan,
                ]);
            }
        }
    }
}
