@extends('layouts.app')

@section('title', 'Form Pendaftaran Pedagang')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Form Pendaftaran Calon Pedagang</h2>
        <p class="page-subtitle">Isi formulir data calon pedagang baru untuk diproses verifikasi.</p>
    </div>
    <div>
        <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('pendaftaran.store') }}" method="POST">
        @csrf
        
        <div class="grid-2">
            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Nama Calon Pedagang *</label>
                <input type="text" name="nama_calon" class="form-input" required placeholder="Contoh: Ahmad Subagyo" value="{{ old('nama_calon') }}">
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Nomor KTP (NIK) *</label>
                <input type="text" name="no_ktp" class="form-input" required maxlength="20" placeholder="16 digit NIK" value="{{ old('no_ktp') }}">
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Nomor WhatsApp / HP</label>
                <input type="text" name="no_telepon" class="form-input" placeholder="08xxxxxxxxxx" value="{{ old('no_telepon') }}">
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Nama Usaha / Toko</label>
                <input type="text" name="nama_usaha" class="form-input" placeholder="Contoh: Toko Berkah Jaya" value="{{ old('nama_usaha') }}">
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Kategori Usaha *</label>
                <select name="kategori_usaha" class="form-select" required>
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
                <label class="form-label">Blok Kios *</label>
                <select name="blok_kios" class="form-select" required>
                    <option value="Blok A">Blok A</option>
                    <option value="Blok B">Blok B</option>
                    <option value="Blok C">Blok C</option>
                    <option value="Blok D">Blok D</option>
                    <option value="Blok E">Blok E</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Kode / Nomor Kios *</label>
                <input type="text" name="no_kios" class="form-input" required placeholder="Contoh: A-05" value="{{ old('no_kios') }}">
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Tarif Sewa Iuran *</label>
                <select name="tarif_id" class="form-select" required>
                    @foreach($tarifs as $t)
                        <option value="{{ $t->id }}">{{ $t->nama_tarif }} (Rp {{ number_format($t->nominal_harian, 0, ',', '.') }}/hari)</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label class="form-label">Tanggal Mulai Pendaftaran *</label>
                <input type="date" name="tanggal_daftar" class="form-input" required value="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: var(--space-md);">
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="alamat" class="form-input" rows="2" placeholder="Alamat calon pedagang...">{{ old('alamat') }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom: var(--space-lg);">
            <label class="form-label">Catatan Tambahan</label>
            <textarea name="catatan" class="form-input" rows="2" placeholder="Catatan opsional...">{{ old('catatan') }}</textarea>
        </div>

        <div style="display: flex; gap: var(--space-sm); justify-content: flex-end;">
            <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Kirim Pendaftaran</button>
        </div>
    </form>
</div>
@endsection
