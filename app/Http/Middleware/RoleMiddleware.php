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
        $isJsonRequest = $request->expectsJson() || $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if (!Auth::check()) {
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login telah berakhir atau Anda belum masuk. Silakan muat ulang halaman dan login kembali sebagai Superadmin/Admin.',
                    'need_login' => true,
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika tidak ada role spesifik yang diminta atau role pengguna termasuk dalam daftar role yang diizinkan
        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        if ($isJsonRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Anda saat ini login sebagai akun dengan peran (' . $user->role . ') yang tidak memiliki izin untuk tindakan ini. Jika Anda baru saja mencoba login akun pedagang di tab sebelah, silakan muat ulang halaman atau login kembali sebagai Superadmin.',
                'need_login' => true,
            ], 403);
        }

        // Jika pengguna ber-role 'pedagang' mencoba membuka halaman admin
        if ($user->role === 'pedagang') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengakses menu administrasi pengelola.');
        }

        // Tampilkan 403 Forbidden dengan pesan jelas
        abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses (role: ' . $user->role . ') untuk halaman ini.');
    }
}
