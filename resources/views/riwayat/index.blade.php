@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Riwayat Transaksi Pembayaran</h2>
        <p class="page-subtitle">Pencatatan seluruh transaksi pembayaran iuran pedagang pasar.</p>
    </div>
    <div>
        <a href="{{ route('export.riwayat', request()->query()) }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
    </div>
</div>

<form action="{{ route('riwayat.index') }}" method="GET" class="filter-bar">
    <div class="filter-item" style="flex: 2;">
        <label for="search" class="form-label">Cari Struk / Pedagang</label>
        <input type="text" name="search" id="search" class="form-control" placeholder="No Struk, Kios, atau Pedagang" value="{{ request('search') }}">
    </div>

    <div class="filter-item">
        <label for="start_date" class="form-label">Dari Tanggal</label>
        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>

    <div class="filter-item">
        <label for="end_date" class="form-label">Sampai Tanggal</label>
        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>

    <div class="filter-item">
        <label for="metode_bayar" class="form-label">Metode Bayar</label>
        <select name="metode_bayar" id="metode_bayar" class="form-control">
            <option value="">Semua Metode</option>
            <option value="tunai" {{ request('metode_bayar') == 'tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="qris" {{ request('metode_bayar') == 'qris' ? 'selected' : '' }}>QRIS</option>
            <option value="transfer" {{ request('metode_bayar') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
        </select>
    </div>

    <div style="flex-shrink: 0; display: flex; gap: 6px;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('riwayat.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center;">Reset</a>
    </div>
</form>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu Pembayaran</th>
                    <th>No Bukti / Struk</th>
                    <th>Nama Pedagang</th>
                    <th>Nominal Dibayar</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Petugas</th>
                    <th style="text-align: right;">Cetak</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $txn)
                    <tr>
                        <td class="numeric-display">{{ $txn->waktu_bayar ? $txn->waktu_bayar->format('d/m/Y H:i') : '-' }}</td>
                        <td class="font-monospace" style="font-weight: 700;">{{ $txn->no_bukti ?? '-' }}</td>
                        <td style="font-weight: 600;">
                            @if ($txn->pedagang)
                                <a href="{{ route('pedagang.show', $txn->pedagang_id) }}" style="color: var(--primary); text-decoration: none;">
                                    {{ $txn->pedagang->nama_pedagang }} ({{ $txn->pedagang->no_kios }})
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="numeric-display" style="color: var(--secondary); font-weight: 700;">Rp {{ number_format($txn->nominal_dibayar, 0, ',', '.') }}</td>
                        <td><span class="badge badge-bayar-lunas">{{ strtoupper($txn->metode_bayar ?? 'TUNAI') }}</span></td>
                        <td><span class="badge badge-bayar-{{ $txn->status_bayar }}">{{ str_replace('_', ' ', $txn->status_bayar) }}</span></td>
                        <td>{{ $txn->petugas->nama ?? 'Sistem' }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('riwayat.cetak', $txn->id) }}" target="_blank" class="btn btn-secondary btn-sm">
                                Nota Kuitansi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--outline); padding: var(--space-xl);">Belum ada riwayat transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: var(--space-md);">
    {{ $transaksis->links() }}
</div>
@endsection
