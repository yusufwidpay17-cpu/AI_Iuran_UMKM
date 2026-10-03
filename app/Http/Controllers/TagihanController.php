<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedagang;
use App\Models\Tagihan;
use App\Models\Lapak;
use App\Models\Tarif;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TagihanController extends Controller
{
    /**
     * Display a mobile-first list of today's / selected date's bills for data entry
     */
    public function input(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        
        // 1. Check if bills are generated for this date
        $billCount = Tagihan::where('tanggal_tagihan', $tanggal)->count();
        
        if ($billCount == 0 && $tanggal == Carbon::today()->format('Y-m-d')) {
            $this->generateBillsForDate($tanggal);
        }

        // 2. Fetch bills with filters
        $query = Tagihan::with(['pedagang.tarif', 'petugas'])
            ->where('tanggal_tagihan', $tanggal);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('pedagang', function($q) use ($search) {
                $q->where('nama_pedagang', 'like', "%{$search}%")
                  ->orWhere('no_kios', 'like', "%{$search}%");
            });
        }

        if ($request->filled('blok_kios')) {
            $blok = $request->input('blok_kios');
            $query->whereHas('pedagang', function($q) use ($blok) {
                $q->where('blok_kios', $blok);
            });
        }

        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->input('status_bayar'));
        }

        $tagihans = $query->join('pedagang', 'tagihan.pedagang_id', '=', 'pedagang.id')
            ->select('tagihan.*')
            ->orderBy('pedagang.blok_kios', 'asc')
            ->orderBy('pedagang.no_kios', 'asc')
            ->get();

        $blokList = Pedagang::whereNotNull('blok_kios')
            ->where('blok_kios', '!=', '')
            ->distinct()
            ->pluck('blok_kios');

        $pedagangs = Pedagang::where('status_pedagang', 'aktif')->orderBy('nama_pedagang', 'asc')->get();
        $tarifs = Tarif::where('aktif', 1)->get();

        return view('tagihan.input', compact('tagihans', 'tanggal', 'blokList', 'pedagangs', 'tarifs'));
    }

    /**
     * Manual "+ Tambah Iuran" entry for a merchant
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'pedagang_id' => 'required|exists:pedagang,id',
            'tanggal_tagihan' => 'required|date',
            'jenis_iuran' => 'required|string|max:50',
            'nominal_tagihan' => 'required|numeric|min:0',
            'nominal_dibayar' => 'nullable|numeric|min:0',
            'metode_bayar' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $pdg = Pedagang::findOrFail($request->pedagang_id);
        $nominalTagihan = (float) $request->nominal_tagihan;
        $nominalDibayar = (float) ($request->input('nominal_dibayar') ?? $nominalTagihan);
        $metode = $request->input('metode_bayar') ?? 'tunai';

        $langsungLunas = $request->input('status_pembayaran') === 'lunas' || $request->input('langsung_lunas', 1) == 1;
        if ($langsungLunas && $nominalDibayar <= 0) {
            $nominalDibayar = $nominalTagihan;
        }

        $status = $langsungLunas ? 'lunas' : ($nominalDibayar > 0 ? 'bayar_sebagian' : 'belum_bayar');

        $carbonDate = \Carbon\Carbon::parse($request->tanggal_tagihan);
        $daysIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $monthsIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $namaHari = $daysIndo[$carbonDate->format('l')] ?? $carbonDate->format('l');
        $namaBulan = $monthsIndo[(int)$carbonDate->format('m')] ?? $carbonDate->format('F');
        $tanggalIndo = $namaHari . ', ' . $carbonDate->format('d') . ' ' . $namaBulan . ' ' . $carbonDate->format('Y');

        $autoCatatan = $langsungLunas 
            ? ("Lunas dibayar Rp " . number_format($nominalDibayar, 0, ',', '.') . " (" . strtoupper($metode) . ") pada " . $tanggalIndo . " Pukul " . now()->format('H:i') . " WIB.")
            : ($request->catatan ?? 'Tagihan iuran harian');

        $existing = Tagihan::where('pedagang_id', $pdg->id)
            ->where('tanggal_tagihan', $request->tanggal_tagihan)
            ->first();

        if ($existing) {
            $existing->nominal_tagihan = $nominalTagihan;
            $existing->jenis_iuran = $request->jenis_iuran;
            if ($langsungLunas) {
                $existing->nominal_dibayar = $nominalDibayar;
                $existing->status_bayar = 'lunas';
                $existing->waktu_bayar = now();
                $existing->metode_bayar = $metode;
                $existing->catatan = ($request->filled('catatan') ? $request->catatan . ' | ' : '') . $autoCatatan;
            } else {
                if ($request->filled('catatan')) {
                    $existing->catatan = $request->catatan;
                }
            }
            $existing->petugas_id = Auth::id();
            $existing->save();

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => Auth::id(),
                'aksi' => 'UPDATE_MANUAL_BILL',
                'deskripsi' => 'Mencatat transaksi pedagang: ' . $pdg->nama_pedagang . 
                    ' Rp ' . number_format($nominalDibayar, 0, ',', '.') . ' (LUNAS).',
                'created_at' => now(),
            ]);

            return back()->with('success', 'Transaksi pembayaran pedagang ' . $pdg->nama_pedagang . ' sebesar Rp ' . number_format($nominalDibayar, 0, ',', '.') . ' berhasil dicatat sebagai LUNAS.');
        }

        Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $pdg->id,
            'tarif_id' => $pdg->tarif_id,
            'jenis_iuran' => $request->jenis_iuran,
            'petugas_id' => Auth::id(),
            'tanggal_tagihan' => $request->tanggal_tagihan,
            'nominal_tagihan' => $nominalTagihan,
            'nominal_dibayar' => $nominalDibayar,
            'status_bayar' => $status,
            'waktu_bayar' => $langsungLunas ? now() : null,
            'metode_bayar' => $langsungLunas ? $metode : null,
            'no_bukti' => $langsungLunas ? ('INV-' . date('YmdHis')) : null,
            'catatan' => $autoCatatan
        ]);

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'CREATE_MANUAL_BILL',
            'deskripsi' => 'Menambahkan transaksi pembayaran untuk ' . $pdg->nama_pedagang . 
                ' Rp ' . number_format($nominalDibayar, 0, ',', '.'),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Transaksi pembayaran pedagang ' . $pdg->nama_pedagang . ' berhasil disimpan.');
    }

    /**
     * Force generate bills for a specific date
     */
    public function generate(Request $request)
    {
        $tanggal = $request->input('tanggal_generate', Carbon::today()->format('Y-m-d'));
        $createdCount = $this->generateBillsForDate($tanggal);

        return back()->with('success', "Berhasil me-generate {$createdCount} tagihan untuk tanggal " . Carbon::parse($tanggal)->format('d/m/Y') . ".");
    }

    /**
     * Helper to generate bills for active merchants
     */
    private function generateBillsForDate($tanggal)
    {
        $activeMerchants = Pedagang::where('status_pedagang', 'aktif')->get();
        $createdCount = 0;

        foreach ($activeMerchants as $pdg) {
            $exists = Tagihan::where('pedagang_id', $pdg->id)
                ->where('tanggal_tagihan', $tanggal)
                ->where('jenis_iuran', 'Iuran Harian')
                ->exists();

            if (!$exists) {
                $nominal = $pdg->tarif->nominal_harian;
                $dibayar = 0.00;
                $status = 'belum_bayar';
                $metode = null;
                $waktu = null;
                $catatan = null;

                if ($pdg->saldo_deposit > 0) {
                    if ($pdg->saldo_deposit >= $nominal) {
                        $dibayar = $nominal;
                        $pdg->saldo_deposit -= $nominal;
                        $status = 'lunas';
                        $metode = 'tunai';
                        $waktu = now();
                        $catatan = 'Lunas otomatis menggunakan saldo deposit.';
                    } else {
                        $dibayar = $pdg->saldo_deposit;
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
                    'no_bukti' => $status == 'lunas' ? 'DEP-AUTO-' . date('YmdHis') : null,
                    'catatan' => $catatan
                ]);
                $createdCount++;
            }
        }

        return $createdCount;
    }

    /**
     * Process payment entry for a single bill
     */
    public function bayar(Request $request, $id)
    {
        $tagihan = Tagihan::with('pedagang')->findOrFail($id);

        $request->validate([
            'nominal_dibayar' => 'required|numeric|min:0',
            'metode_bayar' => 'required|in:tunai,transfer,qris',
            'no_bukti' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $nominalDibayarInput = (float) $request->input('nominal_dibayar');
        $pedagang = $tagihan->pedagang;

        $carbonDate = \Carbon\Carbon::parse($tagihan->tanggal_tagihan);
        $daysIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $monthsIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $namaHari = $daysIndo[$carbonDate->format('l')] ?? $carbonDate->format('l');
        $namaBulan = $monthsIndo[(int)$carbonDate->format('m')] ?? $carbonDate->format('F');
        $tanggalIndo = $namaHari . ', ' . $carbonDate->format('d') . ' ' . $namaBulan . ' ' . $carbonDate->format('Y');

        // Adjust nominal_tagihan to nominal_dibayar if nominal_tagihan was higher (e.g. 15.000) but actual payment is 5.000 and user intends full payment
        if ($nominalDibayarInput > 0 && $nominalDibayarInput < $tagihan->nominal_tagihan) {
            if ($request->input('set_lunas', 1) == 1) {
                $tagihan->nominal_tagihan = $nominalDibayarInput;
            }
        }

        $selisih = $nominalDibayarInput - $tagihan->nominal_tagihan;

        if ($selisih >= 0) {
            $tagihan->nominal_dibayar = $tagihan->nominal_tagihan;
            $tagihan->status_bayar = 'lunas';
            
            $autoCatatan = "Lunas dibayar Rp " . number_format($nominalDibayarInput, 0, ',', '.') . " (" . strtoupper($request->input('metode_bayar')) . ") pada " . $tanggalIndo . " Pukul " . now()->format('H:i') . " WIB.";
            if ($selisih > 0) {
                $pedagang->saldo_deposit += $selisih;
                $pedagang->save();
                $autoCatatan .= " Kelebihan bayar Rp " . number_format($selisih, 0, ',', '.') . " dimasukkan ke deposit.";
            }
            $tagihan->catatan = ($request->input('catatan') ? $request->input('catatan') . ' | ' : '') . $autoCatatan;
        } else {
            $tagihan->nominal_dibayar = $nominalDibayarInput;
            $tagihan->status_bayar = 'bayar_sebagian';
            $autoCatatan = "Dibayar sebagian Rp " . number_format($nominalDibayarInput, 0, ',', '.') . " dari Rp " . number_format($tagihan->nominal_tagihan, 0, ',', '.') . " (" . strtoupper($request->input('metode_bayar')) . ") pada " . $tanggalIndo . ".";
            $tagihan->catatan = ($request->input('catatan') ? $request->input('catatan') . ' | ' : '') . $autoCatatan;
        }

        $tagihan->waktu_bayar = now();
        $tagihan->metode_bayar = $request->input('metode_bayar');
        $tagihan->no_bukti = $request->input('no_bukti') ?? ('INV-' . date('YmdHis'));
        $tagihan->petugas_id = Auth::id();
        $tagihan->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'PAY_BILL',
            'deskripsi' => 'Mencatat pembayaran pedagang: ' . $pedagang->nama_pedagang . 
                ' Rp ' . number_format($nominalDibayarInput, 0, ',', '.') . ' (Status: LUNAS).',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran pedagang ' . $pedagang->nama_pedagang . ' sebesar Rp ' . number_format($nominalDibayarInput, 0, ',', '.') . ' berhasil dicatat sebagai LUNAS.');
    }

    public function tidakAda(Request $request, $id)
    {
        $tagihan = Tagihan::with('pedagang')->findOrFail($id);
        
        $carbonDate = \Carbon\Carbon::parse($tagihan->tanggal_tagihan);
        $daysIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $monthsIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $namaHari = $daysIndo[$carbonDate->format('l')] ?? $carbonDate->format('l');
        $namaBulan = $monthsIndo[(int)$carbonDate->format('m')] ?? $carbonDate->format('F');
        $tanggalIndo = $namaHari . ', ' . $carbonDate->format('d') . ' ' . $namaBulan . ' ' . $carbonDate->format('Y');

        $alasan = $request->input('alasan') ?? $request->input('catatan') ?? 'Toko Tutup / Tidak Berdagang';

        $tagihan->status_bayar = 'belum_bayar';
        $tagihan->nominal_dibayar = 0.00;
        $tagihan->waktu_bayar = null;
        $tagihan->metode_bayar = null;
        $tagihan->no_bukti = null;
        $tagihan->catatan = '[TIDAK BERDAGANG] Alasan: ' . $alasan . ' pada ' . $tanggalIndo;
        $tagihan->petugas_id = Auth::id();
        $tagihan->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'MARK_ABSENT',
            'deskripsi' => 'Menandai pedagang tidak berdagang (' . $alasan . '): ' . $tagihan->pedagang->nama_pedagang . ' (Kios: ' . $tagihan->pedagang->no_kios . ').',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Status pedagang ' . $tagihan->pedagang->nama_pedagang . ' berhasil dicatat sebagai "Tidak Berdagang" (' . $alasan . ').');
    }
}
