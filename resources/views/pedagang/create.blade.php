@extends('layouts.app')

@section('title', 'Tambah Pedagang Baru')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Tambah Pedagang Baru</h2>
        <p class="page-subtitle">Daftarkan pedagang baru dan alokasikan nomor kios.</p>
    </div>
    <div>
        <a href="{{ route('pedagang.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card">
    <form action="{{ route('pedagang.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @if ($errors->any())
            <div style="background-color: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                <h4 style="margin: 0 0 8px 0; font-size: 0.9375rem; font-weight: 700;">Gagal Menyimpan Data Pedagang!</h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg);">Identitas Pribadi & Usaha</h3>
        
        <div class="grid-2">
            <!-- Nama Pedagang -->
            <div class="form-group">
                <label for="nama_pedagang" class="form-label">Nama Pedagang <span style="color: var(--error);">*</span></label>
                <input type="text" name="nama_pedagang" id="nama_pedagang" class="form-control" value="{{ old('nama_pedagang') }}" required placeholder="Masukkan nama lengkap pedagang">
                @error('nama_pedagang')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nomor KTP -->
            <div class="form-group">
                <label for="no_ktp" class="form-label">Nomor KTP <span style="color: var(--error);">*</span></label>
                <input type="text" name="no_ktp" id="no_ktp" class="form-control" value="{{ old('no_ktp') }}" required placeholder="16 digit Nomor Induk Kependudukan">
                @error('no_ktp')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nomor Telepon -->
            <div class="form-group">
                <label for="no_telepon" class="form-label">Nomor Telepon / WhatsApp</label>
                <input type="text" name="no_telepon" id="no_telepon" class="form-control" value="{{ old('no_telepon') }}" placeholder="Contoh: 08123456789">
                @error('no_telepon')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nama Usaha -->
            <div class="form-group">
                <label for="nama_usaha" class="form-label">Nama Kios / Usaha</label>
                <input type="text" name="nama_usaha" id="nama_usaha" class="form-control" value="{{ old('nama_usaha') }}" placeholder="Contoh: Toko Sembako Makmur">
                @error('nama_usaha')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kategori Usaha -->
            <div class="form-group">
                <label for="kategori_usaha" class="form-label">Kategori Usaha <span style="color: var(--error);">*</span></label>
                <select name="kategori_usaha" id="kategori_usaha" class="form-control" required style="min-height: 48px;">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="kuliner" {{ old('kategori_usaha') == 'kuliner' ? 'selected' : '' }}>Kuliner</option>
                    <option value="pakaian" {{ old('kategori_usaha') == 'pakaian' ? 'selected' : '' }}>Pakaian</option>
                    <option value="elektronik" {{ old('kategori_usaha') == 'elektronik' ? 'selected' : '' }}>Elektronik</option>
                    <option value="sayuran" {{ old('kategori_usaha') == 'sayuran' ? 'selected' : '' }}>Sayuran</option>
                    <option value="buah" {{ old('kategori_usaha') == 'buah' ? 'selected' : '' }}>Buah</option>
                    <option value="sembako" {{ old('kategori_usaha') == 'sembako' ? 'selected' : '' }}>Sembako</option>
                    <option value="lainnya" {{ old('kategori_usaha') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('kategori_usaha')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Detail Kategori -->
            <div class="form-group">
                <label for="kategori_usaha_detail" class="form-label">Detail Usaha</label>
                <input type="text" name="kategori_usaha_detail" id="kategori_usaha_detail" class="form-control" value="{{ old('kategori_usaha_detail') }}" placeholder="Contoh: Jual Sate Ayam, Nasi Padang">
                @error('kategori_usaha_detail')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg); margin-top: var(--space-xl);">Alamat Pedagang</h3>
        
        <div class="grid-2">
            <!-- Alamat Lengkap -->
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
                <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" rows="3" placeholder="Masukkan jalan, nomor rumah, atau rincian tempat tinggal">{{ old('alamat_lengkap') }}</textarea>
                @error('alamat_lengkap')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- RT -->
            <div class="form-group">
                <label for="rt" class="form-label">RT</label>
                <input type="text" name="rt" id="rt" class="form-control" value="{{ old('rt') }}" placeholder="Contoh: 001">
                @error('rt')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- RW -->
            <div class="form-group">
                <label for="rw" class="form-label">RW</label>
                <input type="text" name="rw" id="rw" class="form-control" value="{{ old('rw') }}" placeholder="Contoh: 005">
                @error('rw')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Desa/Kelurahan -->
            <div class="form-group">
                <label for="desa_kelurahan" class="form-label">Desa / Kelurahan</label>
                <input type="text" name="desa_kelurahan" id="desa_kelurahan" class="form-control" value="{{ old('desa_kelurahan') }}" placeholder="Masukkan desa atau kelurahan">
                @error('desa_kelurahan')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kecamatan -->
            <div class="form-group">
                <label for="kecamatan" class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" id="kecamatan" class="form-control" value="{{ old('kecamatan') }}" placeholder="Masukkan kecamatan">
                @error('kecamatan')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kabupaten/Kota -->
            <div class="form-group">
                <label for="kabupaten_kota" class="form-label">Kabupaten / Kota</label>
                <input type="text" name="kabupaten_kota" id="kabupaten_kota" class="form-control" value="{{ old('kabupaten_kota') }}" placeholder="Masukkan kabupaten atau kota">
                @error('kabupaten_kota')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Provinsi -->
            <div class="form-group">
                <label for="provinsi" class="form-label">Provinsi</label>
                <input type="text" name="provinsi" id="provinsi" class="form-control" value="{{ old('provinsi') }}" placeholder="Masukkan provinsi">
                @error('provinsi')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kode Pos -->
            <div class="form-group">
                <label for="kode_pos" class="form-label">Kode Pos</label>
                <input type="text" name="kode_pos" id="kode_pos" class="form-control" value="{{ old('kode_pos') }}" placeholder="Contoh: 12345">
                @error('kode_pos')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg); margin-top: var(--space-xl);">Penempatan Kios & Tarif</h3>
        
        <div class="grid-2">
            <!-- Blok Kios -->
            <div class="form-group">
                <label for="blok_kios" class="form-label">Blok Kios <span style="color: var(--error);">*</span></label>
                <select name="blok_kios" id="blok_kios" class="form-control" required style="min-height: 48px;">
                    <option value="">-- Pilih Blok Kios --</option>
                    <option value="Blok A" {{ old('blok_kios') == 'Blok A' ? 'selected' : '' }}>Blok A</option>
                    <option value="Blok B" {{ old('blok_kios') == 'Blok B' ? 'selected' : '' }}>Blok B</option>
                    <option value="Blok C" {{ old('blok_kios') == 'Blok C' ? 'selected' : '' }}>Blok C</option>
                    <option value="Blok D" {{ old('blok_kios') == 'Blok D' ? 'selected' : '' }}>Blok D</option>
                    <option value="Blok E" {{ old('blok_kios') == 'Blok E' ? 'selected' : '' }}>Blok E</option>
                </select>
                @error('blok_kios')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nomor Kios (Auto Generated) -->
            <div class="form-group">
                <label for="no_kios" class="form-label">Nomor Kios <span style="color: var(--error);">*</span></label>
                <input type="text" name="no_kios" id="no_kios" class="form-control" value="{{ old('no_kios') }}" required placeholder="Pilih blok untuk generate otomatis" style="font-weight: 700;">
                @error('no_kios')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Pilih Tarif Sewa -->
            <div class="form-group">
                <label for="tarif_id" class="form-label">Tarif Sewa Harian <span style="color: var(--error);">*</span></label>
                <select name="tarif_id" id="tarif_id" class="form-control" required style="min-height: 48px;">
                    <option value="">-- Pilih Tarif --</option>
                    @foreach($tarifs as $trf)
                        <option value="{{ $trf->id }}" {{ old('tarif_id') == $trf->id ? 'selected' : '' }}>
                            {{ $trf->nama_tarif }} (Rp {{ number_format($trf->nominal_harian, 0, ',', '.') }}/hari)
                        </option>
                    @endforeach
                </select>
                @error('tarif_id')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Saldo Deposit Awal -->
            <div class="form-group">
                <label for="saldo_deposit" class="form-label">Saldo Deposit Awal (Rp)</label>
                <input type="number" name="saldo_deposit" id="saldo_deposit" class="form-control" value="{{ old('saldo_deposit', 0) }}" min="0" step="1">
                @error('saldo_deposit')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Daftar -->
            <div class="form-group">
                <label for="tanggal_daftar" class="form-label">Tanggal Terdaftar <span style="color: var(--error);">*</span></label>
                <input type="date" name="tanggal_daftar" id="tanggal_daftar" class="form-control" value="{{ old('tanggal_daftar', date('Y-m-d')) }}" required>
                @error('tanggal_daftar')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status Pedagang -->
            <div class="form-group">
                <label for="status_pedagang" class="form-label">Status Pedagang <span style="color: var(--error);">*</span></label>
                <select name="status_pedagang" id="status_pedagang" class="form-control" required style="min-height: 48px;">
                    <option value="aktif" {{ old('status_pedagang', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="sementara_tutup" {{ old('status_pedagang') == 'sementara_tutup' ? 'selected' : '' }}>Sementara Tutup</option>
                    <option value="nonaktif" {{ old('status_pedagang') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status_pedagang')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status Legalitas -->
            <div class="form-group">
                <label for="status_legal" class="form-label">Status Legalitas <span style="color: var(--error);">*</span></label>
                <select name="status_legal" id="status_legal" class="form-control" required style="min-height: 48px;">
                    <option value="legal" {{ old('status_legal', 'legal') == 'legal' ? 'selected' : '' }}>Legal</option>
                    <option value="proses_izin" {{ old('status_legal') == 'proses_izin' ? 'selected' : '' }}>Proses Izin</option>
                    <option value="ilegal" {{ old('status_legal') == 'ilegal' ? 'selected' : '' }}>Ilegal</option>
                </select>
                @error('status_legal')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg); margin-top: var(--space-xl);">Berkas & Lampiran</h3>
        
        <div class="grid-2">
            <!-- Upload Foto KTP -->
            <div class="form-group">
                <label for="foto_ktp" class="form-label">Upload Foto KTP</label>
                <input type="file" name="foto_ktp" id="foto_ktp" class="form-control" style="padding-top: var(--space-sm);" accept="image/*">
                <p style="font-size: 0.75rem; color: var(--outline); margin-top: var(--space-xs);">Format: JPG, JPEG, PNG. Max: 2 MB.</p>
                @error('foto_ktp')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Upload Foto Izin Usaha -->
            <div class="form-group">
                <label for="foto_izin_usaha" class="form-label">Upload Surat Izin Usaha</label>
                <input type="file" name="foto_izin_usaha" id="foto_izin_usaha" class="form-control" style="padding-top: var(--space-sm);" accept="image/*">
                <p style="font-size: 0.75rem; color: var(--outline); margin-top: var(--space-xs);">Format: JPG, JPEG, PNG. Max: 2 MB.</p>
                @error('foto_izin_usaha')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Catatan tambahan -->
        <div class="form-group" style="margin-top: var(--space-md);">
            <label for="catatan" class="form-label">Catatan Tambahan</label>
            <textarea name="catatan" id="catatan" class="form-control" placeholder="Tulis catatan penting di sini (opsional)...">{{ old('catatan') }}</textarea>
            @error('catatan')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-top: var(--space-xl); display: flex; gap: var(--space-md); justify-content: flex-end;">
            <button type="reset" class="btn btn-secondary">Reset Form</button>
            <button type="submit" class="btn btn-primary" style="box-shadow: var(--shadow-btn);">Simpan Pedagang</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const blokKios = document.getElementById('blok_kios');
        const noKios = document.getElementById('no_kios');

        if (blokKios) {
            blokKios.addEventListener('change', function() {
                const blockVal = this.value;
                if (!blockVal) {
                    noKios.value = '';
                    return;
                }

                noKios.value = 'Mencari nomor...';
                
                // Fetch next kiosk number from our JSON API
                fetch(`{{ route('pedagang.next-kios') }}?block=${encodeURIComponent(blockVal)}`)
                    .then(response => response.json())
                    .then(data => {
                        noKios.value = data.no_kios;
                    })
                    .catch(err => {
                        console.error('Gagal mengambil nomor kios:', err);
                        noKios.value = '';
                    });
            });
        }
    });
</script>
@endsection
