<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendaftaranPedagang;
use App\Models\Pedagang;
use App\Models\Tarif;
use App\Models\Lapak;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PendaftaranPedagang::with('tarif');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_calon', 'like', "%{$search}%")
                  ->orWhere('no_kios', 'like', "%{$search}%")
                  ->orWhere('no_ktp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_pendaftaran', $request->input('status'));
        }

        $pendaftarans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('pendaftaran.index', compact('pendaftarans'));
    }

    public function create()
    {
        $tarifs = Tarif::where('aktif', 1)->get();
        return view('pendaftaran.create', compact('tarifs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_calon' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:pendaftaran_pedagang,no_ktp|unique:pedagang,no_ktp',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'nama_usaha' => 'nullable|string|max:100',
            'kategori_usaha' => 'required|in:kuliner,pakaian,elektronik,sayuran,buah,sembako,lainnya',
            'blok_kios' => 'required|string|max:10',
            'no_kios' => 'required|string|max:20',
            'tarif_id' => 'required|exists:tarif,id',
            'tanggal_daftar' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['id'] = (string) Str::uuid();
        $data['status_pendaftaran'] = 'menunggu_verifikasi';

        PendaftaranPedagang::create($data);

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'CREATE_PENDAFTARAN',
            'deskripsi' => 'Mencatat pendaftaran calon pedagang baru: ' . $request->nama_calon,
            'created_at' => now(),
        ]);

        return redirect()->route('pendaftaran.index')->with('success', 'Pendaftaran calon pedagang berhasil diajukan.');
    }

    public function show($id)
    {
        $pendaftaran = PendaftaranPedagang::with('tarif')->findOrFail($id);
        return view('pendaftaran.show', compact('pendaftaran'));
    }

    public function edit($id)
    {
        $pendaftaran = PendaftaranPedagang::findOrFail($id);
        $tarifs = Tarif::where('aktif', 1)->get();
        return view('pendaftaran.edit', compact('pendaftaran', 'tarifs'));
    }

    public function update(Request $request, $id)
    {
        $pendaftaran = PendaftaranPedagang::findOrFail($id);

        $request->validate([
            'nama_calon' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:pendaftaran_pedagang,no_ktp,' . $id,
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'nama_usaha' => 'nullable|string|max:100',
            'kategori_usaha' => 'required|in:kuliner,pakaian,elektronik,sayuran,buah,sembako,lainnya',
            'blok_kios' => 'required|string|max:10',
            'no_kios' => 'required|string|max:20',
            'tarif_id' => 'required|exists:tarif,id',
            'tanggal_daftar' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $pendaftaran->update($request->all());

        return redirect()->route('pendaftaran.index')->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    public function setujui($id)
    {
        $pendaftaran = PendaftaranPedagang::findOrFail($id);

        if ($pendaftaran->status_pendaftaran === 'disetujui') {
            return back()->with('error', 'Pendaftaran ini sudah disetujui sebelumnya.');
        }

        // Create active merchant
        $pedagangId = (string) Str::uuid();
        $pedagang = Pedagang::create([
            'id' => $pedagangId,
            'nama_pedagang' => $pendaftaran->nama_calon,
            'no_ktp' => $pendaftaran->no_ktp,
            'no_telepon' => $pendaftaran->no_telepon,
            'nama_usaha' => $pendaftaran->nama_usaha ?? $pendaftaran->nama_calon,
            'kategori_usaha' => $pendaftaran->kategori_usaha,
            'no_kios' => $pendaftaran->no_kios,
            'blok_kios' => $pendaftaran->blok_kios,
            'tanggal_daftar' => $pendaftaran->tanggal_daftar,
            'status_pedagang' => 'aktif',
            'status_legal' => 'legal',
            'tarif_id' => $pendaftaran->tarif_id,
            'catatan' => 'Pendaftaran disetujui pada ' . date('d/m/Y') . '. ' . ($pendaftaran->catatan ?? ''),
            'saldo_deposit' => 0.00
        ]);

        // Update Lapak status if table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('lapak')) {
            Lapak::updateOrCreate(
                ['kode_lapak' => $pendaftaran->no_kios],
                [
                    'id' => (string) Str::uuid(),
                    'blok_kios' => $pendaftaran->blok_kios,
                    'pedagang_id' => $pedagangId,
                    'tarif_id' => $pendaftaran->tarif_id,
                    'kategori' => $pendaftaran->kategori_usaha,
                    'status_lapak' => 'aktif',
                    'tanggal_mulai' => $pendaftaran->tanggal_daftar,
                    'keterangan' => 'Lapak diisi oleh ' . $pendaftaran->nama_calon
                ]
            );
        }

        $pendaftaran->update(['status_pendaftaran' => 'disetujui']);

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'APPROVE_PENDAFTARAN',
            'deskripsi' => 'Menyetujui pendaftaran pedagang: ' . $pendaftaran->nama_calon . ' (Kios: ' . $pendaftaran->no_kios . ').',
            'created_at' => now(),
        ]);

        return redirect()->route('pedagang.show', $pedagang->id)->with('success', 'Pendaftaran disetujui! Data pedagang telah diaktifkan.');
    }

    public function tolak(Request $request, $id)
    {
        $pendaftaran = PendaftaranPedagang::findOrFail($id);
        $pendaftaran->update([
            'status_pendaftaran' => 'ditolak',
            'catatan' => ($pendaftaran->catatan ? $pendaftaran->catatan . "\n" : "") . 'Alasan Ditolak: ' . ($request->input('alasan') ?? 'Tidak memenuhi syarat.')
        ]);

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'REJECT_PENDAFTARAN',
            'deskripsi' => 'Menolak pendaftaran pedagang: ' . $pendaftaran->nama_calon,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran pedagang telah ditolak.');
    }

    public function destroy($id)
    {
        $pendaftaran = PendaftaranPedagang::findOrFail($id);
        $pendaftaran->delete();

        return redirect()->route('pendaftaran.index')->with('success', 'Data pendaftaran dihapus.');
    }
}
