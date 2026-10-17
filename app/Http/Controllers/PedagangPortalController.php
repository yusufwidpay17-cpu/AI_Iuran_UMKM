<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedagang;
use App\Models\Tagihan;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Services\KalenderOperasionalService;

class PedagangPortalController extends Controller
{
    protected $kalenderService;

    public function __construct(KalenderOperasionalService $kalenderService)
    {
        $this->kalenderService = $kalenderService;
    }

    /**
     * Helper to get authenticated merchant
     */
    private function getPedagang()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'pedagang') {
            abort(403, 'Akses ditolak: Hanya mitra pedagang yang dapat mengakses portal ini.');
        }

        $pedagang = $user->pedagang ?? ($user->pedagang_id ? Pedagang::with('tarif')->find($user->pedagang_id) : null);
        if (!$pedagang) {
            abort(403, 'Akun Anda belum terhubung dengan data kios pedagang.');
        }

        return $pedagang;
    }

    /**
     * Menu B: Tagihan Iuran Saya
     */
    public function tagihan(Request $request)
    {
        $pedagang = $this->getPedagang();

        $query = Tagihan::where('pedagang_id', $pedagang->id);

        // Filter Status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'lunas') {
                $query->where('status_bayar', 'lunas');
            } elseif ($status === 'bayar_sebagian' || $status === 'cicil') {
                $query->where('status_bayar', 'bayar_sebagian');
            } elseif ($status === 'belum_bayar') {
                $query->where('status_bayar', 'belum_bayar');
            } elseif ($status === 'dibatalkan') {
                $query->where('status_bayar', 'dibatalkan');
            }
        }

        // Filter Periode Bulan & Tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_tagihan', $request->input('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_tagihan', $request->input('tahun'));
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_tagihan', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_tagihan', '<=', $request->input('end_date'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // Summary KPI
        $allBills = Tagihan::where('pedagang_id', $pedagang->id)->get();
        $totalTagihanNominal = $allBills->sum('nominal_tagihan');
        $totalTerbayarNominal = $allBills->sum('nominal_dibayar');
        $totalSisaNominal = $allBills->whereIn('status_bayar', ['belum_bayar', 'bayar_sebagian'])->sum(function($b) {
            return max(0, $b->nominal_tagihan - $b->nominal_dibayar);
        });

        $tagihans = $query->orderBy('tanggal_tagihan', 'desc')->paginate(15)->withQueryString();

        return view('pedagang_portal.tagihan', compact(
            'pedagang',
            'tagihans',
            'totalTagihanNominal',
            'totalTerbayarNominal',
            'totalSisaNominal'
        ));
    }

    /**
     * Menu C: Tunggakan Saya
     */
    public function tunggakan(Request $request)
    {
        $pedagang = $this->getPedagang();

        // Ambil hanya tagihan yang sah memiliki sisa kewajiban pembayaran (exclude dibatalkan)
        $query = Tagihan::where('pedagang_id', $pedagang->id)
            ->whereIn('status_bayar', ['belum_bayar', 'bayar_sebagian']);

        $unpaidBills = $query->orderBy('tanggal_tagihan', 'asc')->get();

        $totalTunggakanNominal = $unpaidBills->sum(function($b) {
            return max(0, $b->nominal_tagihan - $b->nominal_dibayar);
        });
        $jumlahHariMenunggak = $unpaidBills->count();

        return view('pedagang_portal.tunggakan', compact(
            'pedagang',
            'unpaidBills',
            'totalTunggakanNominal',
            'jumlahHariMenunggak'
        ));
    }

    /**
     * Menu E: Notifikasi
     */
    public function notifikasi(Request $request)
    {
        $pedagang = $this->getPedagang();
        $today = Carbon::today()->format('Y-m-d');

        // Aturan kalender operasional hari ini
        $operasionalHariIni = $this->kalenderService->getRuleForDate($today);
        $isBebasIuranHariIni = $operasionalHariIni && $operasionalHariIni->isBebasIuran();

        // Status tagihan hari ini
        $tagihanHariIni = Tagihan::where('pedagang_id', $pedagang->id)
            ->where('tanggal_tagihan', $today)
            ->where('status_bayar', '!=', 'dibatalkan')
            ->first();

        // 5 Pembayaran terakhir
        $pembayaranTerakhir = Tagihan::where('pedagang_id', $pedagang->id)
            ->where('nominal_dibayar', '>', 0)
            ->orderBy('waktu_bayar', 'desc')
            ->limit(5)
            ->get();

        // Tunggakan sah (belum bayar & cicil)
        $unpaidBills = Tagihan::where('pedagang_id', $pedagang->id)
            ->whereIn('status_bayar', ['belum_bayar', 'bayar_sebagian'])
            ->orderBy('tanggal_tagihan', 'desc')
            ->get();

        $totalTunggakan = $unpaidBills->sum(fn($b) => max(0, $b->nominal_tagihan - $b->nominal_dibayar));

        return view('pedagang_portal.notifikasi', compact(
            'pedagang',
            'tagihanHariIni',
            'operasionalHariIni',
            'isBebasIuranHariIni',
            'pembayaranTerakhir',
            'unpaidBills',
            'totalTunggakan'
        ));
    }

    /**
     * Menu F: Profil dan Data Lapak
     */
    public function profil(Request $request)
    {
        $pedagang = $this->getPedagang();
        $user = auth()->user();

        return view('pedagang_portal.profil', compact('pedagang', 'user'));
    }

    /**
     * Update Foto Profil Pedagang Mandiri
     */
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'foto_profil.required' => 'Silakan pilih berkas foto terlebih dahulu.',
            'foto_profil.image' => 'Berkas harus berupa gambar (JPG, PNG, WEBP).',
            'foto_profil.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $pedagang = $this->getPedagang();
        $user = auth()->user();

        if ($request->hasFile('foto_profil')) {
            if ($pedagang->foto_profil && !str_starts_with($pedagang->foto_profil, 'data:') && file_exists(public_path($pedagang->foto_profil))) {
                @unlink(public_path($pedagang->foto_profil));
            }

            $file = $request->file('foto_profil');
            $filename = time() . '_profil_' . Str::slug($pedagang->nama_pedagang) . '.' . $file->getClientOriginalExtension();
            try {
                if (!file_exists(public_path('uploads/profil'))) {
                    @mkdir(public_path('uploads/profil'), 0777, true);
                }
                $file->move(public_path('uploads/profil'), $filename);
                $path = 'uploads/profil/' . $filename;
            } catch (\Exception $e) {
                $path = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            }

            $pedagang->foto_profil = $path;
            $pedagang->save();

            if ($user) {
                $user->foto_profil = $path;
                $user->save();
            }

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'aksi' => 'UPDATE_FOTO_PROFIL',
                'deskripsi' => "Mitra pedagang {$pedagang->nama_pedagang} memperbarui foto profil mandiri.",
                'created_at' => now(),
            ]);

            return back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memproses unggahan foto.');
    }
}

