@extends('layouts.app')

@section('title', 'Edit Pedagang')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Edit Pedagang: {{ $pedagang->nama_pedagang }}</h2>
        <p class="page-subtitle">Ubah data profil pedagang, status, atau penempatan kios.</p>
    </div>
    <div>
        <a href="{{ route('pedagang.show', $pedagang->id) }}" class="btn btn-secondary">Batal</a>
    </div>
</div>

<style>
    .form-grid-duo {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 16px 20px !important;
        margin-bottom: 24px !important;
    }
    .form-grid-duo > .form-group {
        margin-bottom: 0 !important;
    }
    .form-grid-full {
        grid-column: 1 / -1 !important;
    }
    @media (max-width: 640px) {
        .form-grid-duo {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
    }
</style>

<div class="card">
    <form action="{{ route('pedagang.update', $pedagang->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- BAGIAN 1: IDENTITAS PRIBADI & USAHA (2 KOLOM BERSAMPINGAN) -->
        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg);">Identitas Pribadi & Usaha</h3>
        
        <div class="form-grid-duo">
            <!-- Kolom Kiri: Nama Pedagang -->
            <div class="form-group">
                <label for="nama_pedagang" class="form-label">Nama Pedagang <span style="color: var(--error);">*</span></label>
                <input type="text" name="nama_pedagang" id="nama_pedagang" class="form-control" value="{{ old('nama_pedagang', $pedagang->nama_pedagang) }}" required placeholder="Masukkan nama lengkap pedagang">
                @error('nama_pedagang')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Nomor KTP -->
            <div class="form-group">
                <label for="no_ktp" class="form-label">Nomor KTP <span style="color: var(--error);">*</span></label>
                <input type="text" name="no_ktp" id="no_ktp" class="form-control" value="{{ old('no_ktp', $pedagang->no_ktp) }}" required placeholder="16 digit NIK KTP">
                @error('no_ktp')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: Nomor Telepon / WhatsApp -->
            <div class="form-group">
                <label for="no_telepon" class="form-label">Nomor Telepon / WhatsApp</label>
                <input type="text" name="no_telepon" id="no_telepon" class="form-control" value="{{ old('no_telepon', $pedagang->no_telepon) }}" placeholder="Contoh: 08123456789">
                @error('no_telepon')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Nama Kios / Usaha -->
            <div class="form-group">
                <label for="nama_usaha" class="form-label">Nama Kios / Usaha</label>
                <input type="text" name="nama_usaha" id="nama_usaha" class="form-control" value="{{ old('nama_usaha', $pedagang->nama_usaha) }}" placeholder="Contoh: Toko Sembako Makmur">
                @error('nama_usaha')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: Kategori Usaha -->
            <div class="form-group">
                <label for="kategori_usaha" class="form-label">Kategori Usaha <span style="color: var(--error);">*</span></label>
                <select name="kategori_usaha" id="kategori_usaha" class="form-control" required style="min-height: 48px;">
                    <option value="kuliner" {{ old('kategori_usaha', $pedagang->kategori_usaha) == 'kuliner' ? 'selected' : '' }}>Kuliner</option>
                    <option value="pakaian" {{ old('kategori_usaha', $pedagang->kategori_usaha) == 'pakaian' ? 'selected' : '' }}>Pakaian</option>
                    <option value="elektronik" {{ old('kategori_usaha', $pedagang->kategori_usaha) == 'elektronik' ? 'selected' : '' }}>Elektronik</option>
                    <option value="sayuran" {{ old('kategori_usaha', $pedagang->kategori_usaha) == 'sayuran' ? 'selected' : '' }}>Sayuran</option>
                    <option value="buah" {{ old('kategori_usaha', $pedagang->kategori_usaha) == 'buah' ? 'selected' : '' }}>Buah</option>
                    <option value="sembako" {{ old('kategori_usaha', $pedagang->kategori_usaha) == 'sembako' ? 'selected' : '' }}>Sembako</option>
                    <option value="lainnya" {{ old('kategori_usaha', $pedagang->kategori_usaha) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('kategori_usaha')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Detail Usaha -->
            <div class="form-group">
                <label for="kategori_usaha_detail" class="form-label">Detail Usaha</label>
                <input type="text" name="kategori_usaha_detail" id="kategori_usaha_detail" class="form-control" value="{{ old('kategori_usaha_detail', $pedagang->kategori_usaha_detail) }}" placeholder="Contoh: Aneka Jus & Kopi">
                @error('kategori_usaha_detail')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- BAGIAN 2: ALAMAT PEDAGANG (2 KOLOM BERSAMPINGAN) -->
        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg); margin-top: var(--space-xl);">Alamat Pedagang</h3>
        
        <div class="form-grid-duo">
            <!-- Alamat Lengkap (Full Width) -->
            <div class="form-group form-grid-full">
                <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
                <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" rows="2" placeholder="Masukkan jalan, nomor rumah, atau rincian tempat tinggal">{{ old('alamat_lengkap', $pedagang->alamat_lengkap) }}</textarea>
                @error('alamat_lengkap')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: RT -->
            <div class="form-group">
                <label for="rt" class="form-label">RT</label>
                <input type="text" name="rt" id="rt" class="form-control" value="{{ old('rt', $pedagang->rt) }}" placeholder="Contoh: 001">
                @error('rt') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <!-- Kolom Kanan: RW -->
            <div class="form-group">
                <label for="rw" class="form-label">RW</label>
                <input type="text" name="rw" id="rw" class="form-control" value="{{ old('rw', $pedagang->rw) }}" placeholder="Contoh: 005">
                @error('rw') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <!-- Kolom Kiri: Desa/Kelurahan -->
            <div class="form-group">
                <label for="desa_kelurahan" class="form-label">Desa / Kelurahan</label>
                <input type="text" name="desa_kelurahan" id="desa_kelurahan" class="form-control" value="{{ old('desa_kelurahan', $pedagang->desa_kelurahan) }}" placeholder="Nama Desa / Kelurahan">
                @error('desa_kelurahan')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Kecamatan -->
            <div class="form-group">
                <label for="kecamatan" class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" id="kecamatan" class="form-control" value="{{ old('kecamatan', $pedagang->kecamatan) }}" placeholder="Nama Kecamatan">
                @error('kecamatan')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: Kabupaten/Kota -->
            <div class="form-group">
                <label for="kabupaten_kota" class="form-label">Kabupaten / Kota</label>
                <input type="text" name="kabupaten_kota" id="kabupaten_kota" class="form-control" value="{{ old('kabupaten_kota', $pedagang->kabupaten_kota) }}" placeholder="Nama Kabupaten / Kota">
                @error('kabupaten_kota')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Provinsi -->
            <div class="form-group">
                <label for="provinsi" class="form-label">Provinsi</label>
                <input type="text" name="provinsi" id="provinsi" class="form-control" value="{{ old('provinsi', $pedagang->provinsi) }}" placeholder="Nama Provinsi">
                @error('provinsi')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: Kode Pos -->
            <div class="form-group">
                <label for="kode_pos" class="form-label">Kode Pos</label>
                <input type="text" name="kode_pos" id="kode_pos" class="form-control" value="{{ old('kode_pos', $pedagang->kode_pos) }}" placeholder="Contoh: 12345">
                @error('kode_pos')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan (Penyeimbang Grid) -->
            <div></div>
        </div>

        <!-- BAGIAN 3: PENEMPATAN KIOS & TARIF (2 KOLOM BERSAMPINGAN) -->
        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg); margin-top: var(--space-xl);">Penempatan Kios & Tarif</h3>
        
        <div class="form-grid-duo">
            <!-- Kolom Kiri: Blok Kios -->
            <div class="form-group">
                <label for="blok_kios" class="form-label">Blok Kios <span style="color: var(--error);">*</span></label>
                <select name="blok_kios" id="blok_kios" class="form-control" required style="min-height: 48px;">
                    <option value="Blok A" {{ old('blok_kios', $pedagang->blok_kios) == 'Blok A' ? 'selected' : '' }}>Blok A</option>
                    <option value="Blok B" {{ old('blok_kios', $pedagang->blok_kios) == 'Blok B' ? 'selected' : '' }}>Blok B</option>
                    <option value="Blok C" {{ old('blok_kios', $pedagang->blok_kios) == 'Blok C' ? 'selected' : '' }}>Blok C</option>
                    <option value="Blok D" {{ old('blok_kios', $pedagang->blok_kios) == 'Blok D' ? 'selected' : '' }}>Blok D</option>
                    <option value="Blok E" {{ old('blok_kios', $pedagang->blok_kios) == 'Blok E' ? 'selected' : '' }}>Blok E</option>
                </select>
                @error('blok_kios')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Nomor Kios -->
            <div class="form-group">
                <label for="no_kios" class="form-label">Nomor Kios <span style="color: var(--error);">*</span></label>
                <input type="text" name="no_kios" id="no_kios" class="form-control" value="{{ old('no_kios', $pedagang->no_kios) }}" required style="font-weight: 700;">
                @error('no_kios')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: Tarif Sewa Harian -->
            <div class="form-group">
                <label for="nominal_tarif" class="form-label">Tarif Sewa Harian (Rp/hari) <span style="color: var(--error);">*</span></label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #64748b; font-size: 0.95rem;">Rp</span>
                    <input type="number" name="nominal_tarif" id="nominal_tarif" class="form-control" 
                        value="{{ old('nominal_tarif', (int)($pedagang->tarif->nominal_harian ?? 0)) }}" 
                        placeholder="Contoh: 15000" min="0" step="500" required 
                        style="padding-left: 44px; font-weight: 700; font-size: 1.05rem; min-height: 48px;">
                </div>
                @error('nominal_tarif')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Saldo Deposit -->
            <div class="form-group">
                <label for="saldo_deposit" class="form-label">Saldo Deposit (Rp)</label>
                <input type="number" name="saldo_deposit" id="saldo_deposit" class="form-control" value="{{ old('saldo_deposit', (int)$pedagang->saldo_deposit) }}" min="0" step="1">
                @error('saldo_deposit')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: Tanggal Terdaftar -->
            <div class="form-group">
                <label for="tanggal_daftar" class="form-label">Tanggal Terdaftar <span style="color: var(--error);">*</span></label>
                <input type="date" name="tanggal_daftar" id="tanggal_daftar" class="form-control" value="{{ old('tanggal_daftar', $pedagang->tanggal_daftar) }}" required>
                @error('tanggal_daftar')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Status Pedagang -->
            <div class="form-group">
                <label for="status_pedagang" class="form-label">Status Pedagang <span style="color: var(--error);">*</span></label>
                <select name="status_pedagang" id="status_pedagang" class="form-control" required style="min-height: 48px;">
                    <option value="aktif" {{ old('status_pedagang', $pedagang->status_pedagang) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="sementara_tutup" {{ old('status_pedagang', $pedagang->status_pedagang) == 'sementara_tutup' ? 'selected' : '' }}>Sementara Tutup</option>
                    <option value="nonaktif" {{ old('status_pedagang', $pedagang->status_pedagang) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status_pedagang')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kiri: Status Legalitas -->
            <div class="form-group">
                <label for="status_legal" class="form-label">Status Legalitas <span style="color: var(--error);">*</span></label>
                <select name="status_legal" id="status_legal" class="form-control" required style="min-height: 48px;">
                    <option value="legal" {{ in_array(old('status_legal', $pedagang->status_legal), ['legal', 'proses_izin', 'tutup_sementara']) ? 'selected' : '' }}>Legal</option>
                    <option value="ilegal" {{ old('status_legal', $pedagang->status_legal) == 'ilegal' ? 'selected' : '' }}>Ilegal</option>
                </select>
                @error('status_legal')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan (Penyeimbang Grid) -->
            <div></div>
        </div>

        <!-- BAGIAN 4: FOTO PROFIL & BERKAS LAMPIRAN -->
        <h3 class="card-title" style="border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm); margin-bottom: var(--space-lg); margin-top: var(--space-xl);">Foto Profil & Berkas Lampiran</h3>
        
        <!-- Upload Foto Profil -->
        <div class="form-group" style="margin-bottom: var(--space-lg); background: var(--surface-container-low); padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant);">
            <label for="foto_profil" class="form-label" style="font-weight: 700; display: block; margin-bottom: 8px;">Foto Profil / Foto Diri Pedagang</label>
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <div style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; border: 2px solid var(--primary); flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    @if($pedagang->foto_profil_url)
                        <img id="preview-foto-profil" src="{{ $pedagang->foto_profil_url }}" alt="Preview Foto" style="width: 100%; height: 100%; object-fit: cover;">
                        <span id="placeholder-foto-profil" style="display: none; align-items: center; justify-content: center; color: #64748b;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                    @else
                        <img id="preview-foto-profil" src="" alt="Preview Foto" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <span id="placeholder-foto-profil" style="display: flex; align-items: center; justify-content: center; color: #64748b;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                    @endif
                </div>
                <div style="flex-grow: 1; min-width: 220px;">
                    <input type="file" name="foto_profil" id="foto_profil" class="form-control" style="padding-top: var(--space-sm);" accept="image/*" onchange="previewAvatar(this)">
                    <span style="font-size: 0.75rem; color: var(--on-surface-variant); display: block; margin-top: 4px;">Pilih foto formal atau foto diri pedagang. Format: JPG, PNG, WEBP (Maksimal 2 MB).</span>
                </div>
            </div>
            @error('foto_profil')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-grid-duo">
            <!-- Kolom Kiri: Upload Foto KTP -->
            <div class="form-group">
                <label for="foto_ktp" class="form-label">Ganti Foto KTP</label>
                <input type="file" name="foto_ktp" id="foto_ktp" class="form-control" style="padding-top: var(--space-sm);" accept="image/*">
                @if($pedagangs->foto_ktp ?? $pedagang->foto_ktp)
                    <div style="margin-top: var(--space-sm);">
                        <a href="{{ asset($pedagang->foto_ktp) }}" target="_blank" style="font-size: 0.8125rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            Lihat Foto KTP Saat Ini
                        </a>
                    </div>
                @endif
                @error('foto_ktp')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Kanan: Upload Foto Izin Usaha -->
            <div class="form-group">
                <label for="foto_izin_usaha" class="form-label">Ganti Surat Izin Usaha</label>
                <input type="file" name="foto_izin_usaha" id="foto_izin_usaha" class="form-control" style="padding-top: var(--space-sm);" accept="image/*">
                @if($pedagangs->foto_izin_usaha ?? $pedagang->foto_izin_usaha)
                    <div style="margin-top: var(--space-sm);">
                        <a href="{{ asset($pedagang->foto_izin_usaha) }}" target="_blank" style="font-size: 0.8125rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            Lihat Surat Izin Saat Ini
                        </a>
                    </div>
                @endif
                @error('foto_izin_usaha')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Catatan Tambahan (Full Width) -->
            <div class="form-group form-grid-full">
                <label for="catatan" class="form-label">Catatan Tambahan</label>
                <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan', $pedagang->catatan) }}</textarea>
                @error('catatan')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-top: var(--space-xl); display: flex; gap: var(--space-md); justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="box-shadow: var(--shadow-btn);">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewAvatar(input) {
        const preview = document.getElementById('preview-foto-profil');
        const placeholder = document.getElementById('placeholder-foto-profil');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const blokKios = document.getElementById('blok_kios');
        const noKios = document.getElementById('no_kios');
        
        let originalBlok = "{{ $pedagang->blok_kios }}";
        let originalNoKios = "{{ $pedagang->no_kios }}";

        if (blokKios) {
            blokKios.addEventListener('change', function() {
                const blockVal = this.value;
                if (!blockVal) {
                    noKios.value = '';
                    return;
                }
                
                if (blockVal === originalBlok) {
                    noKios.value = originalNoKios;
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
