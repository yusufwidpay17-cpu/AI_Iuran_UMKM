<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedagang;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Models\Lapak;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

        $pedagangs = $query->orderBy('no_kios', 'asc')->paginate(12)->withQueryString();

        $blokList = Pedagang::whereNotNull('blok_kios')
            ->where('blok_kios', '!=', '')
            ->distinct()
            ->pluck('blok_kios');

        return view('pedagang.index', compact('pedagangs', 'blokList'));
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
            'status_legal' => 'required|in:legal,ilegal,proses_izin',
            'foto_ktp' => 'nullable|image|max:2048',
            'foto_izin_usaha' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
            'tarif_id' => 'required|exists:tarif,id',
            'saldo_deposit' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['foto_ktp', 'foto_izin_usaha']);
        $pedagangId = (string) Str::uuid();
        $data['id'] = $pedagangId;
        $data['saldo_deposit'] = $request->input('saldo_deposit') ?? 0;

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

        $pedagang = Pedagang::create($data);

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

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'CREATE_PEDAGANG',
            'deskripsi' => 'Menambahkan pedagang baru: ' . $pedagang->nama_pedagang . ' (Kios: ' . $pedagang->no_kios . ').',
            'created_at' => now(),
        ]);

        return redirect()->route('pedagang.index')->with('success', 'Data pedagang berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pedagang = Pedagang::with(['tarif', 'tagihan.petugas', 'segmentasi.model'])->findOrFail($id);
        
        $lapak = \Illuminate\Support\Facades\Schema::hasTable('lapak')
            ? Lapak::where('kode_lapak', $pedagang->no_kios)->first()
            : null;

        // Financial totals for this merchant
        $totalTagihan = $pedagang->tagihan->sum('nominal_tagihan');
        $totalPembayaran = $pedagang->tagihan->sum('nominal_dibayar');
        $totalTunggakan = $totalTagihan - $totalPembayaran;

        // Categorized History
        $riwayatIuran = $pedagang->tagihan->sortByDesc('tanggal_tagihan');
        $riwayatPembayaran = $pedagang->tagihan->where('nominal_dibayar', '>', 0)->sortByDesc('waktu_bayar');
        $riwayatTunggakan = $pedagang->tagihan->where('status_bayar', '!=', 'lunas')->sortByDesc('tanggal_tagihan');

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
            'status_legal' => 'required|in:legal,ilegal,proses_izin',
            'foto_ktp' => 'nullable|image|max:2048',
            'foto_izin_usaha' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
            'tarif_id' => 'required|exists:tarif,id',
            'saldo_deposit' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['foto_ktp', 'foto_izin_usaha']);
        $data['saldo_deposit'] = $request->input('saldo_deposit') ?? 0;

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

        $pedagang->update($data);

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
