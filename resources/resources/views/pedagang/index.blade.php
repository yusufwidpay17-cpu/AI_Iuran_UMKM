@extends('layouts.app')

@section('title', 'Daftar Pedagang')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Manajemen Data Pedagang</h2>
        <p class="page-subtitle">Kelola seluruh data pedagang pasar, status legalitas, serta tarif sewa harian.</p>
    </div>
    <div style="display: flex; gap: var(--space-sm);">
        <a href="{{ route('export.pedagang', request()->query()) }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('pedagang.create') }}" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pedagang
        </a>
    </div>
</div>

<!-- Filter Bar -->
<form action="{{ route('pedagang.index') }}" method="GET" class="filter-bar">
    <div class="filter-item" style="flex: 2; min-width: 250px;">
        <label for="search" class="form-label">Cari Pedagang</label>
        <input type="text" name="search" id="search" class="form-control" placeholder="Nama, No Kios, atau Usaha" value="{{ request('search') }}">
    </div>
    
    <div class="filter-item">
        <label for="status_pedagang" class="form-label">Status Pedagang</label>
        <select name="status_pedagang" id="status_pedagang" class="form-control" style="min-height: 48px;">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status_pedagang') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status_pedagang') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            <option value="sementara_tutup" {{ request('status_pedagang') == 'sementara_tutup' ? 'selected' : '' }}>Sementara Tutup</option>
        </select>
    </div>

    <div class="filter-item">
        <label for="status_legal" class="form-label">Legalitas</label>
        <select name="status_legal" id="status_legal" class="form-control" style="min-height: 48px;">
            <option value="">Semua Legalitas</option>
            <option value="legal" {{ request('status_legal') == 'legal' ? 'selected' : '' }}>Legal</option>
            <option value="ilegal" {{ request('status_legal') == 'ilegal' ? 'selected' : '' }}>Ilegal</option>
            <option value="proses_izin" {{ request('status_legal') == 'proses_izin' ? 'selected' : '' }}>Proses Izin</option>
        </select>
    </div>

    <div class="filter-item">
        <label for="blok_kios" class="form-label">Blok Kios</label>
        <select name="blok_kios" id="blok_kios" class="form-control" style="min-height: 48px;">
            <option value="">Semua Blok</option>
            @foreach($blokList as $blok)
                <option value="{{ $blok }}" {{ request('blok_kios') == $blok ? 'selected' : '' }}>{{ $blok }}</option>
            @endforeach
        </select>
    </div>

    <div class="filter-item">
        <label for="kategori_usaha" class="form-label">Kategori Usaha</label>
        <select name="kategori_usaha" id="kategori_usaha" class="form-control" style="min-height: 48px;">
            <option value="">Semua Kategori</option>
            <option value="kuliner" {{ request('kategori_usaha') == 'kuliner' ? 'selected' : '' }}>Kuliner</option>
            <option value="pakaian" {{ request('kategori_usaha') == 'pakaian' ? 'selected' : '' }}>Pakaian</option>
            <option value="elektronik" {{ request('kategori_usaha') == 'elektronik' ? 'selected' : '' }}>Elektronik</option>
            <option value="sayuran" {{ request('kategori_usaha') == 'sayuran' ? 'selected' : '' }}>Sayuran</option>
            <option value="buah" {{ request('kategori_usaha') == 'buah' ? 'selected' : '' }}>Buah</option>
            <option value="sembako" {{ request('kategori_usaha') == 'sembako' ? 'selected' : '' }}>Sembako</option>
            <option value="lainnya" {{ request('kategori_usaha') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
    </div>

    <div style="flex-shrink: 0; display: flex; gap: var(--space-sm);">
        <button type="submit" class="btn btn-primary" style="height: 48px;">Filter</button>
        <a href="{{ route('pedagang.index') }}" class="btn btn-secondary" style="height: 48px; display: inline-flex; align-items: center;">Reset</a>
    </div>
</form>

<!-- Table Card -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No Kios</th>
                    <th>Nama Pedagang</th>
                    <th>Nama Usaha / Kategori</th>
                    <th>Status</th>
                    <th>Legalitas</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedagangs as $pdg)
                    <tr>
                        <td class="numeric-display" style="font-weight: 700;">{{ $pdg->no_kios }}</td>
                        <td>
                            <div style="font-weight: 600;">
                                <a href="{{ route('pedagang.show', $pdg->id) }}" style="color: var(--primary); text-decoration: none;">
                                    {{ $pdg->nama_pedagang }}
                                </a>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--outline);">{{ $pdg->no_ktp }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $pdg->nama_usaha ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--outline); text-transform: capitalize;">{{ $pdg->kategori_usaha }}</div>
                        </td>
                        <td>
                            <span class="badge badge-status-{{ $pdg->status_pedagang }}">
                                {{ str_replace('_', ' ', $pdg->status_pedagang) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-legal-{{ $pdg->status_legal }}">
                                {{ str_replace('_', ' ', $pdg->status_legal) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: var(--space-xs);">
                                <a href="{{ route('pedagang.show', $pdg->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                                <a href="{{ route('pedagang.edit', $pdg->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                @if($pdg->status_pedagang !== 'nonaktif')
                                    <form action="{{ route('pedagang.destroy', $pdg->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan pedagang ini?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="min-height: 32px; padding: 0.25rem 0.75rem;">Nonaktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--outline); padding: var(--space-xl);">
                            Data pedagang tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Links -->
<div style="margin-top: var(--space-md);">
    {{ $pedagangs->links() }}
</div>
@endsection
