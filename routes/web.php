<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

/*
|--------------------------------------------------------------------------
| Web Routes - SI Iuran UMKM (AmanahLedger)
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Authentication Required)
Route::middleware(['auth'])->group(function () {
    
    // 1. Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Data Pedagang
    Route::get('/pedagang', [PedagangController::class, 'index'])->name('pedagang.index');
    Route::get('/pedagang/create', [PedagangController::class, 'create'])->name('pedagang.create');
    Route::post('/pedagang', [PedagangController::class, 'store'])->name('pedagang.store');
    Route::get('/pedagang/{id}', [PedagangController::class, 'show'])->name('pedagang.show');
    Route::get('/pedagang/{id}/edit', [PedagangController::class, 'edit'])->name('pedagang.edit');
    Route::put('/pedagang/{id}', [PedagangController::class, 'update'])->name('pedagang.update');
    Route::delete('/pedagang/{id}', [PedagangController::class, 'destroy'])->name('pedagang.destroy');
    Route::get('/api/next-kios', [PedagangController::class, 'getNextKios'])->name('pedagang.next-kios');

    // 3. Pendaftaran Pedagang
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
    Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::get('/pendaftaran/{id}/edit', [PendaftaranController::class, 'edit'])->name('pendaftaran.edit');
    Route::put('/pendaftaran/{id}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
    Route::post('/pendaftaran/{id}/setujui', [PendaftaranController::class, 'setujui'])->name('pendaftaran.setujui');
    Route::post('/pendaftaran/{id}/tolak', [PendaftaranController::class, 'tolak'])->name('pendaftaran.tolak');
    Route::delete('/pendaftaran/{id}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

    // 4. Data Lapak
    Route::get('/lapak', [LapakController::class, 'index'])->name('lapak.index');
    Route::post('/lapak', [LapakController::class, 'store'])->name('lapak.store');
    Route::put('/lapak/{id}', [LapakController::class, 'update'])->name('lapak.update');
    Route::delete('/lapak/{id}', [LapakController::class, 'destroy'])->name('lapak.destroy');

    // 5. Iuran / Tagihan
    Route::get('/tagihan/input', [TagihanController::class, 'input'])->name('tagihan.input');
    Route::post('/tagihan/tambah-manual', [TagihanController::class, 'storeManual'])->name('tagihan.store-manual');
    Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');
    Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar');
    Route::post('/tagihan/{id}/tidak-ada', [TagihanController::class, 'tidakAda'])->name('tagihan.tidak-ada');

    // 6. Riwayat Transaksi
    Route::get('/riwayat-transaksi', [RiwayatTransaksiController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat-transaksi/{id}/cetak', [RiwayatTransaksiController::class, 'cetak'])->name('riwayat.cetak');

    // 7. Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    // 8. Export Excel
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/pedagang', [ExportExcelController::class, 'pedagang'])->name('pedagang');
        Route::get('/pendaftaran', [ExportExcelController::class, 'pendaftaran'])->name('pendaftaran');
        Route::get('/lapak', [ExportExcelController::class, 'lapak'])->name('lapak');
        Route::get('/tagihan', [ExportExcelController::class, 'tagihan'])->name('tagihan');
        Route::get('/riwayat', [ExportExcelController::class, 'riwayat'])->name('riwayat');
        Route::get('/laporan', [ExportExcelController::class, 'laporan'])->name('laporan');
        Route::get('/pedagang/{id}', [ExportExcelController::class, 'detailPedagang'])->name('detail-pedagang');
    });

    // 9. AI / Segmentasi Analitik
    Route::get('/segmentasi', [SegmentasiController::class, 'index'])->name('segmentasi.index');
    Route::post('/segmentasi/train', [SegmentasiController::class, 'train'])->name('segmentasi.train');
    Route::get('/segmentasi/cek', [SegmentasiController::class, 'cek'])->name('segmentasi.cek');

    // 10. Manajemen Pengguna
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
});
