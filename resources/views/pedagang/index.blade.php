@extends('layouts.app')

@section('title', 'Data Pedagang')

@section('content')
@php
    // Hitung jumlah filter yang sedang aktif
    $activeFilterCount = 0;
    if (request('status_pedagang')) $activeFilterCount++;
    if (request('status_legal')) $activeFilterCount++;
    if (request('blok_kios')) $activeFilterCount++;
    if (request('kategori_usaha')) $activeFilterCount++;
@endphp

<!-- Page Header -->
<div class="page-header pedagang-page-header">
    <div class="pedagang-title-group">
        <h2 class="page-title">Data Pedagang</h2>
        <p class="page-subtitle">Kelola data pedagang UMKM Pesantren & pasar harian</p>
    </div>
    
    <div class="pedagang-header-actions">
        <!-- Top Search Form -->
        <form action="{{ route('pedagang.index') }}" method="GET" class="pedagang-search-form">
            <div style="position: relative; width: 100%;">
                <input type="text" name="search" class="form-input" placeholder="Cari pedagang / lapak..." value="{{ request('search') }}" style="min-height: 38px; padding-left: 36px; width: 100%; font-size: 0.84375rem; border-radius: 8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position: absolute; left: 12px; top: 11px; color: #94a3b8;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            @if(request('status_pedagang')) <input type="hidden" name="status_pedagang" value="{{ request('status_pedagang') }}"> @endif
            @if(request('status_legal')) <input type="hidden" name="status_legal" value="{{ request('status_legal') }}"> @endif
            @if(request('blok_kios')) <input type="hidden" name="blok_kios" value="{{ request('blok_kios') }}"> @endif
            @if(request('kategori_usaha')) <input type="hidden" name="kategori_usaha" value="{{ request('kategori_usaha') }}"> @endif
        </form>

        <div class="pedagang-btn-secondary-row">
            <!-- Filter Toggle Button (Collapsible) -->
            <button type="button" class="btn btn-secondary" id="toggle-filter-btn" aria-expanded="{{ $activeFilterCount > 0 ? 'true' : 'false' }}" title="Buka/Tutup Pilihan Filter" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Filter</span>
                @if($activeFilterCount > 0)
                    <span class="badge-filter-count">{{ $activeFilterCount }}</span>
                @endif
                <svg class="chevron-icon" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="transition: transform 0.2s ease; transform: {{ $activeFilterCount > 0 ? 'rotate(180deg)' : 'rotate(0deg)' }};">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Export Button -->
            <a href="{{ route('export.pedagang', request()->query()) }}" class="btn btn-secondary" title="Export Excel Data Pedagang" style="display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Export</span>
            </a>
        </div>

        <!-- Tambah Pedagang Button -->
        <a href="{{ route('pedagang.create') }}" class="btn btn-primary btn-tambah-pedagang" style="white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Pedagang</span>
        </a>
    </div>
</div>

<!-- Collapsible Filter Panel -->
<div id="pedagang-filter-panel" class="pedagang-filter-panel {{ $activeFilterCount > 0 ? '' : 'is-collapsed' }}">
    <div class="filter-panel-header">
        <div style="display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            <span style="font-weight: 700; font-size: 0.8125rem;">Opsi Penyaringan Pedagang</span>
            @if($activeFilterCount > 0)
                <span class="badge badge-status-aktif" style="font-size: 0.65rem; padding: 2px 7px;">{{ $activeFilterCount }} Filter Diterapkan</span>
            @endif
        </div>
        <button type="button" id="close-filter-btn" class="btn btn-secondary btn-sm" style="min-height: 26px; height: 26px; padding: 0 8px; font-size: 0.75rem;">
            Tutup
        </button>
    </div>

    <form action="{{ route('pedagang.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
        
        <div style="flex: 1; min-width: 140px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Status</label>
            <select name="status_pedagang" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status_pedagang') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="sementara_tutup" {{ request('status_pedagang') == 'sementara_tutup' ? 'selected' : '' }}>Tutup Sementara</option>
                <option value="nonaktif" {{ request('status_pedagang') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Legalitas</label>
            <select name="status_legal" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
                <option value="">Semua Legalitas</option>
                <option value="legal" {{ request('status_legal') == 'legal' ? 'selected' : '' }}>Legal</option>
                <option value="tutup_sementara" {{ request('status_legal') == 'tutup_sementara' ? 'selected' : '' }}>Tutup Sementara</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 120px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Blok Kios</label>
            <select name="blok_kios" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
                <option value="">Semua Blok</option>
                @foreach($blokList as $blok)
                    <option value="{{ $blok }}" {{ request('blok_kios') == $blok ? 'selected' : '' }}>{{ $blok }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Kategori Usaha</label>
            <select name="kategori_usaha" class="form-select" style="min-height: 36px; padding: 4px 10px; font-size: 0.8125rem;">
                <option value="">Semua Kategori</option>
                <option value="kuliner" {{ request('kategori_usaha') == 'kuliner' ? 'selected' : '' }}>Kuliner</option>
                <option value="pakaian" {{ request('kategori_usaha') == 'pakaian' ? 'selected' : '' }}>Pakaian</option>
                <option value="elektronik" {{ request('kategori_usaha') == 'elektronik' ? 'selected' : '' }}>Elektronik</option>
                <option value="sayuran" {{ request('kategori_usaha') == 'sayuran' ? 'selected' : '' }}>Sayuran</option>
                <option value="buah" {{ request('kategori_usaha') == 'buah' ? 'selected' : '' }}>Buah</option>
                <option value="sembako" {{ request('kategori_usaha') == 'sembako' ? 'selected' : '' }}>Sembako</option>
                <option value="lainnya" {{ request('kategori_usaha') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>

        <div style="display: flex; gap: 6px; align-items: flex-end; flex-wrap: wrap;">
            <button type="submit" class="btn btn-primary btn-sm" style="min-height: 36px; padding: 0 14px;">Terapkan Filter</button>
            <a href="{{ route('pedagang.index') }}" class="btn btn-secondary btn-sm" style="min-height: 36px; padding: 0 12px; display: inline-flex; align-items: center;">Reset</a>
            <a href="{{ route('export.pedagang', request()->query()) }}" class="btn btn-secondary btn-sm" style="min-height: 36px; padding: 0 12px; display: inline-flex; align-items: center; gap: 4px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </a>
        </div>
    </form>
</div>

<!-- Slide System Wrapper (Supports Dynamic AJAX & Infinite Slides) -->
<div id="pedagang-slide-content" class="pedagang-slide-content">
    <!-- Pedagang Slide Container (Animasi Transisi Halus) -->
    <div id="pedagang-slide-wrapper" class="pedagang-slide-wrapper">
        <div id="pedagang-slide-pane" class="pedagang-slide-pane">
            <div class="pedagang-grid">
                @forelse($pedagangs as $pdg)
                    @php
                        // Hitung Inisial Avatar
                        $words = explode(' ', trim($pdg->nama_pedagang));
                        $initials = '';
                        if (count($words) >= 2) {
                            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($pdg->nama_pedagang, 0, 2));
                        }

                        // Warna Avatar konsisten berdasarkan ID Pedagang
                        $colors = ['#854d0e', '#003d9b', '#065f46', '#1e3a8a', '#581c87', '#0284c7', '#374151'];
                        $avatarBg = $colors[abs(crc32($pdg->id)) % count($colors)];
                    @endphp

                    <div class="pedagang-card">
                        <!-- Header Kartu: Avatar (34px), Nama Pedagang, serta Nama Toko & Status yang sejajar -->
                        <div class="pedagang-card-top">
                            @if($pdg->foto_profil_url)
                                <img src="{{ $pdg->foto_profil_url }}" 
                                     alt="{{ $pdg->nama_pedagang }}" 
                                     class="pedagang-avatar" 
                                     style="object-fit: cover;">
                            @else
                                <div class="pedagang-avatar" style="background-color: {{ $avatarBg }};">
                                    {{ $initials }}
                                </div>
                            @endif
                            <div class="pedagang-identity-col">
                                <div class="pedagang-name" title="{{ $pdg->nama_pedagang }}">
                                    {{ $pdg->nama_pedagang }}
                                </div>
                                <!-- Baris Sejajar: Nama Toko di kiri & Status di kanan -->
                                <div class="pedagang-shop-status-row">
                                    <div class="pedagang-shop" title="{{ $pdg->nama_usaha ?? 'Lapak Retribusi' }}">
                                        {{ $pdg->nama_usaha ?? 'Lapak Retribusi' }}
                                    </div>
                                    <div class="pedagang-status-wrapper">
                                        @if($pdg->status_pedagang == 'aktif')
                                            <span class="badge badge-status-aktif" style="border-radius: 999px; padding: 2px 7px; font-weight: 600; text-transform: capitalize; font-size: 0.6875rem;">Aktif</span>
                                        @elseif($pdg->status_pedagang == 'sementara_tutup')
                                            <span class="badge badge-status-sementara_tutup" style="border-radius: 999px; padding: 2px 7px; font-weight: 600; text-transform: capitalize; font-size: 0.6875rem;">Tutup Sementara</span>
                                        @else
                                            <span class="badge badge-status-nonaktif" style="border-radius: 999px; padding: 2px 7px; font-weight: 600; text-transform: capitalize; font-size: 0.6875rem;">Nonaktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2: Nomor Lapak & Kategori dalam Satu Baris -->
                        <div class="pedagang-card-row-meta">
                            <div class="pedagang-meta-item">
                                <span class="pedagang-meta-label">No Lapak:</span>
                                <span class="pedagang-meta-val badge-lapak">{{ $pdg->no_kios ?? '-' }}</span>
                            </div>
                            <div class="pedagang-meta-item">
                                <span class="pedagang-meta-label">Kategori:</span>
                                <span class="pedagang-meta-val" style="text-transform: capitalize;">{{ $pdg->kategori_usaha ?? '-' }}</span>
                            </div>
                        </div>

                        <!-- Baris 3: Tarif Sewa & Tombol Aksi Detail/Edit dalam Satu Baris -->
                        <div class="pedagang-card-footer">
                            <div class="pedagang-tarif-group">
                                <span class="pedagang-tarif-label">Tarif:</span>
                                <span class="pedagang-tarif-val">
                                    Rp {{ number_format($pdg->tarif->nominal_harian ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="pedagang-tarif-unit">/hari</span>
                            </div>

                            <div class="pedagang-actions">
                                <a href="{{ route('pedagang.show', $pdg->id) }}" class="btn btn-secondary btn-compact" title="Lihat Detail Pedagang">Detail</a>
                                <a href="{{ route('pedagang.edit', $pdg->id) }}" class="btn btn-secondary btn-compact" title="Edit Data Pedagang">Edit</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; background: #ffffff; border: 1px solid var(--outline-variant); border-radius: 10px; padding: 36px 20px; text-align: center; color: var(--outline);">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 10px; color: #94a3b8; display: block;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <div style="font-weight: 700; color: var(--on-surface); margin-bottom: 4px;">Data Pedagang Tidak Ditemukan</div>
                        <div style="font-size: 0.8125rem;">Coba sesuaikan kata kunci pencarian atau reset filter Anda.</div>
                        <a href="{{ route('pedagang.index') }}" class="btn btn-secondary btn-sm" style="margin-top: 12px; display: inline-flex;">Reset Filter & Pencarian</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom Slide Controller Dock (Focused Carousel + Chevron < >) -->
    <div class="pedagang-slide-dock">
        @php
            $currentPage = $pedagangs->currentPage();
            $lastPage = max(1, $pedagangs->lastPage());
            $hasPrev = $currentPage > 1;
            $hasNext = $currentPage < $lastPage;
        @endphp

        <!-- Chevron Prev Button < -->
        @if($hasPrev)
            <a href="{{ $pedagangs->previousPageUrl() }}" 
               class="slide-chevron-btn ajax-slide-btn" 
               data-slide-dir="prev" 
               title="Slide Sebelumnya" 
               aria-label="Slide Sebelumnya">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </a>
        @else
            <button type="button" class="slide-chevron-btn disabled" disabled aria-label="Slide Sebelumnya" title="Slide Pertama">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
        @endif

        <!-- Focused Slide Numbers Viewport -->
        <div class="slide-carousel-viewport">
            <div class="slide-carousel-track" id="slideCarouselTrack">
                @for($p = 1; $p <= $lastPage; $p++)
                    @php
                        $diff = abs($p - $currentPage);
                    @endphp
                    @if($p == $currentPage)
                        <span class="slide-num-item active" title="Slide {{ $p }} (Sedang aktif)">{{ $p }}</span>
                    @elseif($diff == 1)
                        <a href="{{ $pedagangs->url($p) }}" 
                           class="slide-num-item neighbor-blurred ajax-slide-btn" 
                           data-slide-dir="{{ $p > $currentPage ? 'next' : 'prev' }}" 
                           title="Ke Slide {{ $p }}">{{ $p }}</a>
                    @else
                        <a href="{{ $pedagangs->url($p) }}" 
                           class="slide-num-item hidden-distant ajax-slide-btn" 
                           data-slide-dir="{{ $p > $currentPage ? 'next' : 'prev' }}" 
                           title="Ke Slide {{ $p }}">{{ $p }}</a>
                    @endif
                @endfor
            </div>
        </div>

        <!-- Chevron Next Button > -->
        @if($hasNext)
            <a href="{{ $pedagangs->nextPageUrl() }}" 
               class="slide-chevron-btn ajax-slide-btn" 
               data-slide-dir="next" 
               title="Slide Berikutnya" 
               aria-label="Slide Berikutnya">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        @else
            <button type="button" class="slide-chevron-btn disabled" disabled aria-label="Slide Berikutnya" title="Slide Terakhir">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Filter Panel
        const toggleFilterBtn = document.getElementById('toggle-filter-btn');
        const closeFilterBtn = document.getElementById('close-filter-btn');
        const filterPanel = document.getElementById('pedagang-filter-panel');
        const chevronIcon = toggleFilterBtn ? toggleFilterBtn.querySelector('.chevron-icon') : null;

        function toggleFilterPanel() {
            if (!filterPanel) return;
            const isCollapsed = filterPanel.classList.toggle('is-collapsed');
            if (toggleFilterBtn) {
                toggleFilterBtn.setAttribute('aria-expanded', !isCollapsed);
            }
            if (chevronIcon) {
                chevronIcon.style.transform = isCollapsed ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        if (toggleFilterBtn) {
            toggleFilterBtn.addEventListener('click', toggleFilterPanel);
        }
        if (closeFilterBtn) {
            closeFilterBtn.addEventListener('click', toggleFilterPanel);
        }

        // Setup Slide Navigation & Gestures
        initSlideNavigation();
    });

    // Function to handle smooth AJAX slide loading
    let isSlideLoading = false;

    function loadSlideAjax(targetUrl, direction = 'next') {
        if (isSlideLoading) return;
        isSlideLoading = true;

        const slidePane = document.getElementById('pedagang-slide-pane');
        const carouselTrack = document.getElementById('slideCarouselTrack');
        const outAnim = direction === 'next' ? 'slide-out-left' : 'slide-out-right';
        const inAnim = direction === 'next' ? 'slide-in-right' : 'slide-in-left';
        const trackAnim = direction === 'next' ? 'track-slide-next' : 'track-slide-prev';

        if (slidePane) {
            slidePane.classList.remove('slide-in-right', 'slide-in-left');
            slidePane.classList.add(outAnim);
        }
        if (carouselTrack) {
            carouselTrack.classList.remove('track-slide-next', 'track-slide-prev');
        }

        fetch(targetUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('pedagang-slide-content');
            const targetContainer = document.getElementById('pedagang-slide-content');

            if (newContent && targetContainer) {
                targetContainer.innerHTML = newContent.innerHTML;
                window.history.pushState({ path: targetUrl }, '', targetUrl);

                // Scroll view smoothly to top when switching slides
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Re-animate in
                const newPane = document.getElementById('pedagang-slide-pane');
                if (newPane) {
                    newPane.classList.add(inAnim);
                }
                const newTrack = document.getElementById('slideCarouselTrack');
                if (newTrack) {
                    newTrack.classList.add(trackAnim);
                }

                // Re-bind click listeners
                bindSlideButtons();
            } else {
                window.location.href = targetUrl;
            }
        })
        .catch(err => {
            console.warn('AJAX slide transition error, falling back to page reload:', err);
            window.location.href = targetUrl;
        })
        .finally(() => {
            isSlideLoading = false;
        });
    }

    // Bind AJAX buttons
    function bindSlideButtons() {
        document.querySelectorAll('.ajax-slide-btn').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                const dir = this.getAttribute('data-slide-dir') || 'next';
                if (url) {
                    loadSlideAjax(url, dir);
                }
            };
        });
    }

    // Initialize all slide handlers
    function initSlideNavigation() {
        bindSlideButtons();

        // Browser Back / Forward History support
        window.addEventListener('popstate', function() {
            window.location.reload();
        });

        // Keyboard Arrow Navigation (Left = Prev Slide, Right = Next Slide)
        document.addEventListener('keydown', function(e) {
            const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
            if (activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select') {
                return; // Jangan ganggu saat mengetik pencarian/filter
            }

            if (e.key === 'ArrowLeft') {
                const prevBtn = document.querySelector('.slide-chevron-btn[data-slide-dir="prev"]');
                if (prevBtn) {
                    prevBtn.click();
                }
            } else if (e.key === 'ArrowRight') {
                const nextBtn = document.querySelector('.slide-chevron-btn[data-slide-dir="next"]');
                if (nextBtn) {
                    nextBtn.click();
                }
            }
        });

        // Touch Swipe Gesture Support on Mobile & Tablet
        const slideWrapper = document.getElementById('pedagang-slide-wrapper');
        if (slideWrapper) {
            let touchStartX = 0;
            let touchEndX = 0;

            slideWrapper.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            slideWrapper.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipeGesture();
            }, { passive: true });

            function handleSwipeGesture() {
                const diff = touchEndX - touchStartX;
                if (Math.abs(diff) > 60) {
                    if (diff < 0) {
                        // Geser ke kiri -> Slide Berikutnya
                        const nextBtn = document.querySelector('.slide-chevron-btn[data-slide-dir="next"]');
                        if (nextBtn) {
                            nextBtn.click();
                        }
                    } else {
                        // Geser ke kanan -> Slide Sebelumnya
                        const prevBtn = document.querySelector('.slide-chevron-btn[data-slide-dir="prev"]');
                        if (prevBtn) {
                            prevBtn.click();
                        }
                    }
                }
            }
        }
    }
</script>
@endsection


