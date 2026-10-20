@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="page-header riwayat-page-header">
    <div>
        <h2 class="page-title">
            @if(Auth::user()->role === 'pedagang')
                Riwayat Pembayaran Saya
            @else
                Riwayat Transaksi Pembayaran
            @endif
        </h2>
        <p class="page-subtitle">
            @if(Auth::user()->role === 'pedagang')
                Catatan resmi seluruh bukti pembayaran iuran retribusi lapak Anda.
            @else
                Pencatatan seluruh transaksi pembayaran iuran pedagang pasar.
            @endif
        </p>
    </div>
    @if(Auth::user()->role !== 'pedagang')
    <div class="riwayat-export-wrap">
        <a href="{{ route('export.riwayat', request()->query()) }}" class="btn btn-secondary riwayat-btn-export">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
    </div>
    @endif
</div>

{{-- Filter Bar --}}
@php
    $currentPeriode = $periode ?? request('periode', '');
    if (!$currentPeriode) {
        if (!request('start_date') && !request('end_date')) {
            $currentPeriode = 'semua';
        } elseif (request('start_date') === now()->toDateString() && request('end_date') === now()->toDateString()) {
            $currentPeriode = 'hari_ini';
        } elseif (request('start_date') === now()->subDays(6)->toDateString() && request('end_date') === now()->toDateString()) {
            $currentPeriode = '7_hari';
        } elseif (request('start_date') === now()->startOfMonth()->toDateString() && request('end_date') === now()->endOfMonth()->toDateString()) {
            $currentPeriode = 'bulan_ini';
        }
    }
@endphp
<form action="{{ route('riwayat.index') }}" method="GET" id="riwayat-filter-form" class="card riwayat-filter-card">
    <input type="hidden" name="periode" id="periode_input" value="{{ $currentPeriode }}">

    {{-- Pilihan Periode Praktis --}}
    <div style="width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
            <label class="riwayat-label" style="margin-bottom: 0;">Pilihan Periode:</label>
            @if(request('start_date') || request('end_date') || request('search') || request('status_bayar'))
                <span style="font-size: 0.7rem; color: #0284c7; font-weight: 600;">Filter Aktif</span>
            @endif
        </div>
        <div class="riwayat-preset-pills">
            <button type="button" class="preset-pill {{ $currentPeriode === 'semua' ? 'active' : '' }}" onclick="setPeriod('semua')">
                Semua Periode
            </button>
            <button type="button" class="preset-pill {{ $currentPeriode === 'hari_ini' ? 'active' : '' }}" onclick="setPeriod('hari_ini')">
                Hari Ini
            </button>
            <button type="button" class="preset-pill {{ $currentPeriode === '7_hari' ? 'active' : '' }}" onclick="setPeriod('7_hari')">
                7 Hari Terakhir
            </button>
            <button type="button" class="preset-pill {{ $currentPeriode === 'bulan_ini' ? 'active' : '' }}" onclick="setPeriod('bulan_ini')">
                Bulan Ini
            </button>
        </div>
    </div>

    {{-- Grid Form Fields --}}
    <div class="riwayat-filter-grid">
        <div class="riwayat-filter-field riwayat-search-field">
            <label for="search" class="riwayat-label">
                Cari No. Bukti / @if(Auth::user()->role !== 'pedagang') Pedagang / Kios @else Keterangan @endif
            </label>
            <input type="text" name="search" id="search" class="form-control riwayat-input" placeholder="Contoh: STK-2026 atau No Kios..." value="{{ request('search') }}">
        </div>

        <div class="riwayat-filter-field riwayat-status-field">
            <label for="status_bayar" class="riwayat-label">Status</label>
            <select name="status_bayar" id="status_bayar" class="form-control riwayat-select">
                <option value="">Semua Status</option>
                <option value="lunas" {{ request('status_bayar') == 'lunas' ? 'selected' : '' }}>LUNAS</option>
                <option value="bayar_sebagian" {{ request('status_bayar') == 'bayar_sebagian' ? 'selected' : '' }}>CICIL</option>
            </select>
        </div>

        <div class="riwayat-filter-field riwayat-date-field">
            <label for="start_date" class="riwayat-label">Dari Tanggal Bayar</label>
            <input type="date" name="start_date" id="start_date" class="form-control riwayat-input" value="{{ $startDate ?? request('start_date') }}" onchange="clearPresetActive()">
        </div>

        <div class="riwayat-filter-field riwayat-date-field">
            <label for="end_date" class="riwayat-label">Sampai Tanggal Bayar</label>
            <input type="date" name="end_date" id="end_date" class="form-control riwayat-input" value="{{ $endDate ?? request('end_date') }}" onchange="clearPresetActive()">
        </div>

        <div class="riwayat-btn-group">
            <button type="submit" class="btn btn-primary riwayat-btn-filter">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                </svg>
                Filter
            </button>
            <a href="{{ route('riwayat.index') }}" class="btn btn-secondary riwayat-btn-reset">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- Desktop Table View --}}
<div class="card riwayat-desktop-table" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
    <div class="table-responsive">
        <table class="data-table" style="width: 100%; margin: 0; border-collapse: collapse;">
            <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <tr>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">No. Kuitansi</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Tanggal & Waktu Bayar</th>
                    @if(Auth::user()->role !== 'pedagang')
                        <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Nama Pedagang / Kios</th>
                    @else
                        <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Periode Tagihan</th>
                    @endif
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Nominal Dibayar</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Sisa Tagihan</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Status</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">Bukti Kuitansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $txn)
                    @php
                        $nominalTagihan = (float) $txn->nominal_tagihan;
                        $nominalDibayar = (float) $txn->nominal_dibayar;
                        $sisaTagihan = max(0, $nominalTagihan - $nominalDibayar);
                    @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        <td class="font-monospace" style="padding: 14px 16px; font-weight: 700; color: #0284c7; font-size: 0.82rem;">
                            {{ $txn->no_bukti ?? '-' }}
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.82rem; color: #334155;">
                            @if($txn->waktu_bayar)
                                <div><strong>{{ $txn->waktu_bayar->format('d M Y') }}</strong></div>
                                <div style="font-size: 0.75rem; color: #64748b;">Pukul {{ $txn->waktu_bayar->format('H:i') }} WIB</div>
                            @elseif($txn->tanggal_tagihan)
                                <div><strong>{{ \Carbon\Carbon::parse($txn->tanggal_tagihan)->format('d M Y') }}</strong></div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">Waktu tidak tercatat</div>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        @if(Auth::user()->role !== 'pedagang')
                            <td style="padding: 14px 16px; font-size: 0.85rem; font-weight: 600;">
                                @if ($txn->pedagang)
                                    <a href="{{ route('pedagang.show', $txn->pedagang_id) }}" style="color: var(--primary); text-decoration: none; font-weight: 700;">
                                        {{ $txn->pedagang->nama_pedagang }}
                                    </a>
                                    <div style="font-size: 0.75rem; color: #64748b;">Kios {{ $txn->pedagang->no_kios }}</div>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                        @else
                            <td style="padding: 14px 16px; font-size: 0.82rem; color: #334155;">
                                <div style="font-weight: 600;">{{ $txn->jenis_iuran ?: 'Iuran Retribusi Harian' }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">{{ \Carbon\Carbon::parse($txn->tanggal_tagihan)->format('d M Y') }}</div>
                            </td>
                        @endif
                        <td class="numeric-display" style="padding: 14px 16px; color: #059669; font-weight: 800; font-size: 0.9rem;">
                            Rp {{ number_format($nominalDibayar, 0, ',', '.') }}
                        </td>
                        <td class="numeric-display" style="padding: 14px 16px; font-size: 0.82rem;">
                            @if($txn->status_bayar === 'lunas' || $sisaTagihan <= 0)
                                <span style="color: #059669; font-weight: 700;">Rp 0 (Lunas)</span>
                            @else
                                <span style="color: #d97706; font-weight: 800;">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px;">
                            @if ($txn->status_bayar === 'lunas')
                                <span style="background: #dcfce7; color: #15803d; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; display: inline-block;">
                                    LUNAS
                                </span>
                            @elseif ($txn->status_bayar === 'bayar_sebagian')
                                <span style="background: #fef3c7; color: #b45309; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; display: inline-block;">
                                    CICIL
                                </span>
                            @else
                                <span style="background: #fee2e2; color: #b91c1c; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; display: inline-block;">
                                    BELUM BAYAR
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <a href="{{ route('riwayat.cetak', $txn->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="border-radius: 6px; font-size: 0.75rem; padding: 6px 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak Nota
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px 16px;">
                            <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>Belum ada riwayat transaksi pembayaran yang tercatat.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile Card View (Muncul otomatis di layar kecil / smartphone) --}}
<div class="riwayat-mobile-cards">
    @forelse ($transaksis as $txn)
        @php
            $nominalTagihan = (float) $txn->nominal_tagihan;
            $nominalDibayar = (float) $txn->nominal_dibayar;
            $sisaTagihan = max(0, $nominalTagihan - $nominalDibayar);
        @endphp
        <div class="card riwayat-tx-card">
            {{-- Card Header --}}
            <div class="riwayat-tx-header">
                <div>
                    <span class="riwayat-tx-sublabel">No. Kuitansi:</span>
                    <div class="font-monospace riwayat-tx-receipt">{{ $txn->no_bukti ?? '-' }}</div>
                </div>
                <div>
                    @if ($txn->status_bayar === 'lunas')
                        <span class="badge-status-pill badge-status-lunas">
                            LUNAS
                        </span>
                    @elseif ($txn->status_bayar === 'bayar_sebagian')
                        <span class="badge-status-pill badge-status-cicil">
                            CICIL
                        </span>
                    @else
                        <span class="badge-status-pill badge-status-belum">
                            BELUM BAYAR
                        </span>
                    @endif
                </div>
            </div>

            {{-- Card Details --}}
            <div class="riwayat-tx-details">
                <div class="riwayat-tx-col">
                    <div class="riwayat-tx-label">Waktu Bayar:</div>
                    <div class="riwayat-tx-val riwayat-tx-val-dark">
                        @if($txn->waktu_bayar)
                            {{ $txn->waktu_bayar->format('d/m/Y H:i') }} WIB
                        @elseif($txn->tanggal_tagihan)
                            {{ \Carbon\Carbon::parse($txn->tanggal_tagihan)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="riwayat-tx-col">
                    <div class="riwayat-tx-label">Nominal Pembayaran:</div>
                    <div class="riwayat-tx-val riwayat-tx-val-nominal">
                        Rp {{ number_format($nominalDibayar, 0, ',', '.') }}
                    </div>
                </div>
                <div class="riwayat-tx-col">
                    <div class="riwayat-tx-label">Periode / Kios:</div>
                    <div class="riwayat-tx-val riwayat-tx-val-kios">
                        @if(Auth::user()->role === 'pedagang')
                            {{ \Carbon\Carbon::parse($txn->tanggal_tagihan)->format('d M Y') }}
                        @else
                            {{ $txn->pedagang ? $txn->pedagang->nama_pedagang . ' (' . $txn->pedagang->no_kios . ')' : '-' }}
                        @endif
                    </div>
                </div>
                <div class="riwayat-tx-col">
                    <div class="riwayat-tx-label">Sisa Tagihan:</div>
                    <div class="riwayat-tx-val">
                        @if($txn->status_bayar === 'lunas' || $sisaTagihan <= 0)
                            <span style="color: #059669; font-weight: 700;">Rp 0</span>
                        @else
                            <span style="color: #d97706; font-weight: 700;">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Action --}}
            <div class="riwayat-tx-action">
                <a href="{{ route('riwayat.cetak', $txn->id) }}" target="_blank" class="btn btn-secondary btn-sm riwayat-btn-cetak">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Nota Kuitansi
                </a>
            </div>
        </div>
    @empty
        <div class="card riwayat-empty-card">
            Belum ada riwayat transaksi pembayaran yang tercatat.
        </div>
    @endforelse
</div>

<style>
    /* Desktop Default Styles */
    .riwayat-page-header {
        margin-bottom: var(--space-md);
    }
    .riwayat-filter-card {
        margin-bottom: var(--space-md);
        padding: 14px 18px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .riwayat-preset-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .riwayat-preset-pills::-webkit-scrollbar {
        display: none;
    }
    .preset-pill {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        user-select: none;
    }
    .preset-pill:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: var(--primary);
    }
    .preset-pill:active {
        transform: scale(0.97);
    }
    .preset-pill.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(0, 61, 155, 0.2);
    }
    .riwayat-filter-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }
    .riwayat-search-field {
        flex: 2;
        min-width: 180px;
    }
    .riwayat-status-field {
        flex: 1;
        min-width: 120px;
    }
    .riwayat-date-field {
        flex: 1;
        min-width: 135px;
    }
    .riwayat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        display: block;
    }
    .riwayat-input,
    .riwayat-select {
        width: 100%;
        border-radius: 8px;
        font-size: 0.85rem;
        padding: 8px 12px;
        height: 38px;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
    }
    .riwayat-btn-group {
        flex-shrink: 0;
        display: flex;
        gap: 8px;
    }
    .riwayat-btn-filter {
        padding: 8px 18px;
        font-weight: 700;
        border-radius: 8px;
        font-size: 0.85rem;
        height: 38px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .riwayat-btn-filter:active {
        transform: scale(0.97);
    }
    .riwayat-btn-reset {
        padding: 8px 16px;
        font-weight: 600;
        border-radius: 8px;
        font-size: 0.85rem;
        height: 38px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .riwayat-btn-reset:active {
        transform: scale(0.97);
    }

    /* Mobile Cards Base Style */
    .riwayat-mobile-cards {
        display: none;
    }
    .riwayat-tx-card {
        margin-bottom: 10px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        box-sizing: border-box;
    }
    .riwayat-tx-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
        padding-bottom: 7px;
        border-bottom: 1px solid #f1f5f9;
        gap: 8px;
    }
    .riwayat-tx-sublabel {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        line-height: 1.2;
        display: block;
    }
    .riwayat-tx-receipt {
        font-weight: 800;
        color: #0284c7;
        font-size: 0.88rem;
        letter-spacing: -0.01em;
        line-height: 1.3;
    }
    .badge-status-pill {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 3px 9px;
        border-radius: 999px;
        display: inline-block;
        letter-spacing: 0.02em;
    }
    .badge-status-lunas {
        background: #dcfce7;
        color: #15803d;
    }
    .badge-status-cicil {
        background: #fef3c7;
        color: #b45309;
    }
    .badge-status-belum {
        background: #fee2e2;
        color: #b91c1c;
    }
    .riwayat-tx-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 12px;
        margin-bottom: 9px;
        font-size: 0.8125rem;
    }
    .riwayat-tx-col {
        min-width: 0;
    }
    .riwayat-tx-label {
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 600;
        margin-bottom: 2px;
        line-height: 1.2;
    }
    .riwayat-tx-val {
        line-height: 1.35;
        word-break: break-word;
    }
    .riwayat-tx-val-dark {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.8rem;
    }
    .riwayat-tx-val-nominal {
        font-weight: 800;
        color: #059669;
        font-size: 0.9rem;
    }
    .riwayat-tx-val-kios {
        font-weight: 600;
        color: #334155;
        font-size: 0.8rem;
    }
    .riwayat-tx-action {
        text-align: right;
        border-top: 1px dashed #f1f5f9;
        padding-top: 8px;
    }
    .riwayat-btn-cetak {
        width: 100%;
        justify-content: center;
        border-radius: 8px;
        font-weight: 600;
        min-height: 36px;
        height: 36px;
        padding: 6px 12px;
        font-size: 0.8125rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .riwayat-btn-cetak:active {
        transform: scale(0.98);
    }
    .riwayat-empty-card {
        padding: 24px 16px;
        text-align: center;
        color: #94a3b8;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
        background: #ffffff;
    }

    /* Mobile Responsive Optimizations (Android & iOS) */
    @media (max-width: 768px) {
        .riwayat-page-header {
            margin-bottom: 10px !important;
        }
        .riwayat-export-wrap {
            margin-top: 8px;
            width: 100%;
        }
        .riwayat-export-wrap .riwayat-btn-export {
            width: 100%;
            min-height: 38px !important;
            height: 38px !important;
            font-size: 0.8125rem !important;
            padding: 8px 14px !important;
            justify-content: center;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        .riwayat-export-wrap .riwayat-btn-export:active {
            transform: scale(0.98);
        }
        .riwayat-desktop-table {
            display: none !important;
        }
        .riwayat-mobile-cards {
            display: block !important;
        }

        /* Filter form compact & touch-friendly */
        .riwayat-filter-card {
            padding: 12px 14px !important;
            gap: 10px !important;
            margin-bottom: 12px !important;
            border-radius: 12px !important;
        }
        .riwayat-preset-pills {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            gap: 6px !important;
            padding-bottom: 4px !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .riwayat-preset-pills::-webkit-scrollbar {
            display: none !important;
        }
        .preset-pill {
            padding: 6px 12px !important;
            font-size: 0.75rem !important;
            min-height: 32px !important;
            display: inline-flex !important;
            align-items: center !important;
            flex-shrink: 0 !important;
        }
        .riwayat-filter-grid {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        .riwayat-search-field {
            width: 100% !important;
            flex: 1 1 100% !important;
            min-width: 0 !important;
        }
        .riwayat-status-field {
            width: 100% !important;
            flex: 1 1 100% !important;
            min-width: 0 !important;
        }
        .riwayat-date-field {
            width: calc(50% - 4px) !important;
            flex: 1 1 calc(50% - 4px) !important;
            min-width: 0 !important;
        }
        /* Cegah iOS Safari auto-zoom dengan font-size 16px saat focused */
        .riwayat-input,
        .riwayat-select {
            height: 38px !important;
            min-height: 38px !important;
            padding: 6px 10px !important;
            font-size: 16px !important; /* Standar iOS HIG untuk mencegah zoom in otomatis */
            border-radius: 8px !important;
            line-height: 1.3 !important;
        }
        .riwayat-btn-group {
            width: 100% !important;
            display: flex !important;
            gap: 8px !important;
            margin-top: 4px !important;
        }
        .riwayat-btn-filter,
        .riwayat-btn-reset {
            flex: 1 1 50% !important;
            width: 50% !important;
            min-height: 38px !important;
            height: 38px !important;
            padding: 6px 12px !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            justify-content: center !important;
        }
    }

    /* Small screens (iPhone SE, 320px - 360px) */
    @media (max-width: 360px) {
        .riwayat-date-field {
            width: 100% !important;
            flex: 1 1 100% !important;
        }
    }

    /* Tablet & Medium Screens (440px - 768px) */
    @media (min-width: 440px) and (max-width: 768px) {
        .riwayat-search-field {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
        .riwayat-status-field {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
        .riwayat-date-field {
            flex: 1 1 calc(50% - 4px) !important;
            width: calc(50% - 4px) !important;
        }
        .riwayat-btn-group {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
    }

    /* Safe Area Inset support for iPhone with Home Bar */
    .riwayat-pagination-wrap {
        margin-top: 14px;
        margin-bottom: 14px;
        display: flex;
        justify-content: center;
        padding-bottom: calc(14px + env(safe-area-inset-bottom, 0px));
    }

    .pagination {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 4px;
    }
    .pagination li {
        display: inline-block;
    }
    .pagination li a, .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 8px;
        border-radius: 7px;
        background: white;
        border: 1px solid #e2e8f0;
        color: var(--primary);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .pagination li a:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .pagination li a:active {
        transform: scale(0.95);
    }
    .pagination li.active span {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    .pagination li.disabled span {
        color: #94a3b8;
        background: #f8fafc;
        cursor: not-allowed;
    }
</style>
<div class="riwayat-pagination-wrap">
    {{ $transaksis->links('pagination::bootstrap-4') }}
</div>

<script>
function setPeriod(type) {
    const today = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const periodeInput = document.getElementById('periode_input');

    if (periodeInput) periodeInput.value = type;

    if (type === 'hari_ini') {
        const todayStr = formatDate(today);
        if (startInput) startInput.value = todayStr;
        if (endInput) endInput.value = todayStr;
    } else if (type === '7_hari') {
        const past7 = new Date();
        past7.setDate(today.getDate() - 6);
        if (startInput) startInput.value = formatDate(past7);
        if (endInput) endInput.value = formatDate(today);
    } else if (type === 'bulan_ini') {
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        if (startInput) startInput.value = formatDate(firstDay);
        if (endInput) endInput.value = formatDate(lastDay);
    } else if (type === 'semua') {
        if (startInput) startInput.value = '';
        if (endInput) endInput.value = '';
    }

    const form = document.getElementById('riwayat-filter-form');
    if (form) form.submit();
}

function clearPresetActive() {
    const periodeInput = document.getElementById('periode_input');
    if (periodeInput) periodeInput.value = '';
    document.querySelectorAll('.preset-pill').forEach(el => el.classList.remove('active'));
}
</script>
@endsection
