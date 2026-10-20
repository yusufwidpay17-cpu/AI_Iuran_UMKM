@extends('layouts.app')

@section('title', 'Notifikasi & Pengumuman - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span>Notifikasi & Pengumuman Lapak</span>
        </h2>
        <p class="page-subtitle" style="margin: 4px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Pemberitahuan resmi terkait status tagihan, konfirmasi pembayaran, dan informasi pasar (Kios {{ $pedagang->no_kios ?? '-' }}).
        </p>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 14px; max-width: 850px;">
    <!-- 1. Notifikasi Status Hari Ini -->
    @if($isBebasIuranHariIni)
        <div class="card" style="padding: 16px; border-radius: 12px; border: 1.5px solid #a7f3d0; background: #ecfdf5; display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                    <strong style="color: #065f46; font-size: 0.95rem;">Hari Ini Bebas Iuran (Penagihan Libur)</strong>
                    <span style="font-size: 0.75rem; color: #047857; font-weight: 700;">Pengumuman Resmi</span>
                </div>
                <p style="margin: 4px 0 0; font-size: 0.85rem; color: #047857; line-height: 1.5;">
                    Berdasarkan kalender operasional pasar, hari ini iuran harian ditiadakan sehubungan dengan <strong>{{ $operasionalHariIni->kategori_label ?? 'Agenda Pasar' }} ({{ $operasionalHariIni->alasan ?? '-' }})</strong>. Kios Anda bebas dari beban retribusi untuk tanggal ini.
                </p>
            </div>
        </div>
    @elseif($tagihanHariIni && $tagihanHariIni->status_bayar === 'lunas')
        <div class="card" style="padding: 16px; border-radius: 12px; border: 1.5px solid #a7f3d0; background: #f0fdf4; display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                    <strong style="color: #065f46; font-size: 0.95rem;">Iuran Hari Ini Telah Lunas Terbayar</strong>
                    <span style="font-size: 0.75rem; color: #047857;">Hari Ini</span>
                </div>
                <p style="margin: 4px 0 8px; font-size: 0.85rem; color: #047857; line-height: 1.5;">
                    Pembayaran iuran harian sebesar <strong>Rp {{ number_format($tagihanHariIni->nominal_dibayar, 0, ',', '.') }}</strong> telah diterima oleh petugas <em>{{ $tagihanHariIni->petugas->nama ?? 'Kasir Pasar' }}</em> dengan No. Bukti <code>{{ $tagihanHariIni->no_bukti }}</code>.
                </p>
                <a href="{{ route('riwayat.cetak', $tagihanHariIni->id) }}" target="_blank" class="btn btn-sm btn-primary" style="background: #059669; font-size: 0.775rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 5px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z M7 9V5a2 2 0 012-2h6a2 2 0 012 2v4"></path>
                    </svg>
                    <span>Cetak Kuitansi Pembayaran</span>
                </a>
            </div>
        </div>
    @elseif($tagihanHariIni)
        <div class="card" style="padding: 16px; border-radius: 12px; border: 1.5px solid #fed7aa; background: #fffaf0; display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #f97316; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                    <strong style="color: #9a3412; font-size: 0.95rem;">Tagihan Iuran Hari Ini Menunggu Pembayaran</strong>
                    <span style="font-size: 0.75rem; color: #c2410c;">Hari Ini</span>
                </div>
                <p style="margin: 4px 0 0; font-size: 0.85rem; color: #9a3412; line-height: 1.5;">
                    Tagihan iuran harian kios Anda sebesar <strong>Rp {{ number_format($tagihanHariIni->nominal_tagihan, 0, ',', '.') }}</strong> untuk tanggal {{ date('d M Y') }} siap ditagihkan. Harap siapkan uang pas saat petugas melakukan penarikan keliling.
                </p>
            </div>
        </div>
    @else
        <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--surface-container-high); color: var(--on-surface); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18l-1 9H4L3 3z M4 12v8a1 1 0 001 1h14a1 1 0 001-1v-8 M10 21V15h4v6"></path>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                    <strong style="color: var(--on-surface); font-size: 0.95rem;">Jadwal Penarikan Iuran Harian Kios</strong>
                    <span style="font-size: 0.75rem; color: var(--on-surface-variant);">Pagi - Siang</span>
                </div>
                <p style="margin: 4px 0 0; font-size: 0.85rem; color: var(--on-surface-variant); line-height: 1.5;">
                    Petugas lapangan melakukan penarikan iuran harian secara bergilir mulai pukul 08:30 WIB. Tarif harian resmi untuk kios {{ $pedagang->no_kios }} adalah <strong>Rp {{ number_format($pedagang->tarif->nominal_harian ?? 0, 0, ',', '.') }}</strong>.
                </p>
            </div>
        </div>
    @endif

    <!-- 2. Notifikasi Tunggakan (Jika Ada) -->
    @if($totalTunggakan > 0)
        <div class="card" style="padding: 16px; border-radius: 12px; border: 1.5px solid #fecaca; background: #fef2f2; display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #ef4444; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                    <strong style="color: #991b1b; font-size: 0.95rem;">Pemberitahuan Tunggakan Iuran Tertunda</strong>
                    <span style="font-size: 0.75rem; color: #b91c1c;">Perlu Tindakan</span>
                </div>
                <p style="margin: 4px 0 8px; font-size: 0.85rem; color: #991b1b; line-height: 1.5;">
                    Tercatat total tunggakan iuran tertunda sebesar <strong>Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</strong> dari {{ $unpaidBills->count() }} hari iuran. Anda dapat mencicil atau melunasi langsung kepada petugas penarik pasar.
                </p>
                <a href="{{ route('pedagang.tunggakan') }}" class="btn btn-sm btn-danger" style="background: #dc2626; font-size: 0.775rem; padding: 4px 10px;">
                    Lihat Rincian Tunggakan →
                </a>
            </div>
        </div>
    @endif

    <!-- 3. Notifikasi Pembayaran Terakhir Berhasil -->
    @foreach($pembayaranTerakhir as $p)
        <div class="card" style="padding: 14px 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--on-surface);">Pembayaran Diterima: Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}</span>
                    <span style="font-size: 0.75rem; color: var(--on-surface-variant);">
                        {{ $p->waktu_bayar ? \Carbon\Carbon::parse($p->waktu_bayar)->locale('id')->isoFormat('D MMM Y, HH:mm') : \Carbon\Carbon::parse($p->tanggal_tagihan)->format('d/m/Y') }}
                    </span>
                </div>
                <div style="font-size: 0.8rem; color: var(--on-surface-variant); margin-top: 2px;">
                    Metode: <strong style="text-transform: uppercase;">{{ $p->metode_bayar ?: 'Tunai' }}</strong> | No. Kuitansi: <code>{{ $p->no_bukti ?: '-' }}</code> | Petugas: {{ $p->petugas->nama ?? 'Kasir' }}
                </div>
            </div>
        </div>
    @endforeach

    <!-- 4. Pengumuman Manajemen Pasar -->
    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid #bfdbfe; background: #eff6ff; display: flex; gap: 14px; align-items: flex-start;">
        <div style="width: 40px; height: 40px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
        </div>
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                <strong style="color: #1e40af; font-size: 0.95rem;">Pengumuman Kebersihan & Ketertiban Pasar Pesantren</strong>
                <span style="font-size: 0.75rem; color: #2563eb;">Informasi BUMPes</span>
            </div>
            <p style="margin: 4px 0 0; font-size: 0.85rem; color: #1e40af; line-height: 1.5;">
                Dihimbau kepada seluruh mitra pedagang pasar untuk senantiasa menjaga kebersihan lapak masing-masing, membuang sampah pada tempat yang disediakan, serta mengamankan kios sebelum jam operasional pasar ditutup pada pukul 17:00 WIB.
            </p>
        </div>
    </div>
</div>
@endsection
