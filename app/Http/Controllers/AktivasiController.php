<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AktivasiController extends Controller
{
    /**
     * Menampilkan form penentuan password mandiri via link aktivasi
     */
    public function showActivationForm(Request $request, $token)
    {
        $token = trim($token);
        $email = $request->query('email');

        $query = User::where('activation_token', $token);
        if ($email) {
            $query->where('email', $email);
        }

        $user = $query->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Tautan aktivasi tidak valid atau sudah pernah digunakan sebelumnya. Silakan hubungi pihak pengelola jika memerlukan akses baru.');
        }

        // Periksa masa berlaku token aktivasi (default 24 jam)
        if ($user->activation_expires_at && Carbon::parse($user->activation_expires_at)->isPast()) {
            return redirect()->route('login')->with('error', 'Tautan aktivasi akun telah kedaluwarsa. Silakan hubungi administrator untuk mengirimkan ulang tautan aktivasi.');
        }

        return view('auth.aktivasi', compact('user', 'token'));
    }

    /**
     * Memproses password yang ditentukan sendiri oleh pengguna
     */
    public function activateAccount(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 6 karakter demi keamanan.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok. Silakan ketik ulang.',
        ]);

        $token = trim($request->token);
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Tautan aktivasi tidak valid atau telah kedaluwarsa.');
        }

        if ($user->activation_expires_at && Carbon::parse($user->activation_expires_at)->isPast()) {
            return redirect()->route('login')->with('error', 'Tautan aktivasi akun telah kedaluwarsa. Silakan minta tautan baru kepada administrator.');
        }

        // Perbarui password dan aktifkan akun secara resmi
        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'status_aktivasi' => 'aktif',
            'activation_token' => null,
            'activation_expires_at' => null,
        ])->save();

        // Catat di Audit Log sistem
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'aksi' => 'AKTIVASI_AKUN',
            'deskripsi' => 'Pengguna ' . $user->nama . ' (' . $user->role . ') berhasil mengaktivasi akun dan menentukan kata sandi mandiri.',
            'created_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Selamat, akun Anda telah aktif dan kata sandi berhasil disimpan! Silakan masuk menggunakan username/email dan kata sandi baru Anda.');
    }
}
