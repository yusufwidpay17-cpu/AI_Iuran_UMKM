<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika tidak ada role spesifik yang diminta atau role pengguna termasuk dalam daftar role yang diizinkan
        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika pengguna ber-role 'pedagang' mencoba membuka halaman admin
        if ($user->role === 'pedagang') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengakses menu administrasi pengelola.');
        }

        // Tampilkan 403 Forbidden dengan pesan jelas
        abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses (role: ' . $user->role . ') untuk halaman ini.');
    }
}
