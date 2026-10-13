@extends('layouts.app')

@section('title', 'Portal Mitra Pedagang - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--on-surface);">Portal Mitra Pedagang</h2>
        <p class="page-subtitle" style="margin: 4px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Pusat Informasi Lapak & Pembayaran Iuran Pasar UMKM (<span class="live-pedagang-date">{{ \Carbon\Carbon::today()->isoFormat('D MMMM YYYY') }}</span>)
        </p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; border: 1px solid rgba(16, 185, 129, 0.3);">
            ● Mitra Terverifikasi
        </span>
    </div>
</div>

@if(!$pedagang)
    <div class="card" style="background: var(--surface-container-lowest); border: 1px solid var(--outline-variant); border-radius: var(--radius-lg); padding: 32px; text-align: center; margin-bottom: 24px;">
        <div style="font-size: 3.5rem; margin-bottom: 12px;">🏪</div>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--on-surface);">Akun Belum Tertaut ke Data Kios</h3>
        <p style="color: var(--on-surface-variant); font-size: 0.9rem; max-width: 520px; margin: 8px auto 20px; line-height: 1.6;">
            Akun login Anda telah aktif, namun belum ditautkan ke nomor kios pedagang oleh pengelola pasar. Silakan hubungi pengelola/petugas pasar untuk menghubungkan akun Anda.
        </p>
    </div>
@else

    <!-- 1. WELCOME FLYER BANNER EKSKLUSIF PEDAGANG (Style Sesuai Permintaan) -->
    <div class="welcome-banner" style="margin-bottom: 20px;">
        <div class="banner-content">
            <div class="banner-badge">
                <span class="pulse-green-dot"></span>
                <span>PORTAL MITRA PEDAGANG BUMPES</span>
            </div>
            <h1 class="banner-title" style="font-size: 1.85rem; font-weight: 800; margin: 8px 0 10px; line-height: 1.2;">
                Halo, {{ Auth::user()->nama }}! 👋
            </h1>
            <p class="banner-desc" style="font-size: 0.95rem; line-height: 1.6; opacity: 0.92; margin-bottom: 20px;">
                Selamat datang di portal mandiri lapak Anda (Kios {{ $pedagang->no_kios ?? '-' }} - {{ $pedagang->nama_usaha ?? $pedagang->nama_pedagang }}). Pantau kelancaran iuran harian, cetak kuitansi resmi, dan kelola kios Anda dengan transparan, tertib, dan berkah bersama BUMPes Digital.
            </p>
            <div class="banner-actions">
                <a href="#section-status-iuran" class="btn-banner-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Cek Iuran Hari Ini
                </a>
                <a href="#section-buku-kas" class="btn-banner-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Buku Kas Kios Harian
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
                    <span>Kios: {{ $pedagang->no_kios ?? '-' }} ({{ $pedagang->blok_kios ?? 'Blok -' }})</span>
                </div>
                <div class="glass-pill-item info-pill">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path></svg>
                    <span>{{ $totalTunggakanPedagang > 0 ? 'Ada Tunggakan Berjalan' : 'Iuran Tertib & Lancar' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. WIDGET ISLAMI: WAKTU, JAM DIGITAL, KALENDER HIJRIAH & JADWAL SHOLAT (PERSIS SEPERTI GAMBAR) -->
    <div class="dashboard-prayer-widget" id="dashboard-prayer-widget">
        <!-- Header: Ucapan Salam & Cuaca -->
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
                    <p class="dpw-greeting-sub" id="dpw-greeting-sub">Semoga dagangan berkah & laris manis</p>
                </div>
            </div>
            <div class="dpw-weather-box">
                <div class="dpw-weather-temp" id="dpw-weather-temp">30°C</div>
                <div class="dpw-weather-cond">
                    <span id="dpw-weather-text">Cerah Berawan</span>
                </div>
            </div>
        </div>

        <!-- Body: Jam Digital & Jadwal Sholat Probolinggo -->
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
                    <span class="dpw-crescent">☾</span> <span id="dpw-hijri-text">2 Jumadil Awal 1448 H</span>
                </div>
            </div>

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

    <!-- 3. KARTU APRESIASI MITRA & MUTIARA HIKMAH BERKAH -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- Card Apresiasi Kedisiplinan -->
        <div class="card" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1.5px solid #a7f3d0; border-radius: var(--radius-lg); padding: 20px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #047857; text-transform: uppercase; letter-spacing: 0.5px;">STATUS KEPATUHAN LAPAK</span>
                    @if($totalTunggakanPedagang == 0)
                        <h3 style="font-size: 1.2rem; font-weight: 800; color: #065f46; margin: 6px 0 4px; display: flex; align-items: center; gap: 6px;">
                            <span>⭐</span> Mitra Teladan & Amanah
                        </h3>
                        <p style="font-size: 0.85rem; color: #047857; margin: 0; line-height: 1.5;">
                            Alhamdulillah, tidak ada tunggakan iuran harian. Terima kasih atas kedisiplinan Anda dalam memajukan pasar pesantren!
                        </p>
                    @else
                        <h3 style="font-size: 1.2rem; font-weight: 800; color: #b45309; margin: 6px 0 4px; display: flex; align-items: center; gap: 6px;">
                            <span>⚠️</span> Perhatian Iuran Tertunda
                        </h3>
                        <p style="font-size: 0.85rem; color: #92400e; margin: 0; line-height: 1.5;">
                            Terdapat {{ $jumlahHariMenunggak }} hari iuran yang belum lunas. Silakan hubungi petugas saat penarikan keliling harian.
                        </p>
                    @endif
                </div>
                <div style="background: white; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.06); font-size: 1.4rem; flex-shrink: 0;">
                    {{ $totalTunggakanPedagang == 0 ? '🏆' : '📋' }}
                </div>
            </div>
        </div>

        <!-- Card Mutiara Hikmah Perniagaan -->
        <div class="card" style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #bfdbfe; border-radius: var(--radius-lg); padding: 20px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">MUTIARA PERNIAGAAN BERKAH</span>
            <blockquote style="margin: 8px 0 0; font-size: 0.85rem; color: #1e3a8a; font-style: italic; line-height: 1.5;">
                “Pedagang yang senantiasa jujur dan terpercaya kelak akan dikumpulkan bersama para Nabi, orang-orang benar, dan para syuhada.”
            </blockquote>
            <div style="font-size: 0.75rem; color: #3b82f6; font-weight: 600; margin-top: 6px; text-align: right;">
                — Hadits Riwayat At-Tirmidzi
            </div>
        </div>
    </div>

    <!-- 4. RINGKASAN STATUS IURAN HARI INI & KIOS (Section ID) -->
    <div id="section-status-iuran" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- Card Kios -->
        <div class="card" style="background: var(--surface-container-lowest); border: 1px solid var(--outline-variant); border-radius: var(--radius-lg); padding: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--on-surface-variant); text-transform: uppercase;">Nomor Kios</span>
                <span style="background: rgba(11, 83, 49, 0.1); color: var(--primary); padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">{{ $pedagang->blok_kios ?? 'Blok -' }}</span>
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--on-surface);">
                {{ $pedagang->no_kios ?? 'Kios -' }}
            </div>
            <div style="font-size: 0.8rem; color: var(--on-surface-variant); margin-top: 4px;">
                Usaha: <strong style="color: var(--on-surface);">{{ $pedagang->nama_usaha ?? $pedagang->nama_pedagang }}</strong>
            </div>
        </div>

        <!-- Card Status Hari Ini -->
        <div class="card" style="background: var(--surface-container-lowest); border: 1px solid var(--outline-variant); border-radius: var(--radius-lg); padding: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--on-surface-variant); text-transform: uppercase;">Status Hari Ini</span>
                <span style="font-size: 0.75rem; color: var(--on-surface-variant);">{{ date('d M Y') }}</span>
            </div>
            @if($tagihanHariIni && $tagihanHariIni->status_bayar === 'lunas')
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: #059669;">LUNAS</span>
                    <span style="background: #d1fae5; color: #065f46; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; font-weight: 700;">✓ Terbayar</span>
                </div>
                <div style="font-size: 0.8rem; color: var(--on-surface-variant); margin-top: 4px;">
                    Nominal: Rp {{ number_format($tagihanHariIni->nominal_dibayar, 0, ',', '.') }}
                </div>
            @elseif($tagihanHariIni)
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 1.4rem; font-weight: 800; color: #d97706;">BELUM LUNAS</span>
                    <span style="background: #fef3c7; color: #92400e; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; font-weight: 700;">Menunggu</span>
                </div>
                <div style="font-size: 0.8rem; color: var(--on-surface-variant); margin-top: 4px;">
                    Tagihan: Rp {{ number_format($tagihanHariIni->nominal_tagihan, 0, ',', '.') }}
                </div>
            @else
                <div style="font-size: 1.3rem; font-weight: 700; color: var(--on-surface-variant);">
                    Belum Ditarik
                </div>
                <div style="font-size: 0.8rem; color: var(--on-surface-variant); margin-top: 4px;">
                    Tarif Harian: Rp {{ number_format($pedagang->tarif->nominal_harian ?? 0, 0, ',', '.') }}
                </div>
            @endif
        </div>

        <!-- Card Terbayar Bulan Ini -->
        <div class="card" style="background: var(--surface-container-lowest); border: 1px solid var(--outline-variant); border-radius: var(--radius-lg); padding: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--on-surface-variant); text-transform: uppercase;">Terbayar Bulan Ini</span>
                <span style="font-size: 0.75rem; color: var(--on-surface-variant);">{{ date('F Y') }}</span>
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                Rp {{ number_format($totalDibayarBulanIni, 0, ',', '.') }}
            </div>
            <div style="font-size: 0.8rem; color: var(--on-surface-variant); margin-top: 4px;">
                Kategori: {{ $pedagang->kategori_usaha ?? 'Umum' }}
            </div>
        </div>

        <!-- Card Total Tunggakan -->
        <div class="card" style="background: var(--surface-container-lowest); border: 1px solid var(--outline-variant); border-radius: var(--radius-lg); padding: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--on-surface-variant); text-transform: uppercase;">Total Tunggakan</span>
                @if($totalTunggakanPedagang > 0)
                    <span style="background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">{{ $jumlahHariMenunggak }} Hari</span>
                @else
                    <span style="background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">Lancar</span>
                @endif
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: {{ $totalTunggakanPedagang > 0 ? '#dc2626' : '#059669' }};">
                Rp {{ number_format($totalTunggakanPedagang, 0, ',', '.') }}
            </div>
            <div style="font-size: 0.8rem; color: var(--on-surface-variant); margin-top: 4px;">
                {{ $totalTunggakanPedagang > 0 ? 'Harap diselesaikan ke petugas' : 'Alhamdulillah bebas tunggakan' }}
            </div>
        </div>
    </div>

    <!-- 5. FITUR MENARIK KHUSUS: BUKU KAS HARIAN KIOS MANDIRI (Catatan Penjualan & Pengeluaran Toko) -->
    <div id="section-buku-kas" class="card" style="background: var(--surface-container-lowest); border: 1.5px solid var(--outline-variant); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(11, 83, 49, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    📒
                </div>
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: var(--on-surface);">Buku Kas Mandiri Kios (Catatan Omzet Hari Ini)</h3>
                    <p style="font-size: 0.8rem; color: var(--on-surface-variant); margin: 2px 0 0;">Fitur pembantu khusus pedagang untuk mencatat perkiraan keuntungan harian kios Anda</p>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="resetBukuKas()" style="font-size: 0.75rem;">
                Bersihkan Catatan
            </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 16px;">
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--on-surface); display: block; margin-bottom: 4px;">Total Penjualan / Pemasukan (Rp):</label>
                <input type="number" id="kasPemasukan" class="form-control" placeholder="Contoh: 350000" oninput="hitungLabaHarian()" style="font-size: 0.9rem; font-weight: 600;">
            </div>
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--on-surface); display: block; margin-bottom: 4px;">Pengeluaran / Kulakan / Iuran (Rp):</label>
                <input type="number" id="kasPengeluaran" class="form-control" placeholder="Contoh: 150000" oninput="hitungLabaHarian()" style="font-size: 0.9rem; font-weight: 600;">
            </div>
            <div style="background: var(--surface-container-low); padding: 12px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center;">
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--on-surface-variant); text-transform: uppercase;">Estimasi Keuntungan Bersih:</span>
                <span id="kasLabaBersih" style="font-size: 1.3rem; font-weight: 800; color: #059669; margin-top: 2px;">
                    Rp 0
                </span>
            </div>
        </div>
        <div style="font-size: 0.75rem; color: var(--on-surface-variant); line-height: 1.4;">
            💡 <em>Catatan ini tersimpan secara privat di perangkat Anda untuk memudahkan rekapitulasi harian toko Anda.</em>
        </div>
    </div>

    <!-- 6. RIWAYAT PEMBAYARAN IURAN SAYA BESERTA CETAK BUKTI -->
    <div class="card" style="background: var(--surface-container-lowest); border: 1px solid var(--outline-variant); border-radius: var(--radius-lg); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--outline-variant); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--on-surface);">Riwayat Pembayaran Kios Saya</h3>
                <p style="font-size: 0.8rem; color: var(--on-surface-variant); margin: 2px 0 0;">Daftar pembayaran iuran resmi yang telah tercatat di kasir pasar</p>
            </div>
            <a href="{{ route('riwayat.index') }}" class="btn btn-secondary btn-sm" style="font-size: 0.8rem;">
                Lihat Selengkapnya →
            </a>
        </div>
        <div class="table-responsive">
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Waktu Bayar</th>
                        <th>No. Kuitansi</th>
                        <th>Nominal Dibayar</th>
                        <th>Metode Bayar</th>
                        <th>Petugas Penerima</th>
                        <th style="text-align: center;">Kuitansi Resmi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPembayaran as $tgh)
                        <tr>
                            <td>{{ $tgh->waktu_bayar ? \Carbon\Carbon::parse($tgh->waktu_bayar)->isoFormat('D MMM Y, HH:mm') : \Carbon\Carbon::parse($tgh->tanggal_tagihan)->format('d/m/Y') }}</td>
                            <td><code>{{ $tgh->no_bukti ?? $tgh->no_tagihan ?? '-' }}</code></td>
                            <td style="font-weight: 700; color: #059669;">
                                Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge" style="background: var(--surface-container-high); color: var(--on-surface); text-transform: uppercase;">
                                    {{ $tgh->metode_bayar ?? 'Tunai' }}
                                </span>
                            </td>
                            <td>{{ $tgh->petugas->nama ?? 'Petugas Pasar' }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('riwayat.cetak', $tgh->id) }}" target="_blank" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 4px 12px; display: inline-flex; align-items: center; gap: 4px;" title="Cetak Kuitansi Resmi">
                                    🖨️ Cetak Kuitansi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 36px; color: var(--on-surface-variant);">
                                Belum ada riwayat pembayaran yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- JAVASCRIPT: JAM DIGITAL, KALENDER HIJRIAH, JADWAL SHOLAT & BUKU KAS -->
<script>
    function initPedagangPrayerWidget() {
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

        function updateClockAndGreeting() {
            const now = new Date();
            const h = now.getHours();
            const m = now.getMinutes();
            const s = now.getSeconds();

            hoursEl.textContent = String(h).padStart(2, '0');
            minutesEl.textContent = String(m).padStart(2, '0');
            secondsEl.textContent = String(s).padStart(2, '0');

            if (headerEl) {
                headerEl.classList.remove('theme-pagi', 'theme-siang', 'theme-sore', 'theme-malam');
                if (h >= 4 && h < 11) {
                    headerEl.classList.add('theme-pagi');
                    if (greetingTitle) greetingTitle.textContent = 'Selamat Pagi';
                    if (greetingSub) greetingSub.textContent = 'Bismillah, semoga dagangan hari ini berkah & laris';
                    if (greetingIcon) greetingIcon.innerHTML = '☀️';
                } else if (h >= 11 && h < 15) {
                    headerEl.classList.add('theme-siang');
                    if (greetingTitle) greetingTitle.textContent = 'Selamat Siang';
                    if (greetingSub) greetingSub.textContent = 'Tetap semangat melayani pembeli dengan senyum';
                    if (greetingIcon) greetingIcon.innerHTML = '🌤️';
                } else if (h >= 15 && h < 18) {
                    headerEl.classList.add('theme-sore');
                    if (greetingTitle) greetingTitle.textContent = 'Selamat Sore';
                    if (greetingSub) greetingSub.textContent = 'Waktu ashar berkah, rezeki melimpah ruah';
                    if (greetingIcon) greetingIcon.innerHTML = '🌇';
                } else {
                    headerEl.classList.add('theme-malam');
                    if (greetingTitle) greetingTitle.textContent = 'Selamat Malam';
                    if (greetingSub) greetingSub.textContent = 'Waktu istirahat berkah bersama keluarga tercinta';
                    if (greetingIcon) greetingIcon.innerHTML = '🌙';
                }
            }

            updateActivePrayer(h, m);
        }

        function updateHijriDate() {
            if (!hijriText) return;
            try {
                const now = new Date();
                const formatter = new Intl.DateTimeFormat('id-u-ca-islamic', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const parts = formatter.formatToParts(now);
                let day = '', rawMonth = '', year = '';
                parts.forEach(p => {
                    if (p.type === 'day') day = p.value;
                    if (p.type === 'month') rawMonth = p.value;
                    if (p.type === 'year') year = p.value;
                });

                const hijriMonthNames = [
                    'Muharram', 'Safar', 'Rabiul Awal', 'Rabiul Akhir',
                    'Jumadil Awal', 'Jumadil Akhir', 'Rajab', 'Syaban',
                    'Ramadhan', 'Syawal', 'Dzulqaidah', 'Dzulhijjah'
                ];
                let finalMonth = rawMonth;
                const mIndex = parseInt(rawMonth, 10) - 1;
                if (!isNaN(mIndex) && hijriMonthNames[mIndex]) {
                    finalMonth = hijriMonthNames[mIndex];
                }
                if (day && finalMonth && year) {
                    hijriText.textContent = `${day} ${finalMonth} ${year} H`;
                    return;
                }
            } catch(e) {}
            hijriText.textContent = '2 Jumadil Awal 1448 H';
        }

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

    // Interactive Mini Buku Kas Kios
    function hitungLabaHarian() {
        const inVal = parseFloat(document.getElementById('kasPemasukan').value) || 0;
        const outVal = parseFloat(document.getElementById('kasPengeluaran').value) || 0;
        const laba = inVal - outVal;
        const labaEl = document.getElementById('kasLabaBersih');

        if (labaEl) {
            labaEl.textContent = 'Rp ' + laba.toLocaleString('id-ID');
            labaEl.style.color = laba >= 0 ? '#059669' : '#dc2626';
        }

        localStorage.setItem('bukukas_in', inVal);
        localStorage.setItem('bukukas_out', outVal);
    }

    function resetBukuKas() {
        document.getElementById('kasPemasukan').value = '';
        document.getElementById('kasPengeluaran').value = '';
        document.getElementById('kasLabaBersih').textContent = 'Rp 0';
        document.getElementById('kasLabaBersih').style.color = '#059669';
        localStorage.removeItem('bukukas_in');
        localStorage.removeItem('bukukas_out');
    }

    document.addEventListener('DOMContentLoaded', function() {
        initPedagangPrayerWidget();

        // Restore Buku Kas if exists
        const savedIn = localStorage.getItem('bukukas_in');
        const savedOut = localStorage.getItem('bukukas_out');
        if (savedIn) document.getElementById('kasPemasukan').value = savedIn;
        if (savedOut) document.getElementById('kasPengeluaran').value = savedOut;
        if (savedIn || savedOut) hitungLabaHarian();
    });
</script>
@endsection
