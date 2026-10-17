@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Otoritas Akses')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 10px;">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Manajemen Pengguna & Otoritas Akses
        </h2>
        <p class="page-subtitle" style="margin: 6px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Otoritas penuh Superadmin dalam mengelola staf pengelola pasar dan memberikan hak akses login resmi untuk mitra pedagang.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="btn btn-outline" onclick="bukaModalBeriAksesPedagang()" style="display: flex; align-items: center; gap: 8px; font-weight: 600; border-color: var(--primary); color: var(--primary);">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            + Beri Akses Pedagang
        </button>
        <button type="button" class="btn btn-primary" onclick="bukaModalTambah()" style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            + Tambah Pengguna / Staf
        </button>
    </div>
</div>

<!-- Kartu Ringkasan Otoritas Akses (KPI Cards) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Staf Pengelola -->
    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(99, 102, 241, 0.12); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            🛡️
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--on-surface-variant); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Staf Pengelola</div>
            <div style="font-size: 1.35rem; font-weight: 700; color: var(--on-surface); line-height: 1.2;">{{ $stats['total_staf'] }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--on-surface-variant);">Akun</span></div>
            <div style="font-size: 0.725rem; color: #6366f1; margin-top: 2px;">Superadmin, Admin, Petugas</div>
        </div>
    </div>

    <!-- Total Pedagang -->
    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(14, 165, 233, 0.12); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            🏪
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--on-surface-variant); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Mitra Pedagang Pasar</div>
            <div style="font-size: 1.35rem; font-weight: 700; color: var(--on-surface); line-height: 1.2;">{{ $stats['total_pedagang'] }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--on-surface-variant);">Lapak</span></div>
            <div style="font-size: 0.725rem; color: #0284c7; margin-top: 2px;">Terdaftar di database</div>
        </div>
    </div>

    <!-- Pedagang Berhak Akses -->
    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            🔑
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--on-surface-variant); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Akses Login Aktif</div>
            <div style="font-size: 1.35rem; font-weight: 700; color: #059669; line-height: 1.2;">{{ $stats['pedagang_punya_akses'] }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--on-surface-variant);">Pedagang</span></div>
            <div style="font-size: 0.725rem; color: #059669; margin-top: 2px;">Memiliki akun & dapat login</div>
        </div>
    </div>

    <!-- Pedagang Belum Ada Akses / Dicabut -->
    <div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245, 158, 11, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            🚫
        </div>
        <div>
            <div style="font-size: 0.75rem; color: var(--on-surface-variant); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Belum Memiliki Akses</div>
            <div style="font-size: 1.35rem; font-weight: 700; color: {{ $stats['pedagang_belum_akses'] > 0 ? '#d97706' : 'var(--on-surface)' }}; line-height: 1.2;">{{ $stats['pedagang_belum_akses'] }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--on-surface-variant);">Lapak</span></div>
            <div style="font-size: 0.725rem; color: var(--on-surface-variant); margin-top: 2px;">Perlu diotorisasi Superadmin</div>
        </div>
    </div>
</div>

@if(session('activation_preview_link'))
    <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: var(--radius-md); padding: 16px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.1);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <div style="font-weight: 700; color: #065f46; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                <span>🔗</span> Tautan Aktivasi Akun Siap Digunakan
            </div>
            <span style="font-size: 0.75rem; background: #10b981; color: white; padding: 2px 8px; border-radius: 12px; font-weight: 600;">Privat & Aman</span>
        </div>
        <p style="font-size: 0.85rem; color: #047857; margin: 0 0 10px;">
            Email undangan telah dikirimkan ke kotak masuk pengguna. Anda juga dapat langsung menyalin tautan di bawah ini untuk dibagikan via WhatsApp/chat kepada pengguna terkait:
        </p>
        <div style="display: flex; gap: 8px; align-items: center;">
            <input type="text" id="copyActivationLink" value="{{ session('activation_preview_link') }}" readonly style="flex: 1; padding: 8px 12px; border-radius: 8px; border: 1px solid #6ee7b7; font-size: 0.8rem; background: white; font-family: monospace; color: #065f46;">
            <button type="button" class="btn btn-primary btn-sm" onclick="salinTautanAktivasi()" style="white-space: nowrap; font-size: 0.8rem;">
                📋 Salin Tautan
            </button>
        </div>
    </div>
@endif

<!-- Tab Switcher Navigasi Otoritas Pengguna -->
<div style="display: flex; border-bottom: 2px solid var(--outline-variant); margin-bottom: 20px; gap: 8px;">
    <button type="button" id="tabBtnPedagang" onclick="switchTab('pedagang')" style="padding: 12px 20px; font-weight: 700; font-size: 0.95rem; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; gap: 8px; color: var(--primary); border-bottom: 3px solid var(--primary); margin-bottom: -2px; transition: all 0.2s;">
        <span>🏪</span>
        Hak Akses Mitra Pedagang
        <span style="background: rgba(16, 185, 129, 0.15); color: #047857; padding: 2px 8px; border-radius: 999px; font-size: 0.75rem;">{{ $mitraPedagang->count() }}</span>
    </button>
    <button type="button" id="tabBtnStaf" onclick="switchTab('staf')" style="padding: 12px 20px; font-weight: 700; font-size: 0.95rem; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; gap: 8px; color: var(--on-surface-variant); border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.2s;">
        <span>👔</span>
        Staf & Pengelola Pasar
        <span style="background: var(--surface-container-high); color: var(--on-surface-variant); padding: 2px 8px; border-radius: 999px; font-size: 0.75rem;">{{ $stafPengguna->count() }}</span>
    </button>
</div>

<!-- ========================================================================= -->
<!-- TAB 1: HAK AKSES MITRA PEDAGANG (TAMPILAN KHUSUS & OTORITAS SUPERADMIN)    -->
<!-- ========================================================================= -->
<div id="contentTabPedagang">
    <!-- Banner Panduan Kebijakan Otoritas -->
    <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(6, 95, 70, 0.05) 100%); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 12px; padding: 14px 18px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 38px; height: 38px; border-radius: 8px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
            🔑
        </div>
        <div>
            <div style="font-weight: 700; color: #065f46; font-size: 0.925rem;">
                Otoritas Akses Mandiri Mitra Pedagang
            </div>
            <div style="color: #047857; font-size: 0.8125rem;">
                Pedagang hanya dapat masuk ke aplikasi jika telah <b>diberikan hak akses oleh Superadmin</b>. Jika pedagang berhenti jualan, Anda dapat mencabut akses login tanpa menghapus rekam riwayat transaksi.
            </div>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--surface-container-low); border-bottom: 1px solid var(--outline-variant);">
                    <tr>
                        <th style="padding: 14px 16px; text-align: left; font-size: 0.785rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">KIOS & TOKO</th>
                        <th style="padding: 14px 16px; text-align: left; font-size: 0.785rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">NAMA PEDAGANG & KONTAK</th>
                        <th style="padding: 14px 16px; text-align: left; font-size: 0.785rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">KREDENSIAL LOGIN</th>
                        <th style="padding: 14px 16px; text-align: center; font-size: 0.785rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">STATUS AKSES</th>
                        <th style="padding: 14px 16px; text-align: center; font-size: 0.785rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px; width: 230px;">KENDALI SUPERADMIN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mitraPedagang as $pdg)
                    <tr style="border-bottom: 1px solid var(--outline-variant); transition: background-color 0.2s;">
                        <!-- Kios & Toko -->
                        <td style="padding: 14px 16px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; border: 1px solid #bae6fd;">
                                    {{ $pdg->no_kios ?: 'Kios -' }}
                                </span>
                                @if($pdg->blok_kios)
                                    <span style="font-size: 0.75rem; color: var(--on-surface-variant);">
                                        ({{ $pdg->blok_kios }})
                                    </span>
                                @endif
                            </div>
                            <strong style="color: var(--on-surface); font-size: 0.9rem; margin-top: 4px; display: block;">
                                {{ $pdg->nama_usaha ?: 'Usaha Pedagang' }}
                            </strong>
                            <span style="font-size: 0.75rem; color: var(--on-surface-variant); text-transform: capitalize;">
                                Kategori: {{ $pdg->kategori_usaha ?: 'Umum' }}
                            </span>
                        </td>

                        <!-- Data Pedagang & Kontak -->
                        <td style="padding: 14px 16px; vertical-align: middle;">
                            <div style="font-weight: 600; color: var(--on-surface); font-size: 0.9rem;">
                                {{ $pdg->nama_pedagang }}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                <span>📞</span> {{ $pdg->no_telepon ?: '-' }}
                            </div>
                            <div style="margin-top: 4px;">
                                @if($pdg->status_pedagang === 'aktif')
                                    <span style="font-size: 0.7rem; background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Status Lapak: Aktif</span>
                                @elseif($pdg->status_pedagang === 'sementara_tutup')
                                    <span style="font-size: 0.7rem; background: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Sementara Tutup</span>
                                @else
                                    <span style="font-size: 0.7rem; background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Nonaktif / Berhenti</span>
                                @endif
                            </div>
                        </td>

                        <!-- Kredensial Login -->
                        <td style="padding: 14px 16px; vertical-align: middle;">
                            @if($pdg->user)
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                                    <span style="font-size: 0.75rem; color: var(--outline);">Username:</span>
                                    <code style="background: var(--surface-container-high); color: var(--primary); padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 0.825rem;">
                                        {{ $pdg->user->username }}
                                    </code>
                                    <button type="button" onclick="salinTeks('{{ $pdg->user->username }}', 'Username')" title="Salin Username" style="border: none; background: none; cursor: pointer; padding: 0; font-size: 0.8rem; color: var(--outline);">📋</button>
                                </div>
                                <div style="font-size: 0.785rem; color: var(--on-surface-variant);">
                                    {{ $pdg->user->email ?: 'Tanpa email terdaftar' }}
                                </div>
                                <div style="font-size: 0.725rem; color: #059669; margin-top: 2px;">
                                    Sandi: <span style="font-family: monospace; letter-spacing: 1px;">••••••••</span>
                                </div>
                            @else
                                <div style="color: var(--outline); font-size: 0.8rem; font-style: italic; display: flex; align-items: center; gap: 6px;">
                                    <span style="font-size: 1rem;">⚪</span> Belum dibuatkan akun login
                                </div>
                                <div style="font-size: 0.725rem; color: #f59e0b; margin-top: 2px;">
                                    Perlu izin Superadmin
                                </div>
                            @endif
                        </td>

                        <!-- Status Akses Akun -->
                        <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                            @if($pdg->user)
                                @if(($pdg->user->status_aktivasi ?? 'aktif') === 'pending')
                                    <span style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">
                                        ⏳ Menunggu Sandi
                                    </span>
                                @else
                                    <span style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">
                                        ✓ Hak Akses Aktif
                                    </span>
                                @endif
                            @else
                                <span style="background-color: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                                    Belum Ada Akses
                                </span>
                            @endif
                        </td>

                        <!-- Aksi / Kendali Superadmin -->
                        <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                            @if($pdg->user)
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                    <button type="button" class="btn btn-outline btn-sm" onclick="bukaModalResetPassword('{{ $pdg->user->id }}', '{{ addslashes($pdg->nama_pedagang) }}', '{{ $pdg->user->username }}')" style="padding: 4px 8px; font-size: 0.75rem; border-color: #f59e0b; color: #b45309;" title="Ganti/Reset Kata Sandi Pedagang">
                                        🔑 Sandi
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="editUser('{{ $pdg->user->id }}', '{{ addslashes($pdg->user->nama) }}', '{{ $pdg->user->username }}', '{{ $pdg->user->email }}', 'pedagang', '{{ $pdg->id }}', '{{ $pdg->user->status_aktivasi ?? 'aktif' }}')" style="padding: 4px 8px; font-size: 0.75rem;">
                                        ✏️ Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="konfirmasiCabutAkses('{{ $pdg->user->id }}', '{{ addslashes($pdg->nama_pedagang) }}', '{{ $pdg->no_kios ?: '-' }}', '{{ $pdg->user->username }}')" style="padding: 4px 8px; font-size: 0.75rem; background: #dc2626; color: white;" title="Cabut Akses Jika Pedagang Berhenti Jualan">
                                        🚫 Cabut Akses
                                    </button>
                                </div>
                            @else
                                <button type="button" class="btn btn-primary btn-sm" onclick="beriAksesLangsung('{{ $pdg->id }}', '{{ addslashes($pdg->nama_pedagang) }}', '{{ $pdg->no_kios ?: '' }}')" style="padding: 5px 12px; font-size: 0.775rem; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                                    <span>➕</span> Beri Hak Akses
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 40px; text-align: center; color: var(--outline);">
                            Belum ada data mitra pedagang yang terdaftar di sistem.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- TAB 2: STAF & PENGELOLA SISTEM (SUPERADMIN, ADMIN, PETUGAS LAPANGAN)      -->
<!-- ========================================================================= -->
<div id="contentTabStaf" style="display: none;">
    <div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div class="table-responsive">
            <table class="table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--surface-container-low); border-bottom: 1px solid var(--outline-variant);">
                    <tr>
                        <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">NAMA & EMAIL STAF</th>
                        <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">USERNAME</th>
                        <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">PERAN (ROLE)</th>
                        <th style="padding: 16px; text-align: center; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">STATUS AKUN</th>
                        <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">TERDAFTAR</th>
                        <th style="padding: 16px; text-align: center; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px; width: 220px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stafPengguna as $user)
                    <tr style="border-bottom: 1px solid var(--outline-variant); transition: background-color 0.2s;">
                        <td style="padding: 16px; vertical-align: middle;">
                            <strong style="color: var(--on-surface); font-weight: 600; display: block;">{{ $user->nama }}</strong>
                            <span style="font-size: 0.8rem; color: var(--on-surface-variant);">
                                {{ $user->email ?? 'Belum ada email' }}
                            </span>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; color: var(--on-surface-variant);">
                            <code>{{ $user->username }}</code>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            @if($user->role == 'superadmin')
                                <span style="background-color: #f3e8ff; color: #7e22ce; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Super Admin</span>
                            @elseif($user->role == 'admin')
                                <span style="background-color: #e0f2fe; color: #0369a1; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Admin</span>
                            @elseif($user->role == 'petugas')
                                <span style="background-color: #fef3c7; color: #b45309; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Petugas Lapangan</span>
                            @endif
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            @if(($user->status_aktivasi ?? 'aktif') === 'pending')
                                <span style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 999px; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                                    ⏳ Menunggu Sandi
                                </span>
                            @else
                                <span style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 999px; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                                    ✓ Aktif
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px; vertical-align: middle; color: var(--on-surface-variant); font-size: 0.85rem;">
                            {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="bukaModalResetPassword('{{ $user->id }}', '{{ addslashes($user->nama) }}', '{{ $user->username }}')" style="padding: 4px 8px; font-size: 0.75rem; border-color: #f59e0b; color: #b45309;" title="Ganti Kata Sandi">
                                    🔑 Sandi
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="editUser('{{ $user->id }}', '{{ addslashes($user->nama) }}', '{{ $user->username }}', '{{ $user->email }}', '{{ $user->role }}', '', '{{ $user->status_aktivasi ?? 'aktif' }}')" style="padding: 4px 10px; font-size: 0.75rem;">
                                    Edit
                                </button>
                                @if(auth()->id() != $user->id)
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser('{{ $user->id }}')" style="padding: 4px 10px; font-size: 0.75rem; background-color: #ef4444; color: white;">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($stafPengguna->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px; color: var(--outline);">
                            Belum ada akun pengelola yang terdaftar.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 1: BERI HAK AKSES LOGIN PEDAGANG LANGSUNG                           -->
<!-- ========================================================================= -->
<div id="modalBeriAkses" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
    <div class="modal-content card" style="width: 100%; max-width: 520px; padding: 24px; position: relative; border-radius: 14px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 1.4rem;">🔑</span>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--on-surface);">Otorisasi Hak Akses Pedagang</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalBeriAkses').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--outline);">&times;</button>
        </div>

        @if($pedagangTanpaAkun->isEmpty())
            <div style="background: rgba(16, 185, 129, 0.12); border: 1.5px solid #10b981; border-radius: 12px; padding: 18px; margin-bottom: 20px; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 8px;">🎉</div>
                <h4 style="margin: 0 0 6px; color: #065f46; font-size: 1.05rem; font-weight: 700;">Semua Mitra Pedagang Sudah Memiliki Akun Login!</h4>
                <p style="margin: 0; font-size: 0.85rem; color: #047857; line-height: 1.5;">
                    Seluruh pedagang yang terdaftar di pasar saat ini sudah memiliki akun aktif (termasuk <b>Sinta Erina Faradhila</b> dengan username <code>erin</code>).
                </p>
                <div style="margin-top: 14px; background: white; padding: 12px; border-radius: 8px; border: 1px solid #a7f3d0; text-align: left; font-size: 0.8rem; color: #065f46;">
                    💡 <b>Petunjuk:</b> Jika Anda ingin mengganti kata sandi atau memperbarui data akun pedagang yang sudah ada, silakan tutup jendela ini dan klik tombol <b>🔑 Sandi</b> atau <b>✏️ Edit</b> yang tersedia langsung pada baris tabel pedagang.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('modalBeriAkses').style.display='none'" style="padding: 10px 20px; font-weight: 700;">
                    ✓ Mengerti & Tutup Jendela
                </button>
            </div>
        @else
            <form action="{{ route('pengguna.beri-akses-pedagang') }}" method="POST">
                @csrf

                <!-- Pilih Pedagang -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Pilih Mitra Pedagang <span style="color: #ef4444;">*</span></label>
                    <select name="pedagang_id" id="akses_pedagang_id" class="form-select" required onchange="onPilihPedagangAkses(this)">
                        <option value="">-- Pilih Pedagang yang Belum Memiliki Akses --</option>
                        @foreach($pedagangTanpaAkun as $pTanpa)
                            <option value="{{ $pTanpa->id }}" data-nama="{{ $pTanpa->nama_pedagang }}" data-kios="{{ $pTanpa->no_kios }}">
                                {{ $pTanpa->nama_pedagang }} (Kios: {{ $pTanpa->no_kios ?: '-' }} - {{ $pTanpa->nama_usaha ?: 'Usaha' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Akun (Auto-filled) -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Nama Tampilan Pengguna</label>
                    <input type="text" name="nama" id="akses_nama" class="form-control" placeholder="Nama pedagang">
                </div>

                <!-- Username Login -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Username Login <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="username" id="akses_username" class="form-control" required placeholder="contoh: sriwahyuni" autocomplete="off">
                    <div style="font-size: 0.75rem; color: var(--on-surface-variant); margin-top: 4px;">
                        Username huruf kecil tanpa spasi yang akan digunakan pedagang untuk login.
                    </div>
                </div>

                <!-- Email (Opsional) -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Alamat Email <small style="font-weight: normal; color: var(--on-surface-variant);">(Opsional)</small></label>
                    <input type="email" name="email" id="akses_email" class="form-control" placeholder="contoh: pedagang@amanahledger.id">
                </div>

                <!-- Kata Sandi Awal -->
                <div class="form-group" style="margin-bottom: 18px;">
                    <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Kata Sandi (Password) Login <span style="color: #ef4444;">*</span></label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="password" id="akses_password" class="form-control" required minlength="6" value="password123" style="font-family: monospace; font-weight: 600;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('akses_password').value='pass' + Math.floor(1000 + Math.random() * 9000)" style="white-space: nowrap; font-size: 0.75rem;">
                            Acak Sandi
                        </button>
                    </div>
                    <div style="font-size: 0.75rem; color: #059669; margin-top: 4px;">
                        Sandi default: <code>password123</code>. Beritahukan sandi ini kepada pedagang terkait.
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modalBeriAkses').style.display='none'">Batal</button>
                    <button type="submit" class="btn btn-primary">Aktifkan & Beri Hak Akses</button>
                </div>
            </form>
        @endif
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: CABUT HAK AKSES LOGIN PEDAGANG (JIKA BERHENTI JUALAN)            -->
<!-- ========================================================================= -->
<div id="modalCabutAkses" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
    <div class="modal-content card" style="width: 100%; max-width: 480px; padding: 24px; position: relative; border-radius: 14px;">
        <div style="display: flex; align-items: flex-start; gap: 14px; margin-bottom: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                ⚠️
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #991b1b;">Cabut Akses Login Pedagang</h3>
                <p style="margin: 4px 0 0; font-size: 0.85rem; color: var(--on-surface-variant);">
                    Tindakan ini akan menghapus akun login pedagang terpilih.
                </p>
            </div>
        </div>

        <form id="formCabutAkses" method="POST">
            @csrf

            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 14px; margin-bottom: 16px; font-size: 0.85rem; color: #7f1d1d;">
                <div>Pedagang: <strong id="cabut_nama_pedagang">-</strong></div>
                <div>Kios: <strong id="cabut_kios_pedagang">-</strong> | Username: <code id="cabut_username">-</code></div>
                <div style="margin-top: 8px; font-size: 0.8rem; color: #991b1b;">
                    🛡️ <b>Keamanan Data:</b> Seluruh data lapak, riwayat pembayaran iuran, dan catatan tagihan tetap <b>tersimpan aman</b> di sistem untuk arsip laporan keuangan.
                </div>
            </div>

            <!-- Opsi status berhenti -->
            <div style="margin-bottom: 18px; background: var(--surface-container-low); padding: 12px; border-radius: 8px;">
                <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-size: 0.85rem; color: var(--on-surface);">
                    <input type="checkbox" name="tandai_berhenti" value="1" checked style="margin-top: 3px;">
                    <div>
                        <strong>Tandai status pedagang sebagai Nonaktif (Berhenti Jualan)</strong>
                        <div style="font-size: 0.75rem; color: var(--on-surface-variant);">
                            Status pedagang di menu Data Pedagang akan otomatis diubah menjadi Nonaktif.
                        </div>
                    </div>
                </label>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalCabutAkses').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger" style="background: #dc2626; color: white;">Cabut Akses Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: RESET KATA SANDI CEPAT                                           -->
<!-- ========================================================================= -->
<div id="modalResetPassword" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
    <div class="modal-content card" style="width: 100%; max-width: 440px; padding: 24px; position: relative; border-radius: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 1.3rem;">🔑</span>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--on-surface);">Reset Kata Sandi</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalResetPassword').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--outline);">&times;</button>
        </div>

        <form id="formResetPassword" method="POST">
            @csrf

            <div style="background: var(--surface-container-low); padding: 12px; border-radius: 8px; margin-bottom: 14px; font-size: 0.85rem;">
                <div>Pengguna: <strong id="reset_user_nama">-</strong></div>
                <div>Username: <code id="reset_user_username">-</code></div>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Kata Sandi Baru (Min. 6 Karakter) <span style="color: #ef4444;">*</span></label>
                <input type="text" name="password" id="input_reset_password" class="form-control" required minlength="6" placeholder="Masukkan kata sandi baru" value="password123" style="font-family: monospace;">
                <div style="font-size: 0.75rem; color: var(--on-surface-variant); margin-top: 4px;">
                    Ketik sandi baru dan serahkan kepada pemilik akun.
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalResetPassword').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kata Sandi Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: TAMBAH / UNDANG PENGGUNA (STAF / UMUM)                           -->
<!-- ========================================================================= -->
<div id="modalTambah" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
    <div class="modal-content card" style="width: 100%; max-width: 540px; padding: 24px; position: relative; border-radius: 14px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--on-surface);">Tambah Akun Pengguna / Staf</h3>
            <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--outline);">&times;</button>
        </div>
        
        <form action="{{ route('pengguna.store') }}" method="POST">
            @csrf
            
            <!-- Pilih Peran (Role) -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Peran (Hak Akses Akun) <span style="color: #ef4444;">*</span></label>
                <select name="role" id="tambah_role" class="form-select" required onchange="handleRoleChange(this.value)">
                    <option value="petugas">Petugas Lapangan (Mencatat transaksi & pembayaran)</option>
                    <option value="admin">Admin (Kelola semua data master & laporan)</option>
                    <option value="superadmin">Super Admin (Akses penuh tanpa batas)</option>
                    <option value="pedagang">Pedagang (Hanya melihat kios & tagihan miliknya)</option>
                </select>
            </div>

            <!-- Bagian Khusus Role Pedagang: Pilih Data Pedagang -->
            <div class="form-group" id="group_pedagang_pilihan" style="display: none; margin-bottom: 14px; background: #f0fdf4; padding: 12px; border-radius: 8px; border: 1px solid #bbf7d0;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem; color: #166534;">
                    Hubungkan ke Data Lapak / Pedagang <span style="color: #ef4444;">*</span>
                </label>
                <select name="pedagang_id" id="tambah_pedagang_id" class="form-select" onchange="autoFillPedagang(this)">
                    <option value="">-- Pilih Data Pedagang Yang Terdaftar --</option>
                    @foreach($pedagangTanpaAkun as $pdg)
                        <option value="{{ $pdg->id }}" data-nama="{{ $pdg->nama_pedagang }}" data-telepon="{{ $pdg->no_telepon }}">
                            {{ $pdg->nama_pedagang }} (Kios: {{ $pdg->no_kios ?? '-' }} - {{ $pdg->blok_kios ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nama Lengkap -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Nama Lengkap Pengguna <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama" id="tambah_nama" class="form-control" required placeholder="Nama pemilik akun">
            </div>

            <!-- Email Terdaftar -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Alamat Email</label>
                <input type="email" name="email" id="tambah_email" class="form-control" placeholder="contoh: pengguna@amanahledger.id">
            </div>

            <!-- Username untuk Login -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Username Login <span style="color: #ef4444;">*</span></label>
                <input type="text" name="username" id="tambah_username" class="form-control" required placeholder="Username tanpa spasi">
            </div>

            <!-- Pilihan Metode Kata Sandi -->
            <div class="form-group" style="margin-bottom: 16px; background: var(--surface-container-low); padding: 12px; border-radius: 8px;">
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; display: block;">
                    Penentuan Kata Sandi (Password):
                </label>
                
                <div style="margin-bottom: 8px;">
                    <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-size: 0.85rem; color: var(--on-surface);">
                        <input type="radio" name="metode_pembuatan" value="manual_password" checked onchange="toggleMetodePassword('manual_password')" style="margin-top: 3px;">
                        <div>
                            <strong>Tentukan Kata Sandi Manual Langsung</strong>
                            <div style="font-size: 0.75rem; color: var(--on-surface-variant);">
                                Admin/Superadmin langsung membuatkan password agar akun siap pakai saat ini juga.
                            </div>
                        </div>
                    </label>
                </div>

                <div>
                    <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-size: 0.85rem; color: var(--on-surface);">
                        <input type="radio" name="metode_pembuatan" value="aktivasi_email" onchange="toggleMetodePassword('aktivasi_email')" style="margin-top: 3px;">
                        <div>
                            <strong>Kirim Tautan Aktivasi ke Email Pengguna</strong>
                            <div style="font-size: 0.75rem; color: var(--on-surface-variant);">
                                Pengguna akan menerima email resmi dan menentukan kata sandinya sendiri secara mandiri.
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Input Manual Password -->
            <div class="form-group" id="group_manual_password" style="margin-bottom: 18px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Kata Sandi Manual (Min. 6 Karakter)</label>
                <input type="text" name="password" id="input_manual_password" class="form-control" value="password123" placeholder="Masukkan kata sandi">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitTambah">Simpan & Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 5: EDIT PENGGUNA                                                    -->
<!-- ========================================================================= -->
<div id="modalEdit" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
    <div class="modal-content card" style="width: 100%; max-width: 520px; padding: 24px; position: relative; border-radius: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--on-surface);">Edit Data Pengguna</h3>
            <button type="button" onclick="document.getElementById('modalEdit').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--outline);">&times;</button>
        </div>
        
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Nama Lengkap</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Alamat Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email pengguna">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Username</label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Peran (Role)</label>
                <select name="role" id="edit_role" class="form-select" required onchange="handleEditRoleChange(this.value)">
                    <option value="pedagang">Pedagang</option>
                    <option value="petugas">Petugas Lapangan</option>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>

            <div class="form-group" id="group_edit_pedagang" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Tautkan ke Kios Pedagang</label>
                <select name="pedagang_id" id="edit_pedagang_id" class="form-select">
                    <option value="">-- Tanpa Tautan Kios --</option>
                    @foreach($semuaPedagang as $pdg)
                        <option value="{{ $pdg->id }}">
                            {{ $pdg->nama_pedagang }} (Kios: {{ $pdg->no_kios ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Status Akun</label>
                <select name="status_aktivasi" id="edit_status_aktivasi" class="form-select" required>
                    <option value="aktif">Aktif (Dapat Login)</option>
                    <option value="pending">Pending (Menunggu Aktivasi Mandiri)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Reset Kata Sandi Baru <small style="font-weight: normal; color: var(--on-surface-variant);">(Kosongkan jika tidak diubah)</small></label>
                <input type="password" name="password" id="edit_password" class="form-control" minlength="6" placeholder="Ketik kata sandi baru untuk mengganti">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Hapus User Staf -->
<form id="formDelete" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Tab Switching Logic
    function switchTab(tab) {
        const btnPedagang = document.getElementById('tabBtnPedagang');
        const btnStaf = document.getElementById('tabBtnStaf');
        const contentPedagang = document.getElementById('contentTabPedagang');
        const contentStaf = document.getElementById('contentTabStaf');

        if (tab === 'pedagang') {
            contentPedagang.style.display = 'block';
            contentStaf.style.display = 'none';
            btnPedagang.style.color = 'var(--primary)';
            btnPedagang.style.borderBottomColor = 'var(--primary)';
            btnStaf.style.color = 'var(--on-surface-variant)';
            btnStaf.style.borderBottomColor = 'transparent';
            localStorage.setItem('activePenggunaTab', 'pedagang');
        } else {
            contentPedagang.style.display = 'none';
            contentStaf.style.display = 'block';
            btnStaf.style.color = 'var(--primary)';
            btnStaf.style.borderBottomColor = 'var(--primary)';
            btnPedagang.style.color = 'var(--on-surface-variant)';
            btnPedagang.style.borderBottomColor = 'transparent';
            localStorage.setItem('activePenggunaTab', 'staf');
        }
    }

    // Modal Beri Hak Akses Pedagang
    function bukaModalBeriAksesPedagang() {
        document.getElementById('modalBeriAkses').style.display = 'flex';
    }

    function beriAksesLangsung(pedagangId, nama, kios) {
        document.getElementById('modalBeriAkses').style.display = 'flex';
        const select = document.getElementById('akses_pedagang_id');
        select.value = pedagangId;
        document.getElementById('akses_nama').value = nama;
        
        // Auto-generate clean username
        const cleanName = nama.toLowerCase().replace(/[^a-z0-9]/g, '').slice(0, 15);
        document.getElementById('akses_username').value = cleanName;
        document.getElementById('akses_email').value = cleanName + '@amanahledger.id';
        document.getElementById('akses_password').value = 'password123';
    }

    function onPilihPedagangAkses(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        if (opt && opt.value) {
            const nama = opt.getAttribute('data-nama');
            const kios = opt.getAttribute('data-kios');
            document.getElementById('akses_nama').value = nama || '';
            const cleanName = (nama || '').toLowerCase().replace(/[^a-z0-9]/g, '').slice(0, 15);
            document.getElementById('akses_username').value = cleanName;
            document.getElementById('akses_email').value = cleanName ? (cleanName + '@amanahledger.id') : '';
        }
    }

    // Modal Cabut Akses Pedagang
    function konfirmasiCabutAkses(userId, namaPedagang, noKios, username) {
        const modal = document.getElementById('modalCabutAkses');
        document.getElementById('cabut_nama_pedagang').textContent = namaPedagang;
        document.getElementById('cabut_kios_pedagang').textContent = noKios;
        document.getElementById('cabut_username').textContent = username;
        document.getElementById('formCabutAkses').action = "{{ url('pengguna') }}/" + userId + "/cabut-akses-pedagang";
        modal.style.display = 'flex';
    }

    // Modal Reset Password Cepat
    function bukaModalResetPassword(userId, nama, username) {
        document.getElementById('modalResetPassword').style.display = 'flex';
        document.getElementById('reset_user_nama').textContent = nama;
        document.getElementById('reset_user_username').textContent = username;
        document.getElementById('formResetPassword').action = "{{ url('pengguna') }}/" + userId + "/reset-password";
        document.getElementById('input_reset_password').value = 'password123';
    }

    // Modal Tambah Pengguna Umum / Staf
    function bukaModalTambah() {
        document.getElementById('modalTambah').style.display = 'flex';
        handleRoleChange(document.getElementById('tambah_role').value);
    }

    function handleRoleChange(role) {
        const groupPedagang = document.getElementById('group_pedagang_pilihan');
        const selectPedagang = document.getElementById('tambah_pedagang_id');
        if (role === 'pedagang') {
            groupPedagang.style.display = 'block';
            selectPedagang.required = true;
        } else {
            groupPedagang.style.display = 'none';
            selectPedagang.required = false;
        }
    }

    function handleEditRoleChange(role) {
        const groupEditPedagang = document.getElementById('group_edit_pedagang');
        if (role === 'pedagang') {
            groupEditPedagang.style.display = 'block';
        } else {
            groupEditPedagang.style.display = 'none';
        }
    }

    function autoFillPedagang(selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const nama = selectedOpt.getAttribute('data-nama');
            const inputNama = document.getElementById('tambah_nama');
            const inputUsername = document.getElementById('tambah_username');
            if (nama && !inputNama.value) {
                inputNama.value = nama;
            }
            if (nama && !inputUsername.value) {
                const cleanUser = nama.toLowerCase().replace(/[^a-z0-9]/g, '').slice(0, 15);
                inputUsername.value = cleanUser + Math.floor(100 + Math.random() * 900);
            }
        }
    }

    function toggleMetodePassword(metode) {
        const groupManual = document.getElementById('group_manual_password');
        const inputManual = document.getElementById('input_manual_password');
        const btnSubmit = document.getElementById('btnSubmitTambah');
        const inputEmail = document.getElementById('tambah_email');

        if (metode === 'manual_password') {
            groupManual.style.display = 'block';
            inputManual.required = true;
            inputEmail.required = false;
            btnSubmit.textContent = 'Simpan & Buat Akun';
        } else {
            groupManual.style.display = 'none';
            inputManual.required = false;
            inputEmail.required = true;
            btnSubmit.textContent = 'Kirim Undangan Akses';
        }
    }

    function editUser(id, nama, username, email, role, pedagangId, statusAktivasi) {
        document.getElementById('formEdit').action = "{{ url('pengguna') }}/" + id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email || '';
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_pedagang_id').value = pedagangId || '';
        document.getElementById('edit_status_aktivasi').value = statusAktivasi || 'aktif';
        document.getElementById('edit_password').value = '';
        
        handleEditRoleChange(role);
        document.getElementById('modalEdit').style.display = 'flex';
    }

    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.')) {
            const form = document.getElementById('formDelete');
            form.action = "{{ url('pengguna') }}/" + id;
            form.submit();
        }
    }

    function salinTeks(teks, label) {
        navigator.clipboard.writeText(teks).then(function() {
            alert("✓ " + label + " berhasil disalin: " + teks);
        });
    }

    function salinTautanAktivasi() {
        const copyText = document.getElementById("copyActivationLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(function() {
            alert("✓ Tautan aktivasi berhasil disalin ke clipboard! Anda dapat mengirimkannya via WhatsApp kepada pedagang.");
        });
    }

    // Inisialisasi Tab aktif dari localStorage jika pernah dipilih
    document.addEventListener('DOMContentLoaded', function() {
        const savedTab = localStorage.getItem('activePenggunaTab');
        if (savedTab === 'staf') {
            switchTab('staf');
        } else {
            switchTab('pedagang');
        }
    });
</script>
@endsection
