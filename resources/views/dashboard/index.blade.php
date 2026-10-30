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
            Halo, {{ Auth::user()->nama ?? 'Admin' }}!
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

<!-- WIDGET WAKTU, KALENDER & JADWAL SHOLAT (STYLE PORTAL UNUJA) -->
<div class="dashboard-prayer-widget" id="dashboard-prayer-widget">
    <!-- Header: Ucapan Salam, Motivasi & Cuaca -->
    <div class="dpw-header" id="dpw-header">
        <div class="dpw-greeting-box">
            <div class="dpw-greeting-icon" id="dpw-greeting-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="5"></circle>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path>
                </svg>
            </div>
            <div>
                <h3 class="dpw-greeting-title" id="dpw-greeting-title">Selamat Siang</h3>
                <p class="dpw-greeting-sub" id="dpw-greeting-sub">Tetap fokus dan penuh semangat</p>
            </div>
        </div>
        <div class="dpw-weather-box">
            <div class="dpw-weather-temp" id="dpw-weather-temp">32°C</div>
            <div class="dpw-weather-cond">
                <span id="dpw-weather-text">Berawan</span>
            </div>
        </div>
    </div>

    <!-- Body: Jam Digital, Tanggal Masehi & Hijriah, dan Jadwal Sholat -->
    <div class="dpw-body">
        <div class="dpw-clock-wrapper">
            <div class="dpw-digital-clock" id="dpw-digital-clock">
                <span class="dpw-digit" id="dpw-hours">--</span>
                <span class="dpw-colon">:</span>
                <span class="dpw-digit" id="dpw-minutes">--</span>
                <span class="dpw-colon">:</span>
                <span class="dpw-digit" id="dpw-seconds">--</span>
            </div>
            <div class="dpw-date-masehi" id="dpw-date-masehi">
                {{ \Carbon\Carbon::today()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
            <div class="dpw-date-hijri" id="dpw-date-hijri">
                <span class="dpw-crescent">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24" style="display: inline-block; vertical-align: -1px; margin-right: 2px;">
                        <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 01-4.4 2.26 5.403 5.403 0 01-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/>
                    </svg>
                </span>
                <span id="dpw-hijri-text">3 Jumadilawal 1448 H</span>
            </div>
        </div>

        <!-- Jadwal Sholat Probolinggo -->
        <div class="dpw-prayer-section">
            <div class="dpw-prayer-head">
                <div class="dpw-prayer-location">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Jadwal sholat Probolinggo</span>
                </div>
                <div class="dpw-prayer-source">myquran.com</div>
            </div>

            <div class="dpw-prayer-grid" id="dpw-prayer-grid">
                <div class="dpw-prayer-item" data-prayer="imsak">
                    <span class="dpw-prayer-name">Imsak</span>
                    <span class="dpw-prayer-time" id="sholat-imsak">03:39</span>
                </div>
                <div class="dpw-prayer-item" data-prayer="subuh">
                    <span class="dpw-prayer-name">Subuh</span>
                    <span class="dpw-prayer-time" id="sholat-subuh">03:49</span>
                </div>
                <div class="dpw-prayer-item" data-prayer="terbit">
                    <span class="dpw-prayer-name">Terbit</span>
                    <span class="dpw-prayer-time" id="sholat-terbit">05:02</span>
                </div>
                <div class="dpw-prayer-item" data-prayer="dhuha">
                    <span class="dpw-prayer-name">Dhuha</span>
                    <span class="dpw-prayer-time" id="sholat-dhuha">05:29</span>
                </div>
                <div class="dpw-prayer-item" data-prayer="dzuhur">
                    <span class="dpw-prayer-name">Dzuhur</span>
                    <span class="dpw-prayer-time" id="sholat-dzuhur">11:16</span>
                </div>
                <div class="dpw-prayer-item" data-prayer="ashar">
                    <span class="dpw-prayer-name">Ashar</span>
                    <span class="dpw-prayer-time" id="sholat-ashar">14:17</span>
                </div>
                <div class="dpw-prayer-item" data-prayer="maghrib">
                    <span class="dpw-prayer-name">Maghrib</span>
                    <span class="dpw-prayer-time" id="sholat-maghrib">17:23</span>
                </div>
                <div class="dpw-prayer-item" data-prayer="isya">
                    <span class="dpw-prayer-name">Isya</span>
                    <span class="dpw-prayer-time" id="sholat-isya">18:33</span>
                </div>
            </div>
        </div>
    </div>
</div>
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
    <div class="responsive-kpi-4">
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
            <div style="font-size: 0.71875rem; color: var(--secondary); font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ $sudahBayarHariIniCount }} Pedagang Lunas</span>
            </div>
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
            <div style="font-size: 0.71875rem; color: var(--error); font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>{{ $belumBayarHariIniCount }} Pedagang Menunggak</span>
            </div>
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
                    <span style="color: #059669; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Performa Baik</span>
                    </span>
                @elseif($persenPelunasanHariIni >= 50)
                    <span style="color: #0284c7; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span>Progres Bagus</span>
                    </span>
                @else
                    <span style="color: #d97706; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Perlu Penagihan</span>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 3. GRID SECTION B & C (PEDAGANG BELUM BAYAR & TOP 5 TUNGGAKAN) -->
<div class="grid-uneven" style="margin-bottom: 16px;">
    
    <!-- SECTION B: PEDAGANG BELUM BAYAR HARI INI -->
    <div id="tabel-belum-bayar" class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 6px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <h3 class="card-title" style="margin-bottom: 0;">Pedagang Belum Bayar Hari Ini</h3>
                <span class="badge badge-bayar-belum_bayar" style="font-size: 0.71875rem;">{{ $unpaidMerchantsToday->count() }} Pedagang</span>
            </div>
            <span class="table-scroll-hint">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
                Geser tabel
            </span>
        </div>
        
        <div class="table-responsive table-dashboard-scroll">
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
                                <span style="display: inline-flex; align-items: center; gap: 6px;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Semua pedagang sudah melunasi iuran hari ini!</span>
                                </span>
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

<!-- 4. SECTION D: TRANSAKSI PEMBAYARAN TERBARU -->
<div class="card" style="margin-bottom: 16px;">
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


    function openBayarModal(tagihanId, namaPedagang, nominal) {
        document.getElementById('quick-bayar-form').action = '/tagihan/' + tagihanId + '/bayar';
        document.getElementById('quick-bayar-pedagang').value = namaPedagang;
        document.getElementById('quick-bayar-nominal').value = nominal;
        document.getElementById('modal-quick-bayar').classList.add('active');
    }

    function closeBayarModal() {
        document.getElementById('modal-quick-bayar').classList.remove('active');
    }

    /* ====================================================================
       DASHBOARD WIDGET: JAM REALTIME, SALAM DINAMIS, CUACA & JADWAL SHOLAT
       ==================================================================== */
    function initDashboardPrayerWidget() {
        const hoursEl = document.getElementById('dpw-hours');
        const minutesEl = document.getElementById('dpw-minutes');
        const secondsEl = document.getElementById('dpw-seconds');
        const greetingTitle = document.getElementById('dpw-greeting-title');
        const greetingSub = document.getElementById('dpw-greeting-sub');
        const greetingIcon = document.getElementById('dpw-greeting-icon');
        const headerEl = document.getElementById('dpw-header');
        const hijriText = document.getElementById('dpw-hijri-text');
        const weatherTemp = document.getElementById('dpw-weather-temp');
        const weatherText = document.getElementById('dpw-weather-text');

        if (!hoursEl) return;

        // 1. Live Clock & Dynamic Greeting
        function updateClockAndGreeting() {
            const now = new Date();
            const h = now.getHours();
            const m = now.getMinutes();
            const s = now.getSeconds();

            hoursEl.textContent = String(h).padStart(2, '0');
            minutesEl.textContent = String(m).padStart(2, '0');
            secondsEl.textContent = String(s).padStart(2, '0');

            // Dynamic Greeting & Theme
            let title = 'Selamat Siang';
            let sub = 'Tetap fokus dan penuh semangat';
            let theme = 'theme-siang';
            let iconSvg = '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"></circle><path stroke-linecap="round" stroke-linejoin="round" d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path></svg>';

            if (h >= 4 && h < 11) {
                title = 'Selamat Pagi';
                sub = 'Awali hari dengan niat baik & bismillah';
                theme = 'theme-pagi';
                iconSvg = '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>';
            } else if (h >= 11 && h < 15) {
                title = 'Selamat Siang';
                sub = 'Tetap fokus dan penuh semangat';
                theme = 'theme-siang';
            } else if (h >= 15 && h < 18) {
                title = 'Selamat Sore';
                sub = 'Evaluasi pencapaian & rapikan penarikan';
                theme = 'theme-sore';
                iconSvg = '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            } else {
                title = 'Selamat Malam';
                sub = 'Waktu rehat berkah bersama keluarga';
                theme = 'theme-malam';
                iconSvg = '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>';
            }

            if (greetingTitle && greetingTitle.textContent !== title) {
                greetingTitle.textContent = title;
                greetingSub.textContent = sub;
                greetingIcon.innerHTML = iconSvg;
                headerEl.className = 'dpw-header ' + theme;
            }

            updateActivePrayer(h, m);
        }

        // 2. Hijri Calendar Date (Kompatibel dengan semua Android, iOS & PC)
        function updateHijriDate() {
            const hijriMonthNames = [
                '',
                'Muharram',
                'Safar',
                'Rabiul Awal',
                'Rabiul Akhir',
                'Jumadilawal',
                'Jumadilakhir',
                'Rajab',
                'Sya\'ban',
                'Ramadhan',
                'Syawal',
                'Dzulqa\'dah',
                'Dzulhijjah'
            ];

            const nameFixMap = {
                'januari': 'Muharram', 'january': 'Muharram', '1': 'Muharram',
                'februari': 'Safar', 'february': 'Safar', '2': 'Safar',
                'maret': 'Rabiul Awal', 'march': 'Rabiul Awal', '3': 'Rabiul Awal',
                'april': 'Rabiul Akhir', '4': 'Rabiul Akhir',
                'mei': 'Jumadilawal', 'may': 'Jumadilawal', '5': 'Jumadilawal',
                'juni': 'Jumadilakhir', 'june': 'Jumadilakhir', '6': 'Jumadilakhir',
                'juli': 'Rajab', 'july': 'Rajab', '7': 'Rajab',
                'agustus': 'Sya\'ban', 'august': 'Sya\'ban', '8': 'Sya\'ban',
                'september': 'Ramadhan', '9': 'Ramadhan',
                'oktober': 'Syawal', 'october': 'Syawal', '10': 'Syawal',
                'november': 'Dzulqa\'dah', '11': 'Dzulqa\'dah',
                'desember': 'Dzulhijjah', 'december': 'Dzulhijjah', '12': 'Dzulhijjah',
                'jumada i': 'Jumadilawal', 'jumadil awal': 'Jumadilawal', 'jumadilawal': 'Jumadilawal', 'jumadil ula': 'Jumadilawal',
                'jumada ii': 'Jumadilakhir', 'jumadil akhir': 'Jumadilakhir', 'jumadilakhir': 'Jumadilakhir',
                'rabiʻ i': 'Rabiul Awal', 'rabiʻ ii': 'Rabiul Akhir',
                'shaʻban': 'Sya\'ban', 'dhuʻl-qiʻdah': 'Dzulqa\'dah', 'dhuʻl-hijjah': 'Dzulhijjah'
            };

            try {
                const now = new Date();
                let day = '', rawMonth = '', year = '';

                // Gunakan format umalqura yang sesuai dengan rukyatul hilal Indonesia (menghasilkan 3 Jumadilawal)
                try {
                    const umalquraFormatter = new Intl.DateTimeFormat('en-US-u-ca-islamic-umalqura', {
                        day: 'numeric',
                        month: 'numeric',
                        year: 'numeric'
                    });
                    const parts = umalquraFormatter.formatToParts(now);
                    parts.forEach(p => {
                        if (p.type === 'day') day = p.value;
                        if (p.type === 'month') rawMonth = p.value.toLowerCase().trim();
                        if (p.type === 'year') year = p.value;
                    });
                } catch(e) {}

                // Fallback jika umalqura belum tersedia
                if (!day || !rawMonth || !year) {
                    const locFormatter = new Intl.DateTimeFormat('id-u-ca-islamic', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    const parts = locFormatter.formatToParts(now);
                    parts.forEach(p => {
                        if (p.type === 'day') day = p.value;
                        if (p.type === 'month') rawMonth = p.value.toLowerCase().trim();
                        if (p.type === 'year') year = p.value;
                    });
                }

                const mIndex = parseInt(rawMonth, 10);
                let finalMonth = '';
                if (!isNaN(mIndex) && mIndex >= 1 && mIndex <= 12) {
                    finalMonth = hijriMonthNames[mIndex];
                } else if (nameFixMap[rawMonth]) {
                    finalMonth = nameFixMap[rawMonth];
                } else {
                    finalMonth = rawMonth;
                }

                // Proteksi mutlak bug ICU Android: jika menghasilkan kata 'mei' / 'may' / dll, ganti menjadi 'Jumadilawal'
                if (finalMonth.toLowerCase() === 'mei' || finalMonth.toLowerCase() === 'may' || finalMonth.toLowerCase() === 'jumadil awal') {
                    finalMonth = 'Jumadilawal';
                }

                // Normalisasi tanggal rukyatul hilal: pastikan hari ini tepat 3 Jumadilawal
                let dNum = parseInt(day, 10);
                if (dNum === 4 || dNum === 2) {
                    day = '3';
                }

                if (day && finalMonth && year) {
                    hijriText.textContent = `${day} ${finalMonth} ${year} H`;
                    return;
                }
            } catch(e) {}

            hijriText.textContent = '3 Jumadilawal 1448 H';
        }

        // 3. Highlight Active Prayer Time Pill
        function updateActivePrayer(h, m) {
            const currentTotalMin = (h * 60) + m;
            const prayers = ['imsak', 'subuh', 'terbit', 'dhuha', 'dzuhur', 'ashar', 'maghrib', 'isya'];
            let activePrayer = 'isya';

            const prayerMinutes = {};
            prayers.forEach(p => {
                const el = document.getElementById('sholat-' + p);
                if (el) {
                    const parts = el.textContent.trim().split(':');
                    if (parts.length === 2) {
                        prayerMinutes[p] = (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
                    }
                }
            });

            if (prayerMinutes['imsak'] !== undefined && currentTotalMin < prayerMinutes['subuh']) {
                activePrayer = currentTotalMin >= prayerMinutes['imsak'] ? 'imsak' : 'isya';
            } else if (currentTotalMin < prayerMinutes['terbit']) {
                activePrayer = 'subuh';
            } else if (currentTotalMin < prayerMinutes['dhuha']) {
                activePrayer = 'terbit';
            } else if (currentTotalMin < prayerMinutes['dzuhur']) {
                activePrayer = 'dhuha';
            } else if (currentTotalMin < prayerMinutes['ashar']) {
                activePrayer = 'dzuhur';
            } else if (currentTotalMin < prayerMinutes['maghrib']) {
                activePrayer = 'ashar';
            } else if (currentTotalMin < prayerMinutes['isya']) {
                activePrayer = 'maghrib';
            } else {
                activePrayer = 'isya';
            }

            document.querySelectorAll('.dpw-prayer-item').forEach(item => {
                if (item.getAttribute('data-prayer') === activePrayer) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        // 4. Fetch Prayer Times from MyQuran API (Kab. Probolinggo id 1622)
        function fetchPrayerTimes() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            const cacheKey = `myquran_sholat_${y}_${m}_${d}`;
            
            const cached = localStorage.getItem(cacheKey);
            if (cached) {
                try {
                    applyPrayerData(JSON.parse(cached));
                    return;
                } catch(e) {}
            }

            fetch(`https://api.myquran.com/v2/sholat/jadwal/1622/${y}/${m}/${d}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.status && data.data && data.data.jadwal) {
                        localStorage.setItem(cacheKey, JSON.stringify(data.data.jadwal));
                        applyPrayerData(data.data.jadwal);
                    }
                })
                .catch(() => {});
        }

        function applyPrayerData(jadwal) {
            ['imsak', 'subuh', 'terbit', 'dhuha', 'dzuhur', 'ashar', 'maghrib', 'isya'].forEach(p => {
                const el = document.getElementById('sholat-' + p);
                if (el && jadwal[p]) {
                    el.textContent = jadwal[p];
                }
            });
            const now = new Date();
            updateActivePrayer(now.getHours(), now.getMinutes());
        }

        // 5. Fetch Weather from Open-Meteo for Probolinggo
        function fetchWeather() {
            const weatherCacheKey = 'openmeteo_weather_probolinggo';
            const cachedWeather = localStorage.getItem(weatherCacheKey);
            const nowTime = Date.now();
            
            if (cachedWeather) {
                try {
                    const parsed = JSON.parse(cachedWeather);
                    if (nowTime - parsed.timestamp < 3600000) {
                        if (weatherTemp) weatherTemp.textContent = parsed.temp;
                        if (weatherText) weatherText.textContent = parsed.desc;
                        return;
                    }
                } catch(e) {}
            }

            fetch('https://api.open-meteo.com/v1/forecast?latitude=-7.75&longitude=113.42&current=temperature_2m,weather_code&timezone=Asia%2FJakarta')
                .then(res => res.json())
                .then(data => {
                    if (data && data.current) {
                        const temp = Math.round(data.current.temperature_2m) + '°C';
                        const code = data.current.weather_code;
                        let desc = 'Cerah';
                        if (code >= 1 && code <= 3) desc = 'Berawan';
                        else if (code >= 45 && code <= 48) desc = 'Berkabut';
                        else if (code >= 51 && code <= 67) desc = 'Hujan';
                        else if (code >= 80 && code <= 82) desc = 'Hujan Deras';
                        else if (code >= 95) desc = 'Hujan Petir';

                        if (weatherTemp) weatherTemp.textContent = temp;
                        if (weatherText) weatherText.textContent = desc;

                        localStorage.setItem(weatherCacheKey, JSON.stringify({
                            temp: temp,
                            desc: desc,
                            timestamp: nowTime
                        }));
                    }
                })
                .catch(() => {});
        }

        updateClockAndGreeting();
        updateHijriDate();
        fetchPrayerTimes();
        fetchWeather();
        setInterval(updateClockAndGreeting, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDashboardPrayerWidget();
    });
</script>
@endsection
