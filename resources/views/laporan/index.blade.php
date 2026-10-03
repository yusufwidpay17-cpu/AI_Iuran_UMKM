@extends('layouts.app')

@section('title', 'Laporan Operational & Keuangan')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Laporan Keuangan & Operasional</h2>
        <p class="page-subtitle">Rekapitulasi laporan iuran harian, mingguan, bulanan, pembayaran, dan tunggakan.</p>
    </div>
    <div style="display: flex; gap: var(--space-sm);">
        <a href="{{ route('export.laporan', request()->query()) }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('laporan.cetak', request()->query()) }}" target="_blank" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak Laporan
        </a>
    </div>
</div>

<!-- FILTER BAR -->
<form action="{{ route('laporan.index') }}" method="GET" class="filter-bar">
    <div class="filter-item">
        <label for="jenis" class="form-label">Jenis Laporan</label>
        <select name="jenis" id="jenis" class="form-control">
            <option value="harian" {{ $jenisLaporan == 'harian' ? 'selected' : '' }}>Laporan Iuran Harian</option>
            <option value="mingguan" {{ $jenisLaporan == 'mingguan' ? 'selected' : '' }}>Laporan Iuran Mingguan</option>
            <option value="bulanan" {{ $jenisLaporan == 'bulanan' ? 'selected' : '' }}>Laporan Iuran Bulanan</option>
            <option value="pembayaran" {{ $jenisLaporan == 'pembayaran' ? 'selected' : '' }}>Laporan Pembayaran</option>
            <option value="tunggakan" {{ $jenisLaporan == 'tunggakan' ? 'selected' : '' }}>Laporan Tunggakan</option>
        </select>
    </div>

    <div class="filter-item">
        <label for="start_date" class="form-label">Dari Tanggal</label>
        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
    </div>

    <div class="filter-item">
        <label for="end_date" class="form-label">Sampai Tanggal</label>
        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
    </div>

    <div class="filter-item">
        <label for="pedagang_id" class="form-label">Pedagang</label>
        <select name="pedagang_id" id="pedagang_id" class="form-control">
            <option value="">Semua Pedagang</option>
            @foreach($pedagangs as $p)
                <option value="{{ $p->id }}" {{ $pedagangId == $p->id ? 'selected' : '' }}>{{ $p->no_kios }} - {{ $p->nama_pedagang }}</option>
            @endforeach
        </select>
    </div>

    <div class="filter-item">
        <label for="status_bayar" class="form-label">Status Bayar</label>
        <select name="status_bayar" id="status_bayar" class="form-control">
            <option value="">Semua Status</option>
            <option value="lunas" {{ $statusBayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="bayar_sebagian" {{ $statusBayar == 'bayar_sebagian' ? 'selected' : '' }}>Bayar Sebagian</option>
            <option value="belum_bayar" {{ $statusBayar == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
        </select>
    </div>

    <div style="flex-shrink: 0; display: flex; gap: 6px;">
        <button type="submit" class="btn btn-primary">Tampilkan</button>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center;">Reset</a>
    </div>
</form>

<!-- REPORT SUMMARY -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: var(--space-lg);">
    <div class="stat-card">
        <div class="stat-label">Total Nominal Tagihan</div>
        <div class="stat-value numeric-display">Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</div>
        <div class="stat-sub">{{ $summary['jumlah_transaksi'] }} Records</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Realisasi Pembayaran</div>
        <div class="stat-value numeric-display" style="color: var(--secondary);">Rp {{ number_format($summary['total_dibayar'], 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--secondary);">{{ $summary['lunas_count'] }} Lunas</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Tunggakan / Piutang</div>
        <div class="stat-value numeric-display" style="color: var(--error);">Rp {{ number_format($summary['total_tunggakan'], 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--error);">{{ $summary['belum_count'] }} Belum Bayar</div>
    </div>
</div>

<!-- REPORT TABLE -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No Kios</th>
                    <th>Nama Pedagang</th>
                    <th>Jenis Iuran</th>
                    <th>Target Tagihan</th>
                    <th>Realisasi Bayar</th>
                    <th>Sisa Tunggakan</th>
                    <th>Status</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tagihans as $tgh)
                    @php $sisa = $tgh->nominal_tagihan - $tgh->nominal_dibayar; @endphp
                    <tr>
                        <td class="numeric-display">{{ $tgh->tanggal_tagihan ? $tgh->tanggal_tagihan->format('d/m/Y') : '-' }}</td>
                        <td class="numeric-display" style="font-weight: 700;">{{ $tgh->pedagang->no_kios ?? '-' }}</td>
                        <td style="font-weight: 600;">
                            @if ($tgh->pedagang)
                                <a href="{{ route('pedagang.show', $tgh->pedagang_id) }}" style="color: var(--primary); text-decoration: none;">
                                    {{ $tgh->pedagang->nama_pedagang }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $tgh->jenis_iuran ?? 'Iuran Harian' }}</td>
                        <td class="numeric-display">Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}</td>
                        <td class="numeric-display" style="color: var(--secondary); font-weight: 700;">Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}</td>
                        <td class="numeric-display" style="color: var(--error); font-weight: 700;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        <td><span class="badge badge-bayar-{{ $tgh->status_bayar }}">{{ str_replace('_', ' ', $tgh->status_bayar) }}</span></td>
                        <td>{{ $tgh->metode_bayar ? strtoupper($tgh->metode_bayar) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--outline); padding: var(--space-xl);">Data laporan tidak ditemukan untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
