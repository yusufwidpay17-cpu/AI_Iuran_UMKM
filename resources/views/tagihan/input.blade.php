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
        <button class="btn btn-secondary" onclick="openGenerateModal()" style="border-radius: 8px; font-weight: 600;">
            ⚡ Generate Batch Hari Ini
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
                <div style="background: #dcfce7; color: #16a34a; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    🏖️
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.95rem; color: #166534; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span>Hari Bebas Iuran — {{ $rule->kategori_label }}</span>
                        <span style="font-size: 0.725rem; background: #bbf7d0; color: #14532d; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Kebijakan: Bebas Iuran</span>
                    </div>
                    <div style="font-size: 0.85rem; color: #15803d; margin-top: 4px;">
                        <strong>Keterangan:</strong> {{ $rule->alasan }}
                    </div>
                    <div style="font-size: 0.775rem; color: #166534; margin-top: 4px; opacity: 0.9;">
                        ℹ️ Sistem tidak menerbitkan tagihan harian baru dan tidak menambah kewajiban/tunggakan pedagang pada tanggal ini.
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
                <div style="background: #fef3c7; color: #d97706; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    ⏸️
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.95rem; color: #92400e; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span>Penagihan Diliburkan / Tertunda — {{ $rule->kategori_label }}</span>
                        <span style="font-size: 0.725rem; background: #fed7aa; color: #9a3412; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Kebijakan: Kewajiban Tetap Berlaku</span>
                    </div>
                    <div style="font-size: 0.85rem; color: #b45309; margin-top: 4px;">
                        <strong>Keterangan:</strong> {{ $rule->alasan }}
                    </div>
                    <div style="font-size: 0.775rem; color: #92400e; margin-top: 4px; opacity: 0.9;">
                        ℹ️ Petugas tidak melakukan penagihan lapangan, namun kewajiban iuran pedagang tetap tercatat sebagai tagihan sah dan dapat dilunasi saat penagihan kembali beroperasi.
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
    <div class="stat-card" style="background: #ffffff; border-radius: 12px; padding: 18px; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6;">
        <div class="stat-label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">TOTAL TARGET TAGIHAN</div>
        <div class="stat-value numeric-display" style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 4px 0;">Rp {{ number_format($tagihans->where('status_bayar', '!=', 'dibatalkan')->sum('nominal_tagihan'), 0, ',', '.') }}</div>
        <div class="stat-sub" style="font-size: 0.8125rem; color: #64748b;">{{ $tagihans->where('status_bayar', '!=', 'dibatalkan')->count() }} Tagihan Aktif</div>
    </div>

    <!-- Card Sudah Bayar -->
    <div class="stat-card" style="background: #ffffff; border-radius: 12px; padding: 18px; border: 1px solid #e2e8f0; border-left: 4px solid #10b981;">
        <div class="stat-label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #047857; letter-spacing: 0.5px;">TOTAL TERKUMPUL (SUDAH BAYAR)</div>
        <div class="stat-value numeric-display" style="font-size: 1.5rem; font-weight: 800; color: #10b981; margin: 4px 0;">Rp {{ number_format($tagihans->sum('nominal_dibayar'), 0, ',', '.') }}</div>
        <div class="stat-sub" style="font-size: 0.8125rem; color: #047857; font-weight: 600;">✔ {{ $tagihanLunas->count() }} Pedagang Lunas Hari Ini</div>
    </div>

    <!-- Card Belum Bayar -->
    <div class="stat-card" style="background: #ffffff; border-radius: 12px; padding: 18px; border: 1px solid #e2e8f0; border-left: 4px solid #ef4444;">
        <div class="stat-label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #b91c1c; letter-spacing: 0.5px;">BELUM TERBAYAR (MENUNGGAK)</div>
        <div class="stat-value numeric-display" style="font-size: 1.5rem; font-weight: 800; color: #ef4444; margin: 4px 0;">Rp {{ number_format($tagihans->where('status_bayar', '!=', 'dibatalkan')->sum('nominal_tagihan') - $tagihans->sum('nominal_dibayar'), 0, ',', '.') }}</div>
        <div class="stat-sub" style="font-size: 0.8125rem; color: #b91c1c; font-weight: 600;">⚠ {{ $tagihanBelum->count() }} Pedagang Belum Bayar</div>
    </div>
</div>

<!-- CATEGORY TABS -->
<div style="display: flex; gap: 8px; margin-top: 12px; margin-bottom: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; flex-wrap: wrap;">
    <button type="button" class="tab-btn active" id="tab-all" onclick="filterTab('all')" style="padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 0.875rem; border: none; background: #2563eb; color: #ffffff; cursor: pointer; transition: all 0.2s;">
        Semua Transaksi ({{ $tagihans->count() }})
    </button>
    <button type="button" class="tab-btn" id="tab-lunas" onclick="filterTab('lunas')" style="padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 0.875rem; border: none; background: #f1f5f9; color: #475569; cursor: pointer; transition: all 0.2s;">
        ✔ Sudah Bayar ({{ $tagihanLunas->count() }})
    </button>
    <button type="button" class="tab-btn" id="tab-belum" onclick="filterTab('belum')" style="padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 0.875rem; border: none; background: #f1f5f9; color: #475569; cursor: pointer; transition: all 0.2s;">
        ⚠ Belum Bayar ({{ $tagihanBelum->count() }})
    </button>
</div>

<!-- TRANSACTIONS TABLE -->
<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div class="table-responsive">
        <table class="data-table" style="width: 100%; border-collapse: collapse; font-family: 'Inter', system-ui, -apple-system, sans-serif;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1; text-transform: uppercase; font-size: 0.75rem; color: #334155; letter-spacing: 0.05em; font-weight: 700;">
                    <th style="padding: 12px 16px; text-align: left; width: 260px;">Kios & Nama Pedagang</th>
                    <th style="padding: 12px 16px; text-align: left;">Rincian Iuran & Keterangan</th>
                    <th style="padding: 12px 16px; text-align: left; width: 180px;">Nominal Tagihan</th>
                    <th style="padding: 12px 16px; text-align: center; width: 130px;">Status</th>
                    <th style="padding: 12px 16px; text-align: right; width: 170px;">Aksi</th>
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
                        <td style="padding: 12px 16px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; font-size: 0.775rem; padding: 4px 8px; border-radius: 6px; white-space: nowrap; font-family: monospace;">
                                    {{ $tgh->pedagang->no_kios ?? '-' }}
                                </span>
                                <div>
                                    @if ($tgh->pedagang)
                                        <a href="{{ route('pedagang.show', $tgh->pedagang_id) }}" style="font-weight: 700; color: #0f172a; text-decoration: none; font-size: 0.9rem; display: block; line-height: 1.3;">
                                            {{ $tgh->pedagang->nama_pedagang }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                            {{ $tgh->pedagang->nama_usaha ?? 'Lapak Pasar' }} <span style="color: #cbd5e1;">•</span> <strong style="color: #475569;">{{ str_starts_with($tgh->pedagang->blok_kios ?? '', 'Blok') ? ($tgh->pedagang->blok_kios ?? '-') : 'Blok ' . ($tgh->pedagang->blok_kios ?? '-') }}</strong>
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic; font-size: 0.85rem;">Data pedagang terhapus</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Rincian Iuran & Keterangan -->
                        <td style="padding: 12px 16px; vertical-align: middle;">
                            <div style="font-size: 0.825rem; font-weight: 600; color: #0f172a;">
                                {{ $tgh->jenis_iuran ?? 'Iuran Harian Pasar' }}
                            </div>
                            @if ($isLunas)
                                <div style="font-size: 0.75rem; color: #059669; font-weight: 500; margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                                    <span>✔ Diterima sah</span>
                                    @if($tgh->waktu_bayar)
                                        <span style="color: #64748b;">({{ $tgh->waktu_bayar->format('H:i') }} WIB)</span>
                                    @endif
                                </div>
                            @elseif ($isSebagian)
                                <div style="font-size: 0.75rem; color: #d97706; font-weight: 500; margin-top: 2px;">
                                    ⚠ Terbayar Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}
                                </div>
                            @elseif ($isBatal)
                                <div style="font-size: 0.75rem; color: #64748b; font-weight: 500; margin-top: 2px;">
                                    🚫 {{ $tgh->catatan ?? 'Dibatalkan / Bebas Iuran' }}
                                </div>
                            @elseif ($isTidakBerdagang)
                                <div style="font-size: 0.75rem; color: #64748b; font-weight: 500; margin-top: 2px;">
                                    🚫 {{ $tgh->catatan ?? 'Lapak Libur / Tutup' }}
                                </div>
                            @else
                                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500; margin-top: 2px;">
                                    Menunggu setoran harian
                                </div>
                            @endif
                        </td>

                        <!-- Nominal Tagihan & Sisa -->
                        <td style="padding: 12px 16px; vertical-align: middle;" class="numeric-display">
                            <div style="font-family: 'Inter', monospace; font-size: 0.925rem; font-weight: 700; color: #0f172a;">
                                Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}
                            </div>
                            @if ($isLunas)
                                <div style="font-size: 0.725rem; font-weight: 600; color: #10b981; margin-top: 1px;">
                                    ✔ Lunas
                                </div>
                            @elseif ($isSebagian)
                                <div style="font-size: 0.725rem; font-weight: 600; color: #d97706; margin-top: 1px;">
                                    Sisa: Rp {{ number_format($tgh->nominal_tagihan - $tgh->nominal_dibayar, 0, ',', '.') }}
                                </div>
                            @elseif ($isBatal)
                                <div style="font-size: 0.725rem; font-weight: 600; color: #64748b; margin-top: 1px;">
                                    Rp 0 (Bebas Iuran)
                                </div>
                            @elseif ($isTidakBerdagang)
                                <div style="font-size: 0.725rem; font-weight: 600; color: #64748b; margin-top: 1px;">
                                    Rp 0 (Libur)
                                </div>
                            @else
                                <div style="font-size: 0.725rem; font-weight: 600; color: #dc2626; margin-top: 1px;">
                                    Sisa: Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}
                                </div>
                            @endif
                        </td>

                        <!-- Status Bayar Badge -->
                        <td style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                            @if ($isLunas)
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #ecfdf5; color: #047857; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: 1px solid #a7f3d0;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span> Lunas
                                </span>
                            @elseif ($isSebagian)
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #fffbeb; color: #b45309; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: 1px solid #fde68a;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b;"></span> Sebagian
                                </span>
                            @elseif ($isBatal)
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: 1px solid #cbd5e1;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #94a3b8;"></span> Bebas / Batal
                                </span>
                            @elseif ($isTidakBerdagang)
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: 1px solid #cbd5e1;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #64748b;"></span> Libur
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #fef2f2; color: #b91c1c; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: 1px solid #fecaca;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></span> Belum Bayar
                                </span>
                            @endif
                        </td>

                        <!-- Aksi Pembayaran -->
                        <td style="padding: 12px 16px; vertical-align: middle; text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                @if ($isLunas)
                                    <button class="btn btn-secondary btn-sm" onclick="openBayarModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}', '{{ $tgh->nominal_tagihan }}', '{{ max(0, $tgh->nominal_tagihan - $tgh->nominal_dibayar) }}', '{{ $tgh->nominal_dibayar }}', 'lunas')" style="border-radius: 6px; font-weight: 600; padding: 5px 12px; font-size: 0.75rem; color: #0284c7; border: 1px solid #bae6fd; background: #f0f9ff;">
                                        ✏️ Edit
                                    </button>
                                @elseif ($isBatal)
                                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; padding-right: 8px;">
                                        Bebas Iuran
                                    </span>
                                @else
                                    <button class="btn btn-primary btn-sm" onclick="openBayarModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}', '{{ $tgh->nominal_tagihan }}', '{{ max(0, $tgh->nominal_tagihan - $tgh->nominal_dibayar) }}', '{{ $tgh->nominal_dibayar }}', '{{ $isSebagian ? 'bayar_sebagian' : 'belum_bayar' }}')" style="border-radius: 6px; font-weight: 700; padding: 5px 12px; font-size: 0.75rem; background: #2563eb; border-color: #2563eb; box-shadow: 0 1px 2px rgba(37,99,235,0.2);">
                                        💵 Bayar
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openTidakBerdagangModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}')" style="border-radius: 6px; padding: 5px 8px; font-size: 0.725rem; color: #64748b; border: 1px solid #cbd5e1; background: #ffffff;" title="Catat Lapak Tutup / Libur">
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
                            <p style="font-size: 0.875rem; margin-bottom: 20px; color: #64748b;">Silakan klik tombol <strong>⚡ Generate Batch Hari Ini</strong> untuk meng-generate tagihan harian pedagang secara otomatis.</p>
                            <button class="btn btn-primary" onclick="openGenerateModal()" style="border-radius: 8px; padding: 10px 20px; font-weight: 600;">
                                ⚡ Generate Tagihan Sekarang
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
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a;">💳 Input Pembayaran Transaksi</h3>
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
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; background: #047857; border-color: #047857; font-weight: 700;">✔ Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL GENERATE BATCH -->
<div class="modal-overlay" id="modal-generate">
    <div class="modal-container" style="max-width: 420px; border-radius: 12px;">
        <div class="modal-header">
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a;">⚡ Generate Tagihan Batch</h3>
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
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a;">💳 Input Transaksi Pembayaran</h3>
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
                <div class="form-group" id="quick-bayar-mode-group" style="margin-bottom: 14px; display: none;">
                    <label class="form-label" style="font-weight: 700;">Tipe Input Transaksi</label>
                    <select name="mode_cicilan" id="quick-bayar-mode" class="form-select" style="border-radius: 8px; min-height: 42px;" onchange="handleModeCicilanChange()">
                        <option value="tambah">➕ Tambah Cicilan Baru (Diakumulasikan)</option>
                        <option value="koreksi">✏️ Koreksi Total Nominal Pembayaran</option>
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
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 700; background: #047857; border-color: #047857;">✔ Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KETERANGAN TIDAK BERDAGANG -->
<div class="modal-overlay" id="modal-tidak-berdagang">
    <div class="modal-container" style="max-width: 460px; border-radius: 12px;">
        <div class="modal-header">
            <h3 class="modal-title" style="font-weight: 700; color: #0f172a;">🚫 Keterangan Tidak Berdagang</h3>
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
