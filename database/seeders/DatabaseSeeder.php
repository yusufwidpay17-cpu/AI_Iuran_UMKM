<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tarif;
use App\Models\Pedagang;
use App\Models\Tagihan;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Users (passwords hashed using bcrypt)
        User::create([
            'id' => 'usr-001',
            'nama' => 'Admin Yusuf',
            'username' => 'yusuf123',
            'password' => bcrypt('12345678'),
            'role' => 'superadmin'
        ]);
        User::create([
            'id' => 'usr-002',
            'nama' => 'Petugas Ahmad',
            'username' => 'ahmad',
            'password' => bcrypt('12345678'),
            'role' => 'petugas'
        ]);
        User::create([
            'id' => 'usr-003',
            'nama' => 'Petugas Budi',
            'username' => 'budi',
            'password' => bcrypt('12345678'),
            'role' => 'petugas'
        ]);
        User::create([
            'id' => 'usr-004',
            'nama' => 'Pak Bambang',
            'username' => 'bambang',
            'password' => bcrypt('12345678'),
            'role' => 'admin'
        ]);

        // 2. Seed Rates (Tarif)
        Tarif::create(['id' => 'trf-001', 'nama_tarif' => 'Tarif Kuliner', 'kategori' => 'kuliner', 'nominal_harian' => 15000.00, 'berlaku_mulai' => '2026-01-01', 'aktif' => 1]);
        Tarif::create(['id' => 'trf-002', 'nama_tarif' => 'Tarif Pakaian', 'kategori' => 'pakaian', 'nominal_harian' => 20000.00, 'berlaku_mulai' => '2026-01-01', 'aktif' => 1]);
        Tarif::create(['id' => 'trf-003', 'nama_tarif' => 'Tarif Sembako', 'kategori' => 'sembako', 'nominal_harian' => 10000.00, 'berlaku_mulai' => '2026-01-01', 'aktif' => 1]);
        Tarif::create(['id' => 'trf-004', 'nama_tarif' => 'Tarif Buah & Sayuran', 'kategori' => 'sayuran', 'nominal_harian' => 8000.00, 'berlaku_mulai' => '2026-01-01', 'aktif' => 1]);
        Tarif::create(['id' => 'trf-005', 'nama_tarif' => 'Tarif Elektronik', 'kategori' => 'elektronik', 'nominal_harian' => 12000.00, 'berlaku_mulai' => '2026-01-01', 'aktif' => 1]);
        Tarif::create(['id' => 'trf-006', 'nama_tarif' => 'Tarif Lain-lain', 'kategori' => 'lainnya', 'nominal_harian' => 10000.00, 'berlaku_mulai' => '2026-01-01', 'aktif' => 1]);

        // 3. Seed Merchants (Pedagang)
        Pedagang::create(['id' => 'pdg-001', 'nama_pedagang' => 'Sri Wahyuni', 'no_ktp' => '3201020304050001', 'no_telepon' => '081234567890', 'nama_usaha' => 'Warung Soto Mbah Sri', 'kategori_usaha' => 'kuliner', 'no_kios' => 'A-01', 'blok_kios' => 'Blok A', 'tanggal_daftar' => '2026-02-15', 'status_pedagang' => 'aktif', 'status_legal' => 'legal', 'tarif_id' => 'trf-001', 'saldo_deposit' => 0.00]);
        Pedagang::create(['id' => 'pdg-002', 'nama_pedagang' => 'H. Mahmud', 'no_ktp' => '3201020304050002', 'no_telepon' => '082134567891', 'nama_usaha' => 'Toko Sembako Jaya', 'kategori_usaha' => 'sembako', 'no_kios' => 'A-02', 'blok_kios' => 'Blok A', 'tanggal_daftar' => '2026-02-20', 'status_pedagang' => 'aktif', 'status_legal' => 'legal', 'tarif_id' => 'trf-003', 'saldo_deposit' => 20000.00]);
        Pedagang::create(['id' => 'pdg-003', 'nama_pedagang' => 'Toni Setiawan', 'no_ktp' => '3201020304050003', 'no_telepon' => '083134567892', 'nama_usaha' => 'Butik Berkah Busana', 'kategori_usaha' => 'pakaian', 'no_kios' => 'B-05', 'blok_kios' => 'Blok B', 'tanggal_daftar' => '2026-03-01', 'status_pedagang' => 'aktif', 'status_legal' => 'legal', 'tarif_id' => 'trf-002', 'saldo_deposit' => 0.00]);
        Pedagang::create(['id' => 'pdg-004', 'nama_pedagang' => 'Siti Rahma', 'no_ktp' => '3201020304050004', 'no_telepon' => '084134567893', 'nama_usaha' => 'Kios Buah Segar Ibu Siti', 'kategori_usaha' => 'buah', 'no_kios' => 'C-10', 'blok_kios' => 'Blok C', 'tanggal_daftar' => '2026-03-05', 'status_pedagang' => 'aktif', 'status_legal' => 'ilegal', 'tarif_id' => 'trf-004', 'saldo_deposit' => 0.00]);
        Pedagang::create(['id' => 'pdg-005', 'nama_pedagang' => 'Joni Rahmat', 'no_ktp' => '3201020304050005', 'no_telepon' => '085134567894', 'nama_usaha' => 'Servis Elektronik Joni', 'kategori_usaha' => 'elektronik', 'no_kios' => 'D-03', 'blok_kios' => 'Blok D', 'tanggal_daftar' => '2026-03-10', 'status_pedagang' => 'sementara_tutup', 'status_legal' => 'proses_izin', 'tarif_id' => 'trf-005', 'saldo_deposit' => 0.00]);
        Pedagang::create(['id' => 'pdg-006', 'nama_pedagang' => 'Budi Santoso', 'no_ktp' => '3201020304050006', 'no_telepon' => '086134567895', 'nama_usaha' => 'Kios Sayur Segar Pak Budi', 'kategori_usaha' => 'sayuran', 'no_kios' => 'A-03', 'blok_kios' => 'Blok A', 'tanggal_daftar' => '2026-03-12', 'status_pedagang' => 'aktif', 'status_legal' => 'legal', 'tarif_id' => 'trf-004', 'saldo_deposit' => 0.00]);
        Pedagang::create(['id' => 'pdg-007', 'nama_pedagang' => 'Dewi Lestari', 'no_ktp' => '3201020304050007', 'no_telepon' => '087134567896', 'nama_usaha' => 'Toko Mainan Dewi', 'kategori_usaha' => 'lainnya', 'no_kios' => 'E-01', 'blok_kios' => 'Blok E', 'tanggal_daftar' => '2026-04-01', 'status_pedagang' => 'nonaktif', 'status_legal' => 'legal', 'tarif_id' => 'trf-006', 'saldo_deposit' => 0.00]);

        // 4. Generate 30 Days of Billing Data (2026-06-10 to 2026-07-09)
        $startDate = Carbon::create(2026, 6, 10);
        $endDate = Carbon::create(2026, 7, 9);
        $pedagangs = Pedagang::whereIn('id', ['pdg-001', 'pdg-002', 'pdg-003', 'pdg-004', 'pdg-006'])->get();
        $petugasList = ['usr-002', 'usr-003'];

        $billCount = 0;
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            foreach ($pedagangs as $pdg) {
                $nominal = $pdg->tarif->nominal_harian;
                $tanggal = $date->format('Y-m-d');
                $petugasId = $petugasList[rand(0, 1)];

                $id = 'tgh-generated-' . ++$billCount;

                // Behavior profiling
                if ($pdg->id == 'pdg-001' || $pdg->id == 'pdg-002') {
                    // Profile: Rajin (95% same day, 5% late by 1 day)
                    $rand = rand(1, 100);
                    if ($rand <= 95) {
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => $petugasId,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => $nominal,
                            'status_bayar' => 'lunas',
                            'waktu_bayar' => $tanggal . ' ' . sprintf('%02d:%02d:00', rand(8, 11), rand(0, 59)),
                            'metode_bayar' => ['tunai', 'transfer', 'qris'][rand(0, 2)],
                            'no_bukti' => 'STK-' . $date->format('Ymd') . '-' . rand(100, 999),
                            'catatan' => 'Lunas'
                        ]);
                    } else {
                        $waktuBayar = (clone $date)->addDay()->format('Y-m-d') . ' 10:00:00';
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => $petugasId,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => $nominal,
                            'status_bayar' => 'lunas',
                            'waktu_bayar' => $waktuBayar,
                            'metode_bayar' => 'tunai',
                            'no_bukti' => 'STK-' . (clone $date)->addDay()->format('Ymd') . '-' . rand(100, 999),
                            'catatan' => 'Lunas terlambat 1 hari'
                        ]);
                    }
                } elseif ($pdg->id == 'pdg-006') {
                    // Profile: Cukup Rajin (65% same day, 20% late by 2-4 days, 10% partial, 5% unpaid)
                    $rand = rand(1, 100);
                    if ($rand <= 65) {
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => $petugasId,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => $nominal,
                            'status_bayar' => 'lunas',
                            'waktu_bayar' => $tanggal . ' ' . sprintf('%02d:%02d:00', rand(9, 12), rand(0, 59)),
                            'metode_bayar' => 'tunai',
                            'no_bukti' => 'STK-' . $date->format('Ymd') . '-' . rand(100, 999),
                            'catatan' => 'Lunas'
                        ]);
                    } elseif ($rand <= 85) {
                        $daysLate = rand(2, 4);
                        $waktuBayar = (clone $date)->addDays($daysLate)->format('Y-m-d') . ' 11:30:00';
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => $petugasId,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => $nominal,
                            'status_bayar' => 'lunas',
                            'waktu_bayar' => $waktuBayar,
                            'metode_bayar' => 'tunai',
                            'no_bukti' => 'STK-' . (clone $date)->addDays($daysLate)->format('Ymd') . '-' . rand(100, 999),
                            'catatan' => "Lunas terlambat $daysLate hari"
                        ]);
                    } elseif ($rand <= 95) {
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => $petugasId,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => $nominal / 2, // partial
                            'status_bayar' => 'bayar_sebagian',
                            'waktu_bayar' => $tanggal . ' 13:00:00',
                            'metode_bayar' => 'tunai',
                            'no_bukti' => 'STK-PRT-' . $date->format('Ymd') . '-' . rand(100, 999),
                            'catatan' => 'Bayar setengah'
                        ]);
                    } else {
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => null,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => 0.00,
                            'status_bayar' => 'belum_bayar',
                            'waktu_bayar' => null,
                            'metode_bayar' => null,
                            'no_bukti' => null,
                            'catatan' => 'Toko tutup saat ditagih'
                        ]);
                    }
                } else {
                    // Profile: Berisiko (25% same day, 15% partial, 60% unpaid)
                    $rand = rand(1, 100);
                    if ($rand <= 25) {
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => $petugasId,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => $nominal,
                            'status_bayar' => 'lunas',
                            'waktu_bayar' => $tanggal . ' ' . sprintf('%02d:%02d:00', rand(10, 14), rand(0, 59)),
                            'metode_bayar' => 'tunai',
                            'no_bukti' => 'STK-' . $date->format('Ymd') . '-' . rand(100, 999),
                            'catatan' => 'Lunas'
                        ]);
                    } elseif ($rand <= 40) {
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => $petugasId,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => 2000.00, // partial
                            'status_bayar' => 'bayar_sebagian',
                            'waktu_bayar' => $tanggal . ' 14:00:00',
                            'metode_bayar' => 'tunai',
                            'no_bukti' => 'STK-PRT-' . $date->format('Ymd') . '-' . rand(100, 999),
                            'catatan' => 'Bayar sebagian kecil'
                        ]);
                    } else {
                        Tagihan::create([
                            'id' => $id,
                            'pedagang_id' => $pdg->id,
                            'tarif_id' => $pdg->tarif_id,
                            'petugas_id' => null,
                            'tanggal_tagihan' => $tanggal,
                            'nominal_tagihan' => $nominal,
                            'nominal_dibayar' => 0.00,
                            'status_bayar' => 'belum_bayar',
                            'waktu_bayar' => null,
                            'metode_bayar' => null,
                            'no_bukti' => null,
                            'catatan' => 'Belum bayar'
                        ]);
                    }
                }
            }
        }
    }
}
