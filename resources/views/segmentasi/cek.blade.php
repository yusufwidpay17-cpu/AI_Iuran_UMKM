@extends('layouts.app')

@section('title', 'Cek Deteksi Pedagang')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Cek Deteksi Perilaku Pedagang</h2>
        <p class="page-subtitle">Analisis individual perilaku pembayaran pedagang menggunakan model segmentasi K-Means.</p>
    </div>
</div>

<!-- Merchant Selection Card -->
<div class="card" style="margin-bottom: var(--space-lg);">
    <form action="{{ route('segmentasi.cek') }}" method="GET">
        <div style="display: flex; gap: var(--space-md); align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 280px; margin-bottom: 0;">
                <label for="pedagang_id" class="form-label" style="font-weight: 600; color: var(--primary);">Pilih Pedagang / UMKM</label>
                <select name="pedagang_id" id="pedagang_id" class="form-control" style="min-height: 46px; width: 100%; border: 1px solid var(--outline-variant); border-radius: var(--radius-default); padding: 0 var(--space-sm); font-size: 0.9375rem;">
                    <option value="">-- Pilih Pedagang --</option>
                    @foreach($pedagangs as $pdg)
                        <option value="{{ $pdg->id }}" {{ request('pedagang_id') == $pdg->id ? 'selected' : '' }}>
                            {{ $pdg->nama_pedagang }} (Kios {{ $pdg->no_kios }} - {{ $pdg->blok_kios }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="min-height: 46px; padding: 0 var(--space-lg); display: inline-flex; align-items: center; gap: 8px; font-weight: 600; box-shadow: var(--shadow-sm);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cek Segmentasi
                </button>
            </div>
        </div>
    </form>
</div>

@if($selectedPedagang)
    <div class="grid-2" style="gap: var(--space-lg); display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
        
        <!-- AI Decision & Category -->
        <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
            
            <!-- Segment Badge Card -->
            @php
                $themeColor = '#737685';
                $bgColor = 'var(--surface-container-low)';
                $textColor = 'var(--on-surface)';
                $badgeText = 'Belum Ada Model';
                
                if ($segment) {
                    $label = $segment->label_segmen;
                    if ($label === 'Pembayar Rajin') {
                        $themeColor = '#0d8a43';
                        $bgColor = '#ebfcf2';
                        $textColor = '#0c7338';
                        $badgeText = 'Pembayar Rajin';
                    } elseif ($label === 'Cukup Rajin') {
                        $themeColor = '#a67200';
                        $bgColor = '#fffcf0';
                        $textColor = '#805700';
                        $badgeText = 'Cukup Rajin';
                    } elseif ($label === 'Berisiko') {
                        $themeColor = '#c92323';
                        $bgColor = '#fff5f5';
                        $textColor = '#a61c1c';
                        $badgeText = 'Berisiko';
                    }
                }
            @endphp
            
            <div class="card" style="border-left: 6px solid {{ $themeColor }}; background-color: {{ $bgColor }}; display: flex; flex-direction: column; gap: var(--space-sm); padding: var(--space-lg);">
                <div style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--outline);">
                    Hasil Klasifikasi Perilaku (AI)
                </div>
                <div style="font-family: var(--font-heading); font-size: 2.25rem; font-weight: 800; color: {{ $textColor }}; margin: var(--space-xs) 0;">
                    {{ $badgeText }}
                </div>
                <div style="font-size: 0.875rem; color: var(--on-surface-variant); font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    <svg width="18" height="18" fill="none" stroke="{{ $themeColor }}" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Terakhir dianalisis: {{ $segment ? \Carbon\Carbon::parse($segment->tanggal_analisis)->format('d/m/Y H:i') : 'Belum Pernah' }}
                </div>
            </div>

            <!-- Profile Info Card -->
            <div class="card">
                <h3 class="card-title" style="margin-bottom: var(--space-md); border-bottom: 1px solid var(--outline-variant); padding-bottom: var(--space-sm);">
                    Profil Usaha Pedagang
                </h3>
                <div class="profile-details-list" style="display: flex; flex-direction: column; gap: var(--space-sm);">
                    <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                        <span style="color: var(--outline); font-weight: 500;">Nama Pedagang</span>
                        <span style="font-weight: 700; color: var(--on-surface);">{{ $selectedPedagang->nama_pedagang }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                        <span style="color: var(--outline); font-weight: 500;">Nama Usaha</span>
                        <span style="font-weight: 600; color: var(--on-surface);">{{ $selectedPedagang->nama_usaha }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                        <span style="color: var(--outline); font-weight: 500;">No Kios / Blok</span>
                        <span style="font-weight: 600; color: var(--on-surface);">{{ $selectedPedagang->no_kios }} ({{ $selectedPedagang->blok_kios }})</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                        <span style="color: var(--outline); font-weight: 500;">Kategori</span>
                        <span style="text-transform: capitalize; font-weight: 600; color: var(--on-surface);">{{ $selectedPedagang->kategori_usaha }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                        <span style="color: var(--outline); font-weight: 500;">Tarif Harian</span>
                        <span style="font-weight: 700; color: var(--primary);">Rp {{ number_format($selectedPedagang->tarif->nominal_harian, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- AI Explanation -->
            <div class="card" style="border-top: 4px solid var(--primary);">
                <h3 class="card-title" style="margin-bottom: var(--space-xs); display: flex; align-items: center; gap: 8px;">
                    <svg width="22" height="22" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Penjelasan Model AI
                </h3>
                <p style="font-size: 0.9375rem; color: var(--on-surface-variant); line-height: 1.6; margin-top: var(--space-sm);">
                    {{ $explanation }}
                </p>
            </div>

        </div>

        <!-- Metrics & Stats -->
        <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
            
            @if($features)
                <!-- Core Payment Metrics -->
                <div class="card">
                    <h3 class="card-title" style="margin-bottom: var(--space-md);">Statistik Pembayaran ({{ \Carbon\Carbon::parse($features->periode_awal)->format('d M Y') }} - {{ \Carbon\Carbon::parse($features->periode_akhir)->format('d M Y') }})</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-lg);">
                        <!-- Accuracy Ratio -->
                        <div style="background-color: var(--surface-container-low); padding: var(--space-md); border-radius: var(--radius-md); text-align: center; border: 1px solid var(--outline-variant);">
                            <div style="font-size: 0.75rem; color: var(--outline); font-weight: 700; text-transform: uppercase;">Ketepatan Bayar</div>
                            <div style="font-family: var(--font-heading); font-size: 1.75rem; font-weight: 800; color: var(--primary); margin-top: 4px;">
                                {{ number_format($features->rasio_ketepatan_bayar * 100, 1) }}%
                            </div>
                        </div>

                        <!-- Average Delay -->
                        <div style="background-color: var(--surface-container-low); padding: var(--space-md); border-radius: var(--radius-md); text-align: center; border: 1px solid var(--outline-variant);">
                            <div style="font-size: 0.75rem; color: var(--outline); font-weight: 700; text-transform: uppercase;">Rerata Terlambat</div>
                            <div style="font-family: var(--font-heading); font-size: 1.75rem; font-weight: 800; color: var(--tertiary); margin-top: 4px;">
                                {{ number_format($features->rata_rata_keterlambatan_hari, 1) }} Hari
                            </div>
                        </div>

                        <!-- Outstanding Dues -->
                        <div style="background-color: var(--surface-container-low); padding: var(--space-md); border-radius: var(--radius-md); text-align: center; border: 1px solid var(--outline-variant); grid-column: span 2;">
                            <div style="font-size: 0.75rem; color: var(--outline); font-weight: 700; text-transform: uppercase;">Total Tunggakan</div>
                            <div style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: var(--error); margin-top: 6px;">
                                Rp {{ number_format($features->total_tunggakan, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown Detail List -->
                    <div class="profile-details-list" style="display: flex; flex-direction: column; gap: var(--space-sm); border-top: 1px solid var(--outline-variant); padding-top: var(--space-md);">
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                            <span style="color: var(--outline); font-weight: 500;">Total Hari Tagihan</span>
                            <span style="font-weight: 700; color: var(--on-surface);">{{ $features->total_hari_tagihan }} Hari</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                            <span style="color: var(--outline); font-weight: 500;">Jumlah Lunas (Tepat Waktu/Telat)</span>
                            <span style="font-weight: 600; color: var(--on-surface);">{{ $features->jumlah_lunas }} Kali</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                            <span style="color: var(--outline); font-weight: 500;">Jumlah Keterlambatan</span>
                            <span style="font-weight: 600; color: var(--tertiary);">{{ $features->jumlah_terlambat }} Kali</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                            <span style="color: var(--outline); font-weight: 500;">Jumlah Bayar Sebagian</span>
                            <span style="font-weight: 600; color: var(--on-surface);">{{ $features->jumlah_bayar_sebagian }} Kali</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                            <span style="color: var(--outline); font-weight: 500;">Total Nominal Tagihan</span>
                            <span style="font-weight: 600; color: var(--on-surface);">Rp {{ number_format($features->total_nominal_tagihan, 2, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; padding: 4px 0;">
                            <span style="color: var(--outline); font-weight: 500;">Total Nominal Dibayar</span>
                            <span style="font-weight: 600; color: var(--secondary);">Rp {{ number_format($features->total_nominal_dibayar, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Calculated Features Warning -->
                <div class="card" style="text-align: center; padding: var(--space-xl); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: var(--space-md); border: 2px dashed var(--outline-variant);">
                    <svg width="48" height="48" fill="none" stroke="var(--outline)" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h4 style="font-family: var(--font-heading); font-size: 1.125rem; font-weight: 700; color: var(--on-surface);">Belum Ada Data Fitur Pembayaran</h4>
                        <p style="font-size: 0.875rem; color: var(--outline); margin-top: var(--space-xs); max-width: 280px; margin-left: auto; margin-right: auto;">
                            Fitur pola pembayaran untuk pedagang ini belum diekstrak untuk rentang waktu saat ini.
                        </p>
                    </div>
                </div>
            @endif
            
        </div>
        
    </div>
@else
    <!-- Initial Prompt / No Selection -->
    <div class="card" style="text-align: center; padding: 60px var(--space-lg); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: var(--space-md); background: var(--surface-container-low);">
        <div style="background-color: var(--surface-container-lowest); width: 80px; height: 80px; border-radius: var(--radius-pill); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); border: 1px solid var(--outline-variant);">
            <svg width="40" height="40" fill="none" stroke="var(--primary)" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <div>
            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700;">Silakan Pilih Pedagang</h3>
            <p style="font-size: 0.875rem; color: var(--outline); margin-top: 6px; max-width: 380px;">
                Pilih salah satu pedagang aktif dari menu dropdown di atas untuk menganalisis perilaku kedisiplinan bayar mereka secara personal.
            </p>
        </div>
    </div>
@endif
@endsection
