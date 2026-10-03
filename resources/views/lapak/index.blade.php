@extends('layouts.app')

@section('title', 'Data Lapak')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Manajemen Data Lapak Pasar</h2>
        <p class="page-subtitle">Kelola ketersediaan unit kios/lapak, status keterisian, serta alokasi pedagang.</p>
    </div>
    <div style="display: flex; gap: var(--space-sm);">
        <a href="{{ route('export.lapak', request()->query()) }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
        <button type="button" class="btn btn-primary" onclick="openTambahLapakModal()">
            + Tambah Lapak
        </button>
    </div>
</div>

<form action="{{ route('lapak.index') }}" method="GET" class="filter-bar">
    <div class="filter-item" style="flex: 2;">
        <label for="search" class="form-label">Cari Lapak / Pedagang</label>
        <input type="text" name="search" id="search" class="form-control" placeholder="Kode Lapak, Blok, atau Pedagang" value="{{ request('search') }}">
    </div>

    <div class="filter-item">
        <label for="status_lapak" class="form-label">Status Lapak</label>
        <select name="status_lapak" id="status_lapak" class="form-control">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status_lapak') == 'aktif' ? 'selected' : '' }}>Aktif (Terisi)</option>
            <option value="kosong" {{ request('status_lapak') == 'kosong' ? 'selected' : '' }}>Kosong</option>
            <option value="nonaktif" {{ request('status_lapak') == 'nonaktif' ? 'selected' : '' }}>Nonaktif / Rusak</option>
        </select>
    </div>

    <div class="filter-item">
        <label for="blok_kios" class="form-label">Blok Kios</label>
        <select name="blok_kios" id="blok_kios" class="form-control">
            <option value="">Semua Blok</option>
            @foreach($blokList as $b)
                <option value="{{ $b }}" {{ request('blok_kios') == $b ? 'selected' : '' }}>{{ $b }}</option>
            @endforeach
        </select>
    </div>

    <div style="flex-shrink: 0; display: flex; gap: 6px;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('lapak.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center;">Reset</a>
    </div>
</form>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Lapak</th>
                    <th>Blok</th>
                    <th>Nama Pedagang</th>
                    <th>Kategori Usaha</th>
                    <th>Besaran Iuran</th>
                    <th>Status Lapak</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lapaks as $l)
                    <tr>
                        <td class="numeric-display" style="font-weight: 700; color: var(--primary);">{{ $l->kode_lapak }}</td>
                        <td>{{ $l->blok_kios }}</td>
                        <td style="font-weight: 600;">
                            @if ($l->pedagang)
                                <a href="{{ route('pedagang.show', $l->pedagang_id) }}" style="color: var(--primary); text-decoration: none;">
                                    {{ $l->pedagang->nama_pedagang }}
                                </a>
                            @else
                                <span style="color: var(--outline); font-style: italic;">Kosong</span>
                            @endif
                        </td>
                        <td style="text-transform: capitalize;">{{ $l->kategori }}</td>
                        <td class="numeric-display">
                            Rp {{ number_format($l->tarif ? $l->tarif->nominal_harian : ($l->pedagang && $l->pedagang->tarif ? $l->pedagang->tarif->nominal_harian : 0), 0, ',', '.') }}
                        </td>
                        <td>
                            @if ($l->status_lapak === 'aktif')
                                <span class="badge badge-bayar-lunas">Aktif</span>
                            @elseif ($l->status_lapak === 'kosong')
                                <span class="badge badge-bayar-bayar_sebagian">Kosong</span>
                            @else
                                <span class="badge badge-status-nonaktif">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="openEditLapakModal('{{ $l->id }}', '{{ $l->kode_lapak }}', '{{ $l->blok_kios }}', '{{ $l->kategori }}', '{{ $l->status_lapak }}', '{{ $l->pedagang_id }}', '{{ $l->keterangan }}')">
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--outline); padding: var(--space-xl);">Data lapak tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: var(--space-md);">
    {{ $lapaks->links() }}
</div>

<!-- Modal Tambah / Edit Lapak -->
<div class="modal-overlay" id="modal-lapak">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-lapak-title">Tambah Data Lapak</h3>
            <button class="modal-close" onclick="closeLapakModal()">&times;</button>
        </div>
        <form id="form-lapak" method="POST" action="{{ route('lapak.store') }}">
            @csrf
            <input type="hidden" name="_method" id="lapak-method" value="POST">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Kode Lapak / Kios *</label>
                    <input type="text" name="kode_lapak" id="lapak-kode" class="form-input" required placeholder="Contoh: A-01">
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Blok Kios *</label>
                    <select name="blok_kios" id="lapak-blok" class="form-select" required>
                        <option value="Blok A">Blok A</option>
                        <option value="Blok B">Blok B</option>
                        <option value="Blok C">Blok C</option>
                        <option value="Blok D">Blok D</option>
                        <option value="Blok E">Blok E</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Kategori Usaha *</label>
                    <select name="kategori" id="lapak-kategori" class="form-select" required>
                        <option value="kuliner">Kuliner</option>
                        <option value="pakaian">Pakaian</option>
                        <option value="elektronik">Elektronik</option>
                        <option value="sayuran">Sayuran</option>
                        <option value="buah">Buah</option>
                        <option value="sembako">Sembako</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Status Lapak *</label>
                    <select name="status_lapak" id="lapak-status" class="form-select" required>
                        <option value="aktif">Aktif (Terisi)</option>
                        <option value="kosong">Kosong</option>
                        <option value="nonaktif">Nonaktif / Rusak</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" id="lapak-keterangan" class="form-input" placeholder="Opsional (Kondisi lapak)">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: var(--space-sm); justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeLapakModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Lapak</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openTambahLapakModal() {
        document.getElementById('form-lapak').action = "{{ route('lapak.store') }}";
        document.getElementById('lapak-method').value = 'POST';
        document.getElementById('modal-lapak-title').innerText = 'Tambah Data Lapak';
        document.getElementById('lapak-kode').value = '';
        document.getElementById('lapak-keterangan').value = '';
        document.getElementById('modal-lapak').classList.add('active');
    }

    function openEditLapakModal(id, kode, blok, kategori, status, pedagangId, keterangan) {
        document.getElementById('form-lapak').action = '/lapak/' + id;
        document.getElementById('lapak-method').value = 'PUT';
        document.getElementById('modal-lapak-title').innerText = 'Edit Lapak ' + kode;
        document.getElementById('lapak-kode').value = kode;
        document.getElementById('lapak-blok').value = blok;
        document.getElementById('lapak-kategori').value = kategori;
        document.getElementById('lapak-status').value = status;
        document.getElementById('lapak-keterangan').value = keterangan ?? '';
        document.getElementById('modal-lapak').classList.add('active');
    }

    function closeLapakModal() {
        document.getElementById('modal-lapak').classList.remove('active');
    }
</script>
@endsection
