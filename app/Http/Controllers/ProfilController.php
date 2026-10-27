<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    /**
     * Tampilan Halaman Profil Pengguna (Superadmin, Admin, Petugas, Pedagang)
     */
    public function index()
    {
        $user = auth()->user();

        // Jika pedagang, arahkan ke portal profil lapaknya agar tetap terintegrasi lengkap
        if ($user && $user->role === 'pedagang') {
            return redirect()->route('pedagang.profil');
        }

        // Ambil riwayat aktivitas pengguna
        $recentLogs = AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('profil.index', compact('user', 'recentLogs'));
    }

    /**
     * Update Informasi Profil (Nama & Email)
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->nama = $request->nama;
        $user->email = strtolower(trim($request->email));
        $user->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'aksi' => 'UPDATE_PROFIL_DATA',
            'deskripsi' => "Pengguna {$user->nama} ({$user->role}) memperbarui data nama/email akun.",
            'created_at' => now(),
        ]);

        return back()->with('success', 'Informasi profil berhasil diperbarui!');
    }

    /**
     * Update Foto Profil Mandiri
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

        $user = auth()->user();

        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_profil_' . Str::slug($user->username ?? $user->nama) . '.' . $file->getClientOriginalExtension();

            // Coba simpan ke public/uploads/profil/
            try {
                if (!file_exists(public_path('uploads/profil'))) {
                    @mkdir(public_path('uploads/profil'), 0777, true);
                }
                
                // Hapus foto lama jika ada dan berupa file fisik lokal
                if ($user->foto_profil && !str_starts_with($user->foto_profil, 'data:') && file_exists(public_path($user->foto_profil))) {
                    @unlink(public_path($user->foto_profil));
                }

                $file->move(public_path('uploads/profil'), $filename);
                $path = 'uploads/profil/' . $filename;
            } catch (\Exception $e) {
                // Fallback base64 jika filesystem serverless (seperti Vercel)
                $path = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            }

            $user->foto_profil = $path;
            $user->save();

            // Jika user adalah pedagang, sinkronkan juga ke tabel pedagang
            if ($user->role === 'pedagang' && $user->pedagang_id) {
                $pedagang = \App\Models\Pedagang::find($user->pedagang_id);
                if ($pedagang) {
                    $pedagang->foto_profil = $path;
                    $pedagang->save();
                }
            }

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'aksi' => 'UPDATE_FOTO_PROFIL',
                'deskripsi' => "Pengguna {$user->nama} ({$user->role}) memperbarui foto profil mandiri.",
                'created_at' => now(),
            ]);

            return back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memproses unggahan foto.');
    }

    /**
     * Update Kata Sandi Mandiri
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_saat_ini' => 'required',
            'password_baru' => 'required|string|min:6|confirmed',
        ], [
            'password_saat_ini.required' => 'Kata sandi saat ini wajib diisi.',
            'password_baru.required' => 'Kata sandi baru wajib diisi.',
            'password_baru.min' => 'Kata sandi baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password_saat_ini, $user->password)) {
            return back()->withErrors(['password_saat_ini' => 'Kata sandi saat ini yang Anda masukkan salah.']);
        }

        $user->password = Hash::make($request->password_baru);
        $user->password_plain = $request->password_baru;
        $user->save();

        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'aksi' => 'UPDATE_PASSWORD_MANDIRI',
            'deskripsi' => "Pengguna {$user->nama} ({$user->username}) memperbarui kata sandi akun secara mandiri.",
            'created_at' => now(),
        ]);

        return back()->with('success_password', 'Kata sandi akun Anda berhasil diperbarui!');
    }
}
