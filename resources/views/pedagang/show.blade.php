@extends('layouts.app')

@section('title', 'Detail Pedagang - ' . $pedagang->nama_pedagang)

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Detail Pedagang: {{ $pedagang->nama_pedagang }}</h2>
        <p class="page-subtitle">ID: {{ $pedagang->id }} | Kios: <span class="font-monospace" style="font-weight: 700;">{{ $pedagang->no_kios }}</span> ({{ $pedagang->blok_kios }})</p>
    </div>
    <div style="display: flex; gap: var(--space-sm); flex-wrap: wrap;">
        <a href="{{ route('pedagang.index') }}" class="btn btn-secondary">Kembali</a>
        <a href="{{ route('export.detail-pedagang', $pedagang->id) }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Excel
        </a>
        <button class="btn btn-secondary" onclick="openTambahIuranModal()">
            + Tambah Iuran
        </button>
        <a href="{{ route('pedagang.edit', $pedagang->id) }}" class="btn btn-primary">Edit Pedagang</a>
    </div>
</div>

<!-- FINANCIAL SUMMARY CARDS -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: var(--space-lg);">
    <div class="stat-card">
        <div class="stat-label">Besaran Iuran Harian</div>
        <div class="stat-value numeric-display">Rp {{ number_format($pedagang->tarif->nominal_harian, 0, ',', '.') }}</div>
        <div class="stat-sub">{{ $pedagang->tarif->nama_tarif }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Tagihan</div>
        <div class="stat-value numeric-display">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        <div class="stat-sub">Akumulasi Seluruh Iuran</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Pembayaran</div>
        <div class="stat-value numeric-display" style="color: var(--secondary);">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--secondary);">Total Sudah Dibayar</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Tunggakan</div>
        <div class="stat-value numeric-display" style="color: var(--error);">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
        <div class="stat-sub" style="color: var(--error);">Sisa Piutang</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Saldo Deposit</div>
        <div class="stat-value numeric-display" style="color: var(--primary);">Rp {{ number_format($pedagang->saldo_deposit, 2, ',', '.') }}</div>
        <div class="stat-sub">Saldo Potong Otomatis</div>
    </div>
</div>

<!-- TABBED NAVIGATION -->
<div class="card" style="margin-bottom: var(--space-lg); padding: 0; overflow: hidden;">
    <div style="display: flex; border-bottom: 1px solid var(--outline-variant); background: var(--surface-container-lowest); overflow-x: auto;">
        <button class="tab-btn active" onclick="switchTab('tab-informasi', this)">Informasi Pedagang</button>
        <button class="tab-btn" onclick="switchTab('tab-lapak', this)">Data Lapak</button>
        <button class="tab-btn" onclick="switchTab('tab-iuran', this)">Riwayat Iuran</button>
        <button class="tab-btn" onclick="switchTab('tab-pembayaran', this)">Riwayat Pembayaran</button>
        <button class="tab-btn" onclick="switchTab('tab-tunggakan', this)">
            Tunggakan
            @if ($riwayatTunggakan->count() > 0)
                <span class="badge badge-bayar-belum_bayar" style="margin-left: 6px; font-size: 0.75rem;">{{ $riwayatTunggakan->count() }}</span>
            @endif
        </button>
    </div>

    <div style="padding: var(--space-lg);">
        
        <!-- TAB A: INFORMASI PEDAGANG -->
        <div id="tab-informasi" class="tab-content active">
            <div class="grid-2">
                <div>
                    <h4 style="font-size: 1rem; margin-bottom: var(--space-md); color: var(--primary);">Profil & Kontak</h4>
                    <div class="profile-details-list">
                        <div class="profile-details-item">
                            <span class="label">Nama Pedagang</span>
                            <span class="value" style="font-weight: 700;">{{ $pedagang->nama_pedagang }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">No KTP</span>
                            <span class="value font-monospace">{{ $pedagang->no_ktp }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">No Telepon</span>
                            <span class="value">{{ $pedagang->no_telepon ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Nama Usaha</span>
                            <span class="value">{{ $pedagang->nama_usaha ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Kategori Usaha</span>
                            <span class="value" style="text-transform: capitalize;">{{ $pedagang->kategori_usaha }} ({{ $pedagang->kategori_usaha_detail ?? '-' }})</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Tanggal Pendaftaran</span>
                            <span class="value">{{ \Carbon\Carbon::parse($pedagang->tanggal_daftar)->isoFormat('D MMMM YYYY') }}</span>
                        </div>
                    </div>

                    <h4 style="font-size: 1rem; margin-top: var(--space-lg); margin-bottom: var(--space-md); color: var(--primary);">Alamat Pedagang</h4>
                    <div class="profile-details-list">
                        <div class="profile-details-item">
                            <span class="label">Alamat Lengkap</span>
                            <span class="value">{{ $pedagang->alamat_lengkap ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">RT / RW</span>
                            <span class="value">RT {{ $pedagang->rt ?? '-' }} / RW {{ $pedagang->rw ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Desa / Kelurahan</span>
                            <span class="value">{{ $pedagang->desa_kelurahan ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Kecamatan</span>
                            <span class="value">{{ $pedagang->kecamatan ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Kabupaten / Kota</span>
                            <span class="value">{{ $pedagang->kabupaten_kota ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Provinsi</span>
                            <span class="value">{{ $pedagang->provinsi ?? '-' }}</span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Kode Pos</span>
                            <span class="value">{{ $pedagang->kode_pos ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 style="font-size: 1rem; margin-bottom: var(--space-md); color: var(--primary);">Status & Dokumen</h4>
                    <div class="profile-details-list" style="margin-bottom: var(--space-md);">
                        <div class="profile-details-item">
                            <span class="label">Status Pedagang</span>
                            <span class="badge badge-status-{{ $pedagang->status_pedagang }}">
                                {{ str_replace('_', ' ', $pedagang->status_pedagang) }}
                            </span>
                        </div>
                        <div class="profile-details-item">
                            <span class="label">Status Legalitas</span>
                            <span class="badge badge-legal-{{ $pedagang->status_legal }}">
                                {{ str_replace('_', ' ', $pedagang->status_legal) }}
                            </span>
                        </div>
                        @if ($pedagang->segmentasi)
                            <div class="profile-details-item">
                                <span class="label">Segmen Pola Bayar AI</span>
                                <span class="badge badge-segment-rajin">
                                    {{ $pedagang->segmentasi->label_segmen }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div style="background: var(--surface-container-lowest); padding: var(--space-md); border-radius: var(--radius-sm); border: 1px solid var(--outline-variant);">
                        <div style="font-size: 0.875rem; font-weight: 700; margin-bottom: var(--space-xs);">Dokumen Terlampir</div>
                        <div style="display: flex; gap: var(--space-md);">
                            <div>
                                <span style="font-size: 0.75rem; color: var(--outline); display: block;">Foto KTP</span>
                                @if ($pedagang->foto_ktp)
                                    <a href="{{ asset($pedagang->foto_ktp) }}" target="_blank" style="font-size: 0.8125rem; text-decoration: underline;">Lihat KTP</a>
                                @else
                                    <span style="font-size: 0.8125rem; color: var(--outline); font-style: italic;">Belum diupload</span>
                                @endif
                            </div>
                            <div>
                                <span style="font-size: 0.75rem; color: var(--outline); display: block;">Surat Izin Usaha</span>
                                @if ($pedagang->foto_izin_usaha)
                                    <a href="{{ asset($pedagang->foto_izin_usaha) }}" target="_blank" style="font-size: 0.8125rem; text-decoration: underline;">Lihat Izin Usaha</a>
                                @else
                                    <span style="font-size: 0.8125rem; color: var(--outline); font-style: italic;">Belum diupload</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($pedagang->catatan)
                <div style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px dashed var(--outline-variant);">
                    <div style="font-size: 0.8125rem; font-weight: 700; color: var(--outline);">Catatan Operasional</div>
                    <div style="font-size: 0.9375rem; color: var(--on-surface-variant); margin-top: 4px;">{{ $pedagang->catatan }}</div>
                </div>
            @endif
        </div>

        <!-- TAB B: DATA LAPAK -->
        <div id="tab-lapak" class="tab-content" style="display: none;">
            <div class="grid-2">
                <div class="profile-details-list">
                    <div class="profile-details-item">
                        <span class="label">Kode Lapak / Kios</span>
                        <span class="value font-monospace" style="font-weight: 700; font-size: 1.125rem; color: var(--primary);">{{ $pedagang->no_kios }}</span>
                    </div>
                    <div class="profile-details-item">
                        <span class="label">Blok Kios</span>
                        <span class="value" style="font-weight: 600;">{{ $pedagang->blok_kios }}</span>
                    </div>
                    <div class="profile-details-item">
                        <span class="label">Kategori Lapak</span>
                        <span class="value" style="text-transform: capitalize;">{{ $pedagang->kategori_usaha }}</span>
                    </div>
                </div>
                <div class="profile-details-list">
                    <div class="profile-details-item">
                        <span class="label">Status Lapak</span>
                        <span class="badge badge-status-{{ $pedagang->status_pedagang }}">
                            {{ $lapak ? ucfirst($lapak->status_lapak) : 'Aktif Terisi' }}
                        </span>
                    </div>
                    <div class="profile-details-item">
                        <span class="label">Tarif Sewa Harian</span>
                        <span class="value numeric-display">Rp {{ number_format($pedagang->tarif->nominal_harian, 0, ',', '.') }}</span>
                    </div>
                    <div class="profile-details-item">
                        <span class="label">Tanggal Ditempati</span>
                        <span class="value">{{ \Carbon\Carbon::parse($pedagang->tanggal_daftar)->isoFormat('D MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB C: RIWAYAT IURAN -->
        <div id="tab-iuran" class="tab-content" style="display: none;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Iuran</th>
                            <th>Nominal Tagihan</th>
                            <th>Nominal Dibayar</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatIuran as $tgh)
                            <tr>
                                <td class="numeric-display" style="font-weight: 600;">{{ $tgh->tanggal_tagihan ? $tgh->tanggal_tagihan->format('d/m/Y') : '-' }}</td>
                                <td>{{ $tgh->jenis_iuran ?? 'Iuran Harian' }}</td>
                                <td class="numeric-display">Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}</td>
                                <td class="numeric-display" style="font-weight: 700;">Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-bayar-{{ $tgh->status_bayar }}">
                                        {{ str_replace('_', ' ', $tgh->status_bayar) }}
                                    </span>
                                </td>
                                <td>{{ $tgh->waktu_bayar ? $tgh->waktu_bayar->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--outline); padding: var(--space-lg);">Riwayat tagihan iuran kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB D: RIWAYAT PEMBAYARAN -->
        <div id="tab-pembayaran" class="tab-content" style="display: none;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Waktu Bayar</th>
                            <th>No Bukti</th>
                            <th>Jenis Iuran</th>
                            <th>Jumlah Dibayar</th>
                            <th>Metode</th>
                            <th>Petugas</th>
                            <th style="text-align: right;">Cetak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatPembayaran as $tgh)
                            <tr>
                                <td class="numeric-display">{{ $tgh->waktu_bayar ? $tgh->waktu_bayar->format('d/m/Y H:i') : '-' }}</td>
                                <td class="font-monospace">{{ $tgh->no_bukti ?? '-' }}</td>
                                <td>{{ $tgh->jenis_iuran ?? 'Iuran Harian' }}</td>
                                <td class="numeric-display" style="color: var(--secondary); font-weight: 700;">Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}</td>
                                <td><span class="badge badge-bayar-lunas">{{ strtoupper($tgh->metode_bayar ?? 'TUNAI') }}</span></td>
                                <td>{{ $tgh->petugas->nama ?? 'Sistem' }}</td>
                                <td style="text-align: right;">
                                    <a href="{{ route('riwayat.cetak', $tgh->id) }}" target="_blank" class="btn btn-secondary btn-sm">Nota</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--outline); padding: var(--space-lg);">Belum ada riwayat pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB E: TUNGGAKAN -->
        <div id="tab-tunggakan" class="tab-content" style="display: none;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Tagihan</th>
                            <th>Jenis Iuran</th>
                            <th>Nominal Tagihan</th>
                            <th>Nominal Terbayar</th>
                            <th>Sisa Tunggakan</th>
                            <th style="text-align: right;">Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatTunggakan as $tgh)
                            @php $sisa = $tgh->nominal_tagihan - $tgh->nominal_dibayar; @endphp
                            <tr>
                                <td class="numeric-display">{{ $tgh->tanggal_tagihan ? $tgh->tanggal_tagihan->format('d/m/Y') : '-' }}</td>
                                <td>{{ $tgh->jenis_iuran ?? 'Iuran Harian' }}</td>
                                <td class="numeric-display">Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}</td>
                                <td class="numeric-display">Rp {{ number_format($tgh->nominal_dibayar, 0, ',', '.') }}</td>
                                <td class="numeric-display" style="color: var(--error); font-weight: 700;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                <td style="text-align: right;">
                                    <button class="btn btn-primary btn-sm" onclick="openBayarModal('{{ $tgh->id }}', '{{ $pedagang->nama_pedagang }}', '{{ $sisa }}')">
                                        Bayar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--secondary); padding: var(--space-lg); font-weight: 600;">
                                    🎉 Pedagang ini tidak memiliki tunggakan!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- MODAL TAMBAH IURAN MANUAL -->
<div class="modal-overlay" id="modal-tambah-iuran">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Tagihan Iuran Manual</h3>
            <button class="modal-close" onclick="closeTambahIuranModal()">&times;</button>
        </div>
        <form action="{{ route('tagihan.store-manual') }}" method="POST">
            @csrf
            <input type="hidden" name="pedagang_id" value="{{ $pedagang->id }}">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Tanggal Tagihan</label>
                    <input type="date" name="tanggal_tagihan" class="form-input" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Jenis Iuran</label>
                    <select name="jenis_iuran" class="form-select" required>
                        <option value="Iuran Harian">Iuran Harian Pasar</option>
                        <option value="Iuran Kebersihan">Iuran Kebersihan</option>
                        <option value="Iuran Keamanan">Iuran Keamanan</option>
                        <option value="Listrik / Air">Restribusi Listrik / Air</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Nominal Tagihan (Rp)</label>
                    <input type="number" name="nominal_tagihan" class="form-input" required min="0" value="{{ $pedagang->tarif->nominal_harian }}">
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="catatan" class="form-input" placeholder="Opsional (Catatan penambahan)">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: var(--space-sm); justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeTambahIuranModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Tagihan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL QUICK BAYAR -->
<div class="modal-overlay" id="modal-quick-bayar">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header">
            <h3 class="modal-title">Bayar Tagihan</h3>
            <button class="modal-close" onclick="closeBayarModal()">&times;</button>
        </div>
        <form id="quick-bayar-form" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Pedagang</label>
                    <input type="text" id="quick-bayar-pedagang" class="form-input" readonly style="background: var(--surface-container-high);">
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Nominal Pembayaran (Rp)</label>
                    <input type="number" name="nominal_dibayar" id="quick-bayar-nominal" class="form-input" required min="0" step="1000">
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode_bayar" class="form-select" required>
                        <option value="tunai">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: var(--space-md);">
                    <label class="form-label">No Bukti / Keterangan</label>
                    <input type="text" name="no_bukti" class="form-input" placeholder="Opsional (Nomor Struk)">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: var(--space-sm); justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeBayarModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<style>
    .tab-btn {
        padding: 0.85rem 1.25rem;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--outline);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .tab-btn:hover {
        color: var(--primary);
    }
    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background: var(--surface-container-low);
    }
</style>
@endsection

@section('scripts')
<script>
    function switchTab(tabId, btnElement) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        document.getElementById(tabId).style.display = 'block';
        btnElement.classList.add('active');
    }

    function openTambahIuranModal() {
        document.getElementById('modal-tambah-iuran').classList.add('active');
    }
    function closeTambahIuranModal() {
        document.getElementById('modal-tambah-iuran').classList.remove('active');
    }

    function openBayarModal(tagihanId, namaPedagang, nominal) {
        document.getElementById('quick-bayar-form').action = '/tagihan/' + tagihanId + '/bayar';
        document.getElementById('quick-bayar-pedagang').value = namaPedagang;
        document.getElementById('quick-bayar-nominal').value = nominal;
        document.getElementById('modal-quick-bayar').classList.add('active');
    }
    function closeBayarModal() {
        document.getElementById('modal-quick-bayar').classList.remove('active');
    }
</script>
@endsection
