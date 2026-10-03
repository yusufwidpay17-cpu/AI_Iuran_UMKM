@extends('layouts.app')

@section('title', 'Detail Pendaftaran Calon Pedagang')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Detail Pendaftaran: {{ $pendaftaran->nama_calon }}</h2>
        <p class="page-subtitle">Status: 
            @if ($pendaftaran->status_pendaftaran === 'menunggu_verifikasi')
                <span class="badge badge-bayar-bayar_sebagian">Menunggu Verifikasi</span>
            @elseif ($pendaftaran->status_pendaftaran === 'disetujui')
                <span class="badge badge-bayar-lunas">Disetujui</span>
            @else
                <span class="badge badge-status-nonaktif">Ditolak</span>
            @endif
        </p>
    </div>
    <div style="display: flex; gap: var(--space-sm);">
        <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary">Kembali</a>
        @if ($pendaftaran->status_pendaftaran === 'menunggu_verifikasi')
            <form action="{{ route('pendaftaran.setujui', $pendaftaran->id) }}" method="POST" onsubmit="return confirm('Setujui pendaftaran ini?');">
                @csrf
                <button type="submit" class="btn btn-primary">Setujui Pendaftaran</button>
            </form>
        @endif
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h3 class="card-title" style="margin-bottom: var(--space-md);">Informasi Calon Pedagang</h3>
    
    <div class="profile-details-list">
        <div class="profile-details-item">
            <span class="label">Nama Calon</span>
            <span class="value" style="font-weight: 700;">{{ $pendaftaran->nama_calon }}</span>
        </div>
        <div class="profile-details-item">
            <span class="label">No KTP</span>
            <span class="value font-monospace">{{ $pendaftaran->no_ktp }}</span>
        </div>
        <div class="profile-details-item">
            <span class="label">No HP</span>
            <span class="value">{{ $pendaftaran->no_telepon ?? '-' }}</span>
        </div>
        <div class="profile-details-item">
            <span class="label">Nama Usaha</span>
            <span class="value">{{ $pendaftaran->nama_usaha ?? '-' }}</span>
        </div>
        <div class="profile-details-item">
            <span class="label">Kategori Usaha</span>
            <span class="value" style="text-transform: capitalize;">{{ $pendaftaran->kategori_usaha }}</span>
        </div>
        <div class="profile-details-item">
            <span class="label">Rencana Kios</span>
            <span class="value font-monospace" style="font-weight: 700;">Kios {{ $pendaftaran->no_kios }} ({{ $pendaftaran->blok_kios }})</span>
        </div>
        <div class="profile-details-item">
            <span class="label">Tarif Sewa</span>
            <span class="value numeric-display">Rp {{ number_format($pendaftaran->tarif->nominal_harian, 0, ',', '.') }}/hari</span>
        </div>
        <div class="profile-details-item">
            <span class="label">Tanggal Pendaftaran</span>
            <span class="value">{{ \Carbon\Carbon::parse($pendaftaran->tanggal_daftar)->isoFormat('D MMMM YYYY') }}</span>
        </div>
    </div>

    @if ($pendaftaran->catatan)
        <div style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px dashed var(--outline-variant);">
            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--outline);">Catatan</div>
            <div style="font-size: 0.9375rem; color: var(--on-surface-variant); margin-top: 4px;">{{ $pendaftaran->catatan }}</div>
        </div>
    @endif
</div>
@endsection
