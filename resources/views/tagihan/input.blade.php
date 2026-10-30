@extends('layouts.app')

@section('title', 'Transaksi Pembayaran Iuran')

@section('head')
<style>
    .transaction-row {
        transition: background-color 0.15s ease;
    }
    .transaction-row:hover {
        background-color: #f8fafc !important;
    }
    .transaction-row td {
        border-bottom: 1px solid #e2e8f0;
    }
    .transaction-row:last-child td {
        border-bottom: none;
    }
    [data-theme="dark"] .transaction-row:hover {
        background-color: #1e293b !important;
    }
    [data-theme="dark"] .transaction-row td {
        border-bottom-color: #334155;
    }

    /* Refined Stat Cards & Typography */
    .tagihan-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 84px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .tagihan-stat-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .tagihan-stat-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        line-height: 1.2;
    }
    .tagihan-stat-value {
        font-size: 1.15rem !important;
        font-weight: 700 !important;
        margin: 3px 0 2px 0 !important;
        line-height: 1.25;
        letter-spacing: -0.01em;
        font-feature-settings: "tnum";
    }
    .tagihan-stat-sub {
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.2;
    }

    /* Single-line Status Badges */
    .status-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        white-space: nowrap !important;
        line-height: 1.2;
        letter-spacing: 0.01em;
        text-align: center;
    }
    .status-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .status-badge-lunas {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .status-badge-lunas .status-badge-dot {
        background: #10b981;
    }
    .status-badge-sebagian {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .status-badge-sebagian .status-badge-dot {
        background: #f59e0b;
    }
    .status-badge-belum {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .status-badge-belum .status-badge-dot {
        background: #ef4444;
    }
    .status-badge-batal,
    .status-badge-libur {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .status-badge-batal .status-badge-dot {
        background: #94a3b8;
    }
    .status-badge-libur .status-badge-dot {
        background: #64748b;
    }

    /* Dark Mode Adaptations */
    [data-theme="dark"] .tagihan-stat-card {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    [data-theme="dark"] .status-badge-lunas {
        background: rgba(16, 185, 129, 0.2) !important;
        color: #34d399 !important;
        border-color: rgba(16, 185, 129, 0.4) !important;
    }
    [data-theme="dark"] .status-badge-sebagian {
        background: rgba(245, 158, 11, 0.2) !important;
        color: #fbbf24 !important;
        border-color: rgba(245, 158, 11, 0.4) !important;
    }
    [data-theme="dark"] .status-badge-belum {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #f87171 !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
    }
    [data-theme="dark"] .status-badge-batal,
    [data-theme="dark"] .status-badge-libur {
        background: #334155 !important;
        color: #cbd5e1 !important;
        border-color: #475569 !important;
    }

    /* Responsive adjustments for mobile & small screens */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.18rem !important;
        }
        .page-subtitle {
            font-size: 0.75rem !important;
        }
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 8px !important;
        }
        .stats-grid > :nth-child(3) {
            grid-column: 1 / -1 !important;
        }
        .tagihan-stat-card {
            padding: 10px 12px !important;
            border-radius: 10px !important;
            min-height: 72px !important;
        }
        .tagihan-stat-label {
            font-size: 0.65rem !important;
        }
        .tagihan-stat-value {
            font-size: 1.05rem !important;
            margin: 2px 0 !important;
        }
        .tagihan-stat-sub {
            font-size: 0.68rem !important;
        }
        .tab-btn {
            padding: 6px 12px !important;
            font-size: 0.78rem !important;
        }
        .data-table th, .data-table td {
            padding: 8px 10px !important;
        }
        .status-badge-pill {
            font-size: 0.68rem !important;
            padding: 2px 7px !important;
        }
    }
</style>
@endsection

@section('content')
@php
    $carbonDate = \Carbon\Carbon::parse($tanggal);
    $daysIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $monthsIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    
    $namaHari = $daysIndo[$carbonDate->format('l')] ?? $carbonDate->format('l');
    $namaBulan = $monthsIndo[(int)$carbonDate->format('m')] ?? $carbonDate->format('F');
    $tanggalLengkapIndo = $namaHari . ', ' . $carbonDate->format('d') . ' ' . $namaBulan . ' ' . $carbonDate->format('Y');

    $tagihanLunas = $tagihans->where('status_bayar', 'lunas');
    $tagihanBelum = $tagihans->whereIn('status_bayar', ['belum_bayar', 'bayar_sebagian']);
    $tagihanBatal = $tagihans->where('status_bayar', 'dibatalkan');
@endphp

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h2 class="page-title">Transaksi Pembayaran Iuran</h2>
        <p class="page-subtitle">
            Pencatatan & verifikasi pembayaran iuran harian pedagang pada {{ $tanggalLengkapIndo }}
        </p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <!-- Live Clock Card Widget (Terkalibrasi Mode Terang & Gelap / Malam) -->
        <div class="live-clock-badge">
            <svg class="live-clock-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"></circle>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"></path>
            </svg>
            <div class="live-clock-text">
                <span class="live-admin-time">00:00:00</span>
                <span class="live-admin-tz">WIB</span>
            </div>
        </div>

        <a href="{{ route('export.tagihan', request()->query()) }}" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
        <button class="btn btn-secondary" onclick="openGenerateModal()" style="border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Generate Batch Hari Ini
        </button>
        <button class="btn btn-primary" onclick="openTambahIuranModal()" style="border-radius: 8px; font-weight: 600;">
            + Input Pembayaran Manual
        </button>
    </div>
</div>

{{-- KALENDER OPERASIONAL BANNER NOTIFIKASI --}}
@if ($rule)
    @if ($isBebasIuran)
        <div style="background: #f0fdf4; border: 1px solid #86efac; border-left: 5px solid #16a34a; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: flex-start; justify-content: space-between; gap: 14px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #dcfce7; color: #16a34a; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.95rem; color: #166534; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span>Hari Bebas Iuran — {{ $rule->kategori_label }}</span>
                        <span style="font-size: 0.725rem; background: #bbf7d0; color: #14532d; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Kebijakan: Bebas Iuran</span>
                    </div>
                    <div style="font-size: 0.85rem; color: #15803d; margin-top: 4px;">
                        <strong>Keterangan:</strong> {{ $rule->alasan }}
                    </div>
                    <div style="font-size: 0.775rem; color: #166534; margin-top: 4px; opacity: 0.9; display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Sistem tidak menerbitkan tagihan harian baru dan tidak menambah kewajiban/tunggakan pedagang pada tanggal ini.</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('kalender.index') }}" class="btn btn-sm btn-secondary" style="border-radius: 6px; font-size: 0.75rem; white-space: nowrap; flex-shrink: 0;">
                Kelola Kalender &rarr;
            </a>
        </div>
    @elseif ($isPenagihanDiliburkan)
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-left: 5px solid #d97706; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: flex-start; justify-content: space-between; gap: 14px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #fef3c7; color: #d97706; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="10" y1="15" x2="10" y2="9"></line>
                        <line x1="14" y1="15" x2="14" y2="9"></line>
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.95rem; color: #92400e; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span>Penagihan Diliburkan / Tertunda — {{ $rule->kategori_label }}</span>
                        <span style="font-size: 0.725rem; background: #fed7aa; color: #9a3412; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Kebijakan: Kewajiban Tetap Berlaku</span>
                    </div>
                    <div style="font-size: 0.85rem; color: #b45309; margin-top: 4px;">
                        <strong>Keterangan:</strong> {{ $rule->alasan }}
                    </div>
                    <div style="font-size: 0.775rem; color: #92400e; margin-top: 4px; opacity: 0.9; display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Petugas tidak melakukan penagihan lapangan, namun kewajiban iuran pedagang tetap tercatat sebagai tagihan sah dan dapat dilunasi saat penagihan kembali beroperasi.</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('kalender.index') }}" class="btn btn-sm btn-secondary" style="border-radius: 6px; font-size: 0.75rem; white-space: nowrap; flex-shrink: 0;">
                Kelola Kalender &rarr;
            </a>
        </div>
    @endif
@endif

<!-- FILTER & CONTROL BAR -->
<div class="card" style="padding: 16px; margin-bottom: 20px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <form action="{{ route('tagihan.input') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 180px;">
            <label for="tanggal" class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Tanggal Transaksi</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}" style="border-radius: 8px; min-height: 40px;">
        </div>

        <div style="flex: 2; min-width: 220px;">
            <label for="search" class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Cari Pedagang</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Ketik Nama Pedagang atau Nomor Kios..." value="{{ request('search') }}" style="border-radius: 8px; min-height: 40px;">
        </div>

        <div style="flex: 1; min-width: 160px;">
            <label for="status_bayar" class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Status Pembayaran</label>
            <select name="status_bayar" id="status_bayar" class="form-control" style="border-radius: 8px; min-height: 40px;">
                <option value="">Semua Status</option>
                <option value="lunas" {{ request('status_bayar') == 'lunas' ? 'selected' : '' }}>Sudah Bayar (Lunas)</option>
                <option value="bayar_sebagian" {{ request('status_bayar') == 'bayar_sebagian' ? 'selected' : '' }}>Bayar Sebagian</option>
                <option value="belum_bayar" {{ request('status_bayar') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label for="blok_kios" class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Blok Kios</label>
            <select name="blok_kios" id="blok_kios" class="form-control" style="border-radius: 8px; min-height: 40px;">
                <option value="">Semua Blok</option>
                @foreach($blokList as $blok)
                    <option value="{{ $blok }}" {{ request('blok_kios') == $blok ? 'selected' : '' }}>{{ $blok }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 18px; border-radius: 8px; font-weight: 600;">Terapkan Filter</button>
            <a href="{{ route('tagihan.input') }}" class="btn btn-secondary" style="height: 40px; padding: 0 14px; border-radius: 8px; display: inline-flex; align-items: center; font-weight: 600;" title="Kembali ke Hari Ini">Hari Ini</a>
        </div>
    </form>
</div>

<!-- SUMMARY STATS CARDS -->
<div class="stats-grid">
    <!-- Card Total Target -->
    <div class="stat-card tagihan-stat-card" style="border-left: 4px solid #3b82f6;">
        <div class="stat-label tagihan-stat-label" style="color: #64748b;">TOTAL TARGET TAGIHAN</div>
        <div class="stat-value numeric-display tagihan-stat-value" style="color: #0f172a;">Rp {{ number_format($tagihans->where('status_bayar', '!=', 'dibatalkan')->sum('nominal_tagihan'), 0, ',', '.') }}</div>
        <div class="stat-sub tagihan-stat-sub" style="color: #64748b;">{{ $tagihans->where('status_bayar', '!=', 'dibatalkan')->count() }} Tagihan Aktif</div>
    </div>

    <!-- Card Sudah Bayar -->
    <div class="stat-card tagihan-stat-card" style="border-left: 4px solid #10b981;">
        <div class="stat-label tagihan-stat-label" style="color: #047857;">TOTAL TERKUMPUL (SUDAH BAYAR)</div>
        <div class="stat-value numeric-display tagihan-stat-value" style="color: #10b981;">Rp {{ number_format($tagihans->sum('nominal_dibayar'), 0, ',', '.') }}</div>
        <div class="stat-sub tagihan-stat-sub" style="color: #047857; font-weight: 600; display: flex; align-items: center; gap: 4px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ $tagihanLunas->count() }} Pedagang Lunas Hari Ini</span>
        </div>
    </div>

    <!-- Card Belum Bayar -->
    <div class="stat-card tagihan-stat-card" style="border-left: 4px solid #ef4444;">
        <div class="stat-label tagihan-stat-label" style="color: #b91c1c;">BELUM TERBAYAR (MENUNGGAK)</div>
        <div class="stat-value numeric-display tagihan-stat-value" style="color: #ef4444;">Rp {{ number_format($tagihans->where('status_bayar', '!=', 'dibatalkan')->sum('nominal_tagihan') - $tagihans->sum('nominal_dibayar'), 0, ',', '.') }}</div>
        <div class="stat-sub tagihan-stat-sub" style="color: #b91c1c; font-weight: 600; display: flex; align-items: center; gap: 4px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span>{{ $tagihanBelum->count() }} Pedagang Belum Bayar</span>
        </div>
    </div>
</div>

<!-- CATEGORY TABS -->
<div style="display: flex; gap: 8px; margin-top: 10px; margin-bottom: 14px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; flex-wrap: wrap;">
    <button type="button" class="tab-btn active" id="tab-all" onclick="filterTab('all')" style="padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; border: none; background: #2563eb; color: #ffffff; cursor: pointer; transition: all 0.2s;">
        Semua Transaksi ({{ $tagihans->count() }})
    </button>
    <button type="button" class="tab-btn" id="tab-lunas" onclick="filterTab('lunas')" style="padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; border: none; background: #f1f5f9; color: #475569; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>Sudah Bayar ({{ $tagihanLunas->count() }})</span>
    </button>
    <button type="button" class="tab-btn" id="tab-belum" onclick="filterTab('belum')" style="padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; border: none; background: #f1f5f9; color: #475569; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>Belum Bayar ({{ $tagihanBelum->count() }})</span>
    </button>
</div>

<!-- TRANSACTIONS TABLE -->
<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div class="table-responsive">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1; text-transform: uppercase; font-size: 0.72rem; color: #334155; letter-spacing: 0.05em; font-weight: 700;">
                    <th style="padding: 10px 14px; text-align: left; width: 240px; white-space: nowrap;">Kios & Nama Pedagang</th>
                    <th style="padding: 10px 14px; text-align: left; white-space: nowrap;">Rincian Iuran & Keterangan</th>
                    <th style="padding: 10px 14px; text-align: left; width: 160px; white-space: nowrap;">Nominal Tagihan</th>
                    <th style="padding: 10px 14px; text-align: center; width: 130px; white-space: nowrap;">Status</th>
                    <th style="padding: 10px 14px; text-align: right; width: 150px; white-space: nowrap;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tagihans as $tgh)
                    @php
                        $isLunas = $tgh->status_bayar === 'lunas';
                        $isSebagian = $tgh->status_bayar === 'bayar_sebagian';
                        $isBatal = $tgh->status_bayar === 'dibatalkan';
                        $isTidakBerdagang = str_contains($tgh->catatan ?? '', '[TIDAK BERDAGANG]');
                        
                        $rowStatusClass = $isLunas ? 'row-lunas' : ($isSebagian ? 'row-sebagian' : ($isBatal ? 'row-batal' : 'row-belum'));
                    @endphp
                    <tr class="transaction-row {{ $rowStatusClass }}" style="border-bottom: 1px solid #e2e8f0; transition: background 0.15s ease; {{ $isBatal ? 'background: #f8fafc; opacity: 0.8;' : '' }}">
                        <!-- Kios & Nama Pedagang -->
                        <td style="padding: 10px 14px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; font-size: 0.72rem; padding: 3px 6px; border-radius: 6px; white-space: nowrap; font-family: monospace;">
                                    {{ $tgh->pedagang->no_kios ?? '-' }}
                                </span>
                                <div style="min-width: 0;">
                                    @if ($tgh->pedagang)
                                        <a href="{{ route('pedagang.show', $tgh->pedagang_id) }}" style="font-weight: 600; color: #0f172a; text-decoration: none; font-size: 0.84rem; display: block; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">
                                            {{ $tgh->pedagang->nama_pedagang }}
                                        </a>
                                        <div style="font-size: 0.7rem; color: #64748b; margin-top: 1px; white-space: nowrap;">
                                            {{ $tgh->pedagang->nama_usaha ?? 'Lapak Pasar' }} <span style="color: #cbd5e1;">•</span> <strong style="color: #475569;">{{ str_starts_with($tgh->pedagang->blok_kios ?? '', 'Blok') ? ($tgh->pedagang->blok_kios ?? '-') : 'Blok ' . ($tgh->pedagang->blok_kios ?? '-') }}</strong>
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic; font-size: 0.8rem;">Data pedagang terhapus</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Rincian Iuran & Keterangan -->
                        <td style="padding: 10px 14px; vertical-align: middle;">
                            <div style="font-size: 0.8rem; font-weight: 600; color: #0f172a; line-height: 1.2;">
                                {{ $tgh->jenis_iuran ?? 'Iuran Harian Pasar' }}
                            </div>
                            @if ($isLunas)
                                <div style="font-size: 0.7rem; color: #059669; font-weight: 500; margin-top: 2px; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Diterima sah</span>
                                    @if($tgh->waktu_bayar)
                                        <span style="color: #64748b;">({{ $tgh->waktu_bayar->format('H:i') }} WIB)</span>
                                    @endif
                                </div>
                            @elseif ($isSebagian)
                                <div style="font-size: 0.7rem; color: #d97706; font-weight: 500; margin-top: 2px; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>Terbayar Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}</span>
                                </div>
                            @elseif ($isBatal)
                                <div style="font-size: 0.7rem; color: #64748b; font-weight: 500; margin-top: 2px; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                    </svg>
                                    <span>{{ $tgh->catatan ?? 'Dibatalkan / Bebas Iuran' }}</span>
                                </div>
                            @elseif ($isTidakBerdagang)
                                <div style="font-size: 0.7rem; color: #64748b; font-weight: 500; margin-top: 2px; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                    </svg>
                                    <span>{{ $tgh->catatan ?? 'Lapak Libur / Tutup' }}</span>
                                </div>
                            @else
                                <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 500; margin-top: 2px; white-space: nowrap;">
                                    Menunggu setoran harian
                                </div>
                            @endif
                        </td>

                        <!-- Nominal Tagihan & Sisa -->
                        <td style="padding: 10px 14px; vertical-align: middle; white-space: nowrap;" class="numeric-display">
                            <div style="font-size: 0.85rem; font-weight: 700; color: #0f172a; line-height: 1.2;">
                                Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}
                            </div>
                            @if ($isLunas)
                                <div style="font-size: 0.7rem; font-weight: 600; color: #10b981; margin-top: 1px; display: flex; align-items: center; gap: 3px;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Lunas</span>
                                </div>
                            @elseif ($isSebagian)
                                <div style="font-size: 0.7rem; font-weight: 600; color: #d97706; margin-top: 1px;">
                                    Sisa: Rp {{ number_format($tgh->nominal_tagihan - $tgh->nominal_dibayar, 0, ',', '.') }}
                                </div>
                            @elseif ($isBatal)
                                <div style="font-size: 0.7rem; font-weight: 600; color: #64748b; margin-top: 1px;">
                                    Rp 0 (Bebas)
                                </div>
                            @elseif ($isTidakBerdagang)
                                <div style="font-size: 0.7rem; font-weight: 600; color: #64748b; margin-top: 1px;">
                                    Rp 0 (Libur)
                                </div>
                            @else
                                <div style="font-size: 0.7rem; font-weight: 600; color: #dc2626; margin-top: 1px;">
                                    Sisa: Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}
                                </div>
                            @endif
                        </td>

                        <!-- Status Bayar Badge (Selalu Satu Baris dengan nowrap) -->
                        <td style="padding: 10px 14px; vertical-align: middle; text-align: center; white-space: nowrap;">
                            @if ($isLunas)
                                <span class="status-badge-pill status-badge-lunas">
                                    <span class="status-badge-dot"></span>Lunas
                                </span>
                            @elseif ($isSebagian)
                                <span class="status-badge-pill status-badge-sebagian">
                                    <span class="status-badge-dot"></span>Sebagian
                                </span>
                            @elseif ($isBatal)
                                <span class="status-badge-pill status-badge-batal">
                                    <span class="status-badge-dot"></span>Bebas / Batal
                                </span>
                            @elseif ($isTidakBerdagang)
                                <span class="status-badge-pill status-badge-libur">
                                    <span class="status-badge-dot"></span>Libur
                                </span>
                            @else
                                <span class="status-badge-pill status-badge-belum">
                                    <span class="status-badge-dot"></span>Belum Bayar
                                </span>
                            @endif
                        </td>

                        <!-- Aksi Pembayaran -->
                        <td style="padding: 10px 14px; vertical-align: middle; text-align: right; white-space: nowrap;">
                            <div style="display: flex; gap: 5px; justify-content: flex-end; align-items: center;">
                                @if ($isLunas)
                                    <button class="btn btn-secondary btn-sm" onclick="openBayarModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}', '{{ $tgh->nominal_tagihan }}', '{{ max(0, $tgh->nominal_tagihan - $tgh->nominal_dibayar) }}', '{{ $tgh->nominal_dibayar }}', 'lunas')" style="border-radius: 6px; font-weight: 600; padding: 4px 10px; font-size: 0.72rem; color: #0284c7; border: 1px solid #bae6fd; background: #f0f9ff; display: inline-flex; align-items: center; gap: 3px;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                @elseif ($isBatal)
                                    <span style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; padding-right: 6px;">
                                        Bebas Iuran
                                    </span>
                                @else
                                    <button class="btn btn-primary btn-sm" onclick="openBayarModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}', '{{ $tgh->nominal_tagihan }}', '{{ max(0, $tgh->nominal_tagihan - $tgh->nominal_dibayar) }}', '{{ $tgh->nominal_dibayar }}', '{{ $isSebagian ? 'bayar_sebagian' : 'belum_bayar' }}')" style="border-radius: 6px; font-weight: 600; padding: 4px 10px; font-size: 0.72rem; background: #2563eb; border-color: #2563eb; display: inline-flex; align-items: center; gap: 3px;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span>Bayar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openTidakBerdagangModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}')" style="border-radius: 6px; padding: 4px 7px; font-size: 0.7rem; color: #64748b; border: 1px solid #cbd5e1; background: #ffffff;" title="Catat Lapak Tutup / Libur">
                                        Tutup
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 48px 24px;">
                            <div style="font-size: 1.125rem; font-weight: 700; margin-bottom: 6px; color: #1e293b;">Belum Ada Transaksi Pembayaran Untuk Tanggal Ini</div>
                            <p style="font-size: 0.875rem; margin-bottom: 20px; color: #64748b;">Silakan klik tombol <strong>Generate Batch Hari Ini</strong> untuk meng-generate tagihan harian pedagang secara otomatis.</p>
                            <button class="btn btn-primary" onclick="openGenerateModal()" style="border-radius: 8px; padding: 10px 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Generate Tagihan Sekarang</span>
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH IURAN MANUAL -->
<div class="modal-overlay" id="modal-tambah-iuran">
    <div class="modal-container" style="max-width: 480px; border-radius: 12px;">
        <div class="modal-header">
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span>Input Pembayaran Transaksi</span>
            </h3>
            <button class="modal-close" onclick="closeTambahIuranModal()">&times;</button>
        </div>
        <form action="{{ route('tagihan.store-manual') }}" method="POST">
            @csrf
            <!-- removed hidden status_pembayaran -->
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Pilih Pedagang *</label>
                    <select name="pedagang_id" class="form-select" required style="border-radius: 8px; min-height: 42px;">
                        @foreach($pedagangs as $pdg)
                            <option value="{{ $pdg->id }}">{{ $pdg->no_kios }} - {{ $pdg->nama_pedagang }} ({{ $pdg->nama_usaha }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Tanggal Transaksi *</label>
                    <input type="date" name="tanggal_tagihan" class="form-input" required value="{{ $tanggal }}" style="border-radius: 8px;">
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Jenis Iuran *</label>
                    <select name="jenis_iuran" class="form-select" required style="border-radius: 8px; min-height: 42px;">
                        <option value="Iuran Harian Pasar">Iuran Harian Pasar</option>
                        <option value="Iuran Kebersihan">Iuran Kebersihan</option>
                        <option value="Iuran Keamanan">Iuran Keamanan</option>
                        <option value="Retribusi Listrik & Air">Retribusi Listrik & Air</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Nominal Dibayar (Rp) *</label>
                    <input type="number" name="nominal_tagihan" class="form-input" required min="0" value="5000" step="500" style="border-radius: 8px; font-weight: 800; color: #047857; font-size: 1.125rem;">
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Status Pembayaran *</label>
                    <select name="status_pembayaran" class="form-select" required style="border-radius: 8px; min-height: 42px;">
                        <option value="lunas">SUDAH BAYAR</option>
                        <option value="belum_bayar">BELUM BAYAR</option>
                        <option value="bayar_sebagian">CICIL</option>
                    </select>
                    <!-- Default method kept for database integrity -->
                    <input type="hidden" name="metode_bayar" value="tunai">
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Catatan / Keterangan Transaksi</label>
                    <input type="text" name="catatan" class="form-input" placeholder="Opsional (Catatan pembayaran)" style="border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeTambahIuranModal()" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; background: #047857; border-color: #047857; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL GENERATE BATCH -->
<div class="modal-overlay" id="modal-generate">
    <div class="modal-container" style="max-width: 420px; border-radius: 12px;">
        <div class="modal-header">
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Generate Tagihan Batch</span>
            </h3>
            <button class="modal-close" onclick="closeGenerateModal()">&times;</button>
        </div>
        <form action="{{ route('tagihan.generate') }}" method="POST">
            @csrf
            <div class="modal-body">
                <p style="font-size: 0.875rem; color: #475569; margin-bottom: 16px;">
                    Sistem akan membuat tagihan iuran harian secara otomatis untuk seluruh pedagang bertatus <strong>Aktif</strong> pada tanggal target yang dipilih:
                </p>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 700;">Tanggal Target Generate *</label>
                    <input type="date" name="tanggal_generate" class="form-input" required value="{{ $tanggal }}" style="border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeGenerateModal()" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Proses Generate</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL QUICK BAYAR -->
<div class="modal-overlay" id="modal-quick-bayar">
    <div class="modal-container" style="max-width: 460px; border-radius: 12px;">
        <div class="modal-header">
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span>Input Transaksi Pembayaran</span>
            </h3>
            <button class="modal-close" onclick="closeBayarModal()">&times;</button>
        </div>
        <form id="quick-bayar-form" method="POST">
            @csrf
            <input type="hidden" name="set_lunas" value="1">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Nama Pedagang</label>
                    <input type="text" id="quick-bayar-pedagang" class="form-input" readonly style="background: #f1f5f9; border-radius: 8px; font-weight: 700; color: #1e293b;">
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Nominal Pembayaran (Rp) *</label>
                    <input type="number" name="nominal_dibayar" id="quick-bayar-nominal" class="form-input" required min="0" step="500" style="border-radius: 8px; font-weight: 800; color: #047857; font-size: 1.125rem;">
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">Masukkan nominal pembayaran iuran harian pedagang (misal: 5000).</p>
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Metode Pembayaran *</label>
                    <select name="metode_bayar" class="form-select" required style="border-radius: 8px; min-height: 42px;">
                        <option value="tunai">Tunai / Cash</option>
                        <option value="qris">QRIS (Digital)</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Status Pembayaran *</label>
                    <select name="status_pembayaran" class="form-select" required style="border-radius: 8px; min-height: 42px;">
                        <option value="lunas">SUDAH BAYAR</option>
                        <option value="belum_bayar">BELUM BAYAR</option>
                        <option value="bayar_sebagian">CICIL</option>
                    </select>
                </div>
                <div class="form-group" id="quick-bayar-mode-group" style="margin-bottom: 14px; display: none;">
                    <label class="form-label" style="font-weight: 700;">Tipe Input Transaksi</label>
                    <select name="mode_cicilan" id="quick-bayar-mode" class="form-select" style="border-radius: 8px; min-height: 42px;" onchange="handleModeCicilanChange()">
                        <option value="tambah">Tambah Cicilan Baru (Diakumulasikan)</option>
                        <option value="koreksi">Koreksi Total Nominal Pembayaran</option>
                    </select>
                    <p id="quick-bayar-hint" style="font-size: 0.75rem; color: #0284c7; margin-top: 4px; font-weight: 600;"></p>
                </div>
                <!-- removed Nomor Struk input to use automatic generation -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Catatan Transaksi</label>
                    <input type="text" name="catatan" class="form-input" placeholder="Opsional (Catatan tambahan)" style="border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeBayarModal()" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 700; background: #047857; border-color: #047857; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KETERANGAN TIDAK BERDAGANG -->
<div class="modal-overlay" id="modal-tidak-berdagang">
    <div class="modal-container" style="max-width: 460px; border-radius: 12px;">
        <div class="modal-header">
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                </svg>
                <span>Keterangan Tidak Berdagang</span>
            </h3>
            <button class="modal-close" onclick="closeTidakBerdagangModal()">&times;</button>
        </div>
        <form id="tidak-berdagang-form" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Nama Pedagang</label>
                    <input type="text" id="tidak-berdagang-pedagang" class="form-input" readonly style="background: #f1f5f9; border-radius: 8px; font-weight: 700; color: #1e293b;">
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Alasan Tidak Berdagang *</label>
                    <select name="alasan" id="alasan-select" class="form-select" required style="border-radius: 8px; min-height: 42px;" onchange="toggleCustomAlasan(this)">
                        <option value="Toko Tutup / Libur Rutin">Toko Tutup / Libur Rutin</option>
                        <option value="Pedagang Sakit / Halangan Kesehatan">Pedagang Sakit / Halangan Kesehatan</option>
                        <option value="Izin / Ada Keperluan Keluarga">Izin / Ada Keperluan Keluarga</option>
                        <option value="Lapak Kosong / Belum Ditempati">Lapak Kosong / Belum Ditempati</option>
                        <option value="Lainnya">Lainnya (Ketik Sendiri)</option>
                    </select>
                </div>
                <div class="form-group" id="custom-alasan-group" style="margin-bottom: 14px; display: none;">
                    <label class="form-label" style="font-weight: 700;">Tulis Alasan Lainnya *</label>
                    <input type="text" name="catatan" id="custom-alasan-input" class="form-input" placeholder="Contoh: Barang dagangan habis, perbaikan kios" style="border-radius: 8px;">
                </div>
                <div class="form-group" style="margin-bottom: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
                    <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; margin-bottom: 0;">
                        <input type="checkbox" name="bebas_iuran" value="1" checked style="margin-top: 3px; accent-color: #2563eb; width: 16px; height: 16px;">
                        <div>
                            <strong style="font-size: 0.8125rem; color: #0f172a; display: block;">Bebaskan Iuran Hari Ini (Rp 0)</strong>
                            <span style="font-size: 0.75rem; color: #64748b; line-height: 1.3; display: block;">Jika dicentang, tagihan pedagang hari ini dinolkan dan tidak dihitung sebagai tunggakan. Jika tidak dicentang, kewajiban iuran tetap menjadi tunggakan yang sah.</span>
                        </div>
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeTidakBerdagangModal()" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; background: #64748b; border-color: #64748b;">Simpan Status Tidak Berdagang</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openTambahIuranModal() {
        document.getElementById('modal-tambah-iuran').classList.add('active');
    }
    function closeTambahIuranModal() {
        document.getElementById('modal-tambah-iuran').classList.remove('active');
    }

    function openGenerateModal() {
        document.getElementById('modal-generate').classList.add('active');
    }
    function closeGenerateModal() {
        document.getElementById('modal-generate').classList.remove('active');
    }

    let currentNominalSebelumnya = 0;
    let currentSisaNominal = 0;
    let currentNominalTagihan = 0;

    function openBayarModal(tagihanId, namaPedagang, nominalTagihan, sisaNominal, nominalSebelumnya, currentStatus) {
        document.getElementById('quick-bayar-form').action = '/tagihan/' + tagihanId + '/bayar';
        document.getElementById('quick-bayar-pedagang').value = namaPedagang;
        
        currentNominalSebelumnya = parseFloat(nominalSebelumnya) || 0;
        currentSisaNominal = parseFloat(sisaNominal) || 0;
        currentNominalTagihan = parseFloat(nominalTagihan) || 0;

        const modeGroup = document.getElementById('quick-bayar-mode-group');
        const modeSelect = document.getElementById('quick-bayar-mode');
        const hintEl = document.getElementById('quick-bayar-hint');
        const statusSelect = document.querySelector('#quick-bayar-form select[name="status_pembayaran"]');

        if (currentNominalSebelumnya > 0 && currentStatus !== 'lunas') {
            modeGroup.style.display = 'block';
            modeSelect.value = 'tambah';
            hintEl.innerText = 'ℹ Tagihan ini telah terbayar Rp ' + currentNominalSebelumnya.toLocaleString('id-ID') + ' dari total Rp ' + currentNominalTagihan.toLocaleString('id-ID') + ' (Sisa: Rp ' + currentSisaNominal.toLocaleString('id-ID') + ').';
            document.getElementById('quick-bayar-nominal').value = currentSisaNominal > 0 ? currentSisaNominal : currentNominalTagihan;
            if (statusSelect) statusSelect.value = currentSisaNominal > 0 ? 'bayar_sebagian' : 'lunas';
        } else if (currentStatus === 'lunas') {
            modeGroup.style.display = 'block';
            modeSelect.value = 'koreksi';
            hintEl.innerText = 'ℹ Tagihan ini sudah lunas Rp ' + currentNominalSebelumnya.toLocaleString('id-ID') + '. Pilih "Koreksi" jika ingin meralat nominal total.';
            document.getElementById('quick-bayar-nominal').value = currentNominalSebelumnya;
            if (statusSelect) statusSelect.value = 'lunas';
        } else {
            modeGroup.style.display = 'none';
            modeSelect.value = 'tambah';
            document.getElementById('quick-bayar-nominal').value = currentNominalTagihan > 0 ? currentNominalTagihan : 5000;
            if (statusSelect) statusSelect.value = 'lunas';
        }

        document.getElementById('modal-quick-bayar').classList.add('active');
    }

    function handleModeCicilanChange() {
        const modeSelect = document.getElementById('quick-bayar-mode');
        const nominalInput = document.getElementById('quick-bayar-nominal');
        const statusSelect = document.querySelector('#quick-bayar-form select[name="status_pembayaran"]');
        if (modeSelect.value === 'tambah') {
            nominalInput.value = currentSisaNominal > 0 ? currentSisaNominal : currentNominalTagihan;
            if (statusSelect) statusSelect.value = 'bayar_sebagian';
        } else if (modeSelect.value === 'koreksi') {
            nominalInput.value = currentNominalSebelumnya;
        }
    }
    function closeBayarModal() {
        document.getElementById('modal-quick-bayar').classList.remove('active');
    }

    function openTidakBerdagangModal(tagihanId, namaPedagang) {
        document.getElementById('tidak-berdagang-form').action = '/tagihan/' + tagihanId + '/tidak-ada';
        document.getElementById('tidak-berdagang-pedagang').value = namaPedagang;
        document.getElementById('modal-tidak-berdagang').classList.add('active');
    }
    function closeTidakBerdagangModal() {
        document.getElementById('modal-tidak-berdagang').classList.remove('active');
    }

    function toggleCustomAlasan(selectEl) {
        const customGroup = document.getElementById('custom-alasan-group');
        const customInput = document.getElementById('custom-alasan-input');
        if (selectEl.value === 'Lainnya') {
            customGroup.style.display = 'block';
            customInput.required = true;
        } else {
            customGroup.style.display = 'none';
            customInput.required = false;
        }
    }

    // Client-side Tab Filtering for fast switching
    function filterTab(tabName) {
        const tabBtns = document.querySelectorAll('.tab-btn');
        tabBtns.forEach(btn => {
            btn.style.background = '#f1f5f9';
            btn.style.color = '#475569';
        });

        const activeBtn = document.getElementById('tab-' + tabName);
        if (activeBtn) {
            activeBtn.style.background = '#2563eb';
            activeBtn.style.color = '#ffffff';
        }

        const rows = document.querySelectorAll('.transaction-row');
        rows.forEach(row => {
            if (tabName === 'all') {
                row.style.display = '';
            } else if (tabName === 'lunas') {
                if (row.classList.contains('row-lunas')) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            } else if (tabName === 'belum') {
                if (row.classList.contains('row-belum') || row.classList.contains('row-sebagian')) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }
</script>
@endsection
