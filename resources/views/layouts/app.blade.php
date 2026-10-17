<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Amanah Ledger') - SI Iuran UMKM</title>
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    
    <!-- Fast Theme Initialization (Prevents Light Flash) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    
    <!-- Custom view head elements -->
    @yield('head')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation (Desktop Fixed Sticky) -->
        <aside class="sidebar">
            <div class="sidebar-header" style="height: 72px; padding: 12px 18px;">
                <!-- Official AmanahLedger Emblem Logo -->
                <img src="{{ asset('images/logo.jpg') }}" alt="AmanahLedger Logo" style="height: 48px; width: 48px; border-radius: 12px; object-fit: contain; background: #ffffff; padding: 2px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); border: 1px solid var(--outline-variant); flex-shrink: 0;">
                <div>
                    <h1 class="sidebar-logo-text" style="font-size: 1.25rem;">AmanahLedger</h1>
                    <div class="sidebar-logo-sub" style="font-size: 0.65rem;">SI Iuran UMKM Pesantren</div>
                </div>
            </div>
            
            <nav class="sidebar-nav" style="gap: 4px;">
                @if((Auth::user()->role ?? '') === 'pedagang')
                    <!-- ========================================== -->
                    <!-- MENU PORTAL MITRA PEDAGANG (6 MENU UTAMA)  -->
                    <!-- ========================================== -->
                    <!-- 1. Dashboard Pedagang -->
                    <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        Dashboard Pedagang
                    </a>

                    <!-- 2. Tagihan Iuran Saya -->
                    <a href="{{ route('pedagang.tagihan') }}" class="sidebar-nav-item {{ Route::is('pedagang.tagihan*') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Tagihan Iuran Saya
                    </a>

                    <!-- 3. Riwayat Pembayaran -->
                    <a href="{{ route('riwayat.index') }}" class="sidebar-nav-item {{ Route::is('riwayat.*') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Pembayaran
                    </a>

                    <!-- 4. Tunggakan Saya -->
                    <a href="{{ route('pedagang.tunggakan') }}" class="sidebar-nav-item {{ Route::is('pedagang.tunggakan*') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Tunggakan Saya
                    </a>

                    <!-- 5. Notifikasi -->
                    <a href="{{ route('pedagang.notifikasi') }}" class="sidebar-nav-item {{ Route::is('pedagang.notifikasi*') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        Notifikasi
                    </a>

                    <!-- 6. Profil dan Data Lapak -->
                    <a href="{{ route('pedagang.profil') }}" class="sidebar-nav-item {{ Route::is('pedagang.profil*') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil dan Data Lapak
                    </a>
                @else
                    <!-- ========================================== -->
                    <!-- MENU STAF & PENGELOLA (ADMIN / PETUGAS)    -->
                    <!-- ========================================== -->
                    <!-- 1. Dashboard Utama -->
                    <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        Dashboard
                    </a>

                    @if(in_array(Auth::user()->role ?? '', ['superadmin', 'admin']))
                        <!-- 2. Data Pedagang -->
                        <a href="{{ route('pedagang.index') }}" class="sidebar-nav-item {{ Route::is('pedagang.*') ? 'active' : '' }}">
                            <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Data Pedagang
                        </a>

                        <!-- 3. Data Lapak -->
                        <a href="{{ route('lapak.index') }}" class="sidebar-nav-item {{ Route::is('lapak.*') ? 'active' : '' }}">
                            <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11h4v10m-4-10H7v10"></path>
                            </svg>
                            Data Lapak
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role ?? '', ['superadmin', 'admin', 'petugas']))
                        <!-- 4. Transaksi Pembayaran -->
                        <a href="{{ route('tagihan.input') }}" class="sidebar-nav-item {{ Route::is('tagihan.*') ? 'active' : '' }}">
                            <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Transaksi Pembayaran
                        </a>
                    @endif

                    <!-- 5. Riwayat Transaksi -->
                    <a href="{{ route('riwayat.index') }}" class="sidebar-nav-item {{ Route::is('riwayat.*') ? 'active' : '' }}">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Transaksi
                    </a>

                    @if(in_array(Auth::user()->role ?? '', ['superadmin', 'admin']))
                        <!-- 6. Laporan -->
                        <a href="{{ route('laporan.index') }}" class="sidebar-nav-item {{ Route::is('laporan.*') ? 'active' : '' }}">
                            <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Laporan
                        </a>

                        <!-- 7. Kalender Operasional & Penagihan -->
                        <a href="{{ route('kalender.index') }}" class="sidebar-nav-item {{ Route::is('kalender.*') ? 'active' : '' }}">
                            <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Kalender Operasional
                        </a>

                        <!-- 7. Deteksi Pedagang AI -->
                        <a href="{{ route('segmentasi.cek') }}" class="sidebar-nav-item {{ Route::is('segmentasi.cek') ? 'active' : '' }}">
                            <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cek Deteksi Pedagang
                        </a>

                        <!-- 8. Manajemen Pengguna -->
                        <a href="{{ route('pengguna.index') }}" class="sidebar-nav-item {{ Route::is('pengguna.*') ? 'active' : '' }}">
                            <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Manajemen Pengguna
                        </a>
                    @endif
                @endif
            </nav>
            
            <div class="sidebar-footer">
                <!-- Theme Toggle Row -->
                <div style="display: flex; align-items: center; justify-content: space-between; background: var(--surface-container); border: 1px solid var(--outline-variant); border-radius: var(--radius-md); padding: 6px 10px; margin-bottom: 10px;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; letter-spacing: 0.03em;">MODETEMA</span>
                    <button class="theme-toggle-btn" id="sidebar-theme-toggle" title="Ganti Tema (Gelap / Terang)">
                        🌙
                    </button>
                </div>

                <div class="user-profile-widget">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->nama ?? 'Pengguna' }}</div>
                        <div class="user-role">{{ Auth::user()->role ?? 'Role' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Mobile Navigation Header -->
        <div class="main-container">
            <header class="top-navbar">
                <button class="btn btn-secondary btn-sm top-navbar-toggle" id="mobile-menu-toggle" aria-label="Buka Menu Navigasi">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="top-navbar-brand">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="top-navbar-logo">
                    <span class="sidebar-logo-text top-navbar-brand-name">AmanahLedger</span>
                </div>
                <div class="top-navbar-actions">
                    <button class="theme-toggle-btn top-navbar-theme" id="mobile-theme-toggle" title="Ganti Tema (Gelap / Terang)">
                        🌙
                    </button>
                    <div class="user-avatar top-navbar-avatar">
                        {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </header>
            
            <!-- Mobile Menu Sidebar Drawer Overlay -->
            <div class="modal-overlay" id="mobile-menu-overlay">
                <div class="modal-container" style="max-width: 280px; height: 100%; border-radius: 0; margin-left: 0; margin-right: auto; display: flex; flex-direction: column;">
                    <div class="modal-header" style="background-color: var(--surface-container-lowest);">
                        <span style="font-weight: 700; color: var(--primary);">Navigasi</span>
                        <button class="modal-close" id="mobile-menu-close">&times;</button>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; padding: var(--space-md); overflow-y: auto;">
                        <nav class="sidebar-nav" style="padding: 0;">
                            @if((Auth::user()->role ?? '') === 'pedagang')
                                <!-- 1. Dashboard Pedagang -->
                                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                                    Dashboard Pedagang
                                </a>

                                <!-- 2. Tagihan Iuran Saya -->
                                <a href="{{ route('pedagang.tagihan') }}" class="sidebar-nav-item {{ Route::is('pedagang.tagihan*') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    Tagihan Iuran Saya
                                </a>

                                <!-- 3. Riwayat Pembayaran -->
                                <a href="{{ route('riwayat.index') }}" class="sidebar-nav-item {{ Route::is('riwayat.*') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Riwayat Pembayaran
                                </a>

                                <!-- 4. Tunggakan Saya -->
                                <a href="{{ route('pedagang.tunggakan') }}" class="sidebar-nav-item {{ Route::is('pedagang.tunggakan*') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Tunggakan Saya
                                </a>

                                <!-- 5. Notifikasi -->
                                <a href="{{ route('pedagang.notifikasi') }}" class="sidebar-nav-item {{ Route::is('pedagang.notifikasi*') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    Notifikasi
                                </a>

                                <!-- 6. Profil dan Data Lapak -->
                                <a href="{{ route('pedagang.profil') }}" class="sidebar-nav-item {{ Route::is('pedagang.profil*') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profil dan Data Lapak
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                                    Dashboard
                                </a>

                                @if(in_array(Auth::user()->role ?? '', ['superadmin', 'admin']))
                                    <a href="{{ route('pedagang.index') }}" class="sidebar-nav-item {{ Route::is('pedagang.*') ? 'active' : '' }}">
                                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Data Pedagang
                                    </a>
                                    <a href="{{ route('lapak.index') }}" class="sidebar-nav-item {{ Route::is('lapak.*') ? 'active' : '' }}">
                                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11h4v10m-4-10H7v10"></path></svg>
                                        Data Lapak
                                    </a>
                                @endif

                                @if(in_array(Auth::user()->role ?? '', ['superadmin', 'admin', 'petugas']))
                                    <a href="{{ route('tagihan.input') }}" class="sidebar-nav-item {{ Route::is('tagihan.*') ? 'active' : '' }}">
                                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                        Transaksi Pembayaran
                                    </a>
                                @endif

                                <a href="{{ route('riwayat.index') }}" class="sidebar-nav-item {{ Route::is('riwayat.*') ? 'active' : '' }}">
                                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Riwayat Transaksi
                                </a>

                                @if(in_array(Auth::user()->role ?? '', ['superadmin', 'admin']))
                                    <a href="{{ route('laporan.index') }}" class="sidebar-nav-item {{ Route::is('laporan.*') ? 'active' : '' }}">
                                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Laporan
                                    </a>
                                    <a href="{{ route('kalender.index') }}" class="sidebar-nav-item {{ Route::is('kalender.*') ? 'active' : '' }}">
                                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Kalender Operasional
                                    </a>
                                    <a href="{{ route('segmentasi.cek') }}" class="sidebar-nav-item {{ Route::is('segmentasi.cek') ? 'active' : '' }}">
                                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Cek Deteksi Pedagang
                                    </a>
                                    <a href="{{ route('pengguna.index') }}" class="sidebar-nav-item {{ Route::is('pengguna.*') ? 'active' : '' }}">
                                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        Manajemen Pengguna
                                    </a>
                                @endif
                            @endif
                        </nav>
                        <div style="margin-top: var(--space-md);">
                            <div class="user-profile-widget" style="margin-bottom: var(--space-md);">
                                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}</div>
                                <div class="user-info">
                                    <div class="user-name">{{ Auth::user()->nama ?? 'Pengguna' }}</div>
                                    <div class="user-role">{{ Auth::user()->role ?? 'Role' }}</div>
                                </div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="logout-btn">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Yield Content -->
            <main class="content-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('mobile-menu-close');
            const overlay = document.getElementById('mobile-menu-overlay');
            
            if (toggleBtn && overlay) {
                toggleBtn.addEventListener('click', function() {
                    overlay.classList.add('active');
                });
            }
            
            if (closeBtn && overlay) {
                closeBtn.addEventListener('click', function() {
                    overlay.classList.remove('active');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        overlay.classList.remove('active');
                    }
                });
            }
            
            // Live Admin Timezone Clock Handler
            function updateLiveAdminClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const timeStr = `${hours}:${minutes}:${seconds}`;

                const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                
                const dayName = daysIndo[now.getDay()];
                const dateNum = now.getDate();
                const monthName = monthsIndo[now.getMonth()];
                const yearNum = now.getFullYear();
                const dateStr = `${dayName}, ${dateNum} ${monthName} ${yearNum}`;

                const offsetMin = -now.getTimezoneOffset();
                const offsetHours = offsetMin / 60;
                let tzName = '';
                if (offsetHours === 7) tzName = 'WIB';
                else if (offsetHours === 8) tzName = 'WITA';
                else if (offsetHours === 9) tzName = 'WIT';
                else {
                    const sign = offsetHours >= 0 ? '+' : '-';
                    tzName = `UTC${sign}${Math.abs(offsetHours)}`;
                }

                document.querySelectorAll('.live-admin-time').forEach(el => el.textContent = timeStr);
                document.querySelectorAll('.live-admin-date').forEach(el => el.textContent = dateStr);
                document.querySelectorAll('.live-admin-tz').forEach(el => el.textContent = tzName);
            }

            // Theme Toggle Switch Handler
            const currentTheme = localStorage.getItem('app-theme') || 'light';
            const themeBtns = document.querySelectorAll('#sidebar-theme-toggle, #mobile-theme-toggle');

            function updateThemeUI(theme) {
                themeBtns.forEach(btn => {
                    btn.innerHTML = theme === 'dark' ? '☀️' : '🌙';
                    btn.setAttribute('title', theme === 'dark' ? 'Ganti ke Tema Terang' : 'Ganti ke Tema Gelap');
                });
            }

            updateThemeUI(currentTheme);

            themeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', activeTheme);
                    localStorage.setItem('app-theme', activeTheme);
                    updateThemeUI(activeTheme);
                });
            });

            updateLiveAdminClock();
            setInterval(updateLiveAdminClock, 1000);
        });
    </script>
    
    @yield('scripts')
</body>
</html>
