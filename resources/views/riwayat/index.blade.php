@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="page-header" style="margin-bottom: var(--space-lg);">
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
    <div>
        <a href="{{ route('export.riwayat', request()->query()) }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
    </div>
    @endif
</div>

{{-- Filter Bar --}}
<form action="{{ route('riwayat.index') }}" method="GET" class="card filter-bar" style="margin-bottom: var(--space-md); padding: 14px 18px; border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
    <div style="flex: 2; min-width: 220px;">
        <label for="search" class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block;">
            Cari No. Bukti / @if(Auth::user()->role !== 'pedagang') Pedagang / Kios @else Keterangan @endif
        </label>
        <input type="text" name="search" id="search" class="form-control" placeholder="Contoh: STK-2026 atau No Kios..." value="{{ request('search') }}" style="width: 100%; border-radius: 8px; font-size: 0.85rem; padding: 8px 12px;">
    </div>
    <div style="flex: 1; min-width: 160px;">
        <label for="status_bayar" class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block;">Status Pembayaran</label>
        <select name="status_bayar" id="status_bayar" class="form-control" style="width: 100%; border-radius: 8px; font-size: 0.85rem; padding: 8px 12px;">
            <option value="">Semua Status</option>
            <option value="lunas" {{ request('status_bayar') == 'lunas' ? 'selected' : '' }}>LUNAS</option>
            <option value="bayar_sebagian" {{ request('status_bayar') == 'bayar_sebagian' ? 'selected' : '' }}>CICIL</option>
        </select>
    </div>
    <div style="flex-shrink: 0; display: flex; gap: 8px;">
        <button type="submit" class="btn btn-primary" style="padding: 8px 18px; font-weight: 700; border-radius: 8px; font-size: 0.85rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px; vertical-align: middle;">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="M21 21l-4.35-4.35"></path>
            </svg>
            Filter
        </button>
        <a href="{{ route('riwayat.index') }}" class="btn btn-secondary" style="padding: 8px 16px; font-weight: 600; border-radius: 8px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center;">
            Reset
        </a>
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
        <div class="card" style="margin-bottom: 12px; padding: 14px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            {{-- Card Header --}}
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">No. Kuitansi:</span>
                    <div class="font-monospace" style="font-weight: 800; color: #0284c7; font-size: 0.88rem;">{{ $txn->no_bukti ?? '-' }}</div>
                </div>
                <div>
                    @if ($txn->status_bayar === 'lunas')
                        <span style="background: #dcfce7; color: #15803d; font-size: 0.7rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                            LUNAS
                        </span>
                    @elseif ($txn->status_bayar === 'bayar_sebagian')
                        <span style="background: #fef3c7; color: #b45309; font-size: 0.7rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                            CICIL
                        </span>
                    @else
                        <span style="background: #fee2e2; color: #b91c1c; font-size: 0.7rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                            BELUM BAYAR
                        </span>
                    @endif
                </div>
            </div>

            {{-- Card Details --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px; font-size: 0.82rem;">
                <div>
                    <div style="color: #64748b; font-size: 0.72rem;">Waktu Bayar:</div>
                    <div style="font-weight: 700; color: #1e293b;">
                        @if($txn->waktu_bayar)
                            {{ $txn->waktu_bayar->format('d/m/Y H:i') }} WIB
                        @elseif($txn->tanggal_tagihan)
                            {{ \Carbon\Carbon::parse($txn->tanggal_tagihan)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.72rem;">Nominal Pembayaran:</div>
                    <div style="font-weight: 800; color: #059669; font-size: 0.95rem;">
                        Rp {{ number_format($nominalDibayar, 0, ',', '.') }}
                    </div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.72rem;">Periode / Kios:</div>
                    <div style="font-weight: 600; color: #334155;">
                        @if(Auth::user()->role === 'pedagang')
                            {{ \Carbon\Carbon::parse($txn->tanggal_tagihan)->format('d M Y') }}
                        @else
                            {{ $txn->pedagang ? $txn->pedagang->nama_pedagang . ' (' . $txn->pedagang->no_kios . ')' : '-' }}
                        @endif
                    </div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.72rem;">Sisa Tagihan:</div>
                    <div style="font-weight: 700; font-size: 0.85rem;">
                        @if($txn->status_bayar === 'lunas' || $sisaTagihan <= 0)
                            <span style="color: #059669;">Rp 0</span>
                        @else
                            <span style="color: #d97706;">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Action --}}
            <div style="text-align: right; border-top: 1px dashed #f1f5f9; padding-top: 10px;">
                <a href="{{ route('riwayat.cetak', $txn->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; border-radius: 8px; font-weight: 700; padding: 8px 12px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Nota Kuitansi
                </a>
            </div>
        </div>
    @empty
        <div class="card" style="padding: 30px; text-align: center; color: #94a3b8; border-radius: 12px; border: 1px solid #e2e8f0;">
            Belum ada riwayat transaksi pembayaran yang tercatat.
        </div>
    @endforelse
</div>

<style>
    /* Desktop vs Mobile Responsive switch */
    .riwayat-mobile-cards {
        display: none;
    }
    @media (max-width: 768px) {
        .riwayat-desktop-table {
            display: none !important;
        }
        .riwayat-mobile-cards {
            display: block !important;
        }
    }

    .pagination {
        display: flex;
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
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 6px;
        background: white;
        border: 1px solid #e2e8f0;
        color: var(--primary);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pagination li a:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
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
<div style="margin-top: var(--space-md); display: flex; justify-content: center;">
    {{ $transaksis->links('pagination::bootstrap-4') }}
</div>
@endsection
