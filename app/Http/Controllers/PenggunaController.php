<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::orderBy('created_at', 'desc')->get();
        return view('pengguna.index', compact('pengguna'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:150|unique:users',
            'role' => 'required|in:superadmin,admin,petugas',
        ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)), // Random secure password
            'role' => $request->role,
        ]);

        // Send password reset link so user can set their own password
        try {
            \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);
            return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan. Link pembuatan password telah dikirim ke email pengguna.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SMTP Error: ' . $e->getMessage());
            return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan, namun Email reset password belum dapat dikirim. Silakan coba beberapa saat lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|email|max:150|unique:users,email,' . $id,
            'role' => 'required|in:superadmin,admin,petugas',
        ]);

        $user->update([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function sendResetLink($id)
    {
        $user = User::findOrFail($id);
        
        $code = sprintf("%06d", random_int(100000, 999999));
        \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => \Illuminate\Support\Facades\Hash::make($code), 'created_at' => now()]
        );
        
        try {
            $appName = config('app.name', 'AmanahLedger');
            \Illuminate\Support\Facades\Mail::send(
                'emails.reset-code',
                ['nama' => $user->nama, 'code' => $code],
                function ($message) use ($user, $appName) {
                    $message->to($user->email)->subject("Kode Verifikasi Reset Password - {$appName}");
                }
            );
            return redirect()->route('pengguna.index')->with('success', 'Kode verifikasi reset password telah berhasil dikirimkan ke email ' . $user->email . '.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $user->email)->delete();
            \Illuminate\Support\Facades\Log::error('SMTP Error (Admin Reset Password): ' . $e->getMessage());
            return redirect()->route('pengguna.index')->with('error', 'Gagal mengirim email reset password. Layanan SMTP email sistem sedang mengalami gangguan. Silakan periksa konfigurasi email di file .env.');
        }
    }

    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return redirect()->route('pengguna.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
