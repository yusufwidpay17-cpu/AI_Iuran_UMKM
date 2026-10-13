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
        <label for="status_bayar" class="form-label">Status</label>
        <select name="status_bayar" id="status_bayar" class="form-control">
            <option value="">Semua Status</option>
            <option value="lunas" {{ request('status_bayar') == 'lunas' ? 'selected' : '' }}>SUDAH BAYAR</option>
            <option value="bayar_sebagian" {{ request('status_bayar') == 'bayar_sebagian' ? 'selected' : '' }}>CICIL</option>
        </select>
    </div>
    <div style="flex-shrink: 0; display: flex; gap: 6px;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('riwayat.index', ['reset' => 1]) }}'">Reset</button>
    </div>
</form>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No Bukti / Struk</th>
                    <th>Nama Pedagang</th>
                    <th>Nominal Dibayar</th>
                    <th>Status</th>
                    <th style="text-align: right;">Cetak</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $txn)
                    <tr>
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
                        <td>
                            @if ($txn->status_bayar === 'lunas')
                                <span style="background: #dcfce7; color: #15803d; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">LUNAS</span>
                            @elseif ($txn->status_bayar === 'bayar_sebagian')
                                <span style="background: #fef3c7; color: #b45309; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">CICIL</span>
                            @else
                                <span style="background: #fee2e2; color: #b91c1c; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 999px;">BELUM BAYAR</span>
                            @endif
                        </td>
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

<style>
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
