<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Pembayaran - {{ $tagihan->no_bukti }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 320px; margin: 0 auto; padding: 20px 10px; color: #000; background: #fff; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .header { border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .footer { border-top: 1px dashed #000; padding-top: 10px; margin-top: 15px; text-align: center; font-size: 12px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 13px; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 14px; cursor: pointer;">Cetak Kuitansi</button>
        <button onclick="window.close()" style="padding: 8px 16px; font-size: 14px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header text-center">
        <div class="bold" style="font-size: 16px;">AMANAH LEDGER</div>
        <div style="font-size: 12px;">PENGELOLAAN IURAN UMKM PASAR</div>
        <div style="font-size: 11px;">Bukti Pembayaran Resmi</div>
    </div>

    <div class="row">
        <span>No Struk:</span>
        <span class="bold">{{ $tagihan->no_bukti }}</span>
    </div>
    <div class="row">
        <span>Tanggal:</span>
        <span>{{ $tagihan->waktu_bayar ? $tagihan->waktu_bayar->format('d/m/Y H:i') : '-' }}</span>
    </div>
    <div class="row">
        <span>Petugas:</span>
        <span>{{ $tagihan->petugas->nama ?? 'Sistem' }}</span>
    </div>

    <div class="divider"></div>

    <div class="row">
        <span>Pedagang:</span>
        <span class="bold">{{ $tagihan->pedagang->nama_pedagang ?? '-' }}</span>
    </div>
    <div class="row">
        <span>No Kios:</span>
        <span>{{ $tagihan->pedagang->no_kios ?? '-' }} ({{ $tagihan->pedagang->blok_kios ?? '-' }})</span>
    </div>
    <div class="row">
        <span>Jenis Iuran:</span>
        <span>{{ $tagihan->jenis_iuran ?? 'Iuran Harian' }}</span>
    </div>

    <div class="divider"></div>

    <div class="row">
        <span>Nominal Tagihan:</span>
        <span>Rp {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}</span>
    </div>
    <div class="row bold" style="font-size: 15px;">
        <span>DIBAYAR:</span>
        <span>Rp {{ number_format($tagihan->nominal_dibayar, 0, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Status Bayar:</span>
        <span class="bold">{{ strtoupper(str_replace('_', ' ', $tagihan->status_bayar)) }}</span>
    </div>
    <div class="row">
        <span>Metode:</span>
        <span>{{ strtoupper($tagihan->metode_bayar ?? 'TUNAI') }}</span>
    </div>

    @if ($tagihan->catatan)
        <div class="divider"></div>
        <div style="font-size: 11px; font-style: italic;">
            Catatan: {{ $tagihan->catatan }}
        </div>
    @endif

    <div class="footer">
        <div>Terima kasih atas partisipasi Anda</div>
        <div>Membangun Pasar UMKM Maju & Berkah</div>
    </div>
</body>
</html>
