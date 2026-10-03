@extends('layouts.app')

@section('title', 'Dashboard Operasional')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="page-title">Dashboard Operasional</h2>
        <p class="page-subtitle">Pusat Informasi & Pengelolaan Iuran Pasar UMKM (<span class="live-admin-date">{{ \Carbon\Carbon::today()->isoFormat('D MMMM YYYY') }}</span>)</p>
    </div>
    <div>
        <a href="{{ route('tagihan.input') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Kelola Tagihan Hari Ini
        </a>
    </div>
</div>

<!-- WELCOME FLYER BANNER -->
<div class="welcome-banner">
    <div class="banner-content">
        <div class="banner-badge">
            <span class="pulse-green-dot"></span>
            <span>PUSAT KONTROL DIGITAL BUMPES</span>
        </div>
        <h1 class="banner-title">
            Halo, {{ Auth::user()->nama ?? 'Admin' }}! 👋
        </h1>
        <p class="banner-desc">
            Selamat datang di pusat kontrol pengelolaan iuran harian pedagang. Mari wujudkan transparansi keuangan dan tingkatkan kedisiplinan penarikan lapak hari ini demi kemajuan bersama BUMPes Digital.
        </p>
        <div class="banner-actions">
            <a href="#tabel-belum-bayar" class="btn-banner-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Cek Follow-Up Pedagang
            </a>
            <a href="#progres-section" class="btn-banner-secondary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Lihat Ringkasan
            </a>
        </div>
    </div>
    
    <div class="banner-illustration">
        <div class="illustration-glass-card">
            <div class="chart-graphic" style="padding: 4px 0;">
                <svg width="100%" height="70" viewBox="0 0 220 70" fill="none">
                    <path d="M10 55 Q 50 45, 90 28 T 170 18 T 210 10" stroke="#34d399" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <circle cx="210" cy="10" r="4" fill="#34d399"/>
                    <rect x="25" y="38" width="12" height="22" rx="3" fill="#38bdf8" opacity="0.8"/>
                    <rect x="55" y="25" width="12" height="35" rx="3" fill="#34d399" opacity="0.9"/>
                    <rect x="85" y="42" width="12" height="18" rx="3" fill="#38bdf8" opacity="0.7"/>
                    <rect x="115" y="18" width="12" height="42" rx="3" fill="#34d399"/>
                </svg>
            </div>
            <div class="glass-pill-item success-pill">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                <span>Transparansi 100% Terjaga</span>
            </div>
            <div class="glass-pill-item info-pill">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path></svg>
                <span>Disiplin Lapak BUMPes Digital</span>
            </div>
        </div>
    </div>
</div>

<!-- 1. COMPACT STATS GRID (10 Key Operational Indicators) -->
<div class="stats-grid">
    <!-- Total Pedagang -->
    <div class="stat-card">
        <div class="stat-label">Total Pedagang</div>
        <div class="stat-value">{{ number_format($totalPedagang) }}</div>
        <div class="stat-sub">Terdaftar Sistem</div>
    </div>
    <!-- Pedagang Aktif -->
    <div class="stat-card" style="border-left: 3px solid var(--secondary);">
        <div class="stat-label" style="color: var(--secondary);">Pedagang Aktif</div>
        <div class="stat-value" style="color: var(--secondary);">{{ number_format($pedagangAktif) }}</div>
        <div class="stat-sub" style="color: var(--secondary); font-weight: 600;">Status Berjualan</div>
    </div>
    <!-- Pedagang Nonaktif -->
    <div class="stat-card" style="border-left: 3px solid var(--error);">
        <div class="stat-label" style="color: var(--error);">Pedagang Nonaktif</div>
        <div class="stat-value" style="color: var(--error);">{{ number_format($pedagangNonaktif) }}</div>
        <div class="stat-sub" style="color: var(--error); font-weight: 600;">Tutup / Nonaktif</div>
    </div>
    <!-- Total Lapak -->
    <div class="stat-card">
        <div class="stat-label">Total Lapak</div>
        <div class="stat-value">{{ number_format($totalLapak) }}</div>
        <div class="stat-sub">Unit Kios Market</div>
    </div>
    <!-- Target Iuran Hari Ini -->
    <div class="stat-card">
        <div class="stat-label">Target Iuran Hari Ini</div>
        <div class="stat-value numeric-display">Rp {{ number_format($targetIuranHariIni, 0, ',', '.') }}</div>
        <div class="stat-sub">Target Nominal</div>
    </div>
    <!-- Terkumpul Hari Ini -->
    <div class="stat-card" style="border-left: 3px solid var(--secondary);">
        <div class="stat-label" style="color: var(--secondary);">Terkumpul Hari Ini</div>
        <div class="stat-value numeric-display" style="color: var(--secondary);">Rp {{ number_format($totalIuranTerkumpulHariIni, 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--secondary); font-weight: 600;">Total Masuk</div>
    </div>
    <!-- Belum Bayar Hari Ini -->
    <div class="stat-card" style="border-left: 3px solid var(--error);">
        <div class="stat-label" style="color: var(--error);">Belum Bayar Hari Ini</div>
        <div class="stat-value numeric-display" style="color: var(--error);">Rp {{ number_format($sisaBelumBayarHariIni, 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--error); font-weight: 600;">{{ $belumBayarHariIniCount }} Pedagang</div>
    </div>
    <!-- Sudah Bayar Count -->
    <div class="stat-card">
        <div class="stat-label">Sudah Bayar Hari Ini</div>
        <div class="stat-value" style="color: var(--secondary);">{{ $sudahBayarHariIniCount }}</div>
        <div class="stat-sub" style="color: var(--secondary); font-weight: 600;">Pedagang Lunas</div>
    </div>
    <!-- Total Tunggakan -->
    <div class="stat-card" style="border-left: 3px solid var(--error);">
        <div class="stat-label" style="color: var(--error);">Total Tunggakan</div>
        <div class="stat-value numeric-display" style="color: var(--error);">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--error); font-weight: 600;">Akumulasi Piutang</div>
    </div>
    <!-- Pendapatan Bulan Ini -->
    <div class="stat-card" style="border-left: 3px solid var(--primary);">
        <div class="stat-label" style="color: var(--primary);">Pendapatan Bulan Ini</div>
        <div class="stat-value numeric-display" style="color: var(--primary);">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--primary); font-weight: 600;">{{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</div>
    </div>
</div>

<!-- 2. PROGRES REALISASI IURAN HARI INI -->
<div id="progres-section" class="card" style="border-top: 3px solid var(--primary);">
    <!-- Hero Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--primary-container); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--on-surface); margin: 0; line-height: 1.2;">Progres Realisasi Iuran Hari Ini</h3>
                <div style="font-size: 0.78125rem; color: var(--on-surface-variant); margin-top: 2px;">Pantauan transaksi pelunasan retribusi harian secara realtime</div>
            </div>
        </div>

        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 999px; font-size: 0.75rem; font-weight: 600; color: #047857;">
            <span style="width: 7px; height: 7px; border-radius: 50%; background-color: #10b981; display: inline-block;"></span>
            <span>Rasio Pelunasan: {{ $persenPelunasanHariIni }}%</span>
        </div>
    </div>

    <!-- Progress Track Bar -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
            <span style="font-size: 0.75rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.03em;">Progres Pembayaran Iuran</span>
            <span style="font-size: 0.875rem; font-weight: 700;">
                <span style="color: var(--secondary); font-weight: 800;">Rp {{ number_format($totalIuranTerkumpulHariIni, 0, ',', '.') }}</span>
                <span style="color: #94a3b8; font-weight: 500;"> / Rp {{ number_format($targetIuranHariIni, 0, ',', '.') }}</span>
            </span>
        </div>
        
        <div style="position: relative; background: #e2e8f0; height: 12px; border-radius: 999px; overflow: hidden;">
            <div style="height: 100%; border-radius: 999px; background: linear-gradient(90deg, #059669 0%, #003d9b 100%); width: {{ min(100, max(0, $persenPelunasanHariIni)) }}%; transition: width 0.8s ease;"></div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 6px; font-size: 0.71875rem; color: #64748b; font-weight: 500;">
            <span>0%</span>
            <span>50%</span>
            <span style="font-weight: 700; color: var(--primary);">Target 100%</span>
        </div>
    </div>

    <!-- 4 KPI Mini Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
        <!-- Target Nominal -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.71875rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Target Nominal</span>
                    <div style="width: 26px; height: 26px; border-radius: 6px; background: #eff6ff; color: var(--primary); display: flex; align-items: center; justify-content: center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle></svg>
                    </div>
                </div>
                <div class="numeric-display" style="font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">
                    Rp {{ number_format($targetIuranHariIni, 0, ',', '.') }}
                </div>
            </div>
            <div style="font-size: 0.71875rem; color: #64748b; margin-top: 4px;">{{ $sudahBayarHariIniCount + $belumBayarHariIniCount }} Pedagang Tagihan</div>
        </div>

        <!-- Iuran Terkumpul -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 3px solid var(--secondary); border-radius: 8px; padding: 10px 12px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.71875rem; font-weight: 600; color: var(--secondary); text-transform: uppercase;">Iuran Terkumpul</span>
                    <div style="width: 26px; height: 26px; border-radius: 6px; background: #ecfdf5; color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="numeric-display" style="font-size: 1.1rem; font-weight: 700; color: var(--secondary);">
                    Rp {{ number_format($totalIuranTerkumpulHariIni, 0, ',', '.') }}
                </div>
            </div>
            <div style="font-size: 0.71875rem; color: var(--secondary); font-weight: 600; margin-top: 4px;">✓ {{ $sudahBayarHariIniCount }} Pedagang Lunas</div>
        </div>

        <!-- Sisa Belum Bayar -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 3px solid var(--error); border-radius: 8px; padding: 10px 12px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.71875rem; font-weight: 600; color: var(--error); text-transform: uppercase;">Sisa Belum Bayar</span>
                    <div style="width: 26px; height: 26px; border-radius: 6px; background: #fef2f2; color: var(--error); display: flex; align-items: center; justify-content: center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="numeric-display" style="font-size: 1.1rem; font-weight: 700; color: var(--error);">
                    Rp {{ number_format($sisaBelumBayarHariIni, 0, ',', '.') }}
                </div>
            </div>
            <div style="font-size: 0.71875rem; color: var(--error); font-weight: 600; margin-top: 4px;">⏳ {{ $belumBayarHariIniCount }} Pedagang Menunggak</div>
        </div>

        <!-- Tingkat Capaian -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.71875rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Tingkat Capaian</span>
                    <div style="width: 26px; height: 26px; border-radius: 6px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="numeric-display" style="font-size: 1.1rem; font-weight: 700; color: var(--primary);">
                    {{ $persenPelunasanHariIni }}%
                </div>
            </div>
            <div style="font-size: 0.71875rem; font-weight: 600; margin-top: 4px;">
                @if($persenPelunasanHariIni >= 80)
                    <span style="color: #059669;">🔥 Performa Baik</span>
                @elseif($persenPelunasanHariIni >= 50)
                    <span style="color: #0284c7;">📈 Progres Bagus</span>
                @else
                    <span style="color: #d97706;">⚠️ Perlu Penagihan</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 3. GRID SECTION B & C (PEDAGANG BELUM BAYAR & TOP 5 TUNGGAKAN) -->
<div class="grid-2" style="grid-template-columns: 1.6fr 1fr; gap: 16px; margin-bottom: 16px;">
    
    <!-- SECTION B: PEDAGANG BELUM BAYAR HARI INI -->
    <div id="tabel-belum-bayar" class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 class="card-title" style="margin-bottom: 0;">Pedagang Belum Bayar Hari Ini</h3>
            <span class="badge badge-bayar-belum_bayar" style="font-size: 0.71875rem;">{{ $unpaidMerchantsToday->count() }} Pedagang</span>
        </div>
        
        <div class="table-responsive" style="max-height: 310px; overflow-y: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Lapak</th>
                        <th>Nama Pedagang</th>
                        <th>Tagihan Hari Ini</th>
                        <th>Total Tunggakan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unpaidMerchantsToday as $unp)
                        <tr>
                            <td><span class="badge" style="background: #f1f5f9; color: #475569;">{{ $unp->pedagang->no_kios ?? '-' }}</span></td>
                            <td style="font-weight: 600;">
                                <a href="{{ route('pedagang.show', $unp->pedagang_id) }}" style="color: var(--primary); text-decoration: none;">
                                    {{ $unp->pedagang->nama_pedagang ?? '-' }}
                                </a>
                            </td>
                            <td class="numeric-display">Rp {{ number_format($unp->nominal_tagihan, 0, ',', '.') }}</td>
                            <td class="numeric-display" style="color: var(--error); font-weight: 700;">
                                Rp {{ number_format($unp->total_tunggakan_pedagang, 0, ',', '.') }}
                                <div style="font-size: 0.71875rem; color: var(--outline); font-weight: 400;">{{ $unp->jumlah_hari_menunggak }} Hari</div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                    <button class="btn btn-primary btn-sm" onclick="openBayarModal('{{ $unp->id }}', '{{ addslashes($unp->pedagang->nama_pedagang ?? '') }}', '{{ $unp->nominal_tagihan }}')">
                                        Bayar
                                    </button>
                                    <a href="{{ route('pedagang.show', $unp->pedagang_id) }}" class="btn btn-secondary btn-sm">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--secondary); padding: 24px; font-weight: 600;">
                                🎉 Semua pedagang sudah melunasi iuran hari ini!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION C: TOP 5 TUNGGAKAN TERBESAR -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 class="card-title" style="margin-bottom: 0; color: var(--error);">Top 5 Tunggakan Terbesar</h3>
            <span style="font-size: 0.75rem; color: var(--outline);">Akumulasi Retribusi</span>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @forelse ($topArrears as $index => $top)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8fafc; border-radius: 8px; border-left: 3px solid var(--error); border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #fee2e2; color: #991b1b; display: flex; align-items: center; justify-content: center; font-size: 0.71875rem; font-weight: 700; flex-shrink: 0;">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <div style="font-weight: 600; font-size: 0.84375rem; line-height: 1.2;">
                                <a href="{{ route('pedagang.show', $top->id) }}" style="color: var(--on-surface); text-decoration: none;">
                                    {{ $top->nama_pedagang }}
                                </a>
                            </div>
                            <div style="font-size: 0.71875rem; color: #64748b; margin-top: 2px;">
                                Kios {{ $top->no_kios }} ({{ $top->blok_kios }})
                            </div>
                        </div>
                    </div>
                    <div class="numeric-display" style="font-weight: 700; color: var(--error); font-size: 0.875rem;">
                        Rp {{ number_format($top->total_tunggakan, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--outline); padding: 24px; font-size: 0.8125rem;">Tidak ada pedagang menunggak.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- 4. GRID SECTION D & E (TRANSAKSI TERBARU & GRAFIK TREN) -->
<div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
    
    <!-- SECTION D: TRANSAKSI PEMBAYARAN TERBARU -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 class="card-title" style="margin-bottom: 0;">Transaksi Pembayaran Terbaru</h3>
            <a href="{{ route('riwayat.index') }}" style="font-size: 0.78125rem; font-weight: 600; color: var(--primary);">Lihat Semua &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pedagang</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentTransactions as $txn)
                        <tr>
                            <td style="font-size: 0.75rem; color: #64748b;">{{ $txn->waktu_bayar ? $txn->waktu_bayar->format('d/m/Y H:i') : '-' }}</td>
                            <td style="font-weight: 600;">
                                <a href="{{ route('pedagang.show', $txn->pedagang_id) }}" style="color: var(--primary); text-decoration: none;">
                                    {{ $txn->pedagang->nama_pedagang ?? '-' }}
                                </a>
                            </td>
                            <td class="numeric-display" style="color: var(--secondary); font-weight: 700;">Rp {{ number_format($txn->nominal_dibayar, 0, ',', '.') }}</td>
                            <td><span class="badge badge-bayar-lunas" style="font-size: 0.6875rem;">{{ strtoupper($txn->metode_bayar) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--outline); padding: 20px; font-size: 0.8125rem;">Belum ada transaksi hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION E: GRAFIK TREN PENDAPATAN IURAN -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 class="card-title" style="margin-bottom: 0;">Tren Pendapatan Iuran</h3>
            <span style="font-size: 0.75rem; color: #64748b;">7 Hari Terakhir</span>
        </div>
        <div style="position: relative; height: 230px; width: 100%;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- MODAL QUICK BAYAR -->
<div class="modal-overlay" id="modal-quick-bayar">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title" id="quick-bayar-title">Input Pembayaran Iuran</h3>
            <button class="modal-close" onclick="closeBayarModal()">&times;</button>
        </div>
        <form id="quick-bayar-form" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Pedagang</label>
                    <input type="text" id="quick-bayar-pedagang" class="form-input" readonly style="background: #f8fafc; font-weight: 600;">
                </div>
                <div class="form-group">
                    <label class="form-label">Nominal Pembayaran (Rp)</label>
                    <input type="number" name="nominal_dibayar" id="quick-bayar-nominal" class="form-input" required min="0" step="1000">
                </div>
                <div class="form-group">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode_bayar" class="form-select" required>
                        <option value="tunai">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">No Bukti / Keterangan</label>
                    <input type="text" name="no_bukti" class="form-input" placeholder="Opsional (Struk/Keterangan)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBayarModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chart7Labels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($chart7Values) !!},
                    backgroundColor: '#003d9b',
                    hoverBackgroundColor: '#00317e',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 12,
                        bottom: 0,
                        left: 0,
                        right: 0
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: '600', family: 'Inter' },
                        bodyFont: { size: 13, weight: '700', family: 'Inter' },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Inter' }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grace: '15%',
                        border: { dash: [4, 4] },
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11, family: 'Inter' },
                            color: '#64748b',
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                                if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });

    function openBayarModal(tagihanId, namaPedagang, nominal) {
        document.getElementById('quick-bayar-form').action = '/tagihan/' + tagihanId + '/bayar';
        document.getElementById('quick-bayar-pedagang').value = namaPedagang;
        document.getElementById('quick-bayar-nominal').value = nominal;
        document.getElementById('modal-quick-bayar').classList.add('active');
    }

    function closeBayarModal() {
        document.getElementById('modal-quick-bayar').classList.remove('active');
    }
</script>
@endsection
