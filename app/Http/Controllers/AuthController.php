<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log successful login to audit log
            $user = Auth::user();
            \App\Models\AuditLog::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'aksi' => 'LOGIN',
                'deskripsi' => 'Pengguna ' . $user->nama . ' berhasil masuk ke sistem.',
                'created_at' => now(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Username atau password salah. Silakan coba lagi.')->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            \App\Models\AuditLog::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'aksi' => 'LOGOUT',
                'deskripsi' => 'Pengguna ' . $user->nama . ' keluar dari sistem.',
                'created_at' => now(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
