@extends('layouts.app')

@section('title', 'Tunggakan Iuran Saya - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #dc2626;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span>Tunggakan Iuran Saya</span>
        </h2>
        <p class="page-subtitle" style="margin: 4px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Rincian tagihan iuran yang masih memiliki sisa kewajiban pembayaran (Kios {{ $pedagang->no_kios ?? '-' }} - {{ $pedagang->nama_usaha ?? $pedagang->nama_pedagang }}).
        </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <span class="badge" style="background: {{ $totalTunggakanNominal > 0 ? '#fee2e2' : '#dcfce7' }}; color: {{ $totalTunggakanNominal > 0 ? '#b91c1c' : '#15803d' }}; font-weight: 700; padding: 6px 12px; border-radius: 20px; font-size: 0.775rem;">
            {{ $totalTunggakanNominal > 0 ? '● Terdapat Tunggakan' : '● Bebas Tunggakan' }}
        </span>
    </div>
</div>

@if($totalTunggakanNominal == 0 && $unpaidBills->isEmpty())
    <!-- TAMPILAN JIKA TIDAK ADA TUNGGAKAN SAMA SEKALI -->
    <div class="card" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1.5px solid #a7f3d0; border-radius: 16px; padding: 40px 24px; text-align: center; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.08);">
        <div style="width: 72px; height: 72px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h3 style="font-size: 1.4rem; font-weight: 800; color: #065f46; margin: 0 0 8px;">
            Alhamdulillah, Seluruh Tagihan Telah Lunas!
        </h3>
        <p style="font-size: 0.95rem; color: #047857; max-width: 540px; margin: 0 auto 20px; line-height: 1.6;">
            Kios Anda <strong>({{ $pedagang->no_kios ?? '-' }} - {{ $pedagang->nama_usaha ?? $pedagang->nama_pedagang }})</strong> tidak memiliki tunggakan iuran yang tertunda. Terima kasih atas kedisiplinan dan amanah Anda dalam mendukung kemajuan pasar bersama.
        </p>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('pedagang.tagihan') }}" class="btn btn-outline" style="border-color: #059669; color: #065f46; font-weight: 600;">
                Lihat Seluruh Tagihan
            </a>
            <a href="{{ route('riwayat.index') }}" class="btn btn-primary" style="background: #059669; font-weight: 600;">
                Lihat Riwayat Pembayaran
            </a>
        </div>
    </div>
@else
    <!-- RINGKASAN TUNGGAKAN AKTIF -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 20px;">
        <div class="card" style="padding: 18px; border-radius: 12px; border: 1.5px solid #fecaca; background: #fff5f5;">
            <div style="font-size: 0.75rem; color: #991b1b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total Sisa Tunggakan</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #dc2626; margin: 6px 0;">
                Rp {{ number_format($totalTunggakanNominal, 0, ',', '.') }}
            </div>
            <div style="font-size: 0.8rem; color: #b91c1c;">
                Akumulasi seluruh sisa tagihan yang belum lunas
            </div>
        </div>

        <div class="card" style="padding: 18px; border-radius: 12px; border: 1.5px solid #fed7aa; background: #fffaf0;">
            <div style="font-size: 0.75rem; color: #9a3412; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Jumlah Hari / Transaksi Menunggak</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #ea580c; margin: 6px 0;">
                {{ $jumlahHariMenunggak }} <span style="font-size: 0.95rem; font-weight: 600;">Hari</span>
            </div>
            <div style="font-size: 0.8rem; color: #c2410c;">
                Terdiri dari tagihan belum bayar & cicilan
            </div>
        </div>

        <div class="card" style="padding: 18px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); display: flex; flex-direction: column; justify-content: center;">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface); margin-bottom: 4px; display: flex; align-items: center; gap: 5px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #d97706;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Cara Pelunasan Iuran:</span>
            </div>
            <div style="font-size: 0.8rem; color: var(--on-surface-variant); line-height: 1.5;">
                Silakan siapkan dana saat petugas lapangan melakukan penarikan keliling harian, atau hubungi kantor kasir BUMPes Digital pasar.
            </div>
        </div>
    </div>
            <div style="font-size: 0.8rem; color: var(--on-surface-variant); line-height: 1.5;">
                Silakan siapkan dana saat petugas lapangan melakukan penarikan keliling harian, atau hubungi kantor kasir BUMPes Digital pasar.
            </div>
        </div>
    </div>

    <!-- TABEL RINCIAN TUNGGAKAN -->
    <div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--outline-variant); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--on-surface);">
                Rincian Hari Tagihan yang Belum Lunas
            </h3>
            <span style="font-size: 0.775rem; color: var(--on-surface-variant);">Diurutkan dari tagihan terlama</span>
        </div>

        <div class="table-responsive">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 14px 16px; text-align: left;">Tanggal Iuran</th>
                        <th style="padding: 14px 16px; text-align: left;">Jenis Tagihan</th>
                        <th style="padding: 14px 16px; text-align: right;">Nominal Awal</th>
                        <th style="padding: 14px 16px; text-align: right;">Telah Dicicil / Dibayar</th>
                        <th style="padding: 14px 16px; text-align: right;">Sisa Belum Dibayar</th>
                        <th style="padding: 14px 16px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unpaidBills as $b)
                    @php
                        $sisa = max(0, $b->nominal_tagihan - $b->nominal_dibayar);
                    @endphp
                    <tr style="border-bottom: 1px solid var(--outline-variant);">
                        <!-- Tanggal -->
                        <td style="padding: 14px 16px; vertical-align: middle;">
                            <strong style="color: var(--on-surface); display: block;">
                                {{ \Carbon\Carbon::parse($b->tanggal_tagihan)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                            </strong>
                            <span style="font-size: 0.75rem; color: #dc2626; font-weight: 600;">
                                Menunggak {{ \Carbon\Carbon::parse($b->tanggal_tagihan)->diffForHumans() }}
                            </span>
                        </td>

                        <!-- Jenis Tagihan -->
                        <td style="padding: 14px 16px; vertical-align: middle;">
                            <span style="font-weight: 600; color: var(--on-surface);">{{ $b->jenis_iuran ?: 'Iuran Harian' }}</span>
                            @if($b->catatan)
                                <div style="font-size: 0.725rem; color: var(--on-surface-variant); max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $b->catatan }}
                                </div>
                            @endif
                        </td>

                        <!-- Nominal Awal -->
                        <td style="padding: 14px 16px; vertical-align: middle; text-align: right; font-weight: 600; color: var(--on-surface);">
                            Rp {{ number_format($b->nominal_tagihan, 0, ',', '.') }}
                        </td>

                        <!-- Jumlah Dibayar (Cicilan) -->
                        <td style="padding: 14px 16px; vertical-align: middle; text-align: right; font-weight: 600; color: {{ $b->nominal_dibayar > 0 ? '#059669' : 'var(--outline)' }};">
                            @if($b->nominal_dibayar > 0)
                                Rp {{ number_format($b->nominal_dibayar, 0, ',', '.') }}
                            @else
                                <span style="font-style: italic; color: var(--outline);">Rp 0 (Belum dicicil)</span>
                            @endif
                        </td>

                        <!-- Sisa yang Belum Dibayar -->
                        <td style="padding: 14px 16px; vertical-align: middle; text-align: right; font-weight: 800; color: #dc2626; font-size: 0.95rem;">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>

                        <!-- Status -->
                        <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                            @if($b->status_bayar === 'bayar_sebagian')
                                <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                                    CICIL (SEBAGIAN)
                                </span>
                            @else
                                <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                                    BELUM BAYAR
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
