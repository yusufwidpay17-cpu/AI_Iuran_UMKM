<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedagang;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Models\Lapak;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\KalenderOperasionalService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedagangController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedagang::with('tarif');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_pedagang', 'like', "%{$search}%")
                  ->orWhere('no_kios', 'like', "%{$search}%")
                  ->orWhere('nama_usaha', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_pedagang')) {
            $query->where('status_pedagang', $request->input('status_pedagang'));
        }
        if ($request->filled('status_legal')) {
            $query->where('status_legal', $request->input('status_legal'));
        }
        if ($request->filled('blok_kios')) {
            $query->where('blok_kios', $request->input('blok_kios'));
        }
        if ($request->filled('kategori_usaha')) {
            $query->where('kategori_usaha', $request->input('kategori_usaha'));
        }

        $perPage = (int) $request->input('per_page', 9);
        if (!in_array($perPage, [6, 9, 12, 15, 18, 24])) {
            $perPage = 9;
        }

        $pedagangs = $query->orderBy('no_kios', 'asc')->paginate($perPage)->withQueryString();

        $blokList = Pedagang::whereNotNull('blok_kios')
            ->where('blok_kios', '!=', '')
            ->distinct()
            ->pluck('blok_kios');

        return view('pedagang.index', compact('pedagangs', 'blokList', 'perPage'));
    }

    public function create()
    {
        $tarifs = Tarif::where('aktif', 1)->get();
        return view('pedagang.create', compact('tarifs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pedagang' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:pedagang,no_ktp',
            'no_telepon' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'nama_usaha' => 'nullable|string|max:100',
            'kategori_usaha' => 'required|in:kuliner,pakaian,elektronik,sayuran,buah,sembako,lainnya',
            'kategori_usaha_detail' => 'nullable|string|max:100',
            'blok_kios' => 'required|string|max:10',
            'no_kios' => 'required|string|max:20|unique:pedagang,no_kios',
            'tanggal_daftar' => 'required|date',
            'status_pedagang' => 'required|in:aktif,nonaktif,sementara_tutup',
            'status_legal' => 'required|in:legal,tutup_sementara,ilegal,proses_izin',
            'foto_profil' => 'nullable|image|max:2048',
            'foto_ktp' => 'nullable|image|max:2048',
            'foto_izin_usaha' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
            'tarif_id' => 'required_without:nominal_tarif|nullable|exists:tarif,id',
            'nominal_tarif' => 'required_without:tarif_id|nullable|numeric|min:0',
            'saldo_deposit' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['foto_profil', 'foto_ktp', 'foto_izin_usaha', 'nominal_tarif']);

        if ($request->filled('nominal_tarif')) {
            $nominal = (float) $request->input('nominal_tarif');
            $tarif = Tarif::where('nominal_harian', $nominal)->first();
            if (!$tarif) {
                $tarif = Tarif::create([
                    'id' => (string) Str::uuid(),
                    'nama_tarif' => 'Tarif Rp ' . number_format($nominal, 0, ',', '.'),
                    'kategori' => $request->input('kategori_usaha', 'manual'),
                    'nominal_harian' => $nominal,
                    'berlaku_mulai' => now()->toDateString(),
                    'aktif' => 1,
                ]);
            }
            $data['tarif_id'] = $tarif->id;
        }

        $pedagangId = (string) Str::uuid();
        $data['id'] = $pedagangId;
        $data['saldo_deposit'] = $request->input('saldo_deposit') ?? 0;

        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_profil_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            try {
                if (!file_exists(public_path('uploads/profil'))) {
                    @mkdir(public_path('uploads/profil'), 0777, true);
                }
                $file->move(public_path('uploads/profil'), $filename);
                $data['foto_profil'] = 'uploads/profil/' . $filename;
            } catch (\Exception $e) {
                $data['foto_profil'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            }
        }

        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $filename = time() . '_ktp_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/ktp'), $filename);
            $data['foto_ktp'] = 'uploads/ktp/' . $filename;
        }

        if ($request->hasFile('foto_izin_usaha')) {
            $file = $request->file('foto_izin_usaha');
            $filename = time() . '_izin_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/izin'), $filename);
            $data['foto_izin_usaha'] = 'uploads/izin/' . $filename;
        }

        try {
            $pedagang = Pedagang::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'status_legal') || str_contains($e->getMessage(), '1265')) {
                try {
                    DB::statement("ALTER TABLE pedagang MODIFY COLUMN status_legal VARCHAR(50) NOT NULL DEFAULT 'legal'");
                    $pedagang = Pedagang::create($data);
                } catch (\Exception $ex) {
                    if (!in_array($data['status_legal'], ['legal', 'ilegal', 'proses_izin'])) {
                        $data['status_legal'] = 'legal';
                    }
                    $pedagang = Pedagang::create($data);
                }
            } else {
                throw $e;
            }
        }

        // Sync Lapak if table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('lapak')) {
            Lapak::updateOrCreate(
                ['kode_lapak' => $pedagang->no_kios],
                [
                    'id' => (string) Str::uuid(),
                    'blok_kios' => $pedagang->blok_kios,
                    'pedagang_id' => $pedagangId,
                    'tarif_id' => $pedagang->tarif_id,
                    'kategori' => $pedagang->kategori_usaha,
                    'status_lapak' => $pedagang->status_pedagang === 'aktif' ? 'aktif' : 'nonaktif',
                    'tanggal_mulai' => $pedagang->tanggal_daftar,
                    'keterangan' => 'Kios ' . $pedagang->nama_usaha
                ]
            );
        }

        // Hubungkan ke menu Transaksi Pembayaran: generate tagihan perdana jatuh tempo besok harinya (H+1 pendaftaran)
        $tagihanPerdana = null;
        if ($pedagang->status_pedagang === 'aktif') {
            $kalenderService = app(KalenderOperasionalService::class);
            $tagihanPerdana = $kalenderService->generateInitialBillNextDay($pedagang);
        }

        $tanggalJatuhTempo = $pedagang->tanggal_daftar 
            ? Carbon::parse($pedagang->tanggal_daftar)->addDay() 
            : Carbon::tomorrow();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'CREATE_PEDAGANG',
            'deskripsi' => 'Menambahkan pedagang baru: ' . $pedagang->nama_pedagang . ' (Kios: ' . $pedagang->no_kios . '). Tagihan perdana terhubung ke Transaksi Pembayaran (jatuh tempo: ' . $tanggalJatuhTempo->format('d/m/Y') . ').',
            'created_at' => now(),
        ]);

        $pesanSukses = 'Data pedagang ' . $pedagang->nama_pedagang . ' berhasil ditambahkan dan terhubung ke menu Transaksi Pembayaran (jatuh tempo mulai besok: ' . $tanggalJatuhTempo->isoFormat('D MMMM YYYY') . ').';

        return redirect()->route('pedagang.index')->with('success', $pesanSukses);
    }

    public function show($id)
    {
        $pedagang = Pedagang::with(['tarif', 'tagihan.petugas', 'segmentasi.model'])->findOrFail($id);
        
        $lapak = \Illuminate\Support\Facades\Schema::hasTable('lapak')
            ? Lapak::where('kode_lapak', $pedagang->no_kios)->first()
            : null;

        // Financial totals for this merchant (Consistent with Kalender Operasional: Exclude 'dibatalkan')
        $activeTagihans = $pedagang->tagihan->where('status_bayar', '!=', 'dibatalkan');
        $totalTagihan = $activeTagihans->sum('nominal_tagihan');
        $totalPembayaran = $pedagang->tagihan->sum('nominal_dibayar');
        $totalTunggakan = max(0, $totalTagihan - $totalPembayaran);

        // Categorized History
        $riwayatIuran = $pedagang->tagihan->sortByDesc('tanggal_tagihan');
        $riwayatPembayaran = $pedagang->tagihan->where('nominal_dibayar', '>', 0)->sortByDesc('waktu_bayar');
        $riwayatTunggakan = $pedagang->tagihan->whereIn('status_bayar', ['belum_bayar', 'bayar_sebagian'])->sortByDesc('tanggal_tagihan');

        return view('pedagang.show', compact(
            'pedagang',
            'lapak',
            'totalTagihan',
            'totalPembayaran',
            'totalTunggakan',
            'riwayatIuran',
            'riwayatPembayaran',
            'riwayatTunggakan'
        ));
    }

    public function edit($id)
    {
        $pedagang = Pedagang::findOrFail($id);
        $tarifs = Tarif::where('aktif', 1)->get();
        return view('pedagang.edit', compact('pedagang', 'tarifs'));
    }

    public function update(Request $request, $id)
    {
        $pedagang = Pedagang::findOrFail($id);

        $request->validate([
            'nama_pedagang' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:pedagang,no_ktp,' . $id,
            'no_telepon' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'nama_usaha' => 'nullable|string|max:100',
            'kategori_usaha' => 'required|in:kuliner,pakaian,elektronik,sayuran,buah,sembako,lainnya',
            'kategori_usaha_detail' => 'nullable|string|max:100',
            'blok_kios' => 'required|string|max:10',
            'no_kios' => 'required|string|max:20|unique:pedagang,no_kios,' . $id,
            'tanggal_daftar' => 'required|date',
            'status_pedagang' => 'required|in:aktif,nonaktif,sementara_tutup',
            'status_legal' => 'required|in:legal,tutup_sementara,ilegal,proses_izin',
            'foto_profil' => 'nullable|image|max:2048',
            'foto_ktp' => 'nullable|image|max:2048',
            'foto_izin_usaha' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
            'tarif_id' => 'required_without:nominal_tarif|nullable|exists:tarif,id',
            'nominal_tarif' => 'required_without:tarif_id|nullable|numeric|min:0',
            'saldo_deposit' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['foto_profil', 'foto_ktp', 'foto_izin_usaha', 'nominal_tarif']);

        if ($request->filled('nominal_tarif')) {
            $nominal = (float) $request->input('nominal_tarif');
            $tarif = Tarif::where('nominal_harian', $nominal)->first();
            if (!$tarif) {
                $tarif = Tarif::create([
                    'id' => (string) Str::uuid(),
                    'nama_tarif' => 'Tarif Rp ' . number_format($nominal, 0, ',', '.'),
                    'kategori' => $request->input('kategori_usaha', 'manual'),
                    'nominal_harian' => $nominal,
                    'berlaku_mulai' => now()->toDateString(),
                    'aktif' => 1,
                ]);
            }
            $data['tarif_id'] = $tarif->id;
        }

        $data['saldo_deposit'] = $request->input('saldo_deposit') ?? 0;

        if ($request->hasFile('foto_profil')) {
            if ($pedagang->foto_profil && !str_starts_with($pedagang->foto_profil, 'data:') && file_exists(public_path($pedagang->foto_profil))) {
                @unlink(public_path($pedagang->foto_profil));
            }
            $file = $request->file('foto_profil');
            $filename = time() . '_profil_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            try {
                if (!file_exists(public_path('uploads/profil'))) {
                    @mkdir(public_path('uploads/profil'), 0777, true);
                }
                $file->move(public_path('uploads/profil'), $filename);
                $data['foto_profil'] = 'uploads/profil/' . $filename;
            } catch (\Exception $e) {
                $data['foto_profil'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            }

            // Sync ke user account jika ada
            User::where('pedagang_id', $pedagang->id)->update(['foto_profil' => $data['foto_profil']]);
        }

        if ($request->hasFile('foto_ktp')) {
            if ($pedagang->foto_ktp && file_exists(public_path($pedagang->foto_ktp))) {
                @unlink(public_path($pedagang->foto_ktp));
            }
            $file = $request->file('foto_ktp');
            $filename = time() . '_ktp_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/ktp'), $filename);
            $data['foto_ktp'] = 'uploads/ktp/' . $filename;
        }

        if ($request->hasFile('foto_izin_usaha')) {
            if ($pedagang->foto_izin_usaha && file_exists(public_path($pedagang->foto_izin_usaha))) {
                @unlink(public_path($pedagang->foto_izin_usaha));
            }
            $file = $request->file('foto_izin_usaha');
            $filename = time() . '_izin_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/izin'), $filename);
            $data['foto_izin_usaha'] = 'uploads/izin/' . $filename;
        }

        try {
            $pedagang->update($data);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'status_legal') || str_contains($e->getMessage(), '1265')) {
                try {
                    DB::statement("ALTER TABLE pedagang MODIFY COLUMN status_legal VARCHAR(50) NOT NULL DEFAULT 'legal'");
                    $pedagang->update($data);
                } catch (\Exception $ex) {
                    if (!in_array($data['status_legal'], ['legal', 'ilegal', 'proses_izin'])) {
                        $data['status_legal'] = 'legal';
                    }
                    $pedagang->update($data);
                }
            } else {
                throw $e;
            }
        }

        // Sinkronisasi tagihan belum bayar yang belum dicicil untuk hari ini / mendatang
        if (isset($data['tarif_id'])) {
            $pedagang->load('tarif');
            Tagihan::where('pedagang_id', $pedagang->id)
                ->where('status_bayar', 'belum_bayar')
                ->where('nominal_dibayar', 0)
                ->whereDate('tanggal_tagihan', '>=', now()->toDateString())
                ->update([
                    'tarif_id' => $pedagang->tarif_id,
                    'nominal_tagihan' => (float) ($pedagang->tarif->nominal_harian ?? 0),
                ]);
        }

        // Sync Lapak if table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('lapak')) {
            Lapak::updateOrCreate(
                ['kode_lapak' => $pedagang->no_kios],
                [
                    'blok_kios' => $pedagang->blok_kios,
                    'pedagang_id' => $pedagang->id,
                    'tarif_id' => $pedagang->tarif_id,
                    'kategori' => $pedagang->kategori_usaha,
                    'status_lapak' => $pedagang->status_pedagang === 'aktif' ? 'aktif' : 'nonaktif',
                    'tanggal_mulai' => $pedagang->tanggal_daftar,
                ]
            );
        }

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'UPDATE_PEDAGANG',
            'deskripsi' => 'Mengubah data pedagang: ' . $pedagang->nama_pedagang . ' (Kios: ' . $pedagang->no_kios . ').',
            'created_at' => now(),
        ]);

        return redirect()->route('pedagang.index')->with('success', 'Data pedagang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pedagang = Pedagang::findOrFail($id);
        $pedagang->update(['status_pedagang' => 'nonaktif']);

        if (\Illuminate\Support\Facades\Schema::hasTable('lapak')) {
            Lapak::where('kode_lapak', $pedagang->no_kios)->update(['status_lapak' => 'nonaktif']);
        }

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'DEACTIVATE_PEDAGANG',
            'deskripsi' => 'Menonaktifkan pedagang: ' . $pedagang->nama_pedagang . ' (Kios: ' . $pedagang->no_kios . ').',
            'created_at' => now(),
        ]);

        return redirect()->route('pedagang.index')->with('success', 'Pedagang berhasil dinonaktifkan.');
    }

    public function getNextKios(Request $request)
    {
        $block = $request->input('block');
        if (empty($block)) {
            return response()->json(['no_kios' => '']);
        }

        $maxNum = Pedagang::where('blok_kios', $block)
            ->get()
            ->map(function($p) {
                preg_match('/-(\d+)/', $p->no_kios, $matches);
                return isset($matches[1]) ? (int)$matches[1] : 0;
            })
            ->max();

        $nextNum = ($maxNum ?? 0) + 1;
        
        $letter = 'K';
        if (preg_match('/Blok\s+(\w)/i', $block, $matches)) {
            $letter = strtoupper($matches[1]);
        } elseif (strlen($block) > 0) {
            $letter = strtoupper(substr($block, 0, 1));
        }

        $noKios = sprintf('%s-%02d', $letter, $nextNum);

        return response()->json(['no_kios' => $noKios]);
    }
}
