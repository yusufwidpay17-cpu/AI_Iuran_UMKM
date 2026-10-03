@extends('layouts.app')

@section('title', 'Transaksi Pembayaran Iuran')

@section('content')
@php
    $carbonDate = \Carbon\Carbon::parse($tanggal);
    $daysIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $monthsIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    
    $namaHari = $daysIndo[$carbonDate->format('l')] ?? $carbonDate->format('l');
    $namaBulan = $monthsIndo[(int)$carbonDate->format('m')] ?? $carbonDate->format('F');
    $tanggalLengkapIndo = $namaHari . ', ' . $carbonDate->format('d') . ' ' . $namaBulan . ' ' . $carbonDate->format('Y');

    $tagihanLunas = $tagihans->where('status_bayar', 'lunas');
    $tagihanBelum = $tagihans->where('status_bayar', '!=', 'lunas');
@endphp

<!-- PAGE HEADER -->
<div class="page-header" style="margin-bottom: 24px;">
    <div>
        <h2 class="page-title" style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Transaksi Pembayaran Iuran</h2>
        <p class="page-subtitle" style="color: #64748b; font-size: 0.875rem; margin: 0;">
            Pencatatan & verifikasi pembayaran iuran harian pedagang pada {{ $tanggalLengkapIndo }}
        </p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <!-- Live Clock Card Widget -->
        <div style="display: flex; align-items: center; gap: 8px; background: #ffffff; border: 1px solid var(--outline-variant); border-radius: var(--radius-md); padding: 6px 12px; box-shadow: var(--shadow-xs);">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div style="font-size: 0.95rem; font-weight: 800; color: var(--on-surface); font-family: 'Inter', monospace; line-height: 1;">
                <span class="live-admin-time">00:00:00</span>
                <span class="live-admin-tz" style="font-size: 0.7rem; font-weight: 700; color: var(--primary); margin-left: 2px;">WIB</span>
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
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Card Total Target -->
    <div class="stat-card" style="background: #ffffff; border-radius: 12px; padding: 18px; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6;">
        <div class="stat-label" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">TOTAL TARGET TAGIHAN</div>
        <div class="stat-value numeric-display" style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 4px 0;">Rp {{ number_format($tagihans->sum('nominal_tagihan'), 0, ',', '.') }}</div>
        <div class="stat-sub" style="font-size: 0.8125rem; color: #64748b;">{{ $tagihans->count() }} Pedagang Terdaftar</div>
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
        <div class="stat-value numeric-display" style="font-size: 1.5rem; font-weight: 800; color: #ef4444; margin: 4px 0;">Rp {{ number_format($tagihans->sum('nominal_tagihan') - $tagihans->sum('nominal_dibayar'), 0, ',', '.') }}</div>
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
<div class="card" style="padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    <div class="table-responsive">
        <table class="data-table" style="width: 100%; border-collapse: collapse; font-family: 'Inter', system-ui, -apple-system, sans-serif;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 0.725rem; color: #475569; letter-spacing: 0.6px; font-weight: 800;">
                    <th style="padding: 14px 18px; text-align: left; width: 250px;">Kios & Nama Pedagang</th>
                    <th style="padding: 14px 18px; text-align: left;">Deskripsi & Keterangan Status</th>
                    <th style="padding: 14px 18px; text-align: left; width: 190px;">Nominal Tagihan / Dibayar</th>
                    <th style="padding: 14px 18px; text-align: center; width: 140px;">Status Bayar</th>
                    <th style="padding: 14px 18px; text-align: right; width: 180px;">Aksi Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tagihans as $tgh)
                    @php
                        $isLunas = $tgh->status_bayar === 'lunas';
                        $isSebagian = $tgh->status_bayar === 'bayar_sebagian';
                        $isTidakBerdagang = str_contains($tgh->catatan ?? '', '[TIDAK BERDAGANG]');
                        
                        $rowStatusClass = $isLunas ? 'row-lunas' : ($isSebagian ? 'row-sebagian' : 'row-belum');

                        $colorDibayar = '#ef4444';
                        if ($isLunas) {
                            $colorDibayar = '#10b981';
                        } elseif ($isSebagian) {
                            $colorDibayar = '#d97706';
                        } elseif ($isTidakBerdagang) {
                            $colorDibayar = '#64748b';
                        }

                        $catatanLunasText = $tgh->catatan ?? ('Pembayaran iuran harian pada ' . $tanggalLengkapIndo . ' telah diterima secara sah.');
                    @endphp
                    <tr class="transaction-row {{ $rowStatusClass }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        <!-- Kios & Nama Pedagang -->
                        <td style="padding: 14px 18px; vertical-align: top;">
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <div style="background: #2563eb; color: #ffffff; font-weight: 800; font-size: 0.8125rem; padding: 6px 10px; border-radius: 8px; white-space: nowrap; box-shadow: 0 2px 4px rgba(37,99,235,0.15); font-family: monospace;">
                                    {{ $tgh->pedagang->no_kios ?? '-' }}
                                </div>
                                <div>
                                    @if ($tgh->pedagang)
                                        <a href="{{ route('pedagang.show', $tgh->pedagang_id) }}" style="font-weight: 700; color: #0f172a; text-decoration: none; font-size: 0.9375rem; display: block; line-height: 1.3;">
                                            {{ $tgh->pedagang->nama_pedagang }}
                                        </a>
                                        <div style="font-size: 0.775rem; color: #64748b; margin-top: 3px; font-weight: 500;">
                                            {{ $tgh->pedagang->nama_usaha ?? 'Usaha Lapak Pasar' }} <span style="color: #cbd5e1;">•</span> <strong style="color: #475569;">{{ str_starts_with($tgh->pedagang->blok_kios ?? '', 'Blok') ? ($tgh->pedagang->blok_kios ?? '-') : 'Blok ' . ($tgh->pedagang->blok_kios ?? '-') }}</strong>
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">Data pedagang terhapus</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Deskripsi & Keterangan Status -->
                        <td style="padding: 14px 18px; vertical-align: top;">
                            <div style="font-size: 0.8125rem; color: #1e293b; font-weight: 700; margin-bottom: 5px; display: flex; align-items: center; gap: 6px;">
                                <span>{{ $tgh->jenis_iuran ?? 'Iuran Harian Pasar' }}</span>
                                @if($tgh->waktu_bayar && ($isLunas || $isSebagian))
                                    <span style="background: #e2e8f0; color: #334155; font-size: 0.7rem; padding: 2px 7px; border-radius: 4px; font-weight: 600;">🕒 {{ $tgh->waktu_bayar->format('H:i') }} WIB</span>
                                @endif
                            </div>
                            @if ($isLunas)
                                <div style="font-size: 0.775rem; color: #047857; background: #f0fdf4; padding: 6px 10px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 6px;">
                                    ✔ <strong>LUNAS:</strong> Setoran iuran harian diterima secara sah.
                                </div>
                            @elseif ($isSebagian)
                                <div style="font-size: 0.775rem; color: #b45309; background: #fffbeb; padding: 6px 10px; border-radius: 6px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 6px;">
                                    ⚠ <strong>BAYAR SEBAGIAN:</strong> Terbayar Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }} (Sisa Rp {{ number_format($tgh->nominal_tagihan - $tgh->nominal_dibayar, 0, ',', '.') }})
                                </div>
                            @elseif ($isTidakBerdagang)
                                <div style="font-size: 0.775rem; color: #475569; background: #f8fafc; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 6px;">
                                    🚫 <strong>TIDAK BERDAGANG:</strong> {{ $tgh->catatan ?? 'Lapak Tutup Hari Ini' }}
                                </div>
                            @else
                                <div style="font-size: 0.775rem; color: #b91c1c; background: #fef2f2; padding: 6px 10px; border-radius: 6px; border: 1px solid #fecaca; display: inline-flex; align-items: center; gap: 6px;">
                                    🏪 <strong>BELUM BAYAR:</strong> Belum ada setoran masuk hari ini.
                                </div>
                            @endif
                        </td>

                        <!-- Nominal Tagihan & Dibayar -->
                        <td style="padding: 16px 18px; vertical-align: top;" class="numeric-display">
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Tagihan: <strong style="color: #0f172a; font-weight: 700;">Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}</strong></div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: {{ $colorDibayar }}; margin-top: 3px;">
                                Dibayar: Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- Status Bayar Badge -->
                        <td style="padding: 16px 18px; vertical-align: top; text-align: center;">
                            @if ($isLunas)
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #15803d; font-size: 0.75rem; font-weight: 800; padding: 5px 14px; border-radius: 999px; border: 1px solid #86efac;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span> LUNAS
                                </span>
                            @elseif ($isSebagian)
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #fef3c7; color: #b45309; font-size: 0.75rem; font-weight: 800; padding: 5px 14px; border-radius: 999px; border: 1px solid #fde047;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #d97706;"></span> SEBAGIAN
                                </span>
                            @elseif ($isTidakBerdagang)
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #475569; font-size: 0.75rem; font-weight: 800; padding: 5px 14px; border-radius: 999px; border: 1px solid #cbd5e1;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #64748b;"></span> LIBUR / TUTUP
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #fee2e2; color: #b91c1c; font-size: 0.75rem; font-weight: 800; padding: 5px 14px; border-radius: 999px; border: 1px solid #fca5a5;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #dc2626;"></span> BELUM BAYAR
                                </span>
                            @endif
                        </td>

                        <!-- Aksi Pembayaran -->
                        <td style="padding: 16px 18px; vertical-align: top; text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap;">
                                <button class="btn btn-primary btn-sm" onclick="openBayarModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}', '{{ $tgh->nominal_tagihan }}', '{{ $tgh->nominal_tagihan - $tgh->nominal_dibayar }}')" style="border-radius: 8px; font-weight: 700; padding: 7px 14px; font-size: 0.8125rem; {{ $isLunas ? 'background: #0284c7; border-color: #0284c7;' : '' }}">
                                    {{ $isLunas ? '✏️ Edit Transaksi' : '💵 Bayar Sekarang' }}
                                </button>
                                @if (!$isLunas)
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openTidakBerdagangModal('{{ $tgh->id }}', '{{ $tgh->pedagang->nama_pedagang ?? '' }}')" style="border-radius: 8px; padding: 7px 10px; font-size: 0.75rem; color: #475569; font-weight: 600; border: 1px solid #cbd5e1; background: #ffffff;" title="Catat Alasan Tidak Berdagang / Toko Tutup">
                                        ❌ Tidak Berdagang
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
            <input type="hidden" name="status_pembayaran" value="lunas">
            <input type="hidden" name="langsung_lunas" value="1">
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
                    <label class="form-label" style="font-weight: 700;">Metode Pembayaran *</label>
                    <select name="metode_bayar" class="form-select" required style="border-radius: 8px; min-height: 42px;">
                        <option value="tunai">Tunai / Cash</option>
                        <option value="qris">QRIS (Digital)</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Catatan / Keterangan Transaksi</label>
                    <input type="text" name="catatan" class="form-input" placeholder="Opsional (Catatan pembayaran)" style="border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeTambahIuranModal()" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; background: #047857; border-color: #047857; font-weight: 700;">✔ Simpan Pembayaran (LUNAS)</button>
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
                    <label class="form-label" style="font-weight: 700;">Nomor Struk / Bukti Transaksi</label>
                    <input type="text" name="no_bukti" class="form-input" placeholder="Opsional (Kosongkan untuk otomatis)" style="border-radius: 8px;">
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 700;">Catatan Transaksi</label>
                    <input type="text" name="catatan" class="form-input" placeholder="Opsional (Catatan tambahan)" style="border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeBayarModal()" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 700; background: #047857; border-color: #047857;">✔ Simpan Pembayaran (LUNAS)</button>
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

    function openBayarModal(tagihanId, namaPedagang, nominalTagihan, sisaNominal) {
        document.getElementById('quick-bayar-form').action = '/tagihan/' + tagihanId + '/bayar';
        document.getElementById('quick-bayar-pedagang').value = namaPedagang;
        
        // Default to sisaNominal or 5000 if 0
        const defaultVal = parseFloat(sisaNominal) > 0 ? parseFloat(sisaNominal) : (parseFloat(nominalTagihan) > 0 ? parseFloat(nominalTagihan) : 5000);
        document.getElementById('quick-bayar-nominal').value = defaultVal;
        document.getElementById('modal-quick-bayar').classList.add('active');
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
