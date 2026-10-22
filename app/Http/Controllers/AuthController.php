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
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->username);
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Cek jika akun ada tapi belum diaktivasi kata sandinya
        $targetUser = \App\Models\User::where($fieldType, $loginInput)->first();
        if ($targetUser && $targetUser->status_aktivasi === 'pending') {
            return back()->with('error', 'Akun Anda belum diaktivasi. Silakan periksa email undangan Anda untuk menentukan kata sandi pertama kali, atau hubungi administrator pengelola.')->withInput($request->only('username'));
        }

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log successful login to audit log
            $user = Auth::user();
            \App\Models\AuditLog::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'aksi' => 'LOGIN',
                'deskripsi' => 'Pengguna ' . $user->nama . ' (' . $user->role . ') berhasil masuk ke sistem.',
                'created_at' => now(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Username/Email atau password salah. Silakan coba lagi.')->withInput($request->only('username'));
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

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $email = strtolower(trim($request->email));
        $throttleKey = 'forgot-password-request:' . sha1($request->ip() . '|' . $email);

        // Batasi frekuensi permintaan reset (maksimal 3 kali dalam 15 menit)
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withInput()->withErrors([
                'email' => 'Terlalu banyak permintaan reset password. Silakan coba kembali dalam ' . ceil($seconds / 60) . ' menit.'
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 900); // 15 menit

        $user = \App\Models\User::where('email', $email)->first();

        // Pesan respons umum untuk mencegah kebocoran informasi pendaftaran email (Email Enumeration)
        $genericSuccessMessage = 'Jika alamat email tersebut terdaftar dalam sistem kami, kode verifikasi 6 digit telah dikirimkan ke kotak masuk Anda. Silakan periksa email dan masukkan kode verifikasi.';

        // Jika email tidak terdaftar, tetap arahkan ke form verifikasi dengan pesan umum tanpa membocorkan status akun
        if (!$user) {
            return redirect()->route('password.reset', ['email' => $email])
                ->with('success', $genericSuccessMessage);
        }

        // Generate kode acak kriptografis 6-digit (CSPRNG)
        $code = sprintf("%06d", random_int(100000, 999999));

        // Simpan token dalam bentuk hash bcrypt di database (token asli tidak disimpan polos)
        \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => \Illuminate\Support\Facades\Hash::make($code),
                'created_at' => now()
            ]
        );

        // Kirim email kode verifikasi secara aman
        try {
            $appName = config('app.name', 'AmanahLedger');
            \Illuminate\Support\Facades\Mail::send(
                'emails.reset-code',
                ['nama' => $user->nama, 'code' => $code],
                function ($message) use ($email, $appName) {
                    $message->to($email)
                            ->subject("Kode Verifikasi Reset Password - {$appName}");
                }
            );
        } catch (\Exception $e) {
            // Hapus token yang dibuat karena pengiriman email gagal
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $email)->delete();
            \Illuminate\Support\Facades\Log::error('SMTP Error saat reset password: ' . $e->getMessage());

            return back()->withInput()->withErrors([
                'email' => 'Permintaan belum dapat diproses karena layanan pengiriman email sistem sedang mengalami gangguan. Silakan hubungi administrator sistem untuk memeriksa konfigurasi layanan email.'
            ]);
        }

        // Redirect ke form reset password
        $redirect = redirect()->route('password.reset', ['email' => $email])
            ->with('success', $genericSuccessMessage);

        // Jika dalam mode pengujian lokal / driver log, simpan dan kirimkan demo_code untuk kemudahan testing
        if (config('mail.default') === 'log' || config('app.env') === 'local') {
            session(['active_reset_code_' . md5($email) => $code]);
            \Illuminate\Support\Facades\Cache::put('active_reset_code_' . md5($email), $code, now()->addMinutes(15));
            $redirect->with('demo_code', $code);
        }

        return $redirect;
    }

    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->input('email', old('email'));
        $demoCode = session('demo_code');
        if (!$demoCode && $email) {
            $demoCode = session('active_reset_code_' . md5($email)) 
                     ?: \Illuminate\Support\Facades\Cache::get('active_reset_code_' . md5($email));
        }
        
        return view('auth.reset-password')->with([
            'token' => null,
            'email' => $email,
            'active_code' => $demoCode
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'token.required' => 'Kode verifikasi wajib diisi.',
            'token.size' => 'Kode verifikasi harus terdiri dari 6 digit angka.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.'
        ]);

        $email = strtolower(trim($request->email));
        $inputToken = trim($request->token);
        $throttleKey = 'verify-reset-code:' . sha1($request->ip() . '|' . $email);

        // Batasi percobaan verifikasi kode (maksimal 5 kali gagal dalam 15 menit)
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            // Hanguskan token demi keamanan jika terdeteksi indikasi brute-force
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $email)->delete();
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withInput()->withErrors([
                'token' => 'Batas percobaan verifikasi telah terlampaui demi keamanan akun. Silakan ajukan permintaan reset baru setelah ' . ceil($seconds / 60) . ' menit.'
            ]);
        }

        // Cari record token di tabel password_resets
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 900);
            return back()->withInput()->withErrors([
                'token' => 'Kode verifikasi tidak valid atau telah kedaluwarsa. Silakan ajukan permintaan reset baru.'
            ]);
        }

        // Cek masa berlaku kode (15 menit)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(15)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $email)->delete();
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 900);
            return back()->withInput()->withErrors([
                'token' => 'Kode verifikasi telah kedaluwarsa (lebih dari 15 menit). Silakan ajukan permintaan reset baru.'
            ]);
        }

        // Verifikasi kecocokan kode secara kriptografis timing-safe (Hash::check)
        $isValid = \Illuminate\Support\Facades\Hash::check($inputToken, $resetRecord->token);

        if (!$isValid) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 900);
            $attemptsLeft = 5 - \Illuminate\Support\Facades\RateLimiter::attempts($throttleKey);
            $errorMsg = 'Kode verifikasi yang Anda masukkan tidak valid.';
            if ($attemptsLeft > 0) {
                $errorMsg .= ' Sisa kesempatan percobaan: ' . $attemptsLeft . ' kali.';
            }
            return back()->withInput()->withErrors(['token' => $errorMsg]);
        }

        // Ambil akun pengguna
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $email)->delete();
            return back()->withInput()->withErrors([
                'email' => 'Akun pengguna tidak ditemukan.'
            ]);
        }

        // Perbarui kata sandi pengguna
        $user->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ])->save();

        // Hapus token setelah berhasil digunakan satu kali (Single-use)
        \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $email)->delete();
        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
        \Illuminate\Support\Facades\Cache::forget('active_reset_code_' . md5($email));
        session()->forget('active_reset_code_' . md5($email));

        // Catat Audit Log
        \App\Models\AuditLog::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'aksi' => 'RESET_PASSWORD',
            'deskripsi' => 'Pengguna ' . $user->nama . ' berhasil merubah password melalui verifikasi kode aman.',
            'created_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Kata sandi berhasil diperbarui! Silakan masuk menggunakan kata sandi baru Anda.');
    }
}
