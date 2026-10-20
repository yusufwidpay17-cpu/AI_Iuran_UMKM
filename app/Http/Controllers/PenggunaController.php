<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pedagang;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data Staf Pengelola (Superadmin, Admin, Petugas Lapangan)
        $stafPengguna = User::whereIn('role', ['superadmin', 'admin', 'petugas'])
            ->orderByRaw("FIELD(role, 'superadmin', 'admin', 'petugas')")
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Data Seluruh Mitra Pedagang beserta Akun Loginnya (dengan Pencarian & Slide)
        $queryPedagang = Pedagang::with('user');

        if ($request->filled('search_pedagang')) {
            $search = $request->input('search_pedagang');
            $queryPedagang->where(function($q) use ($search) {
                $q->where('nama_pedagang', 'like', "%{$search}%")
                  ->orWhere('no_kios', 'like', "%{$search}%")
                  ->orWhere('nama_usaha', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $mitraPedagang = $queryPedagang->orderBy('no_kios', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Data pedagang yang belum memiliki akun login (untuk dropdown modal)
        $pedagangTanpaAkun = Pedagang::whereDoesntHave('user')
            ->orderBy('nama_pedagang', 'asc')
            ->get();

        $semuaPedagang = Pedagang::orderBy('nama_pedagang', 'asc')->get();

        // Statistik ringkasan hak akses
        $totalPedagangSemua = Pedagang::count();
        $pedagangPunyaAkses = User::where('role', 'pedagang')->whereNotNull('pedagang_id')->count();
        $stats = [
            'total_staf' => $stafPengguna->count(),
            'total_pedagang' => $totalPedagangSemua,
            'pedagang_punya_akses' => $pedagangPunyaAkses,
            'pedagang_belum_akses' => max(0, $totalPedagangSemua - $pedagangPunyaAkses),
        ];

        return view('pengguna.index', compact('stafPengguna', 'mitraPedagang', 'pedagangTanpaAkun', 'semuaPedagang', 'stats'));
    }

    public function beriAksesPedagang(Request $request)
    {
        $request->validate([
            'pedagang_id' => 'required|exists:pedagang,id',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email|max:150|unique:users,email',
            'nama' => 'nullable|string|max:100',
        ], [
            'username.unique' => 'Username ini sudah digunakan, silakan pilih username lain.',
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $pedagang = Pedagang::findOrFail($request->pedagang_id);

        // Jika pedagang sudah punya user, jangan duplikasi
        $existing = User::where('pedagang_id', $pedagang->id)->first();
        if ($existing) {
            return redirect()->route('pengguna.index')->with('warning', "Pedagang {$pedagang->nama_pedagang} sudah memiliki akun login (Username: {$existing->username}). Gunakan fitur edit/reset sandi jika diperlukan.");
        }

        $namaUser = $request->nama ?: $pedagang->nama_pedagang;
        $username = strtolower(trim($request->username));
        $email = $request->email ? strtolower(trim($request->email)) : ($username . '@amanahledger.id');

        $newUser = User::create([
            'id' => (string) Str::uuid(),
            'nama' => $namaUser,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role' => 'pedagang',
            'pedagang_id' => $pedagang->id,
            'status_aktivasi' => 'aktif',
            'activation_token' => null,
            'activation_expires_at' => null,
        ]);

        // Catat Audit Log
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => auth()->id() ?? $newUser->id,
            'aksi' => 'BERI_AKSES_PEDAGANG',
            'deskripsi' => 'Superadmin memberikan hak akses login kepada pedagang ' . $pedagang->nama_pedagang . ' (Kios: ' . ($pedagang->no_kios ?? '-') . ', Username: ' . $username . ').',
            'created_at' => now(),
        ]);

        return redirect()->route('pengguna.index')
            ->with('success', "Hak akses login berhasil diberikan kepada pedagang {$pedagang->nama_pedagang} (Username: {$username})!");
    }

    public function cabutAksesPedagang(Request $request, $id)
    {
        $user = User::with('pedagang')->findOrFail($id);

        if ($user->role !== 'pedagang') {
            return redirect()->route('pengguna.index')->with('error', 'Hanya akun mitra pedagang yang dapat dicabut melalui tindakan ini.');
        }

        $namaPedagang = $user->pedagang ? $user->pedagang->nama_pedagang : $user->nama;
        $kios = $user->pedagang ? $user->pedagang->no_kios : '-';

        // Jika opsi tandai pedagang berhenti jualan / nonaktif dipilih
        if ($request->input('tandai_berhenti') == '1' && $user->pedagang) {
            $user->pedagang->update([
                'status_pedagang' => 'nonaktif',
                'catatan' => trim(($user->pedagang->catatan ? $user->pedagang->catatan . ' | ' : '') . 'Berhenti jualan & hak akses login dicabut pada ' . now()->format('d/m/Y H:i')),
            ]);
        }

        // Catat Audit Log sebelum penghapusan user
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'aksi' => 'CABUT_AKSES_PEDAGANG',
            'deskripsi' => "Mencabut hak akses login pedagang {$namaPedagang} (Kios: {$kios}, Akun: {$user->username}). " . ($request->input('tandai_berhenti') == '1' ? 'Status pedagang ditandai Nonaktif/Berhenti.' : 'Data histori lapak dan transaksi tetap tersimpan.'),
            'created_at' => now(),
        ]);

        // Hapus akun login (data transaksi & profil pedagang tetap aman)
        $user->delete();

        return redirect()->route('pengguna.index')
            ->with('success', "Hak akses login pedagang {$namaPedagang} berhasil dicabut. Akun login telah dihapus sehingga pedagang tidak dapat lagi masuk ke aplikasi.");
    }

    public function resetPassword(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:6',
            ], [
                'password.required' => 'Kata sandi baru wajib diisi.',
                'password.min' => 'Kata sandi minimal 6 karakter.',
            ]);

            $user = User::findOrFail($id);
            $user->update([
                'password' => Hash::make($request->password),
                'password_plain' => $request->password,
                'status_aktivasi' => 'aktif',
                'activation_token' => null,
                'activation_expires_at' => null,
            ]);

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => auth()->id(),
                'aksi' => 'RESET_PASSWORD',
                'deskripsi' => "Mereset kata sandi pengguna {$user->nama} ({$user->username}).",
                'created_at' => now(),
            ]);

            $pesan = "Kata sandi untuk pengguna {$user->nama} ({$user->username}) berhasil diubah menjadi '{$request->password}'. Silakan beritahukan sandi baru ini kepada yang bersangkutan.";

            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $pesan,
                    'user_id' => $user->id,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'password_plain' => $request->password,
                ]);
            }

            return redirect()->route('pengguna.index')->with('success', $pesan);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $firstError = collect($ve->errors())->flatten()->first() ?? 'Validasi kata sandi gagal.';
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $firstError,
                ], 422);
            }
            throw $ve;
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah kata sandi: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->route('pengguna.index')->with('error', 'Gagal mereset kata sandi: ' . $e->getMessage());
        }
    }

    public function resetPasswordSemua(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:6',
                'target_akun' => 'required|in:semua,pedagang,staf',
            ], [
                'password.required' => 'Kata sandi baru wajib diisi.',
                'password.min' => 'Kata sandi minimal 6 karakter.',
            ]);

            $target = $request->input('target_akun', 'semua');
            $query = User::query();

            if ($target === 'pedagang') {
                $query->where('role', 'pedagang');
                $labelTarget = 'seluruh akun Mitra Pedagang';
            } elseif ($target === 'staf') {
                $query->whereIn('role', ['admin', 'petugas']);
                $labelTarget = 'seluruh akun Staf Pengelola (Admin & Petugas)';
            } else {
                $labelTarget = 'seluruh akun pengguna di sistem';
            }

            $affectedCount = $query->count();
            $newHashed = Hash::make($request->password);

            $query->update([
                'password' => $newHashed,
                'password_plain' => $request->password,
                'status_aktivasi' => 'aktif',
                'activation_token' => null,
                'activation_expires_at' => null,
            ]);

            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => auth()->id(),
                'aksi' => 'RESET_PASSWORD_MASSAL',
                'deskripsi' => "Admin mereset kata sandi massal untuk {$labelTarget} ({$affectedCount} akun) menjadi '{$request->password}'.",
                'created_at' => now(),
            ]);

            $pesan = "Berhasil! Kata sandi untuk {$labelTarget} ({$affectedCount} akun) telah otomatis diubah menjadi '{$request->password}'.";

            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $pesan,
                    'affected_count' => $affectedCount,
                    'target' => $target,
                    'password_plain' => $request->password,
                ]);
            }

            return redirect()->route('pengguna.index')->with('success', $pesan);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $firstError = collect($ve->errors())->flatten()->first() ?? 'Validasi kata sandi gagal.';
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $firstError,
                ], 422);
            }
            throw $ve;
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah kata sandi massal: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->route('pengguna.index')->with('error', 'Gagal mereset kata sandi massal: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $metode = $request->input('metode_pembuatan', 'aktivasi_email');

        $rules = [
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'role' => 'required|in:superadmin,admin,petugas,pedagang',
            'pedagang_id' => 'nullable|required_if:role,pedagang|exists:pedagang,id',
            'metode_pembuatan' => 'required|in:aktivasi_email,manual_password',
        ];

        if ($metode === 'aktivasi_email') {
            $rules['email'] = 'required|email|max:150|unique:users,email';
        } else {
            $rules['email'] = 'nullable|email|max:150|unique:users,email';
            $rules['password'] = 'required|string|min:6';
        }

        $messages = [
            'username.unique' => 'Username ini sudah terdaftar. Gunakan username lain.',
            'email.required' => 'Alamat email wajib diisi untuk pengiriman tautan aktivasi privat.',
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'pedagang_id.required_if' => 'Untuk role Pedagang, Anda wajib memilih data pedagang yang tertaut.',
            'password.required' => 'Kata sandi wajib diisi jika memilih pembuatan manual.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ];

        $request->validate($rules, $messages);

        $userId = (string) Str::uuid();
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addHours(24);

        $userData = [
            'id' => $userId,
            'nama' => $request->nama,
            'username' => strtolower(trim($request->username)),
            'email' => $request->email ? strtolower(trim($request->email)) : null,
            'role' => $request->role,
            'pedagang_id' => $request->role === 'pedagang' ? $request->pedagang_id : null,
        ];

        if ($metode === 'aktivasi_email') {
            // Password acak sementara terkunci, menunggu user membuat password sendiri
            $userData['password'] = Hash::make(Str::random(32));
            $userData['password_plain'] = 'Menunggu Aktivasi';
            $userData['status_aktivasi'] = 'pending';
            $userData['activation_token'] = $token;
            $userData['activation_expires_at'] = $expiresAt;
        } else {
            $userData['password'] = Hash::make($request->password);
            $userData['password_plain'] = $request->password;
            $userData['status_aktivasi'] = 'aktif';
            $userData['activation_token'] = null;
            $userData['activation_expires_at'] = null;
        }

        $newUser = User::create($userData);

        // Catat Audit Log
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => auth()->id() ?? $newUser->id,
            'aksi' => 'TAMBAH_PENGGUNA',
            'deskripsi' => 'Menambahkan akun ' . $newUser->nama . ' (Role: ' . $newUser->role . ', Metode: ' . $metode . ').',
            'created_at' => now(),
        ]);

        // Jika metode aktivasi email, kirimkan email tautan aktivasi
        if ($metode === 'aktivasi_email' && $newUser->email) {
            $baseUrl = rtrim(config('app.url', 'https://ai-iuran-umkm.vercel.app'), '/');
            $activationUrl = $baseUrl . route('aktivasi.form', [
                'token' => $token,
                'email' => $newUser->email,
            ], false);

            try {
                $appName = config('app.name', 'AmanahLedger');
                Mail::send('emails.aktivasi-akun', [
                    'user' => $newUser,
                    'activationUrl' => $activationUrl,
                ], function ($message) use ($newUser, $appName) {
                    $message->to($newUser->email, $newUser->nama)
                            ->subject("Undangan Akses & Aktivasi Akun - {$appName}");
                });

                $redirect = redirect()->route('pengguna.index')
                    ->with('success', 'Akun pengguna berhasil dibuat! Tautan aktivasi privat telah dikirimkan ke email: ' . $newUser->email);

                // Jika mailer driver adalah log/local, tampilkan tautan di flash message untuk kemudahan pengujian
                if (config('mail.default') === 'log' || config('app.env') === 'local') {
                    $redirect->with('activation_preview_link', $activationUrl);
                }

                return $redirect;
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email aktivasi: ' . $e->getMessage());
                return redirect()->route('pengguna.index')
                    ->with('warning', 'Akun berhasil dibuat dengan status pending, namun pengiriman email mengalami kendala SMTP (' . $e->getMessage() . '). Tautan aktivasi manual: ' . $activationUrl)
                    ->with('activation_preview_link', $activationUrl);
            }
        }

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan dan dapat langsung login menggunakan password yang telah Anda buat.');
    }

    public function resendActivation($id)
    {
        $user = User::findOrFail($id);

        if ($user->status_aktivasi === 'aktif') {
            return redirect()->route('pengguna.index')->with('error', 'Akun pengguna ini sudah berstatus aktif.');
        }

        if (empty($user->email)) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna ini tidak memiliki email terdaftar untuk pengiriman tautan aktivasi.');
        }

        // Generate token baru dengan masa aktif 24 jam baru
        $newToken = Str::random(60);
        $user->forceFill([
            'activation_token' => $newToken,
            'activation_expires_at' => Carbon::now()->addHours(24),
        ])->save();

        $baseUrl = rtrim(config('app.url', 'https://ai-iuran-umkm.vercel.app'), '/');
        $activationUrl = $baseUrl . route('aktivasi.form', [
            'token' => $newToken,
            'email' => $user->email,
        ], false);

        try {
            $appName = config('app.name', 'AmanahLedger');
            Mail::send('emails.aktivasi-akun', [
                'user' => $user,
                'activationUrl' => $activationUrl,
            ], function ($message) use ($user, $appName) {
                $message->to($user->email, $user->nama)
                        ->subject("Undangan Akses & Aktivasi Akun - {$appName}");
            });

            $redirect = redirect()->route('pengguna.index')
                ->with('success', 'Tautan aktivasi baru berhasil dikirimkan ulang ke email: ' . $user->email);

            if (config('mail.default') === 'log' || config('app.env') === 'local') {
                $redirect->with('activation_preview_link', $activationUrl);
            }

            return $redirect;
        } catch (\Exception $e) {
            Log::error('Gagal kirim ulang email aktivasi: ' . $e->getMessage());
            return redirect()->route('pengguna.index')
                ->with('warning', 'Tautan baru berhasil diperbarui, namun pengiriman SMTP gagal. Tautan aktivasi: ' . $activationUrl)
                ->with('activation_preview_link', $activationUrl);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'nullable|email|max:150|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:superadmin,admin,petugas,pedagang',
            'pedagang_id' => $request->role === 'pedagang' ? 'required|exists:pedagang,id' : 'nullable',
            'status_aktivasi' => 'required|in:aktif,pending',
        ], [
            'pedagang_id.required' => 'Untuk role Pedagang, data lapak wajib dipilih.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => strtolower(trim($request->username)),
            'email' => $request->email ? strtolower(trim($request->email)) : null,
            'role' => $request->role,
            'pedagang_id' => $request->role === 'pedagang' ? $request->pedagang_id : null,
            'status_aktivasi' => $request->status_aktivasi,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['password_plain'] = $request->password;
            // Jika admin mengisi password secara manual, set status aktif
            $data['status_aktivasi'] = 'aktif';
            $data['activation_token'] = null;
            $data['activation_expires_at'] = null;
        }

        $user->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
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
