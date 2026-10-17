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

use App\Services\KalenderOperasionalService;

class TagihanController extends Controller
{
    protected $kalenderService;

    public function __construct(KalenderOperasionalService $kalenderService)
    {
        $this->kalenderService = $kalenderService;
    }

    private function generateNoBukti($prefix = 'INV-')
    {
        return $this->kalenderService->generateNoBukti($prefix);
    }

    /**
     * Display a mobile-first list of today's / selected date's bills for data entry
     */
    public function input(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        
        $rule = $this->kalenderService->getRuleForDate($tanggal);
        $isBebasIuran = $rule ? $rule->isBebasIuran() : false;
        $isPenagihanDiliburkan = $rule !== null;

        // 1. Check if bills are generated for this date
        $billCount = Tagihan::where('tanggal_tagihan', $tanggal)->count();
        
        // Hanya generate otomatis jika BUKAN hari bebas iuran
        if ($billCount == 0 && $tanggal == Carbon::today()->format('Y-m-d') && !$isBebasIuran) {
            $this->kalenderService->generateBillsForDate($tanggal);
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
            ->orderBy('tagihan.waktu_bayar', 'desc')
            ->orderBy('pedagang.blok_kios', 'asc')
            ->orderBy('pedagang.no_kios', 'asc')
            ->get();

        $blokList = Pedagang::whereNotNull('blok_kios')
            ->where('blok_kios', '!=', '')
            ->distinct()
            ->pluck('blok_kios');

        $pedagangs = Pedagang::where('status_pedagang', 'aktif')->orderBy('nama_pedagang', 'asc')->get();
        $tarifs = Tarif::where('aktif', 1)->get();

        return view('tagihan.input', compact('tagihans', 'tanggal', 'blokList', 'pedagangs', 'tarifs', 'rule', 'isBebasIuran', 'isPenagihanDiliburkan'));
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
            'status_pembayaran' => 'required|in:lunas,belum_bayar,bayar_sebagian',
            'nominal_tagihan' => 'required|numeric|min:0',
            'nominal_dibayar' => 'nullable|numeric|min:0',
            'metode_bayar' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($this->kalenderService->isBebasIuran($request->tanggal_tagihan)) {
            $rule = $this->kalenderService->getRuleForDate($request->tanggal_tagihan);
            $alasan = $rule ? $rule->alasan : 'Bebas Iuran';
            return back()->with('error', "Tanggal {$request->tanggal_tagihan} telah ditetapkan sebagai Hari Bebas Iuran ({$alasan}). Pembuatan tagihan manual tidak diperkenankan.");
        }

        $pdg = Pedagang::findOrFail($request->pedagang_id);
        $nominalTagihan = (float) $request->nominal_tagihan;
        $nominalDibayar = (float) ($request->input('nominal_dibayar') ?? $nominalTagihan);
        $metode = $request->input('metode_bayar') ?? 'tunai';

        $statusInput = $request->input('status_pembayaran');
        
        if ($statusInput === 'lunas') {
            if ($nominalDibayar <= 0) {
                $nominalDibayar = $nominalTagihan;
            }
            $status = 'lunas';
        } elseif ($statusInput === 'belum_bayar') {
            $nominalDibayar = 0;
            $status = 'belum_bayar';
        } elseif ($statusInput === 'bayar_sebagian') {
            if ($nominalDibayar >= $nominalTagihan) {
                $status = 'lunas';
            } else {
                $status = 'bayar_sebagian';
            }
        } else {
            $langsungLunas = $request->input('langsung_lunas', 1) == 1;
            if ($langsungLunas && $nominalDibayar <= 0) {
                $nominalDibayar = $nominalTagihan;
            }
            $status = $langsungLunas ? 'lunas' : ($nominalDibayar > 0 ? 'bayar_sebagian' : 'belum_bayar');
        }

        $carbonDate = \Carbon\Carbon::parse($request->tanggal_tagihan);
        $daysIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $monthsIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $namaHari = $daysIndo[$carbonDate->format('l')] ?? $carbonDate->format('l');
        $namaBulan = $monthsIndo[(int)$carbonDate->format('m')] ?? $carbonDate->format('F');
        $tanggalIndo = $namaHari . ', ' . $carbonDate->format('d') . ' ' . $namaBulan . ' ' . $carbonDate->format('Y');

        $autoCatatan = $status === 'lunas'
            ? ("Lunas dibayar Rp " . number_format($nominalDibayar, 0, ',', '.') . " (" . strtoupper($metode) . ") pada " . $tanggalIndo . " Pukul " . now()->format('H:i') . " WIB.")
            : ($status === 'bayar_sebagian' 
                ? ("Dibayar sebagian Rp " . number_format($nominalDibayar, 0, ',', '.') . " pada " . $tanggalIndo) 
                : ($request->catatan ?? 'Tagihan iuran harian'));

        $existing = Tagihan::where('pedagang_id', $pdg->id)
            ->where('tanggal_tagihan', $request->tanggal_tagihan)
            ->first();

        if ($existing) {
            $existing->nominal_tagihan = $nominalTagihan;
            $existing->jenis_iuran = $request->jenis_iuran;
            $existing->nominal_dibayar = $nominalDibayar;
            $existing->status_bayar = $status;
            
            if ($status === 'lunas' || $status === 'bayar_sebagian') {
                $existing->waktu_bayar = now();
                $existing->metode_bayar = $metode;
                if (empty($existing->no_bukti)) {
                    $existing->no_bukti = $this->generateNoBukti();
                }
                $existing->catatan = ($request->filled('catatan') ? $request->catatan . ' | ' : '') . $autoCatatan;
            } else {
                $existing->waktu_bayar = null;
                $existing->metode_bayar = null;
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
                    ' Rp ' . number_format($nominalDibayar, 0, ',', '.') . ' (' . strtoupper($status) . ').',
                'created_at' => now(),
            ]);

            return back()->with('success', 'Transaksi pembayaran pedagang ' . $pdg->nama_pedagang . ' sebesar Rp ' . number_format($nominalDibayar, 0, ',', '.') . ' berhasil dicatat.');
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
            'waktu_bayar' => ($status === 'lunas' || $status === 'bayar_sebagian') ? now() : null,
            'metode_bayar' => ($status === 'lunas' || $status === 'bayar_sebagian') ? $metode : null,
            'no_bukti' => ($status === 'lunas' || $status === 'bayar_sebagian') ? $this->generateNoBukti() : null,
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
        $result = $this->kalenderService->generateBillsForDate($tanggal);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Helper to generate bills for active merchants (delegated to KalenderOperasionalService)
     */
    private function generateBillsForDate($tanggal)
    {
        $result = $this->kalenderService->generateBillsForDate($tanggal);
        return $result['created_count'] ?? 0;
    }

    /**
     * Process payment entry for a single bill
     */
    public function bayar(Request $request, $id)
    {
        $tagihan = Tagihan::with('pedagang')->findOrFail($id);

        $request->validate([
            'status_pembayaran' => 'required|in:lunas,belum_bayar,bayar_sebagian',
            'nominal_dibayar' => 'required|numeric|min:0',
            'metode_bayar' => 'required|in:tunai,transfer,qris',
            'catatan' => 'nullable|string',
        ]);

        $statusInput = $request->input('status_pembayaran');
        $nominalDibayarInput = (float) $request->input('nominal_dibayar');
        $pedagang = $tagihan->pedagang;

        $carbonDate = \Carbon\Carbon::parse($tagihan->tanggal_tagihan);
        $daysIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $monthsIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $namaHari = $daysIndo[$carbonDate->format('l')] ?? $carbonDate->format('l');
        $namaBulan = $monthsIndo[(int)$carbonDate->format('m')] ?? $carbonDate->format('F');
        $tanggalIndo = $namaHari . ', ' . $carbonDate->format('d') . ' ' . $namaBulan . ' ' . $carbonDate->format('Y');

        // Determine final status and nominal
        if ($statusInput === 'lunas') {
            if ($nominalDibayarInput <= 0) {
                $nominalDibayarInput = $tagihan->nominal_tagihan;
            }
            $status = 'lunas';
        } elseif ($statusInput === 'belum_bayar') {
            $nominalDibayarInput = 0;
            $status = 'belum_bayar';
        } else {
            if ($nominalDibayarInput >= $tagihan->nominal_tagihan) {
                $status = 'lunas';
            } else {
                $status = 'bayar_sebagian';
            }
        }

        $tagihan->nominal_dibayar = $nominalDibayarInput;
        $tagihan->status_bayar = $status;
        $selisih = $nominalDibayarInput - $tagihan->nominal_tagihan;
        $metodeStr = strtoupper($request->input('metode_bayar'));

        if ($status === 'lunas') {
            $autoCatatan = "Lunas dibayar Rp " . number_format($nominalDibayarInput, 0, ',', '.') . " (" . $metodeStr . ") pada " . $tanggalIndo . " Pukul " . now()->format('H:i') . " WIB.";
            if ($selisih > 0) {
                $pedagang->saldo_deposit += $selisih;
                $pedagang->save();
                $autoCatatan .= " Kelebihan bayar Rp " . number_format($selisih, 0, ',', '.') . " dimasukkan ke deposit.";
            }
            $tagihan->catatan = ($request->input('catatan') ? $request->input('catatan') . ' | ' : '') . $autoCatatan;
            $tagihan->waktu_bayar = now();
            $tagihan->metode_bayar = $request->input('metode_bayar');
            if (empty($tagihan->no_bukti)) {
                $tagihan->no_bukti = $this->generateNoBukti();
            }
        } elseif ($status === 'bayar_sebagian') {
            $autoCatatan = "Dibayar sebagian Rp " . number_format($nominalDibayarInput, 0, ',', '.') . " dari Rp " . number_format($tagihan->nominal_tagihan, 0, ',', '.') . " (" . $metodeStr . ") pada " . $tanggalIndo . ".";
            $tagihan->catatan = ($request->input('catatan') ? $request->input('catatan') . ' | ' : '') . $autoCatatan;
            $tagihan->waktu_bayar = now();
            $tagihan->metode_bayar = $request->input('metode_bayar');
            if (empty($tagihan->no_bukti)) {
                $tagihan->no_bukti = $this->generateNoBukti();
            }
        } else {
            $tagihan->waktu_bayar = null;
            $tagihan->metode_bayar = null;
            if ($request->filled('catatan')) {
                $tagihan->catatan = $request->input('catatan');
            }
        }
        $tagihan->petugas_id = Auth::id();
        $tagihan->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'UPDATE_TRANSACTION',
            'deskripsi' => 'Mengubah transaksi pedagang: ' . $pedagang->nama_pedagang . 
                ' Rp ' . number_format($nominalDibayarInput, 0, ',', '.') . ' (Status: ' . strtoupper($status) . ').',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Transaksi pedagang ' . $pedagang->nama_pedagang . ' berhasil diupdate (Status: ' . strtoupper($status) . ').');
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
        $bebasIuran = $request->has('bebas_iuran') ? $request->boolean('bebas_iuran') : true;

        if ($bebasIuran) {
            $tagihan->nominal_tagihan = 0.00;
            $tagihan->nominal_dibayar = 0.00;
            $tagihan->status_bayar = 'dibatalkan';
            $tagihan->waktu_bayar = null;
            $tagihan->metode_bayar = null;
            $tagihan->no_bukti = null;
            $tagihan->catatan = '[TOKO TUTUP - BEBAS IURAN] Alasan: ' . $alasan . ' pada ' . $tanggalIndo;
        } else {
            $tagihan->status_bayar = 'belum_bayar';
            $tagihan->nominal_dibayar = 0.00;
            $tagihan->waktu_bayar = null;
            $tagihan->metode_bayar = null;
            $tagihan->no_bukti = null;
            $tagihan->catatan = '[TIDAK BERDAGANG - TETAP BERKEWAJIBAN] Alasan: ' . $alasan . ' pada ' . $tanggalIndo;
        }
        $tagihan->petugas_id = Auth::id();
        $tagihan->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'MARK_ABSENT',
            'deskripsi' => 'Menandai pedagang tidak berdagang (' . ($bebasIuran ? 'Bebas Iuran' : 'Tetap Berutang') . ' - ' . $alasan . '): ' . $tagihan->pedagang->nama_pedagang . ' (Kios: ' . $tagihan->pedagang->no_kios . ').',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Status pedagang ' . $tagihan->pedagang->nama_pedagang . ' berhasil dicatat (' . ($bebasIuran ? 'Bebas Iuran' : 'Tetap Berutang') . ' - ' . $alasan . ').');
    }
}
