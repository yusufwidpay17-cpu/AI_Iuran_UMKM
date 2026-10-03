<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapak;
use App\Models\Pedagang;
use App\Models\Tarif;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LapakController extends Controller
{
    public function index(Request $request)
    {
        // Auto-sync missing lapaks from pedagang table if any
        $activePedagang = Pedagang::all();
        foreach ($activePedagang as $pdg) {
            Lapak::firstOrCreate(
                ['kode_lapak' => $pdg->no_kios],
                [
                    'id' => (string) Str::uuid(),
                    'blok_kios' => $pdg->blok_kios,
                    'pedagang_id' => $pdg->id,
                    'tarif_id' => $pdg->tarif_id,
                    'kategori' => $pdg->kategori_usaha,
                    'status_lapak' => $pdg->status_pedagang === 'aktif' ? 'aktif' : 'nonaktif',
                    'tanggal_mulai' => $pdg->tanggal_daftar,
                    'keterangan' => 'Lapak ' . $pdg->nama_usaha
                ]
            );
        }

        $query = Lapak::with(['pedagang', 'tarif']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('kode_lapak', 'like', "%{$search}%")
                  ->orWhere('blok_kios', 'like', "%{$search}%")
                  ->orWhereHas('pedagang', function($qp) use ($search) {
                      $qp->where('nama_pedagang', 'like', "%{$search}%")
                        ->orWhere('nama_usaha', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status_lapak')) {
            $query->where('status_lapak', $request->input('status_lapak'));
        }

        if ($request->filled('blok_kios')) {
            $query->where('blok_kios', $request->input('blok_kios'));
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        $lapaks = $query->orderBy('kode_lapak', 'asc')->paginate(12)->withQueryString();

        $blokList = Lapak::whereNotNull('blok_kios')->where('blok_kios', '!=', '')->distinct()->pluck('blok_kios');
        $tarifs = Tarif::where('aktif', 1)->get();
        $pedagangsWithoutLapak = Pedagang::where('status_pedagang', 'aktif')->get();

        return view('lapak.index', compact('lapaks', 'blokList', 'tarifs', 'pedagangsWithoutLapak'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_lapak' => 'required|string|max:20|unique:lapak,kode_lapak',
            'blok_kios' => 'required|string|max:10',
            'kategori' => 'required|in:kuliner,pakaian,elektronik,sayuran,buah,sembako,lainnya',
            'status_lapak' => 'required|in:aktif,nonaktif,kosong',
            'tarif_id' => 'nullable|exists:tarif,id',
            'pedagang_id' => 'nullable|exists:pedagang,id',
            'tanggal_mulai' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['id'] = (string) Str::uuid();

        Lapak::create($data);

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'CREATE_LAPAK',
            'deskripsi' => 'Menambahkan data lapak baru: Kode ' . $request->kode_lapak,
            'created_at' => now(),
        ]);

        return redirect()->route('lapak.index')->with('success', 'Data lapak berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $lapak = Lapak::findOrFail($id);

        $request->validate([
            'kode_lapak' => 'required|string|max:20|unique:lapak,kode_lapak,' . $id,
            'blok_kios' => 'required|string|max:10',
            'kategori' => 'required|in:kuliner,pakaian,elektronik,sayuran,buah,sembako,lainnya',
            'status_lapak' => 'required|in:aktif,nonaktif,kosong',
            'tarif_id' => 'nullable|exists:tarif,id',
            'pedagang_id' => 'nullable|exists:pedagang,id',
            'tanggal_mulai' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $lapak->update($request->all());

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'UPDATE_LAPAK',
            'deskripsi' => 'Mengubah data lapak: Kode ' . $lapak->kode_lapak,
            'created_at' => now(),
        ]);

        return redirect()->route('lapak.index')->with('success', 'Data lapak berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $lapak = Lapak::findOrFail($id);
        $lapak->delete();

        return redirect()->route('lapak.index')->with('success', 'Data lapak berhasil dihapus.');
    }
}
