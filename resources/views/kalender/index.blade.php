@extends('layouts.app')

@section('title', 'Kalender Operasional & Kontrol Penagihan')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 10px;">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="color: var(--primary);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Kalender Operasional & Kontrol Penagihan
        </h2>
        <p class="page-subtitle" style="margin: 6px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Kontrol resmi penagihan iuran harian pasar: libur pasar, agenda pesantren, tugas kedinasan, dan penetapan bebas iuran.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="btn btn-primary" onclick="bukaModalTambahKalender()" style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            + Tambah Catatan Kalender
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="kalender-kpi-grid">
    <!-- Total Catatan -->
    <div class="card kalender-kpi-card">
        <div class="kalender-kpi-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="kalender-kpi-content">
            <div class="kalender-kpi-title" style="color: var(--on-surface-variant);">Total Agenda Dicatat</div>
            <div class="kalender-kpi-value" style="color: var(--on-surface);">{{ $stats['total'] }} <span class="kalender-kpi-unit">Kejadian</span></div>
            <div class="kalender-kpi-sub" style="color: #2563eb;">{{ $stats['aktif'] }} Berstatus Aktif</div>
        </div>
    </div>

    <!-- Bebas Iuran -->
    <div class="card kalender-kpi-card">
        <div class="kalender-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <div class="kalender-kpi-content">
            <div class="kalender-kpi-title" style="color: #047857;">Bebas Iuran Aktif</div>
            <div class="kalender-kpi-value" style="color: #059669;">{{ $stats['bebas_iuran'] }} <span class="kalender-kpi-unit">Agenda</span></div>
            <div class="kalender-kpi-sub" style="color: #059669;">Pedagang tidak ditagih</div>
        </div>
    </div>

    <!-- Penagihan Tertunda -->
    <div class="card kalender-kpi-card">
        <div class="kalender-kpi-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
            </svg>
        </div>
        <div class="kalender-kpi-content">
            <div class="kalender-kpi-title" style="color: #b45309;">Penagihan Tertunda</div>
            <div class="kalender-kpi-value" style="color: #d97706;">{{ $stats['tertunda'] }} <span class="kalender-kpi-unit">Agenda</span></div>
            <div class="kalender-kpi-sub" style="color: #b45309;">Kewajiban tetap berjalan</div>
        </div>
    </div>

    <!-- Dibatalkan -->
    <div class="card kalender-kpi-card">
        <div class="kalender-kpi-icon" style="background: rgba(100, 116, 139, 0.12); color: #64748b;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.93 4.93l14.14 14.14"></path>
            </svg>
        </div>
        <div class="kalender-kpi-content">
            <div class="kalender-kpi-title" style="color: var(--on-surface-variant);">Aturan Dibatalkan</div>
            <div class="kalender-kpi-value" style="color: var(--outline);">{{ $stats['dibatalkan'] }} <span class="kalender-kpi-unit">Catatan</span></div>
            <div class="kalender-kpi-sub" style="color: var(--outline);">Jadwal normal berlaku</div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="padding: 16px; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest); margin-bottom: 20px;">
    <form action="{{ route('kalender.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items: flex-end;">
        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 4px; display: block;">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" style="border-radius: 8px; font-size: 0.85rem;">
        </div>

        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 4px; display: block;">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" style="border-radius: 8px; font-size: 0.85rem;">
        </div>

        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 4px; display: block;">Kategori Kejadian</label>
            <select name="kategori" class="form-select" style="border-radius: 8px; font-size: 0.85rem;">
                <option value="">Semua Kategori</option>
                <option value="libur_nasional_pasar" {{ request('kategori') === 'libur_nasional_pasar' ? 'selected' : '' }}>Libur Nasional / Pasar</option>
                <option value="kegiatan_pesantren" {{ request('kategori') === 'kegiatan_pesantren' ? 'selected' : '' }}>Kegiatan Pesantren</option>
                <option value="petugas_tugas_lain" {{ request('kategori') === 'petugas_tugas_lain' ? 'selected' : '' }}>Petugas Tugas Lain</option>
                <option value="penagihan_dihentikan_sementara" {{ request('kategori') === 'penagihan_dihentikan_sementara' ? 'selected' : '' }}>Penagihan Dihentikan</option>
                <option value="keadaan_darurat" {{ request('kategori') === 'keadaan_darurat' ? 'selected' : '' }}>Keadaan Darurat</option>
                <option value="lainnya" {{ request('kategori') === 'lainnya' ? 'selected' : '' }}>Alasan Lainnya</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 4px; display: block;">Kebijakan Iuran</label>
            <select name="kebijakan_iuran" class="form-select" style="border-radius: 8px; font-size: 0.85rem;">
                <option value="">Semua Kebijakan</option>
                <option value="bebas_iuran" {{ request('kebijakan_iuran') === 'bebas_iuran' ? 'selected' : '' }}>Bebas Iuran</option>
                <option value="kewajiban_tetap_berlaku" {{ request('kebijakan_iuran') === 'kewajiban_tetap_berlaku' ? 'selected' : '' }}>Kewajiban Tetap Berlaku</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--on-surface-variant); margin-bottom: 4px; display: block;">Cari Alasan</label>
            <input type="text" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ request('search') }}" style="border-radius: 8px; font-size: 0.85rem;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="flex: 1; border-radius: 8px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                </svg>
                Filter
            </button>
            <a href="{{ route('kalender.index') }}" class="btn btn-secondary" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem;" title="Reset Filter">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
    <div class="table-responsive">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--surface-container-low); border-bottom: 1px solid var(--outline-variant);">
                <tr>
                    <th style="padding: 14px 16px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Rentang Tanggal</th>
                    <th style="padding: 14px 16px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Kategori Kejadian</th>
                    <th style="padding: 14px 16px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Alasan / Keterangan</th>
                    <th style="padding: 14px 16px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; text-align: center;">Kebijakan Iuran</th>
                    <th style="padding: 14px 16px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; text-align: center;">Status</th>
                    <th style="padding: 14px 16px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Pencatat</th>
                    <th style="padding: 14px 16px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                @php
                    $mulai = $rec->tanggal_mulai;
                    $selesai = $rec->tanggal_selesai;
                    $durasiHari = $mulai->diffInDays($selesai) + 1;
                    $isSingleDay = $mulai->isSameDay($selesai);
                @endphp
                <tr style="border-bottom: 1px solid var(--outline-variant);">
                    <!-- Rentang Tanggal -->
                    <td style="padding: 14px 16px; vertical-align: middle;">
                        <strong style="color: var(--on-surface); font-size: 0.88rem;">
                            {{ $mulai->isoFormat('D MMM YYYY') }}
                            @if(!$isSingleDay)
                                <span style="color: var(--outline);">s/d</span> {{ $selesai->isoFormat('D MMM YYYY') }}
                            @endif
                        </strong>
                        <div style="font-size: 0.75rem; color: var(--on-surface-variant); margin-top: 2px;">
                            {{ $durasiHari }} Hari Kalender
                        </div>
                    </td>

                    <!-- Kategori -->
                    <td style="padding: 14px 16px; vertical-align: middle;">
                        <span style="font-weight: 700; font-size: 0.825rem; color: var(--on-surface); display: inline-flex; align-items: center; gap: 6px;">
                            @if($rec->kategori === 'kegiatan_pesantren')
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #7c3aed;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $rec->kategori_label }}
                            @elseif($rec->kategori === 'libur_nasional_pasar')
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0284c7;">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path>
                                </svg>
                                {{ $rec->kategori_label }}
                            @elseif($rec->kategori === 'petugas_tugas_lain')
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #d97706;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                {{ $rec->kategori_label }}
                            @elseif($rec->kategori === 'keadaan_darurat')
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #dc2626;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                {{ $rec->kategori_label }}
                            @else
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--outline);">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $rec->kategori_label }}
                            @endif
                        </span>
                    </td>

                    <!-- Alasan / Keterangan -->
                    <td style="padding: 14px 16px; vertical-align: middle; max-width: 280px;">
                        <div style="font-size: 0.85rem; color: var(--on-surface); line-height: 1.4;">
                            {{ $rec->alasan }}
                        </div>
                        @if($rec->catatan_audit)
                            <div style="font-size: 0.72rem; color: var(--outline); margin-top: 4px; font-family: monospace; white-space: pre-line; display: flex; align-items: flex-start; gap: 4px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>{{ Str::limit($rec->catatan_audit, 100) }}</span>
                            </div>
                        @endif
                    </td>

                    <!-- Kebijakan Iuran -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                        @if($rec->kebijakan_iuran === 'bebas_iuran')
                            <span style="background: rgba(16, 185, 129, 0.15); color: #047857; border: 1px solid rgba(16, 185, 129, 0.35); font-size: 0.725rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; display: inline-flex; align-items: center; gap: 5px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                BEBAS IURAN
                            </span>
                        @else
                            <span style="background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.35); font-size: 0.725rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; display: inline-flex; align-items: center; gap: 5px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
                                </svg>
                                TETAP BERUTANG
                            </span>
                        @endif
                    </td>

                    <!-- Status -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: center;">
                        @if($rec->status === 'aktif')
                            <span style="background: #dcfce7; color: #15803d; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 6px;">
                                AKTIF
                            </span>
                        @else
                            <span style="background: #f1f5f9; color: #64748b; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                DIBATALKAN
                            </span>
                        @endif
                    </td>

                    <!-- Pencatat -->
                    <td style="padding: 14px 16px; vertical-align: middle; font-size: 0.8rem; color: var(--on-surface-variant);">
                        <div>{{ $rec->creator->nama ?? 'Admin' }}</div>
                        <div style="font-size: 0.72rem; color: var(--outline);">{{ $rec->created_at ? $rec->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </td>

                    <!-- Aksi -->
                    <td style="padding: 14px 16px; vertical-align: middle; text-align: right;">
                        <div style="display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap;">
                            @if($rec->kebijakan_iuran === 'bebas_iuran' && $rec->status === 'aktif')
                                <button type="button" class="btn btn-sm btn-secondary" onclick="bukaModalDampak('{{ $rec->id }}')" style="font-size: 0.75rem; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;" title="Tinjau & Koreksi Tagihan Terdampak">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="M21 21l-4.35-4.35"></path>
                                    </svg>
                                    Cek Dampak
                                </button>
                            @endif

                            <button type="button" class="btn btn-sm btn-outline" onclick='bukaModalEditKalender(@json($rec))' style="font-size: 0.75rem; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>

                            @if($rec->status === 'aktif')
                                <form action="{{ route('kalender.batalkan', $rec->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan / membatalkan catatan kalender operasional ini? Jadwal penagihan akan kembali normal.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" style="font-size: 0.75rem; padding: 4px 8px; background: #fee2e2; color: #b91c1c; border-color: #fecaca; display: inline-flex; align-items: center; gap: 4px;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.93 4.93l14.14 14.14"></path>
                                        </svg>
                                        Batalkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px 16px; color: var(--outline);">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 8px; opacity: 0.5;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>Belum ada catatan kalender operasional yang dicatat. Penagihan berjalan setiap hari secara normal.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: var(--space-md); display: flex; justify-content: center;">
    {{ $records->links('pagination::bootstrap-4') }}
</div>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH CATATAN KALENDER OPERASIONAL                              -->
<!-- ========================================================================= -->
<div class="modal" id="modalTambahKalender" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 1050; align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(4px);">
    <div class="modal-dialog" style="max-width: 600px; width: 100%; background: var(--surface-container-lowest); border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
        <div style="padding: 18px 22px; border-bottom: 1px solid var(--outline-variant); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Tambah Agenda Kalender Operasional
            </h3>
            <button type="button" class="modal-close" onclick="tutupModalTambahKalender()" style="background: none; border: none; font-size: 1.5rem; color: var(--outline); cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('kalender.store') }}" method="POST" style="padding: 22px;">
            @csrf
            <!-- Rentang Tanggal -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">Tanggal Mulai <span style="color: #dc2626;">*</span></label>
                    <input type="date" name="tanggal_mulai" id="input_tanggal_mulai" class="form-control" value="{{ date('Y-m-d') }}" required onchange="hitungDurasiHari()" style="border-radius: 8px;">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">Tanggal Selesai <span style="color: #dc2626;">*</span></label>
                    <input type="date" name="tanggal_selesai" id="input_tanggal_selesai" class="form-control" value="{{ date('Y-m-d') }}" required onchange="hitungDurasiHari()" style="border-radius: 8px;">
                </div>
            </div>
            <div id="badgeDurasiHari" style="font-size: 0.775rem; color: var(--primary); font-weight: 600; margin: -10px 0 16px 0;">
                Durasi: 1 Hari
            </div>

            <!-- Kategori -->
            <div style="margin-bottom: 16px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">Kategori Kejadian <span style="color: #dc2626;">*</span></label>
                <select name="kategori" class="form-select" required style="border-radius: 8px;">
                    <option value="kegiatan_pesantren">Kegiatan Pesantren / Pengajian Akbar</option>
                    <option value="libur_nasional_pasar">Libur Nasional / Libur Pasar</option>
                    <option value="petugas_tugas_lain">Petugas Mendapat Tugas Lain</option>
                    <option value="penagihan_dihentikan_sementara">Penagihan Dihentikan Sementara</option>
                    <option value="keadaan_darurat">Keadaan Darurat / Cuaca Ekstrem</option>
                    <option value="lainnya">Alasan Lainnya</option>
                </select>
            </div>

            <!-- Kebijakan Iuran: ATURAN BISNIS PALING PENTING -->
            <div style="margin-bottom: 18px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface); margin-bottom: 8px; display: block;">
                    Keputusan Kebijakan Kewajiban Iuran <span style="color: #dc2626;">*</span>
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <!-- Pilihan A: Bebas Iuran -->
                    <label style="border: 2px solid #10b981; background: #ecfdf5; border-radius: 10px; padding: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 4px;">
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 800; color: #047857; font-size: 0.88rem;">
                            <input type="radio" name="kebijakan_iuran" value="bebas_iuran" checked style="accent-color: #059669;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Iuran Tidak Dikenakan
                        </div>
                        <div style="font-size: 0.75rem; color: #065f46; line-height: 1.4;">
                            Bebas retribusi. Sistem <b>TIDAK</b> membuat tagihan baru, tidak menambah tunggakan, dan tidak memotong deposit pedagang.
                        </div>
                    </label>

                    <!-- Pilihan B: Kewajiban Tetap Berlaku -->
                    <label style="border: 2px solid #f59e0b; background: #fffaf0; border-radius: 10px; padding: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 4px;">
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 800; color: #b45309; font-size: 0.88rem;">
                            <input type="radio" name="kebijakan_iuran" value="kewajiban_tetap_berlaku" style="accent-color: #d97706;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
                            </svg>
                            Kewajiban Tetap Berlaku
                        </div>
                        <div style="font-size: 0.75rem; color: #92400e; line-height: 1.4;">
                            Petugas tidak keliling hari ini, tetapi kewajiban iuran <b>tetap berlaku</b> dan dapat ditagih pada hari penagihan berikutnya.
                        </div>
                    </label>
                </div>
            </div>

            <!-- Alasan Wajib -->
            <div style="margin-bottom: 20px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">
                    Alasan / Keterangan Resmi <span style="color: #dc2626;">*</span>
                </label>
                <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Haul Akbar Pesantren Zainul Hasan, pasar libur satu hari penuh..." style="border-radius: 8px; font-size: 0.85rem;"></textarea>
                <div style="font-size: 0.725rem; color: var(--outline); margin-top: 4px;">Keterangan ini akan tercatat dalam audit trail sistem dan ditampilkan sebagai pemberitahuan resmi.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="tutupModalTambahKalender()">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700;">Simpan Catatan Kalender</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: EDIT CATATAN KALENDER OPERASIONAL                                -->
<!-- ========================================================================= -->
<div class="modal" id="modalEditKalender" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 1050; align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(4px);">
    <div class="modal-dialog" style="max-width: 600px; width: 100%; background: var(--surface-container-lowest); border-radius: 16px; border: 1px solid var(--outline-variant); overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
        <div style="padding: 18px 22px; border-bottom: 1px solid var(--outline-variant); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Catatan Kalender Operasional
            </h3>
            <button type="button" class="modal-close" onclick="tutupModalEditKalender()" style="background: none; border: none; font-size: 1.5rem; color: var(--outline); cursor: pointer;">&times;</button>
        </div>

        <form id="formEditKalender" method="POST" style="padding: 22px;">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">Tanggal Mulai <span style="color: #dc2626;">*</span></label>
                    <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" class="form-control" required style="border-radius: 8px;">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">Tanggal Selesai <span style="color: #dc2626;">*</span></label>
                    <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" class="form-control" required style="border-radius: 8px;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">Kategori Kejadian <span style="color: #dc2626;">*</span></label>
                <select name="kategori" id="edit_kategori" class="form-select" required style="border-radius: 8px;">
                    <option value="kegiatan_pesantren">Kegiatan Pesantren / Pengajian Akbar</option>
                    <option value="libur_nasional_pasar">Libur Nasional / Libur Pasar</option>
                    <option value="petugas_tugas_lain">Petugas Mendapat Tugas Lain</option>
                    <option value="penagihan_dihentikan_sementara">Penagihan Dihentikan Sementara</option>
                    <option value="keadaan_darurat">Keadaan Darurat / Cuaca Ekstrem</option>
                    <option value="lainnya">Alasan Lainnya</option>
                </select>
            </div>

            <div style="margin-bottom: 18px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface); margin-bottom: 8px; display: block;">
                    Keputusan Kebijakan Kewajiban Iuran <span style="color: #dc2626;">*</span>
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <label style="border: 2px solid #10b981; background: #ecfdf5; border-radius: 10px; padding: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 4px;">
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 800; color: #047857; font-size: 0.88rem;">
                            <input type="radio" name="kebijakan_iuran" id="edit_kebijakan_bebas" value="bebas_iuran" style="accent-color: #059669;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Bebas Iuran
                        </div>
                        <div style="font-size: 0.72rem; color: #065f46;">
                            Tidak menerbitkan tagihan harian & tidak ada tunggakan.
                        </div>
                    </label>

                    <label style="border: 2px solid #f59e0b; background: #fffaf0; border-radius: 10px; padding: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 4px;">
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 800; color: #b45309; font-size: 0.88rem;">
                            <input type="radio" name="kebijakan_iuran" id="edit_kebijakan_tertunda" value="kewajiban_tetap_berlaku" style="accent-color: #d97706;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
                            </svg>
                            Tetap Berlaku
                        </div>
                        <div style="font-size: 0.72rem; color: #92400e;">
                            Penagihan tertunda, kewajiban iuran tetap berjalan.
                        </div>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">Status Aturan</label>
                <select name="status" id="edit_status" class="form-select" style="border-radius: 8px;">
                    <option value="aktif">Aktif (Berlaku)</option>
                    <option value="dibatalkan">Dibatalkan (Nonaktif)</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--on-surface);">
                    Alasan / Keterangan Resmi <span style="color: #dc2626;">*</span>
                </label>
                <textarea name="alasan" id="edit_alasan" class="form-control" rows="3" required style="border-radius: 8px; font-size: 0.85rem;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="tutupModalEditKalender()">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: TINJAU & KOREKSI DAMPAK TAGIHAN TERLANJUR TERBIT (TAHAP 5)        -->
<!-- ========================================================================= -->
<div class="modal" id="modalDampak" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1060; align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(4px);">
    <div class="modal-dialog" style="max-width: 650px; width: 100%; background: var(--surface-container-lowest); border-radius: 16px; border: 1.5px solid #a7f3d0; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="padding: 18px 22px; border-bottom: 1px solid var(--outline-variant); background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(6, 95, 70, 0.05) 100%); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #065f46; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #059669;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Tinjau & Koreksi Dampak Tagihan Bebas Iuran
            </h3>
            <button type="button" class="modal-close" onclick="tutupModalDampak()" style="background: none; border: none; font-size: 1.5rem; color: var(--outline); cursor: pointer;">&times;</button>
        </div>

        <div style="padding: 22px;">
            <div id="dampakLoading" style="text-align: center; padding: 30px; color: var(--outline);">
                Memuat data tagihan terdampak...
            </div>

            <div id="dampakContent" style="display: none;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                    <div style="font-size: 0.8rem; color: #64748b;">Agenda Bebas Iuran:</div>
                    <strong id="dampakTitle" style="color: #1e293b; font-size: 0.95rem;"></strong>
                    <div id="dampakRange" style="font-size: 0.8rem; color: #059669; font-weight: 700; margin-top: 2px;"></div>
                </div>

                <!-- Rincian Status Tagihan Terdampak -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px;">
                    <div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px; text-align: center;">
                        <div style="font-size: 0.72rem; color: #991b1b; font-weight: 700;">BELUM BAYAR</div>
                        <div id="dampakBelumBayar" style="font-size: 1.25rem; font-weight: 800; color: #b91c1c;">0</div>
                        <div style="font-size: 0.7rem; color: #dc2626;">Dapat dibebaskan</div>
                    </div>
                    <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 10px; text-align: center;">
                        <div style="font-size: 0.72rem; color: #92400e; font-weight: 700;">CICILAN</div>
                        <div id="dampakCicilan" style="font-size: 1.25rem; font-weight: 800; color: #b45309;">0</div>
                        <div style="font-size: 0.7rem; color: #b45309;">Sisa dibebaskan</div>
                    </div>
                    <div style="background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px; text-align: center;">
                        <div style="font-size: 0.72rem; color: #166534; font-weight: 700;">LUNAS TERBAYAR</div>
                        <div id="dampakLunas" style="font-size: 1.25rem; font-weight: 800; color: #15803d;">0</div>
                        <div style="font-size: 0.7rem; color: #15803d;">Bukti sah terjaga</div>
                    </div>
                </div>

                <!-- Petunjuk Keamanan Data Finansial Sesuai Tahap 5 -->
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px; font-size: 0.8rem; color: #1e40af; line-height: 1.5; margin-bottom: 18px;">
                    <div style="font-weight: 700; display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"></path>
                        </svg>
                        Prinsip Keamanan & Integritas Data Keuangan:
                    </div>
                    <ul style="margin: 4px 0 0 18px; padding: 0;">
                        <li>Tagihan <b>Belum Bayar</b> akan dibatalkan/dibebaskan secara aman sehingga tidak lagi menagih pedagang.</li>
                        <li>Transaksi yang <b>Sudah Dibayar / Cicilan</b> dan nomor kuitansi <b>TIDAK DIHAPUS</b>, melainkan dipertahankan sebagai rekam pembayaran sah yang diakui.</li>
                        <li>Seluruh perubahan akan mencatat identitas akun Anda di <i>Audit Trail</i>.</li>
                    </ul>
                </div>

                <form id="formKoreksiTagihan" method="POST">
                    @csrf
                    <div style="margin-bottom: 18px; background: #fff5f5; border: 1px solid #fed7aa; border-radius: 8px; padding: 12px;">
                        <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-size: 0.825rem; color: #991b1b; font-weight: 700;">
                            <input type="checkbox" name="konfirmasi_koreksi" value="1" required style="margin-top: 3px; accent-color: #dc2626;">
                            <span>Saya mengonfirmasi untuk membebaskan kewajiban tagihan belum bayar pada rentang tanggal ini sesuai aturan Bebas Iuran.</span>
                        </label>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="btn btn-secondary" onclick="tutupModalDampak()">Tutup</button>
                        <button type="submit" id="btnEksekusiKoreksi" class="btn btn-primary" style="background: #059669; border-color: #059669; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Eksekusi Pembebasan Tagihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function hitungDurasiHari() {
        const m = document.getElementById('input_tanggal_mulai').value;
        const s = document.getElementById('input_tanggal_selesai').value;
        const badge = document.getElementById('badgeDurasiHari');
        if (m && s) {
            const dateM = new Date(m);
            const dateS = new Date(s);
            const diffTime = dateS - dateM;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            if (diffDays > 0) {
                badge.textContent = 'Durasi: ' + diffDays + ' Hari Kalender';
                badge.style.color = 'var(--primary)';
            } else {
                badge.textContent = 'Peringatan: Tanggal selesai harus sama atau setelah tanggal mulai!';
                badge.style.color = '#dc2626';
            }
        }
    }

    function bukaModalTambahKalender() {
        document.getElementById('modalTambahKalender').style.display = 'flex';
        hitungDurasiHari();
    }

    function tutupModalTambahKalender() {
        document.getElementById('modalTambahKalender').style.display = 'none';
    }

    function bukaModalEditKalender(rec) {
        const form = document.getElementById('formEditKalender');
        form.action = '/kalender-operasional/' + rec.id;

        document.getElementById('edit_tanggal_mulai').value = rec.tanggal_mulai ? rec.tanggal_mulai.substring(0, 10) : '';
        document.getElementById('edit_tanggal_selesai').value = rec.tanggal_selesai ? rec.tanggal_selesai.substring(0, 10) : '';
        document.getElementById('edit_kategori').value = rec.kategori;
        document.getElementById('edit_alasan').value = rec.alasan;
        document.getElementById('edit_status').value = rec.status;

        if (rec.kebijakan_iuran === 'bebas_iuran') {
            document.getElementById('edit_kebijakan_bebas').checked = true;
        } else {
            document.getElementById('edit_kebijakan_tertunda').checked = true;
        }

        document.getElementById('modalEditKalender').style.display = 'flex';
    }

    function tutupModalEditKalender() {
        document.getElementById('modalEditKalender').style.display = 'none';
    }

    function bukaModalDampak(id) {
        const modal = document.getElementById('modalDampak');
        const loading = document.getElementById('dampakLoading');
        const content = document.getElementById('dampakContent');
        modal.style.display = 'flex';
        loading.style.display = 'block';
        content.style.display = 'none';

        fetch('/kalender-operasional/' + id + '/dampak', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loading.style.display = 'none';
                content.style.display = 'block';

                document.getElementById('dampakTitle').textContent = data.record.kategori_label + ' (' + data.record.alasan + ')';
                document.getElementById('dampakRange').textContent = 'Rentang: ' + data.record.tanggal_mulai.substring(0, 10) + ' s/d ' + data.record.tanggal_selesai.substring(0, 10);
                
                document.getElementById('dampakBelumBayar').textContent = data.dampak.belum_bayar_count + ' Kios';
                document.getElementById('dampakCicilan').textContent = data.dampak.cicilan_count + ' Kios';
                document.getElementById('dampakLunas').textContent = data.dampak.lunas_count + ' Kios';

                const form = document.getElementById('formKoreksiTagihan');
                form.action = '/kalender-operasional/' + id + '/koreksi-tagihan';

                const btn = document.getElementById('btnEksekusiKoreksi');
                if (data.dampak.belum_bayar_count === 0 && data.dampak.cicilan_sisa === 0) {
                    btn.disabled = true;
                    btn.textContent = 'Tidak Ada Tagihan yang Perlu Dikoreksi';
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Eksekusi Pembebasan Tagihan (' + data.dampak.belum_bayar_count + ' Belum Bayar)';
                }
            }
        })
        .catch(err => {
            alert('Gagal memuat pratinjau dampak tagihan: ' + err.message);
            tutupModalDampak();
        });
    }

    function tutupModalDampak() {
        document.getElementById('modalDampak').style.display = 'none';
    }

    // Auto buka review modal jika query review_id ada
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const reviewId = urlParams.get('review_id');
        if (reviewId) {
            bukaModalDampak(reviewId);
        }
    });
</script>
@endsection
