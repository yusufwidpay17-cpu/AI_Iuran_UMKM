@extends('layouts.app')

@section('title', 'Profil Saya & Pengaturan Akun - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Profil Saya & Pengaturan Akun</span>
        </h2>
        <p class="page-subtitle" style="margin: 4px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Kelola foto profil, informasi akun, dan keamanan kata sandi Anda.
        </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <span class="badge" style="background: rgba(0, 61, 155, 0.12); color: var(--primary); font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 0.775rem; border: 1px solid rgba(0, 61, 155, 0.25); text-transform: uppercase; letter-spacing: 0.05em;">
            ● {{ $user->role === 'superadmin' ? 'Superadmin Pengelola' : ($user->role === 'admin' ? 'Administrator' : 'Petugas Lapangan') }}
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
        <div style="font-weight: 700; margin-bottom: 6px;">Terdapat beberapa kesalahan:</div>
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
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">Foto Profil Saya</h3>
            </div>
            <span style="background: rgba(16, 185, 129, 0.12); color: #047857; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(16, 185, 129, 0.25);">
                Aktif
            </span>
        </div>

        <form id="formFotoProfilAdmin" action="{{ route('profil.foto') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Input Berkas Tersembunyi (Dibuka saat mengklik foto atau tombol pensil) -->
            <input type="file" 
                   name="foto_profil" 
                   id="adminFotoInput" 
                   accept="image/png, image/jpeg, image/jpg, image/webp" 
                   onchange="previewAdminAvatar(this)" 
                   style="display: none;">

            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 12px; padding: 10px 0;">
                
                <!-- Lingkaran Foto Profil dengan Tombol Ikon Pensil Melayang (Mirip WhatsApp) -->
                <div style="position: relative; width: 130px; height: 130px; margin: 0 auto;">
                    <div style="width: 130px; height: 130px; border-radius: 50%; padding: 4px; border: 3px solid var(--primary); background: var(--surface-container-low); box-shadow: 0 6px 18px rgba(0,0,0,0.12); overflow: hidden; cursor: pointer;" 
                         onclick="document.getElementById('adminFotoInput').click()" 
                         title="Klik foto untuk mengganti profil">
                        <img id="adminAvatarPreview" 
                             src="{{ $user->foto_profil_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=003d9b&color=fff&size=200' }}" 
                             alt="Foto Profil {{ $user->nama }}" 
                             style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; transition: filter 0.2s;"
                             onmouseover="this.style.filter='brightness(0.85)'"
                             onmouseout="this.style.filter='brightness(1)'">
                    </div>

                    <!-- Tombol Pensil Melayang -->
                    <button type="button" 
                            onclick="document.getElementById('adminFotoInput').click()" 
                            style="position: absolute; bottom: 4px; right: 4px; width: 40px; height: 40px; border-radius: 50%; background: #10b981; color: #ffffff; border: 3px solid var(--surface-container-lowest); display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: transform 0.2s, background 0.2s;"
                            title="Edit / Ganti Foto Profil"
                            onmouseover="this.style.transform='scale(1.1)';" 
                            onmouseout="this.style.transform='scale(1)';">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </button>
                </div>

                <div>
                    <h4 style="margin: 6px 0 2px 0; font-size: 1.15rem; font-weight: 700; color: var(--on-surface);">{{ $user->nama }}</h4>
                    <p style="margin: 0; font-size: 0.85rem; color: var(--on-surface-variant); font-weight: 500;">
                        <span>@</span>{{ $user->username }} &bull; <span style="text-transform: capitalize; color: var(--primary); font-weight: 600;">{{ $user->role }}</span>
                    </p>
                </div>

                <div style="background: var(--surface-container); border: 1px dashed var(--outline-variant); border-radius: 10px; padding: 10px 14px; width: 100%; box-sizing: border-box; text-align: left; font-size: 0.775rem; color: var(--on-surface-variant); line-height: 1.45;">
                    💡 <b>Petunjuk:</b> Klik ikon pensil hijau atau lingkaran foto untuk memilih gambar profil baru (Format: JPG, PNG, WEBP, maks 2 MB).
                </div>

                <!-- Tombol Konfirmasi Simpan Foto (Muncul otomatis setelah berkas dipilih) -->
                <div id="boxSimpanFotoAdmin" style="display: none; width: 100%; margin-top: 4px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 61, 155, 0.25);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan & Pasang Foto Ini</span>
                    </button>
                    <button type="button" onclick="cancelAdminAvatarPreview()" style="margin-top: 8px; background: none; border: none; color: var(--error); font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                        Batalkan Pilihan
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- KARTU 2: INFORMASI IDENTITAS AKUN & PENGATURAN -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                </svg>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">Informasi Akun</h3>
            </div>
            <span style="background: rgba(0, 61, 155, 0.1); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(0, 61, 155, 0.2);">
                ID Akun Resmi
            </span>
        </div>

        <form action="{{ route('profil.update') }}" method="POST">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 6px;">
                        Nama Lengkap <span style="color: var(--error);">*</span>
                    </label>
                    <input type="text" 
                           name="nama" 
                           value="{{ old('nama', $user->nama) }}" 
                           class="form-control" 
                           required 
                           style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--outline-variant); font-size: 0.9rem; background: var(--surface-container-low);">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 6px;">
                        Username Login (Permanen)
                    </label>
                    <input type="text" 
                           value="{{ $user->username }}" 
                           disabled 
                           class="form-control" 
                           style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--outline-variant); font-size: 0.9rem; background: var(--surface-container); opacity: 0.85; cursor: not-allowed;">
                    <span style="font-size: 0.725rem; color: var(--outline); margin-top: 3px; display: block;">Username bersifat unik dan dikunci untuk keamanan sistem.</span>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 6px;">
                        Alamat Email <span style="color: var(--error);">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           class="form-control" 
                           required 
                           style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--outline-variant); font-size: 0.9rem; background: var(--surface-container-low);">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 4px;">Hak Akses</label>
                        <div style="background: var(--surface-container); padding: 8px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; color: var(--primary); border: 1px solid var(--outline-variant); text-transform: uppercase;">
                            {{ $user->role }}
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 4px;">Terdaftar Sejak</label>
                        <div style="background: var(--surface-container); padding: 8px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; color: var(--on-surface); border: 1px solid var(--outline-variant);">
                            {{ $user->created_at ? $user->created_at->isoFormat('D MMM Y') : '-' }}
                        </div>
                    </div>
                </div>

                <div style="margin-top: 8px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px 16px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Perubahan Identitas</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- KARTU 3: KEAMANAN & GANTI KATA SANDI MANDIRI -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--on-surface);">Ganti Kata Sandi</h3>
            </div>
            <span style="background: rgba(217, 119, 6, 0.12); color: #d97706; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(217, 119, 6, 0.25);">
                Keamanan Akun
            </span>
        </div>

        <form action="{{ route('profil.password') }}" method="POST">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 6px;">
                        Kata Sandi Saat Ini <span style="color: var(--error);">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" 
                               name="password_saat_ini" 
                               id="pwd_current" 
                               required 
                               placeholder="Masukkan kata sandi lama Anda" 
                               class="form-control" 
                               style="width: 100%; padding: 10px 42px 10px 14px; border-radius: 8px; border: 1px solid var(--outline-variant); font-size: 0.9rem; background: var(--surface-container-low);">
                        <button type="button" 
                                onclick="togglePwdVisibility('pwd_current', this)" 
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--outline);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 6px;">
                        Kata Sandi Baru <span style="color: var(--error);">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" 
                               name="password_baru" 
                               id="pwd_new" 
                               required 
                               placeholder="Minimal 6 karakter" 
                               class="form-control" 
                               style="width: 100%; padding: 10px 42px 10px 14px; border-radius: 8px; border: 1px solid var(--outline-variant); font-size: 0.9rem; background: var(--surface-container-low);">
                        <button type="button" 
                                onclick="togglePwdVisibility('pwd_new', this)" 
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--outline);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 6px;">
                        Konfirmasi Kata Sandi Baru <span style="color: var(--error);">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" 
                               name="password_baru_confirmation" 
                               id="pwd_confirm" 
                               required 
                               placeholder="Ulangi kata sandi baru" 
                               class="form-control" 
                               style="width: 100%; padding: 10px 42px 10px 14px; border-radius: 8px; border: 1px solid var(--outline-variant); font-size: 0.9rem; background: var(--surface-container-low);">
                        <button type="button" 
                                onclick="togglePwdVisibility('pwd_confirm', this)" 
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--outline);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                <div style="margin-top: 8px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px 16px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- KARTU 4: RIWAYAT AKTIVITAS PENGGUNA TERAKHIR (AUDIT LOGS) -->
<div class="card" style="margin-top: 24px; padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--on-surface);">Aktivitas Terakhir Saya</h3>
        </div>
        <span style="font-size: 0.75rem; color: var(--outline);">5 Log Terkini</span>
    </div>

    @if($recentLogs && $recentLogs->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($recentLogs as $log)
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 10px 14px; background: var(--surface-container-low); border: 1px solid var(--outline-variant); border-radius: 10px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <span style="font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 4px; background: var(--primary-container); color: var(--primary); text-transform: uppercase;">
                                {{ $log->aksi }}
                            </span>
                            <span style="font-size: 0.75rem; color: var(--outline);">
                                {{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--on-surface); font-weight: 500;">
                            {{ $log->deskripsi }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 24px 0; color: var(--outline); font-size: 0.85rem;">
            Belum ada riwayat aktivitas tercatat untuk akun ini.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Preview Avatar Image saat dipilih
    let originalAvatarSrc = "{{ $user->foto_profil_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=003d9b&color=fff&size=200' }}";

    function previewAdminAvatar(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validasi ukuran maks 2MB
            if (file.size > 2097152) {
                alert('Ukuran foto terlalu besar! Maksimal ukuran foto adalah 2 MB.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('adminAvatarPreview').src = e.target.result;
                document.getElementById('boxSimpanFotoAdmin').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function cancelAdminAvatarPreview() {
        document.getElementById('adminFotoInput').value = '';
        document.getElementById('adminAvatarPreview').src = originalAvatarSrc;
        document.getElementById('boxSimpanFotoAdmin').style.display = 'none';
    }

    // Toggle Intip Password
    function togglePwdVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>`;
        } else {
            input.type = 'password';
            btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
        }
    }
</script>
@endsection
