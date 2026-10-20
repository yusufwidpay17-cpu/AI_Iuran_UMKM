<?php

namespace App\Services;

use App\Models\KalenderOperasional;
use App\Models\Pedagang;
use App\Models\Tagihan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KalenderOperasionalService
{
    /**
     * Dapatkan aturan kalender operasional aktif untuk tanggal tertentu
     */
    public function getRuleForDate($date)
    {
        $d = Carbon::parse($date)->format('Y-m-d');
        return KalenderOperasional::aktif()->forDate($d)->first();
    }

    /**
     * Cek apakah tanggal tertentu adalah hari bebas iuran (iuran tidak dikenakan)
     */
    public function isBebasIuran($date): bool
    {
        $rule = $this->getRuleForDate($date);
        return $rule && $rule->isBebasIuran();
    }

    /**
     * Cek apakah penagihan operasional diliburkan / tidak dilakukan (apapun kebijakan iurannya)
     */
    public function isPenagihanDiliburkan($date): bool
    {
        return $this->getRuleForDate($date) !== null;
    }

    /**
     * Dapatkan nomor bukti kuitansi unik
     */
    public function generateNoBukti($prefix = 'INV-')
    {
        do {
            $noBukti = $prefix . date('YmdHis') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Tagihan::where('no_bukti', $noBukti)->exists());

        return $noBukti;
    }

    /**
     * Helper terpusat untuk generate tagihan harian dengan proteksi kalender & transaksi database
     */
    public function generateBillsForDate($date, $force = false)
    {
        $tanggal = Carbon::parse($date)->format('Y-m-d');

        // 1. Cek apakah tanggal ini ditetapkan sebagai Bebas Iuran
        if (!$force && $this->isBebasIuran($tanggal)) {
            $rule = $this->getRuleForDate($tanggal);
            return [
                'success' => false,
                'created_count' => 0,
                'reason' => 'bebas_iuran',
                'rule' => $rule,
                'message' => "Tanggal " . Carbon::parse($tanggal)->isoFormat('D MMMM YYYY') . " ditetapkan sebagai hari Bebas Iuran (" . ($rule->alasan ?? 'Kalender Operasional') . "). Tidak ada tagihan baru yang diterbitkan."
            ];
        }

        $createdCount = 0;

        // 2. Gunakan database transaction untuk atomic & concurrency safety
        DB::beginTransaction();
        try {
            $activeMerchants = Pedagang::where('status_pedagang', 'aktif')
                ->where(function($q) use ($tanggal) {
                    $q->whereNull('tanggal_daftar')
                      ->orWhere('tanggal_daftar', '<', $tanggal);
                })
                ->lockForUpdate()
                ->get();

            foreach ($activeMerchants as $pdg) {
                // Cek duplikasi tagihan (berdasarkan pedagang_id dan tanggal_tagihan)
                $exists = Tagihan::where('pedagang_id', $pdg->id)
                    ->where('tanggal_tagihan', $tanggal)
                    ->exists();

                if (!$exists) {
                    $nominal = (float) ($pdg->tarif->nominal_harian ?? 0);
                    $dibayar = 0.00;
                    $status = 'belum_bayar';
                    $metode = null;
                    $waktu = null;
                    $catatan = null;

                    // Potong deposit jika tersedia (hanya pada hari penagihan sah)
                    if ($pdg->saldo_deposit > 0) {
                        if ($pdg->saldo_deposit >= $nominal) {
                            $dibayar = $nominal;
                            $pdg->saldo_deposit -= $nominal;
                            $status = 'lunas';
                            $metode = 'tunai';
                            $waktu = now();
                            $catatan = 'Lunas otomatis menggunakan saldo deposit.';
                        } else {
                            $dibayar = (float) $pdg->saldo_deposit;
                            $status = 'bayar_sebagian';
                            $pdg->saldo_deposit = 0.00;
                            $waktu = now();
                            $catatan = 'Dibayar sebagian menggunakan sisa saldo deposit.';
                        }
                        $pdg->save();
                    }

                    Tagihan::create([
                        'id' => (string) Str::uuid(),
                        'pedagang_id' => $pdg->id,
                        'tarif_id' => $pdg->tarif_id,
                        'jenis_iuran' => 'Iuran Harian',
                        'petugas_id' => null,
                        'tanggal_tagihan' => $tanggal,
                        'nominal_tagihan' => $nominal,
                        'nominal_dibayar' => $dibayar,
                        'status_bayar' => $status,
                        'waktu_bayar' => $waktu,
                        'metode_bayar' => $metode,
                        'no_bukti' => ($status === 'lunas' || $status === 'bayar_sebagian') ? $this->generateNoBukti('DEP-AUTO-') : null,
                        'catatan' => $catatan
                    ]);
                    $createdCount++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'created_count' => $createdCount,
                'reason' => 'normal',
                'rule' => null,
                'message' => "Berhasil memproses {$createdCount} tagihan untuk tanggal " . Carbon::parse($tanggal)->format('d/m/Y') . "."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Tampilkan pratinjau dampak tagihan jika suatu rentang tanggal ditetapkan sebagai bebas iuran
     */
    public function previewDampakTagihan(KalenderOperasional $record)
    {
        $bills = Tagihan::with(['pedagang.tarif'])
            ->whereBetween('tanggal_tagihan', [$record->tanggal_mulai->format('Y-m-d'), $record->tanggal_selesai->format('Y-m-d')])
            ->get();

        $belumBayar = $bills->where('status_bayar', 'belum_bayar');
        $cicilan = $bills->where('status_bayar', 'bayar_sebagian');
        $lunas = $bills->where('status_bayar', 'lunas');
        $dibatalkan = $bills->where('status_bayar', 'dibatalkan');

        return [
            'total_bills' => $bills->count(),
            'belum_bayar_count' => $belumBayar->count(),
            'belum_bayar_nominal' => $belumBayar->sum('nominal_tagihan'),
            'cicilan_count' => $cicilan->count(),
            'cicilan_dibayar' => $cicilan->sum('nominal_dibayar'),
            'cicilan_sisa' => $cicilan->sum(fn($b) => max(0, $b->nominal_tagihan - $b->nominal_dibayar)),
            'lunas_count' => $lunas->count(),
            'lunas_nominal' => $lunas->sum('nominal_dibayar'),
            'dibatalkan_count' => $dibatalkan->count(),
            'bills' => $bills,
        ];
    }

    /**
     * Jalankan koreksi tagihan historis yang telanjur dibuat dengan audit trail lengkap
     */
    public function koreksiTagihanHistoris(KalenderOperasional $record, $userId)
    {
        if ($record->kebijakan_iuran !== 'bebas_iuran') {
            return [
                'success' => false,
                'message' => 'Koreksi pembatalan tagihan hanya dapat diterapkan untuk catatan Bebas Iuran.'
            ];
        }

        DB::beginTransaction();
        try {
            $bills = Tagihan::whereBetween('tanggal_tagihan', [$record->tanggal_mulai->format('Y-m-d'), $record->tanggal_selesai->format('Y-m-d')])
                ->where('status_bayar', '!=', 'dibatalkan')
                ->get();

            $correctedCount = 0;
            $totalNominalDibatalkan = 0;

            foreach ($bills as $bill) {
                // Skenario 1: Tagihan belum dibayar sama sekali (nominal_dibayar == 0)
                if ($bill->status_bayar === 'belum_bayar' && (float)$bill->nominal_dibayar == 0) {
                    $totalNominalDibatalkan += (float)$bill->nominal_tagihan;
                    $bill->nominal_tagihan = 0.00;
                    $bill->status_bayar = 'dibatalkan';
                    $bill->catatan = ($bill->catatan ? $bill->catatan . ' | ' : '') . "[DIBEBASKAN - KALENDER OPERASIONAL] Alasan: {$record->alasan} (" . Carbon::now()->isoFormat('D MMM YYYY HH:mm') . ")";
                    $bill->save();
                    $correctedCount++;
                }
                // Skenario 2: Tagihan memiliki pembayaran cicilan/lunas
                // Jangan hapus transaksi/kuitansi yang sah! Sesuaikan nominal tagihan menjadi nominal dibayar agar tidak menunggak
                elseif ((float)$bill->nominal_dibayar > 0) {
                    $sisaSebelum = max(0, $bill->nominal_tagihan - $bill->nominal_dibayar);
                    if ($sisaSebelum > 0) {
                        $bill->nominal_tagihan = $bill->nominal_dibayar;
                        $bill->status_bayar = 'lunas';
                        $bill->catatan = ($bill->catatan ? $bill->catatan . ' | ' : '') . "[SISA DIBEBASKAN - KALENDER OPERASIONAL] Pembayaran sah dipertahankan. Sisa Rp " . number_format($sisaSebelum, 0, ',', '.') . " dibebaskan.";
                        $bill->save();
                        $correctedCount++;
                    }
                }
            }

            // Catat ke Audit Log
            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $userId,
                'aksi' => 'KOREKSI_TAGIHAN_KALENDER',
                'deskripsi' => "Koreksi tagihan historis akibat penetapan Bebas Iuran ({$record->kategori_label}): {$correctedCount} tagihan disesuaikan, total nominal dibebaskan Rp " . number_format($totalNominalDibatalkan, 0, ',', '.') . ". Alasan: {$record->alasan}.",
                'created_at' => now(),
            ]);

            // Perbarui catatan audit pada record kalender
            $record->catatan_audit = ($record->catatan_audit ? $record->catatan_audit . "\n" : "") . "[" . Carbon::now()->isoFormat('D MMM YYYY HH:mm') . "] Koreksi historis diterapkan: {$correctedCount} tagihan disesuaikan oleh user ID: {$userId}.";
            $record->save();

            DB::commit();

            return [
                'success' => true,
                'corrected_count' => $correctedCount,
                'total_nominal_dibatalkan' => $totalNominalDibatalkan,
                'message' => "Berhasil mengoreksi {$correctedCount} tagihan historis. Kewajiban yang belum bayar telah dibebaskan secara aman dan transaksi pembayaran sah tetap terjaga."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Hubungkan pedagang baru ke Transaksi Pembayaran:
     * Otomatis terbitkan tagihan perdana yang jatuh tempo pada besok harinya (H+1 pendaftaran).
     */
    public function generateInitialBillNextDay(Pedagang $pedagang)
    {
        if ($pedagang->status_pedagang !== 'aktif') {
            return null;
        }

        // Tentukan tanggal jatuh tempo perdana: besok harinya (H+1 pendaftaran)
        $tanggalDaftar = $pedagang->tanggal_daftar ? Carbon::parse($pedagang->tanggal_daftar) : Carbon::today();
        $tanggalJatuhTempo = $tanggalDaftar->copy()->addDay()->format('Y-m-d');

        // Proteksi idempotensi: Jangan duplikasi tagihan jika sudah ada
        $existing = Tagihan::where('pedagang_id', $pedagang->id)
            ->where('tanggal_tagihan', $tanggalJatuhTempo)
            ->first();

        if ($existing) {
            return $existing;
        }

        $rule = $this->getRuleForDate($tanggalJatuhTempo);
        $isBebasIuran = $rule && $rule->isBebasIuran();

        $tarif = $pedagang->tarif ?? \App\Models\Tarif::find($pedagang->tarif_id);
        $nominal = (float) ($tarif->nominal_harian ?? 0);
        $dibayar = 0.00;
        $status = 'belum_bayar';
        $metode = null;
        $waktu = null;
        $noBukti = null;
        $catatan = 'Tagihan iuran perdana (jatuh tempo H+1 pendaftaran pedagang).';

        if ($pedagang->saldo_deposit > 0) {
            if ($pedagang->saldo_deposit >= $nominal) {
                $dibayar = $nominal;
                $pedagang->saldo_deposit -= $nominal;
                $status = 'lunas';
                $metode = 'tunai';
                $waktu = now();
                $noBukti = $this->generateNoBukti('DEP-AUTO-');
                $catatan = 'Lunas otomatis menggunakan saldo deposit awal.';
            } else {
                $dibayar = (float) $pedagang->saldo_deposit;
                $pedagang->saldo_deposit = 0.00;
                $status = 'bayar_sebagian';
                $metode = 'tunai';
                $waktu = now();
                $noBukti = $this->generateNoBukti('DEP-AUTO-');
                $catatan = 'Dibayar sebagian menggunakan saldo deposit awal.';
            }
            $pedagang->save();
        }

        return Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $pedagang->id,
            'tarif_id' => $pedagang->tarif_id,
            'jenis_iuran' => 'Iuran Harian',
            'petugas_id' => null,
            'tanggal_tagihan' => $tanggalJatuhTempo,
            'nominal_tagihan' => $nominal,
            'nominal_dibayar' => $dibayar,
            'status_bayar' => $status,
            'waktu_bayar' => $waktu,
            'metode_bayar' => $metode,
            'no_bukti' => $noBukti,
            'catatan' => $catatan
        ]);
    }
}
