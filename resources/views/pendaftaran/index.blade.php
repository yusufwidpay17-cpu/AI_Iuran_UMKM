@extends('layouts.app')

@section('title', 'Pendaftaran Pedagang')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Pendaftaran Pedagang Baru</h2>
        <p class="page-subtitle">Kelola verifikasi dan persetujuan calon pedagang pasar.</p>
    </div>
    <div style="display: flex; gap: var(--space-sm);">
        <a href="{{ route('export.pendaftaran', request()->query()) }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('pendaftaran.create') }}" class="btn btn-primary">
            + Ajukan Pendaftaran
        </a>
    </div>
</div>

<form action="{{ route('pendaftaran.index') }}" method="GET" class="filter-bar">
    <div class="filter-item" style="flex: 2;">
        <label for="search" class="form-label">Cari Pendaftaran</label>
        <input type="text" name="search" id="search" class="form-control" placeholder="Nama, KTP, Kios" value="{{ request('search') }}">
    </div>
    <div class="filter-item">
        <label for="status" class="form-label">Status Pendaftaran</label>
        <select name="status" id="status" class="form-control">
            <option value="">Semua Status</option>
            <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
    </div>
    <div style="flex-shrink: 0; display: flex; gap: 6px;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center;">Reset</a>
    </div>
</form>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Calon Pedagang</th>
                    <th>Kontak / KTP</th>
                    <th>Usaha / Kios</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendaftarans as $p)
                    <tr>
                        <td style="font-weight: 600;">{{ $p->nama_calon }}</td>
                        <td>
                            <div>{{ $p->no_telepon ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--outline);" class="font-monospace">{{ $p->no_ktp }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $p->nama_usaha ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--outline);">Kios {{ $p->no_kios }} ({{ $p->blok_kios }})</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_daftar)->format('d/m/Y') }}</td>
                        <td>
                            @if ($p->status_pendaftaran === 'menunggu_verifikasi')
                                <span class="badge badge-bayar-bayar_sebagian">Menunggu Verifikasi</span>
                            @elseif ($p->status_pendaftaran === 'disetujui')
                                <span class="badge badge-bayar-lunas">Disetujui</span>
                            @else
                                <span class="badge badge-status-nonaktif">Ditolak</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 4px; justify-content: flex-end;">
                                @if ($p->status_pendaftaran === 'menunggu_verifikasi')
                                    <form action="{{ route('pendaftaran.setujui', $p->id) }}" method="POST" onsubmit="return confirm('Setujui pendaftaran ini dan buat pedagang aktif?');">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Setujui</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="openTolakModal('{{ $p->id }}', '{{ $p->nama_calon }}')">Tolak</button>
                                @endif
                                <a href="{{ route('pendaftaran.show', $p->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--outline); padding: var(--space-xl);">Belum ada data pendaftaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: var(--space-md);">
    {{ $pendaftarans->links() }}
</div>

<!-- Modal Tolak Pendaftaran -->
<div class="modal-overlay" id="modal-tolak">
    <div class="modal-container" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title">Tolak Pendaftaran</h3>
            <button class="modal-close" onclick="closeTolakModal()">&times;</button>
        </div>
        <form id="form-tolak" method="POST">
            @csrf
            <div class="modal-body">
                <p style="font-size: 0.875rem; margin-bottom: var(--space-md);">Tolak pendaftaran atas nama <strong id="tolak-nama"></strong>?</p>
                <div class="form-group">
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea name="alasan" class="form-input" rows="3" placeholder="Masukkan alasan..." required></textarea>
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: var(--space-sm); justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeTolakModal()">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Pendaftaran</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openTolakModal(id, nama) {
        document.getElementById('form-tolak').action = '/pendaftaran/' + id + '/tolak';
        document.getElementById('tolak-nama').innerText = nama;
        document.getElementById('modal-tolak').classList.add('active');
    }
    function closeTolakModal() {
        document.getElementById('modal-tolak').classList.remove('active');
    }
</script>
@endsection
