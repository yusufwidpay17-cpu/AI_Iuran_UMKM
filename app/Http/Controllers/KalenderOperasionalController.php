<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KalenderOperasional;
use App\Models\AuditLog;
use App\Services\KalenderOperasionalService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class KalenderOperasionalController extends Controller
{
    protected $service;

    public function __construct(KalenderOperasionalService $service)
    {
        $this->service = $service;
    }

    /**
     * Halaman Utama: Daftar Kalender Operasional & Kontrol Penagihan
     */
    public function index(Request $request)
    {
        $query = KalenderOperasional::with(['creator', 'updater']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->where('tanggal_selesai', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal_mulai', '<=', $request->end_date);
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter kebijakan iuran
        if ($request->filled('kebijakan_iuran')) {
            $query->where('kebijakan_iuran', $request->kebijakan_iuran);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian alasan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('alasan', 'like', "%{$search}%");
        }

        $records = $query->orderBy('tanggal_mulai', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        // Ringkasan Statistik
        $all = KalenderOperasional::all();
        $stats = [
            'total' => $all->count(),
            'aktif' => $all->where('status', 'aktif')->count(),
            'bebas_iuran' => $all->where('status', 'aktif')->where('kebijakan_iuran', 'bebas_iuran')->count(),
            'tertunda' => $all->where('status', 'aktif')->where('kebijakan_iuran', 'kewajiban_tetap_berlaku')->count(),
            'dibatalkan' => $all->where('status', 'dibatalkan')->count(),
        ];

        return view('kalender.index', compact('records', 'stats'));
    }

    /**
     * Simpan Catatan Baru Kalender Operasional
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required|string|in:libur_nasional_pasar,kegiatan_pesantren,petugas_tugas_lain,penagihan_dihentikan_sementara,keadaan_darurat,lainnya',
            'alasan' => 'required|string|min:4',
            'kebijakan_iuran' => 'required|in:bebas_iuran,kewajiban_tetap_berlaku',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'alasan.required' => 'Alasan atau keterangan wajib diisi untuk audit transparansi.',
            'alasan.min' => 'Alasan wajib diisi minimal 4 karakter.',
        ]);

        $record = KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'kategori' => $request->kategori,
            'alasan' => $request->alasan,
            'kebijakan_iuran' => $request->kebijakan_iuran,
            'status' => 'aktif',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'catatan_audit' => "[" . Carbon::now()->isoFormat('D MMM YYYY HH:mm') . "] Dibuat oleh " . (Auth::user()->nama ?? 'Admin'),
        ]);

        // Audit Log
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'CREATE_OPERATIONAL_CALENDAR',
            'deskripsi' => "Menambahkan catatan kalender operasional: {$record->kategori_label} ({$record->tanggal_mulai->format('d/m/Y')} s/d {$record->tanggal_selesai->format('d/m/Y')}). Kebijakan: {$record->kebijakan_label}. Alasan: {$record->alasan}",
            'created_at' => now(),
        ]);

        // Cek apakah ada tagihan yang sudah terlanjur dibuat di rentang tanggal tersebut
        if ($record->kebijakan_iuran === 'bebas_iuran') {
            $dampak = $this->service->previewDampakTagihan($record);
            if ($dampak['total_bills'] > 0) {
                return redirect()->route('kalender.index', ['review_id' => $record->id])
                    ->with('warning', "Catatan berhasil disimpan. PERHATIAN: Ditemukan {$dampak['total_bills']} tagihan yang sudah telanjur terbit pada rentang tanggal ini. Silakan tinjau dampak tagihan.");
            }
        }

        return redirect()->route('kalender.index')->with('success', 'Catatan Kalender Operasional & Penagihan berhasil disimpan.');
    }

    /**
     * Perbarui Catatan Kalender Operasional
     */
    public function update(Request $request, $id)
    {
        $record = KalenderOperasional::findOrFail($id);

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required|string|in:libur_nasional_pasar,kegiatan_pesantren,petugas_tugas_lain,penagihan_dihentikan_sementara,keadaan_darurat,lainnya',
            'alasan' => 'required|string|min:4',
            'kebijakan_iuran' => 'required|in:bebas_iuran,kewajiban_tetap_berlaku',
            'status' => 'required|in:aktif,dibatalkan',
        ]);

        $perubahan = [];
        if ($record->kategori !== $request->kategori) $perubahan[] = "Kategori diubah dari {$record->kategori} ke {$request->kategori}";
        if ($record->kebijakan_iuran !== $request->kebijakan_iuran) $perubahan[] = "Kebijakan diubah dari {$record->kebijakan_iuran} ke {$request->kebijakan_iuran}";
        if ($record->status !== $request->status) $perubahan[] = "Status diubah dari {$record->status} ke {$request->status}";
        if ($record->tanggal_mulai->format('Y-m-d') !== $request->tanggal_mulai) $perubahan[] = "Tanggal mulai diubah ke {$request->tanggal_mulai}";
        if ($record->tanggal_selesai->format('Y-m-d') !== $request->tanggal_selesai) $perubahan[] = "Tanggal selesai diubah ke {$request->tanggal_selesai}";

        $logPerubahan = !empty($perubahan) ? implode(', ', $perubahan) : "Pembaruan keterangan/alasan";

        $record->catatan_audit = ($record->catatan_audit ? $record->catatan_audit . "\n" : "") . 
            "[" . Carbon::now()->isoFormat('D MMM YYYY HH:mm') . "] Diubah oleh " . (Auth::user()->nama ?? 'Admin') . ": " . $logPerubahan;

        $record->tanggal_mulai = $request->tanggal_mulai;
        $record->tanggal_selesai = $request->tanggal_selesai;
        $record->kategori = $request->kategori;
        $record->alasan = $request->alasan;
        $record->kebijakan_iuran = $request->kebijakan_iuran;
        $record->status = $request->status;
        $record->updated_by = Auth::id();
        $record->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'UPDATE_OPERATIONAL_CALENDAR',
            'deskripsi' => "Mengubah catatan kalender operasional ID {$record->id}: {$logPerubahan}. Alasan: {$record->alasan}",
            'created_at' => now(),
        ]);

        return redirect()->route('kalender.index')->with('success', 'Catatan Kalender Operasional berhasil diperbarui.');
    }

    /**
     * Batalkan Catatan Kalender Operasional (Tanpa Menghapus Data)
     */
    public function batalkan(Request $request, $id)
    {
        $record = KalenderOperasional::findOrFail($id);
        $record->status = 'dibatalkan';
        $record->updated_by = Auth::id();
        $record->catatan_audit = ($record->catatan_audit ? $record->catatan_audit . "\n" : "") . 
            "[" . Carbon::now()->isoFormat('D MMM YYYY HH:mm') . "] Dibatalkan oleh " . (Auth::user()->nama ?? 'Admin') . 
            ($request->filled('alasan_batal') ? " (Alasan: " . $request->alasan_batal . ")" : "");
        $record->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'CANCEL_OPERATIONAL_CALENDAR',
            'deskripsi' => "Membatalkan aturan kalender operasional ({$record->kategori_label} {$record->tanggal_mulai->format('d/m/Y')}).",
            'created_at' => now(),
        ]);

        return redirect()->route('kalender.index')->with('success', 'Catatan Kalender Operasional berhasil dinonaktifkan/dibatalkan.');
    }

    /**
     * Pratinjau Dampak Tagihan Terbit
     */
    public function previewDampak($id)
    {
        $record = KalenderOperasional::findOrFail($id);
        $dampak = $this->service->previewDampakTagihan($record);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'record' => $record,
                'dampak' => $dampak,
            ]);
        }

        return redirect()->route('kalender.index', ['review_id' => $record->id]);
    }

    /**
     * Eksekusi Koreksi Tagihan Historis dengan Konfirmasi Eksplisit Admin
     */
    public function koreksiTagihan(Request $request, $id)
    {
        $request->validate([
            'konfirmasi_koreksi' => 'required|accepted',
        ], [
            'konfirmasi_koreksi.accepted' => 'Anda wajib mencentang persetujuan konfirmasi koreksi sebelum melanjutkan.',
        ]);

        $record = KalenderOperasional::findOrFail($id);
        $result = $this->service->koreksiTagihanHistoris($record, Auth::id());

        if (!$result['success']) {
            return redirect()->route('kalender.index')->with('error', $result['message']);
        }

        return redirect()->route('kalender.index')->with('success', $result['message']);
    }
}
