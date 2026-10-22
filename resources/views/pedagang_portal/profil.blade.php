@extends('layouts.app')

@section('title', 'Profil & Data Lapak - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18l-1 9H4L3 3z M4 12v8a1 1 0 001 1h14a1 1 0 001-1v-8 M10 21V15h4v6"></path>
            </svg>
            <span>Profil Mitra & Data Lapak</span>
        </h2>
        <p class="page-subtitle" style="margin: 4px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Informasi identitas resmi pedagang dan data lapak kios yang terdaftar di pasar.
        </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 0.775rem; border: 1px solid rgba(16, 185, 129, 0.3);">
            ● Mitra Terdaftar & Terverifikasi
        </span>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #047857; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('success_password'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #047857; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0110 0v4"></path>
        </svg>
        <span>{{ session('success_password') }}</span>
    </div>
@endif

@if($errors->any())
    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 0.875rem;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
    <!-- KARTU 1: FOTO PROFIL PRIBADI & PENGATURAN AVATAR (GAYA WHATSAPP) -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">Foto Profil Pribadi</h3>
            </div>
            <span style="background: rgba(16, 185, 129, 0.12); color: #047857; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(16, 185, 129, 0.25);">
                Aktif & Terverifikasi
            </span>
        </div>

        <form id="formFotoProfil" action="{{ route('pedagang.profil.foto') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Input Berkas Tersembunyi (Dibuka saat mengklik foto atau pensil) -->
            <input type="file" 
                   name="foto_profil" 
                   id="portalFotoInput" 
                   accept="image/png, image/jpeg, image/jpg, image/webp" 
                   onchange="previewPortalAvatar(this)" 
                   style="display: none;">

            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 12px; padding: 10px 0;">
                
                <!-- Lingkaran Foto Profil dengan Tombol Ikon Pensil Melayang (Mirip WhatsApp) -->
                <div style="position: relative; width: 130px; height: 130px; margin: 0 auto;">
                    <div style="width: 130px; height: 130px; border-radius: 50%; padding: 4px; border: 3px solid var(--primary); background: var(--surface-container-low); box-shadow: 0 6px 18px rgba(0,0,0,0.12); overflow: hidden; cursor: pointer;" 
                         onclick="document.getElementById('portalFotoInput').click()" 
                         title="Klik foto untuk mengganti profil">
                        <img id="portalAvatarPreview" 
                             src="{{ $pedagang->foto_profil_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($pedagang->nama_pedagang) . '&background=008080&color=fff&size=200' }}" 
                             alt="Foto Profil {{ $pedagang->nama_pedagang }}" 
                             style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; transition: filter 0.2s;"
                             onmouseover="this.style.filter='brightness(0.85)'"
                             onmouseout="this.style.filter='brightness(1)'">
                    </div>

                    <!-- Tombol Pensil Melayang -->
                    <button type="button" 
                            onclick="document.getElementById('portalFotoInput').click()" 
                            style="position: absolute; bottom: 4px; right: 4px; width: 40px; height: 40px; border-radius: 50%; background: #10b981; color: #ffffff; border: 3px solid var(--surface-container-lowest); display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: transform 0.2s, background 0.2s;"
                            title="Edit / Ganti Foto Profil"
                            onmouseover="this.style.transform='scale(1.12)'; this.style.background='#059669'"
                            onmouseout="this.style.transform='scale(1)'; this.style.background='#10b981'">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Keterangan Nama & Tombol Edit -->
                <div>
                    <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">{{ $pedagang->nama_pedagang }}</h4>
                    <button type="button" 
                            onclick="document.getElementById('portalFotoInput').click()" 
                            style="margin-top: 6px; background: transparent; border: none; color: var(--primary); font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit Foto Profil</span>
                    </button>
                </div>

                <!-- Box Aksi Konfirmasi (Hanya Muncul Otomatis Saat Ada Foto Baru Dipilih) -->
                <div id="portalAvatarActions" style="display: none; width: 100%; margin-top: 10px; background: rgba(16, 185, 129, 0.08); border: 1.5px dashed #10b981; border-radius: 12px; padding: 14px; text-align: center;">
                    <div style="font-size: 0.875rem; font-weight: 700; color: #047857; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Foto Baru Siap Diterapkan!</span>
                    </div>
                    <div id="namaFileTerpilih" style="font-size: 0.75rem; color: var(--on-surface-variant); margin-bottom: 12px; word-break: break-all;"></div>
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <button type="button" onclick="cancelPortalAvatar()" class="btn btn-secondary btn-sm" style="padding: 7px 16px; font-size: 0.8rem; border-radius: 8px;">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 7px 18px; font-size: 0.8rem; font-weight: 700; background: #10b981; border-color: #10b981; border-radius: 8px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            <span>Simpan Foto Profil</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- 2. KARTU DATA LAPAK KIOS -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">Informasi Lapak Kios</h3>
            </div>
            <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 0.85rem; border: 1px solid #bae6fd;">
                {{ $pedagang->no_kios ?? 'Kios -' }}
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px; font-size: 0.875rem;">
            <div>
                <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Nama Toko / Usaha:</span>
                <strong style="font-size: 1.05rem; color: var(--on-surface);">{{ $pedagang->nama_usaha ?: ($pedagang->nama_pedagang . ' Store') }}</strong>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Blok Kios:</span>
                    <strong style="color: var(--on-surface);">{{ $pedagang->blok_kios ?: 'Blok A' }}</strong>
                </div>
                <div>
                    <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Kategori Usaha:</span>
                    <strong style="color: var(--on-surface); text-transform: capitalize;">{{ $pedagang->kategori_usaha ?: 'Umum' }}</strong>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Status Lapak:</span>
                    @if($pedagang->status_pedagang === 'aktif')
                        <span style="display: inline-block; background: #dcfce7; color: #166534; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 2px;">
                            Aktif Berdagang
                        </span>
                    @elseif($pedagang->status_pedagang === 'sementara_tutup')
                        <span style="display: inline-block; background: #fef3c7; color: #92400e; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 2px;">
                            Sementara Tutup
                        </span>
                    @else
                        <span style="display: inline-block; background: #fee2e2; color: #991b1b; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 2px;">
                            Nonaktif
                        </span>
                    @endif
                </div>
                <div>
                    <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Status Legalitas:</span>
                    @if($pedagang->status_legal === 'tutup_sementara')
                        <span style="display: inline-block; background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 2px;">
                            TUTUP SEMENTARA
                        </span>
                    @else
                        <span style="display: inline-block; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 2px;">
                            LEGAL / TERDAFTAR
                        </span>
                    @endif
                </div>
            </div>

            <div style="background: var(--surface-container-low); padding: 12px; border-radius: 8px; margin-top: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: var(--on-surface-variant);">Tarif Iuran Harian Resmi:</span>
                    <strong style="color: #059669; font-size: 1.05rem;">
                        Rp {{ number_format($pedagang->tarif->nominal_harian ?? 0, 0, ',', '.') }}
                    </strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                    <span style="font-size: 0.8rem; color: var(--on-surface-variant);">Saldo Deposit Lapak:</span>
                    <strong style="color: var(--primary); font-size: 0.95rem;">
                        Rp {{ number_format($pedagang->saldo_deposit ?? 0, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. KARTU IDENTITAS PEDAGANG & AKUN LOGIN -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">Identitas Mitra & Akun Login</h3>
            </div>
            <span style="background: rgba(16, 185, 129, 0.1); color: #047857; font-weight: 700; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px;">
                Pedagang
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px; font-size: 0.875rem;">
            <div>
                <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Nama Lengkap:</span>
                <strong style="font-size: 1.05rem; color: var(--on-surface);">{{ $pedagang->nama_pedagang }}</strong>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Nomor WhatsApp / HP:</span>
                    <strong style="color: var(--on-surface);">{{ $pedagang->no_telepon ?: '-' }}</strong>
                </div>
                <div>
                    <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Nomor KTP (NIK):</span>
                    @php
                        $ktp = $pedagang->no_ktp;
                        $maskedKtp = $ktp ? substr($ktp, 0, 6) . '******' . substr($ktp, -4) : '-';
                    @endphp
                    <strong style="color: var(--on-surface); font-family: monospace;">{{ $maskedKtp }}</strong>
                </div>
            </div>

            <div>
                <span style="color: var(--on-surface-variant); display: block; font-size: 0.775rem;">Alamat Domisili:</span>
                <span style="color: var(--on-surface);">{{ $pedagang->alamat_lengkap ?: 'Alamat terdaftar di paguyuban pasar' }}</span>
            </div>

            <!-- Bagian Kredensial Akun -->
            <div style="background: var(--surface-container-low); padding: 14px; border-radius: 8px; border: 1px solid var(--outline-variant); margin-top: 4px;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--outline); text-transform: uppercase; margin-bottom: 8px;">
                    Kredensial Akses Login Website
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-size: 0.8rem; color: var(--on-surface-variant);">Username:</span>
                    <code style="background: white; padding: 2px 8px; border-radius: 4px; border: 1px solid var(--outline-variant); font-weight: 700; color: var(--primary);">
                        {{ $user->username }}
                    </code>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-size: 0.8rem; color: var(--on-surface-variant);">Email Terdaftar:</span>
                    <span style="font-size: 0.825rem; color: var(--on-surface);">{{ $user->email ?: 'Tidak ada email' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: var(--on-surface-variant);">Status Akun:</span>
                    <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 999px; font-size: 0.725rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Aktif & Terotorisasi</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. KARTU GANTI KATA SANDI MANDIRI -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">Keamanan & Ganti Sandi</h3>
            </div>
            <span style="background: rgba(59, 130, 246, 0.12); color: #2563eb; font-weight: 700; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px;">
                Akses Mandiri
            </span>
        </div>

        <p style="margin: 0 0 16px; font-size: 0.8rem; color: var(--on-surface-variant); line-height: 1.4;">
            Demi keamanan akun Anda, Anda dapat mengganti kata sandi kapan saja tanpa harus menghubungi admin pasar.
        </p>

        <form action="{{ route('pedagang.profil.password') }}" method="POST">
            @csrf

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--on-surface); margin-bottom: 4px;">
                    Kata Sandi Saat Ini <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <input type="password" name="password_saat_ini" id="portalPassCurrent" required placeholder="Masukkan kata sandi saat ini" class="form-control" style="padding-right: 40px; font-size: 0.85rem; width: 100%;">
                    <button type="button" onclick="togglePassVisibility('portalPassCurrent', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--outline); display: flex; align-items: center; justify-content: center;" title="Lihat/Sembunyikan Sandi">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
                @error('password_saat_ini')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--on-surface); margin-bottom: 4px;">
                    Kata Sandi Baru (Min. 6 Karakter) <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <input type="password" name="password_baru" id="portalPassNew" required minlength="6" placeholder="Ketik kata sandi baru" class="form-control" style="padding-right: 40px; font-size: 0.85rem; width: 100%;">
                    <button type="button" onclick="togglePassVisibility('portalPassNew', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--outline); display: flex; align-items: center; justify-content: center;" title="Lihat/Sembunyikan Sandi">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
                @error('password_baru')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--on-surface); margin-bottom: 4px;">
                    Konfirmasi Kata Sandi Baru <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <input type="password" name="password_baru_confirmation" id="portalPassConfirm" required minlength="6" placeholder="Ulangi kata sandi baru" class="form-control" style="padding-right: 40px; font-size: 0.85rem; width: 100%;">
                    <button type="button" onclick="togglePassVisibility('portalPassConfirm', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--outline); display: flex; align-items: center; justify-content: center;" title="Lihat/Sembunyikan Sandi">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.875rem;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
                <span>Simpan Kata Sandi Baru</span>
            </button>
        </form>
    </div>
</div>

<script>
const originalAvatarSrc = "{{ $pedagang->foto_profil_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($pedagang->nama_pedagang) . '&background=008080&color=fff&size=200' }}";

function previewPortalAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto terlalu besar. Maksimal 2 MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('portalAvatarPreview');
            if (preview) {
                preview.src = e.target.result;
            }
            const namaFileEl = document.getElementById('namaFileTerpilih');
            if (namaFileEl) {
                namaFileEl.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            }
            const actions = document.getElementById('portalAvatarActions');
            if (actions) {
                actions.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
}

function cancelPortalAvatar() {
    const input = document.getElementById('portalFotoInput');
    if (input) input.value = '';
    const preview = document.getElementById('portalAvatarPreview');
    if (preview) {
        preview.src = originalAvatarSrc;
    }
    const actions = document.getElementById('portalAvatarActions');
    if (actions) {
        actions.style.display = 'none';
    }
}

const svgEyeOpen = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
const svgEyeClosed = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>`;

function togglePassVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = svgEyeClosed;
    } else {
        input.type = 'password';
        btn.innerHTML = svgEyeOpen;
    }
}
</script>
@endsection
