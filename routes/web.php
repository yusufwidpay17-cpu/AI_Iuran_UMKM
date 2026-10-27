<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AktivasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PedagangController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\LapakController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\RiwayatTransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\SegmentasiController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PedagangPortalController;
use App\Http\Controllers\KalenderOperasionalController;
use App\Http\Controllers\ProfilController;

/*
|--------------------------------------------------------------------------
| Web Routes - SI Iuran UMKM (AmanahLedger)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC AUTHENTICATION & ACTIVATION ROUTES
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/lupa-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token?}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tautan Aktivasi Akun Privat (Pengguna / Pedagang Menentukan Password Mandiri)
Route::get('/aktivasi-akun/{token}', [AktivasiController::class, 'showActivationForm'])->name('aktivasi.form');
Route::post('/aktivasi-akun', [AktivasiController::class, 'activateAccount'])->name('aktivasi.process');

// ==========================================
// ROUTE SINKRONISASI DATABASE ONLINE (AIVEN / VERCEL)
// ==========================================
Route::get('/sync-db', function () {
    try {
        // 1. Jalankan migrasi database otomatis terlebih dahulu
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $migrateOutput = $e->getMessage();
        }

        // 2. Pastikan enum role mendukung pedagang
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'petugas', 'pedagang') NOT NULL DEFAULT 'petugas'");
        } catch (\Exception $e) {}

        // 3. Fallback aman penambahan kolom baru jika belum ada
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN pedagang_id VARCHAR(36) NULL AFTER role");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD INDEX (pedagang_id)");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN status_aktivasi ENUM('aktif', 'pending') NOT NULL DEFAULT 'aktif' AFTER pedagang_id");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN activation_token VARCHAR(100) NULL AFTER status_aktivasi");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN activation_expires_at TIMESTAMP NULL AFTER activation_token");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE pedagang ADD COLUMN foto_profil MEDIUMTEXT NULL AFTER status_legal");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE pedagang MODIFY COLUMN foto_profil MEDIUMTEXT NULL");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN foto_profil MEDIUMTEXT NULL AFTER email");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN foto_profil MEDIUMTEXT NULL");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN password_plain VARCHAR(255) NULL DEFAULT 'password123' AFTER password");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("UPDATE users SET password_plain = 'password123' WHERE password_plain IS NULL OR password_plain = ''");
        } catch (\Exception $e) {}

        // 3b. Pastikan tabel kalender_operasional ada di Cloud Database
        try {
            \Illuminate\Support\Facades\DB::statement("CREATE TABLE IF NOT EXISTS kalender_operasional (
                id VARCHAR(36) NOT NULL PRIMARY KEY,
                tanggal_mulai DATE NOT NULL,
                tanggal_selesai DATE NOT NULL,
                kategori VARCHAR(50) NOT NULL,
                alasan TEXT NOT NULL,
                kebijakan_iuran ENUM('bebas_iuran', 'kewajiban_tetap_berlaku') NOT NULL DEFAULT 'kewajiban_tetap_berlaku',
                status ENUM('aktif', 'dibatalkan') NOT NULL DEFAULT 'aktif',
                created_by VARCHAR(36) NULL,
                updated_by VARCHAR(36) NULL,
                catatan_audit TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_kalender_tanggal_range (tanggal_mulai, tanggal_selesai),
                INDEX idx_kalender_status (status),
                INDEX idx_kalender_kebijakan (kebijakan_iuran)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tagihan MODIFY COLUMN status_bayar ENUM('lunas', 'belum_bayar', 'bayar_sebagian', 'dibatalkan') NOT NULL DEFAULT 'belum_bayar'");
        } catch (\Exception $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE pedagang MODIFY COLUMN status_legal VARCHAR(50) NOT NULL DEFAULT 'legal'");
        } catch (\Exception $e) {}

        // 4. Sinkronkan Akun Superadmin yusuf123
        $superadmin = \App\Models\User::where('username', 'yusuf123')->first();
        if ($superadmin) {
            $superadmin->nama = 'Yusuf';
            $superadmin->role = 'superadmin';
            $superadmin->email = 'yusufwidpay17@gmail.com';
            $superadmin->status_aktivasi = 'aktif';
            if (empty($superadmin->password_plain)) {
                $superadmin->password = \Illuminate\Support\Facades\Hash::make('password123');
                $superadmin->password_plain = 'password123';
            }
            $superadmin->save();
        } else {
            \App\Models\User::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'nama' => 'Yusuf',
                'username' => 'yusuf123',
                'email' => 'yusufwidpay17@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'password_plain' => 'password123',
                'role' => 'superadmin',
                'status_aktivasi' => 'aktif',
            ]);
        }

        // 5. Pastikan Pedagang Sinta Erina Faradhila (Toko Barokah, A-04) ada di Database
        $fotoPath = 'uploads/profil/1792250122_profil_erin.jpeg';
        $fotoData = null;
        if (file_exists(public_path($fotoPath))) {
            $fotoData = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path($fotoPath)));
        }

        $pedagangErin = \App\Models\Pedagang::where('nama_pedagang', 'like', '%Sinta Erina%')
            ->orWhere('no_kios', 'A-04')
            ->first();

        if (!$pedagangErin) {
            $pedagangErin = \App\Models\Pedagang::create([
                'id' => 'f6445d48-2704-47a4-8068-2a661022f6f5',
                'nama_pedagang' => 'Sinta Erina Faradhila',
                'no_ktp' => '32010203040500098',
                'no_telepon' => '082134567891',
                'alamat_lengkap' => 'Semambung, Widoropayung, Besuki Situbondo',
                'nama_usaha' => 'Toko Barokah',
                'kategori_usaha' => 'kuliner',
                'kategori_usaha_detail' => 'bakso',
                'no_kios' => 'A-04',
                'blok_kios' => 'Blok A',
                'tanggal_daftar' => '2026-09-23',
                'status_pedagang' => 'aktif',
                'status_legal' => 'legal',
                'tarif_id' => 'trf-001',
                'saldo_deposit' => 0,
                'foto_profil' => $fotoData ?: $fotoPath,
                'catatan' => 'Pasangan Hidup Semati',
            ]);
        } else {
            $pedagangErin->nama_pedagang = 'Sinta Erina Faradhila';
            $pedagangErin->no_ktp = '32010203040500098';
            $pedagangErin->no_telepon = '082134567891';
            $pedagangErin->alamat_lengkap = 'Semambung, Widoropayung, Besuki Situbondo';
            $pedagangErin->nama_usaha = 'Toko Barokah';
            $pedagangErin->kategori_usaha = 'kuliner';
            $pedagangErin->no_kios = 'A-04';
            $pedagangErin->blok_kios = 'Blok A';
            if ($fotoData) {
                $pedagangErin->foto_profil = $fotoData;
            }
            $pedagangErin->save();
        }

        // Kembalikan foto profil Sri Wahyuni jika sebelumnya tertukar
        $sri = \App\Models\Pedagang::where('nama_pedagang', 'like', '%Sri Wahyuni%')->first();
        if ($sri && $sri->id !== $pedagangErin->id) {
            $sri->foto_profil = null;
            $sri->save();
        }

        // Tautkan akun login erin ke Sinta Erina Faradhila secara presisi
        $merchantUser = \App\Models\User::where('username', 'erin')->first();
        if (!$merchantUser) {
            \App\Models\User::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'nama' => 'Sinta Erina Faradhila',
                'username' => 'erin',
                'email' => 'erin@amanahledger.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'pedagang',
                'pedagang_id' => $pedagangErin->id,
                'foto_profil' => $fotoData ?: $fotoPath,
                'status_aktivasi' => 'aktif',
            ]);
        } else {
            $merchantUser->nama = 'Sinta Erina Faradhila';
            $merchantUser->email = 'erin@amanahledger.id';
            $merchantUser->pedagang_id = $pedagangErin->id;
            $merchantUser->role = 'pedagang';
            if ($fotoData) {
                $merchantUser->foto_profil = $fotoData;
            }
            $merchantUser->status_aktivasi = 'aktif';
            if (empty($merchantUser->password_plain)) {
                $merchantUser->password = \Illuminate\Support\Facades\Hash::make('password123');
                $merchantUser->password_plain = 'password123';
            }
            $merchantUser->save();
        }

        $dbConnection = config('database.default');
        $dbHost = config("database.connections.{$dbConnection}.host");
        $dbName = config("database.connections.{$dbConnection}.database");

        return response("
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; max-width: 580px; margin: 40px auto; padding: 32px; border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.06);'>
                <div style='font-size: 48px; margin-bottom: 12px;'>✨</div>
                <h2 style='color: #059669; margin: 0 0 8px 0; font-size: 24px; font-weight: 800;'>DATABASE AIVEN SUKSES DISINKRONKAN!</h2>
                <p style='color: #475569; font-size: 14px; margin-bottom: 20px;'>Semua fitur terbaru: <b>Role Pedagang</b>, <b>Aktivasi Akun Mandiri</b>, dan <b>Tampilan Dashboard Pedagang</b> sudah aktif di server online!</p>
                
                <div style='text-align: left; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; font-size: 13px; margin-bottom: 16px;'>
                    <div style='font-weight: 700; color: #1e293b; margin-bottom: 8px;'>📋 Akun yang Siap Digunakan untuk Uji Coba:</div>
                    <div style='background: #ffffff; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; margin-bottom: 8px;'>
                        <span style='background: #dbeafe; color: #1e40af; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 11px;'>SUPERADMIN / PENGELOLA</span><br>
                        Username: <b>yusuf123</b><br>
                        Password: <b>password123</b><br>
                        Email: <b>yusufwidpay17@gmail.com</b>
                    </div>
                    <div style='background: #ffffff; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;'>
                        <span style='background: #dcfce7; color: #166534; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 11px;'>MITRA PEDAGANG (MOBILE READY)</span><br>
                        Username: <b>erin</b><br>
                        Password: <b>password123</b>
                    </div>
                </div>

                <div style='text-align: left; background: #0f172a; color: #10b981; padding: 12px; border-radius: 8px; font-family: monospace; font-size: 11px; margin-bottom: 20px; max-height: 120px; overflow-y: auto;'>
                    Status Host: {$dbHost} | DB: {$dbName}<br>
                    " . nl2br(e($migrateOutput ?: 'Semua skema database up-to-date!')) . "
                </div>

                <a href='/login' style='display: block; background: #10b981; color: #ffffff; padding: 14px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px; box-shadow: 0 4px 12px rgba(16,185,129,0.3);'>
                    🚀 Masuk ke Aplikasi Sekarang
                </a>
            </div>
        ", 200, ['Content-Type' => 'text/html']);
    } catch (\Exception $e) {
        return response("
            <div style='font-family: Arial; max-width: 500px; margin: 40px auto; padding: 20px; border-radius: 12px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;'>
                <h3>Gagal Sinkronisasi Database</h3>
                <p>" . e($e->getMessage()) . "</p>
            </div>
        ", 500, ['Content-Type' => 'text/html']);
    }
});


// ==========================================
// 2. PROTECTED ROUTES (LOGIN REQUIRED)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // A. Dashboard Utama (Tampilan disesuaikan otomatis untuk Pengelola vs Pedagang)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // B. Riwayat Transaksi & Cetak Bukti (Tersedia untuk Pengelola dan Pedagang untuk bukti lapaknya)
    Route::get('/riwayat-transaksi', [RiwayatTransaksiController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat-transaksi/{id}/cetak', [RiwayatTransaksiController::class, 'cetak'])->name('riwayat.cetak');

    // C. Portal Khusus Mitra Pedagang (Hanya Role Pedagang & Server-Side Protected)
    Route::middleware(['role:pedagang'])->prefix('pedagang-portal')->name('pedagang.')->group(function () {
        Route::get('/tagihan', [PedagangPortalController::class, 'tagihan'])->name('tagihan');
        Route::get('/tunggakan', [PedagangPortalController::class, 'tunggakan'])->name('tunggakan');
        Route::get('/notifikasi', [PedagangPortalController::class, 'notifikasi'])->name('notifikasi');
        Route::get('/profil', [PedagangPortalController::class, 'profil'])->name('profil');
        Route::post('/profil/foto', [PedagangPortalController::class, 'updateFoto'])->name('profil.foto');
        Route::post('/profil/password', [PedagangPortalController::class, 'updatePassword'])->name('profil.password');
    });

    // D. Profil Saya & Pengaturan Akun (Superadmin, Admin, Petugas, Pedagang)
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil/update', [ProfilController::class, 'updateProfile'])->name('profil.update');
    Route::post('/profil/foto', [ProfilController::class, 'updateFoto'])->name('profil.foto');
    Route::post('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');


    // ==========================================
    // 3. OPERASIONAL PETUGAS & ADMIN (Iuran / Tagihan Lapangan)
    // ==========================================
    Route::middleware(['role:superadmin,admin,petugas'])->group(function () {
        Route::get('/tagihan/input', [TagihanController::class, 'input'])->name('tagihan.input');
        Route::post('/tagihan/tambah-manual', [TagihanController::class, 'storeManual'])->name('tagihan.store-manual');
        Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');
        Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar');
        Route::post('/tagihan/{id}/tidak-ada', [TagihanController::class, 'tidakAda'])->name('tagihan.tidak-ada');
    });


    // ==========================================
    // 4. MODUL KHUSUS PENGELOLA (SUPERADMIN & ADMIN)
    // ==========================================
    Route::middleware(['role:superadmin,admin'])->group(function () {
        
        // Data Pedagang
        Route::get('/pedagang', [PedagangController::class, 'index'])->name('pedagang.index');
        Route::get('/pedagang/create', [PedagangController::class, 'create'])->name('pedagang.create');
        Route::post('/pedagang', [PedagangController::class, 'store'])->name('pedagang.store');
        Route::get('/pedagang/{id}', [PedagangController::class, 'show'])->name('pedagang.show');
        Route::get('/pedagang/{id}/edit', [PedagangController::class, 'edit'])->name('pedagang.edit');
        Route::put('/pedagang/{id}', [PedagangController::class, 'update'])->name('pedagang.update');
        Route::delete('/pedagang/{id}', [PedagangController::class, 'destroy'])->name('pedagang.destroy');
        Route::get('/api/next-kios', [PedagangController::class, 'getNextKios'])->name('pedagang.next-kios');

        // Pendaftaran Pedagang
        Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/pendaftaran/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
        Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
        Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::get('/pendaftaran/{id}/edit', [PendaftaranController::class, 'edit'])->name('pendaftaran.edit');
        Route::put('/pendaftaran/{id}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
        Route::post('/pendaftaran/{id}/setujui', [PendaftaranController::class, 'setujui'])->name('pendaftaran.setujui');
        Route::post('/pendaftaran/{id}/tolak', [PendaftaranController::class, 'tolak'])->name('pendaftaran.tolak');
        Route::delete('/pendaftaran/{id}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

        // Data Lapak
        Route::get('/lapak', [LapakController::class, 'index'])->name('lapak.index');
        Route::post('/lapak', [LapakController::class, 'store'])->name('lapak.store');
        Route::put('/lapak/{id}', [LapakController::class, 'update'])->name('lapak.update');
        Route::delete('/lapak/{id}', [LapakController::class, 'destroy'])->name('lapak.destroy');

        // Laporan Rekapitulasi
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

        // Export Excel
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/pedagang', [ExportExcelController::class, 'pedagang'])->name('pedagang');
            Route::get('/pendaftaran', [ExportExcelController::class, 'pendaftaran'])->name('pendaftaran');
            Route::get('/lapak', [ExportExcelController::class, 'lapak'])->name('lapak');
            Route::get('/tagihan', [ExportExcelController::class, 'tagihan'])->name('tagihan');
            Route::get('/riwayat', [ExportExcelController::class, 'riwayat'])->name('riwayat');
            Route::get('/laporan', [ExportExcelController::class, 'laporan'])->name('laporan');
            Route::get('/pedagang/{id}', [ExportExcelController::class, 'detailPedagang'])->name('detail-pedagang');
        });

        // AI / Segmentasi Analitik
        Route::get('/segmentasi', [SegmentasiController::class, 'index'])->name('segmentasi.index');
        Route::post('/segmentasi/train', [SegmentasiController::class, 'train'])->name('segmentasi.train');
        Route::get('/segmentasi/cek', [SegmentasiController::class, 'cek'])->name('segmentasi.cek');

        // Kalender Operasional & Kontrol Penagihan
        Route::prefix('kalender-operasional')->name('kalender.')->group(function () {
            Route::get('/', [KalenderOperasionalController::class, 'index'])->name('index');
            Route::post('/', [KalenderOperasionalController::class, 'store'])->name('store');
            Route::put('/{id}', [KalenderOperasionalController::class, 'update'])->name('update');
            Route::post('/{id}/batalkan', [KalenderOperasionalController::class, 'batalkan'])->name('batalkan');
            Route::get('/{id}/dampak', [KalenderOperasionalController::class, 'previewDampak'])->name('dampak');
            Route::post('/{id}/koreksi-tagihan', [KalenderOperasionalController::class, 'koreksiTagihan'])->name('koreksi-tagihan');
        });

        // Manajemen Pengguna & Hak Akses
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::put('/pengguna/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
        Route::post('/pengguna/{id}/kirim-ulang-aktivasi', [PenggunaController::class, 'resendActivation'])->name('pengguna.resend-activation');
        Route::post('/pengguna/beri-akses-pedagang', [PenggunaController::class, 'beriAksesPedagang'])->name('pengguna.beri-akses-pedagang');
        Route::post('/pengguna/{id}/cabut-akses-pedagang', [PenggunaController::class, 'cabutAksesPedagang'])->name('pengguna.cabut-akses-pedagang');
        Route::post('/pengguna/reset-password-semua', [PenggunaController::class, 'resetPasswordSemua'])->name('pengguna.reset-password-semua');
        Route::post('/pengguna/{id}/reset-password', [PenggunaController::class, 'resetPassword'])->name('pengguna.reset-password');
    });
});
