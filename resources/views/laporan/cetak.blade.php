<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan - AMANAH LEDGER</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; color: #000; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 12px; color: #444; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .summary-box { display: flex; justify-content: space-between; margin-top: 15px; margin-bottom: 15px; background: #f9f9f9; padding: 10px; border: 1px solid #ccc; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 14px; cursor: pointer;">Cetak Laporan</button>
        <button onclick="window.close()" style="padding: 8px 16px; font-size: 14px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header">
        <div class="title">REKAPITULASI LAPORAN IURAN PASAR UMKM</div>
        <div class="subtitle">AMANAH LEDGER - NUSANTARA SME CORE</div>
        <div style="font-size: 11px; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} | Jenis: {{ strtoupper($jenisLaporan) }}</div>
    </div>

    <div class="summary-box">
        <div><strong>Total Target Tagihan:</strong> Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</div>
        <div><strong>Total Realisasi Bayar:</strong> Rp {{ number_format($summary['total_dibayar'], 0, ',', '.') }}</div>
        <div><strong>Total Sisa Tunggakan:</strong> Rp {{ number_format($summary['total_tunggakan'], 0, ',', '.') }}</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kios</th>
                <th>Nama Pedagang</th>
                <th>Jenis Iuran</th>
                <th class="text-right">Tagihan (Rp)</th>
                <th class="text-right">Dibayar (Rp)</th>
                <th class="text-right">Tunggakan (Rp)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tagihans as $index => $tgh)
                @php $sisa = $tgh->nominal_tagihan - $tgh->nominal_dibayar; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tgh->tanggal_tagihan ? $tgh->tanggal_tagihan->format('d/m/Y') : '-' }}</td>
                    <td>{{ $tgh->pedagang->no_kios ?? '-' }}</td>
                    <td>{{ $tgh->pedagang->nama_pedagang ?? '-' }}</td>
                    <td>{{ $tgh->jenis_iuran ?? 'Iuran Harian' }}</td>
                    <td class="text-right">{{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sisa, 0, ',', '.') }}</td>
                    <td>{{ strtoupper(str_replace('_', ' ', $tgh->status_bayar)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; display: flex; justify-content: space-between;">
        <div style="text-align: center;">
            <p>Mengetahui,</p>
            <br><br><br>
            <p>____________________<br>Kepala Pasar / Admin</p>
        </div>
        <div style="text-align: center;">
            <p>Dicetak Pada: {{ date('d/m/Y H:i') }}</p>
            <br><br><br>
            <p>____________________<br>Petugas Operasional</p>
        </div>
    </div>
</body>
</html>
