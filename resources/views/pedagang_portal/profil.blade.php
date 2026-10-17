@extends('layouts.app')

@section('title', 'Profil & Data Lapak - AmanahLedger')

@section('content')
<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.4rem; font-weight: 700; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
            <span>🏪</span> Profil Mitra & Data Lapak
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

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
    <!-- 1. KARTU DATA LAPAK KIOS -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.4rem;">🏬</span>
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
                    <span style="display: inline-block; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 2px;">
                        {{ strtoupper($pedagang->status_legal ?: 'Legal / Terdaftar') }}
                    </span>
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

    <!-- 2. KARTU IDENTITAS PEDAGANG & AKUN LOGIN -->
    <div class="card" style="padding: 24px; border-radius: 14px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.4rem;">👤</span>
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
                    <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 999px; font-size: 0.725rem; font-weight: 700;">
                        ✓ Aktif & Terotorisasi
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
