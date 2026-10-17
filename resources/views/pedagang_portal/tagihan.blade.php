@extends('layouts.app')

@section('title', 'Tagihan Iuran Saya - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <span>📋</span> Tagihan Iuran Saya
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

<!-- KPI Cards Ringkasan Tagihan -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 20px;">
    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="font-size: 0.75rem; color: var(--on-surface-variant); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Tagihan Tercatat</div>
        <div style="font-size: 1.35rem; font-weight: 800; color: var(--on-surface); margin: 4px 0;">
            Rp {{ number_format($totalTagihanNominal, 0, ',', '.') }}
        </div>
        <div style="font-size: 0.75rem; color: var(--on-surface-variant);">Akumulasi seluruh tagihan lapak</div>
    </div>

    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="font-size: 0.75rem; color: #047857; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Sudah Dibayar</div>
        <div style="font-size: 1.35rem; font-weight: 800; color: #059669; margin: 4px 0;">
            Rp {{ number_format($totalTerbayarNominal, 0, ',', '.') }}
        </div>
        <div style="font-size: 0.75rem; color: #059669;">Telah diterima kasir/petugas</div>
    </div>

    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="font-size: 0.75rem; color: {{ $totalSisaNominal > 0 ? '#b91c1c' : '#047857' }}; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Sisa Tagihan</div>
        <div style="font-size: 1.35rem; font-weight: 800; color: {{ $totalSisaNominal > 0 ? '#dc2626' : '#059669' }}; margin: 4px 0;">
            Rp {{ number_format($totalSisaNominal, 0, ',', '.') }}
        </div>
        <div style="font-size: 0.75rem; color: {{ $totalSisaNominal > 0 ? '#dc2626' : '#059669' }};">
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
            <button type="submit" class="btn btn-primary" style="flex: 1; font-size: 0.85rem;">
                🔍 Terapkan Filter
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
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                        @if($tgh->status_bayar === 'lunas')
                            <span style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                                LUNAS
                            </span>
                        @elseif($tgh->status_bayar === 'bayar_sebagian')
                            <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                                CICIL
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                                BELUM BAYAR
                            </span>
                        @endif
                    </td>

                    <!-- No Kuitansi -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                        @if($tgh->nominal_dibayar > 0)
                            <a href="{{ route('riwayat.cetak', $tgh->id) }}" target="_blank" class="btn btn-outline btn-sm" style="font-size: 0.75rem; padding: 3px 8px; display: inline-flex; align-items: center; gap: 4px; border-color: #10b981; color: #047857;" title="Cetak Kuitansi Pembayaran">
                                🖨️ {{ $tgh->no_bukti ?: 'Kuitansi' }}
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
