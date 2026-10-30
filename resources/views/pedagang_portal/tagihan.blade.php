@extends('layouts.app')

@section('title', 'Tagihan Iuran Saya - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span>Tagihan Iuran Saya</span>
        </h2>
        <p class="page-subtitle" style="margin: 4px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Daftar lengkap seluruh tagihan iuran pasar kios Anda (Kios {{ $pedagang->no_kios ?? '-' }} - {{ $pedagang->nama_usaha ?? $pedagang->nama_pedagang }}).
        </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700; padding: 6px 12px; border-radius: 20px; font-size: 0.775rem; border: 1px solid rgba(16, 185, 129, 0.3);">
            Tarif Harian: Rp {{ number_format($pedagang->tarif->nominal_harian ?? 0, 0, ',', '.') }}
        </span>
    </div>
</div>

<!-- KPI Cards Ringkasan Tagihan (Compact & Rapi) -->
<div class="tagihan-kpi-grid">
    <div class="tagihan-kpi-card">
        <div class="tagihan-kpi-header">
            <span class="tagihan-kpi-title">Total Tagihan</span>
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--on-surface-variant); flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <div class="tagihan-kpi-value" style="color: var(--on-surface);">
            Rp {{ number_format($totalTagihanNominal, 0, ',', '.') }}
        </div>
        <div class="tagihan-kpi-sub">
            Akumulasi tagihan lapak
        </div>
    </div>

    <div class="tagihan-kpi-card">
        <div class="tagihan-kpi-header">
            <span class="tagihan-kpi-title" style="color: #047857;">Sudah Dibayar</span>
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #059669; flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="tagihan-kpi-value" style="color: #059669;">
            Rp {{ number_format($totalTerbayarNominal, 0, ',', '.') }}
        </div>
        <div class="tagihan-kpi-sub" style="color: #059669;">
            Telah diterima kasir/petugas
        </div>
    </div>

    <div class="tagihan-kpi-card" style="border: 1px solid {{ $totalSisaNominal > 0 ? '#fecaca' : '#a7f3d0' }};">
        <div class="tagihan-kpi-header">
            <span class="tagihan-kpi-title" style="color: {{ $totalSisaNominal > 0 ? '#b91c1c' : '#047857' }};">Sisa Tagihan</span>
            @if($totalSisaNominal > 0)
                <span class="badge" style="background: #fee2e2; color: #b91c1c; font-size: 0.63rem; padding: 1px 6px; border-radius: 6px; font-weight: 700;">Tertunda</span>
            @else
                <span class="badge" style="background: #d1fae5; color: #047857; font-size: 0.63rem; padding: 1px 6px; border-radius: 6px; font-weight: 700;">Lunas</span>
            @endif
        </div>
        <div class="tagihan-kpi-value" style="color: {{ $totalSisaNominal > 0 ? '#dc2626' : '#059669' }};">
            Rp {{ number_format($totalSisaNominal, 0, ',', '.') }}
        </div>
        <div class="tagihan-kpi-sub" style="color: {{ $totalSisaNominal > 0 ? '#dc2626' : '#059669' }};">
            {{ $totalSisaNominal > 0 ? 'Perlu diselesaikan saat penarikan' : 'Alhamdulillah seluruhnya lunas' }}
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); margin-bottom: 20px;">
    <form action="{{ route('pedagang.tagihan') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items: flex-end;">
        <div>
            <label class="form-label" style="font-size: 0.785rem; font-weight: 600;">Status Pembayaran</label>
            <select name="status" class="form-select" style="font-size: 0.85rem;">
                <option value="">Semua Status</option>
                <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                <option value="cicil" {{ request('status') === 'cicil' || request('status') === 'bayar_sebagian' ? 'selected' : '' }}>Cicil (Bayar Sebagian)</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.785rem; font-weight: 600;">Bulan</label>
            <select name="bulan" class="form-select" style="font-size: 0.85rem;">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(2026, $m, 1)->locale('id')->isoFormat('MMMM') }}
                    </option>
                @endfor
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.785rem; font-weight: 600;">Tahun</label>
            <select name="tahun" class="form-select" style="font-size: 0.85rem;">
                <option value="">Semua Tahun</option>
                <option value="2026" {{ request('tahun', '2026') == '2026' ? 'selected' : '' }}>2026</option>
                <option value="2025" {{ request('tahun') == '2025' ? 'selected' : '' }}>2025</option>
            </select>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="flex: 1; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Terapkan Filter</span>
            </button>
            <a href="{{ route('pedagang.tagihan') }}" class="btn btn-outline" style="font-size: 0.85rem;" title="Reset Filter">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Daftar Tagihan -->
<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
    <div class="table-responsive">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 14px 16px; text-align: left;">Tanggal Tagihan</th>
                    <th style="padding: 14px 16px; text-align: left;">Jenis Iuran</th>
                    <th style="padding: 14px 16px; text-align: right;">Nominal Tagihan</th>
                    <th style="padding: 14px 16px; text-align: right;">Total Dibayar</th>
                    <th style="padding: 14px 16px; text-align: right;">Sisa Tagihan</th>
                    <th style="padding: 14px 16px; text-align: center;">Status</th>
                    <th style="padding: 14px 16px; text-align: center;">No. Kuitansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihans as $tgh)
                @php
                    $sisa = max(0, $tgh->nominal_tagihan - $tgh->nominal_dibayar);
                @endphp
                <tr style="border-bottom: 1px solid var(--outline-variant);">
                    <!-- Tanggal -->
                    <td style="padding: 14px 16px; vertical-align: middle;">
                        <strong style="color: var(--on-surface); display: block;">
                            {{ \Carbon\Carbon::parse($tgh->tanggal_tagihan)->locale('id')->isoFormat('dddd, D MMM Y') }}
                        </strong>
                        <span style="font-size: 0.75rem; color: var(--on-surface-variant);">
                            {{ \Carbon\Carbon::parse($tgh->tanggal_tagihan)->diffForHumans() }}
                        </span>
                    </td>

                    <!-- Jenis Iuran -->
                    <td style="padding: 14px 16px; vertical-align: middle;">
                        <span style="font-weight: 600; color: var(--on-surface);">{{ $tgh->jenis_iuran ?: 'Iuran Harian Pasar' }}</span>
                        @if($tgh->catatan)
                            <div style="font-size: 0.725rem; color: var(--on-surface-variant); max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $tgh->catatan }}
                            </div>
                        @endif
                    </td>

                    <!-- Nominal Tagihan -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: right; font-weight: 700; color: var(--on-surface);">
                        Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}
                    </td>

                    <!-- Total Dibayar -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: right; font-weight: 700; color: #059669;">
                        Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}
                    </td>

                    <!-- Sisa Tagihan -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: right; font-weight: 800; color: {{ $sisa > 0 ? '#dc2626' : '#059669' }};">
                        Rp {{ number_format($sisa, 0, ',', '.') }}
                    </td>

                    <!-- Status -->
                    <td style="padding: 12px 14px; vertical-align: middle; text-align: center; white-space: nowrap;">
                        @if($tgh->status_bayar === 'lunas')
                            <span style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 0.72rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; display: inline-block; white-space: nowrap;">
                                LUNAS
                            </span>
                        @elseif($tgh->status_bayar === 'bayar_sebagian')
                            <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.72rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; display: inline-block; white-space: nowrap;">
                                CICIL
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-size: 0.72rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; display: inline-block; white-space: nowrap;">
                                BELUM BAYAR
                            </span>
                        @endif
                    </td>

                    <!-- No Kuitansi -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                        @if($tgh->nominal_dibayar > 0)
                            <a href="{{ route('riwayat.cetak', $tgh->id) }}" target="_blank" class="btn btn-outline btn-sm" style="font-size: 0.75rem; padding: 3px 8px; display: inline-flex; align-items: center; gap: 4px; border-color: #10b981; color: #047857;" title="Cetak Kuitansi Pembayaran">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z M7 9V5a2 2 0 012-2h6a2 2 0 012 2v4"></path>
                                </svg>
                                <span>{{ $tgh->no_bukti ?: 'Kuitansi' }}</span>
                            </a>
                        @else
                            <span style="color: var(--outline); font-size: 0.8rem;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                        Tidak ada tagihan yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tagihans->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--outline-variant);">
            {{ $tagihans->links() }}
        </div>
    @endif
</div>
@endsection
