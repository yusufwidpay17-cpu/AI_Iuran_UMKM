@extends('layouts.app')

@section('title', 'Data Pedagang')

@section('content')
<!-- Page Header -->
<div class="page-header" style="align-items: flex-start;">
    <div>
        <h2 class="page-title">Data Pedagang</h2>
        <p class="page-subtitle">Kelola data pedagang UMKM Pesantren & pasar harian</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <!-- Top Search Form -->
        <form action="{{ route('pedagang.index') }}" method="GET" style="display: flex; gap: 8px;">
            <div style="position: relative;">
                <input type="text" name="search" class="form-input" placeholder="Cari pedagang..." value="{{ request('search') }}" style="min-height: 40px; padding-left: 36px; min-width: 220px; font-size: 0.84375rem; border-radius: 8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            @if(request('status_pedagang')) <input type="hidden" name="status_pedagang" value="{{ request('status_pedagang') }}"> @endif
            @if(request('status_legal')) <input type="hidden" name="status_legal" value="{{ request('status_legal') }}"> @endif
            @if(request('blok_kios')) <input type="hidden" name="blok_kios" value="{{ request('blok_kios') }}"> @endif
            @if(request('kategori_usaha')) <input type="hidden" name="kategori_usaha" value="{{ request('kategori_usaha') }}"> @endif
        </form>

        <!-- Tambah Pedagang Button -->
        <a href="{{ route('pedagang.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pedagang
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="padding: 12px 16px; margin-bottom: 20px; background: #ffffff;">
    <form action="{{ route('pedagang.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
        
        <div style="flex: 1; min-width: 150px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Status</label>
            <select name="status_pedagang" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status_pedagang') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="sementara_tutup" {{ request('status_pedagang') == 'sementara_tutup' ? 'selected' : '' }}>Tutup Sementara</option>
                <option value="nonaktif" {{ request('status_pedagang') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Legalitas</label>
            <select name="status_legal" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
                <option value="">Semua Legalitas</option>
                <option value="legal" {{ request('status_legal') == 'legal' ? 'selected' : '' }}>Legal</option>
                <option value="ilegal" {{ request('status_legal') == 'ilegal' ? 'selected' : '' }}>Ilegal</option>
                <option value="proses_izin" {{ request('status_legal') == 'proses_izin' ? 'selected' : '' }}>Proses Izin</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Blok Kios</label>
            <select name="blok_kios" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
                <option value="">Semua Blok</option>
                @foreach($blokList as $blok)
                    <option value="{{ $blok }}" {{ request('blok_kios') == $blok ? 'selected' : '' }}>{{ $blok }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Kategori Usaha</label>
            <select name="kategori_usaha" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
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

        <div style="display: flex; gap: 6px;">
            <button type="submit" class="btn btn-primary btn-sm" style="min-height: 36px; padding: 0 14px;">Filter</button>
            <a href="{{ route('pedagang.index') }}" class="btn btn-secondary btn-sm" style="min-height: 36px; padding: 0 12px; display: inline-flex; align-items: center;">Reset</a>
            <a href="{{ route('export.pedagang', request()->query()) }}" class="btn btn-secondary btn-sm" style="min-height: 36px; padding: 0 12px; display: inline-flex; align-items: center; gap: 4px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </a>
        </div>
    </form>
</div>

<!-- Pedagang Cards Grid (Persis Sesuai Gambar Referensi) -->
<div class="pedagang-grid">
    @forelse($pedagangs as $pdg)
        @php
            // Calculate Initials Avatar
            $words = explode(' ', trim($pdg->nama_pedagang));
            $initials = '';
            if (count($words) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                $initials = strtoupper(substr($pdg->nama_pedagang, 0, 2));
            }

            // Avatar background color palette based on merchant ID
            $colors = ['#854d0e', '#003d9b', '#065f46', '#1e3a8a', '#581c87', '#0284c7', '#374151'];
            $avatarBg = $colors[abs(crc32($pdg->id)) % count($colors)];
        @endphp

        <div class="pedagang-card">
            <div>
                <!-- Top Card Header: Avatar, Name, Shop & Status Badge -->
                <div class="pedagang-card-header">
                    <div class="pedagang-avatar-info">
                        <div class="pedagang-avatar" style="background-color: {{ $avatarBg }};">
                            {{ $initials }}
                        </div>
                        <div class="pedagang-name-group">
                            <div class="pedagang-name" title="{{ $pdg->nama_pedagang }}">
                                {{ $pdg->nama_pedagang }}
                            </div>
                            <div class="pedagang-shop" title="{{ $pdg->nama_usaha ?? 'Lapak Retribusi' }}">
                                {{ $pdg->nama_usaha ?? 'Lapak Retribusi' }}
                            </div>
                        </div>
                    </div>

                    <div>
                        @if($pdg->status_pedagang == 'aktif')
                            <span class="badge badge-status-aktif" style="border-radius: 999px; padding: 4px 10px; font-weight: 600; text-transform: capitalize;">Aktif</span>
                        @elseif($pdg->status_pedagang == 'sementara_tutup')
                            <span class="badge badge-status-sementara_tutup" style="border-radius: 999px; padding: 4px 10px; font-weight: 600; text-transform: capitalize;">Tutup Sementara</span>
                        @else
                            <span class="badge badge-status-nonaktif" style="border-radius: 999px; padding: 4px 10px; font-weight: 600; text-transform: capitalize;">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <!-- Card Details Grid: No Lapak & Kategori -->
                <div class="pedagang-card-details">
                    <div class="pedagang-detail-item">
                        <span class="pedagang-detail-label">NO LAPAK</span>
                        <span class="pedagang-detail-value">{{ $pdg->no_kios ?? '-' }}</span>
                    </div>

                    <div class="pedagang-detail-item">
                        <span class="pedagang-detail-label">KATEGORI</span>
                        <span class="pedagang-detail-value" style="text-transform: capitalize;">{{ $pdg->kategori_usaha ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer: Tarif Sewa & Action Buttons -->
            <div class="pedagang-card-footer">
                <div class="pedagang-detail-item">
                    <span class="pedagang-detail-label">TARIF SEWA</span>
                    <span class="pedagang-detail-value" style="color: var(--primary); font-size: 0.9375rem;">
                        Rp {{ number_format($pdg->tarif->nominal_harian ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                <div style="display: flex; gap: 4px;">
                    <a href="{{ route('pedagang.show', $pdg->id) }}" class="btn btn-secondary btn-sm" title="Lihat Detail">Detail</a>
                    <a href="{{ route('pedagang.edit', $pdg->id) }}" class="btn btn-secondary btn-sm" title="Edit Data">Edit</a>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; background: #ffffff; border: 1px solid var(--outline-variant); border-radius: 12px; padding: 40px; text-align: center; color: var(--outline);">
            Data pedagang tidak ditemukan. Silakan gunakan pencarian lain.
        </div>
    @endforelse
</div>

<!-- Pagination Links -->
<div style="margin-top: 20px; display: flex; justify-content: center;">
    {{ $pedagangs->links() }}
</div>
@endsection
