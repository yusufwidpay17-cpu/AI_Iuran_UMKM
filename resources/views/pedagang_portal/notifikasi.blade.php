@extends('layouts.app')

@section('title', 'Notifikasi & Pengumuman - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <span>🔔</span> Notifikasi & Pengumuman Lapak
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
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                📅
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
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                ✓
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                    <strong style="color: #065f46; font-size: 0.95rem;">Iuran Hari Ini Telah Lunas Terbayar</strong>
                    <span style="font-size: 0.75rem; color: #047857;">Hari Ini</span>
                </div>
                <p style="margin: 4px 0 8px; font-size: 0.85rem; color: #047857; line-height: 1.5;">
                    Pembayaran iuran harian sebesar <strong>Rp {{ number_format($tagihanHariIni->nominal_dibayar, 0, ',', '.') }}</strong> telah diterima oleh petugas <em>{{ $tagihanHariIni->petugas->nama ?? 'Kasir Pasar' }}</em> dengan No. Bukti <code>{{ $tagihanHariIni->no_bukti }}</code>.
                </p>
                <a href="{{ route('riwayat.cetak', $tagihanHariIni->id) }}" target="_blank" class="btn btn-sm btn-primary" style="background: #059669; font-size: 0.775rem; padding: 4px 10px;">
                    🖨️ Cetak Kuitansi Pembayaran
                </a>
            </div>
        </div>
    @elseif($tagihanHariIni)
        <div class="card" style="padding: 16px; border-radius: 12px; border: 1.5px solid #fed7aa; background: #fffaf0; display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #f97316; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                ⏳
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
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--surface-container-high); color: var(--on-surface); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                🏪
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
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #ef4444; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                ⚠️
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
            <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                💳
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
        <div style="width: 40px; height: 40px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
            📢
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
