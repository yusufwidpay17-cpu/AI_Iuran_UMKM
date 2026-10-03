@extends('layouts.app')

@section('title', 'Segmentasi Pola Bayar AI')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Dashboard Segmentasi AI</h2>
        <p class="page-subtitle">Analisis pengelompokan pedagang berdasarkan tingkat kedisiplinan membayar sewa harian (K-Means).</p>
    </div>
</div>

<!-- AI Model Details & Training Trigger -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-md);">
        <div>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--primary);">Metadata Model Aktif</h3>
            @if ($activeModel)
                <p style="font-size: 0.875rem; color: var(--on-surface-variant); margin-top: 4px;">
                    Model: <strong>{{ $activeModel->nama_model }}</strong> | 
                    Score Silhouette: <strong style="color: var(--secondary);">{{ number_format($activeModel->silhouette_score, 3) }}</strong> | 
                    Jumlah Cluster: <strong>{{ $activeModel->jumlah_cluster }}</strong>
                </p>
                <p style="font-size: 0.75rem; color: var(--outline); margin-top: 2px;">
                    Periode Data: {{ $activeModel->periode_data_awal->format('d M Y') }} s/d {{ $activeModel->periode_data_akhir->format('d M Y') }} | 
                    Waktu Latih: {{ $activeModel->trained_at->format('d/m/Y H:i') }}
                </p>
            @else
                <p style="font-size: 0.875rem; color: var(--error); margin-top: 4px; font-weight: 500;">
                    Model K-Means belum dilatih. Klik tombol di samping untuk memulai analisis.
                </p>
            @endif
        </div>
        
        <div>
            <!-- Form to trigger training -->
            <form action="{{ route('segmentasi.train') }}" method="POST" id="train-form" style="display: flex; gap: var(--space-sm); align-items: flex-end;">
                @csrf
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="periode_awal" class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Awal Analisis</label>
                    <input type="date" name="periode_awal" id="periode_awal" class="form-control" 
                           value="{{ request('periode_awal', \Carbon\Carbon::today()->subDays(30)->format('Y-m-d')) }}" 
                           style="min-height: 38px; padding: 4px var(--space-sm); font-size: 0.8125rem; width: 140px;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="periode_akhir" class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Akhir Analisis</label>
                    <input type="date" name="periode_akhir" id="periode_akhir" class="form-control" 
                           value="{{ request('periode_akhir', \Carbon\Carbon::today()->format('Y-m-d')) }}" 
                           style="min-height: 38px; padding: 4px var(--space-sm); font-size: 0.8125rem; width: 140px;">
                </div>
                <button type="submit" class="btn btn-primary" id="train-submit-btn" style="min-height: 38px; padding: 0 1rem; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.25"></path>
                    </svg>
                    Jalankan Analisis
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Cluster stats distribution summary cards -->
<div class="stats-grid">
    <div class="stat-card" style="border-left: 5px solid #0d8a43;">
        <div class="stat-label" style="color: #0d8a43; font-weight: 700;">Pembayar Rajin</div>
        <div class="stat-value" style="color: #0d8a43;">{{ $distribution['rajin'] }}</div>
        <div class="stat-desc">Sewa selalu lunas & tepat waktu</div>
    </div>
    
    <div class="stat-card" style="border-left: 5px solid #a67200;">
        <div class="stat-label" style="color: #a67200; font-weight: 700;">Cukup Rajin</div>
        <div class="stat-value" style="color: #a67200;">{{ $distribution['cukup'] }}</div>
        <div class="stat-desc">Sewa lunas namun sering terlambat</div>
    </div>

    <div class="stat-card" style="border-left: 5px solid #c92323;">
        <div class="stat-label" style="color: #c92323; font-weight: 700;">Berisiko</div>
        <div class="stat-value" style="color: #c92323;">{{ $distribution['berisiko'] }}</div>
        <div class="stat-desc">Tunggakan tinggi & jarang membayar</div>
    </div>
</div>

<div class="grid-2">
    <!-- Scatter Plot Visualizer -->
    <div class="card" style="display: flex; flex-direction: column;">
        <h3 class="card-title">Sebaran Cluster Pedagang (Hasil Reduksi PCA 2D)</h3>
        <p style="font-size: 0.8125rem; color: var(--outline); margin-top: -10px; margin-bottom: var(--space-md);">
            Semakin berdekatan posisi titik pedagang pada grafik, semakin mirip pula pola pembayaran mereka.
        </p>
        <div style="flex: 1; min-height: 320px; display: flex; align-items: center; justify-content: center;" id="scatter-chart-container">
            @if(count($scatterData) > 0)
                <div id="clusterScatterPlot" style="width: 100%; height: 350px;"></div>
            @else
                <span style="font-style: italic; color: var(--outline);">Belum ada data visualisasi cluster. Silakan jalankan analisis model terlebih dahulu.</span>
            @endif
        </div>
    </div>

    <!-- Cluster Descriptions & Advice -->
    <div class="card">
        <h3 class="card-title">Karakteristik & Kebijakan Segmen</h3>
        <div style="display: flex; flex-direction: column; gap: var(--space-md);">
            <div style="background-color: #e6f7ed; padding: var(--space-md); border-radius: var(--radius-md); border: 1px solid #6cf8bb;">
                <h4 style="color: #0d8a43; font-size: 0.9375rem; font-weight: 700;">🟢 Pembayar Rajin (Disiplin Tinggi)</h4>
                <p style="font-size: 0.8125rem; color: #0a5c2d; margin-top: 4px; line-height: 1.5;">
                    Pedagang memiliki rasio ketepatan bayar mendekati 100% dengan total keterlambatan hampir 0 hari.
                    <br><strong>Rekomendasi Tindakan:</strong> Berikan prioritas perpanjangan masa sewa kios dan tawarkan insentif deposit diskon jika memungkinkan.
                </p>
            </div>
            
            <div style="background-color: #fff9eb; padding: var(--space-md); border-radius: var(--radius-md); border: 1px solid #ffeaa8;">
                <h4 style="color: #a67200; font-size: 0.9375rem; font-weight: 700;">🟡 Cukup Rajin (Terlambat Ringan)</h4>
                <p style="font-size: 0.8125rem; color: #704d00; margin-top: 4px; line-height: 1.5;">
                    Pedagang selalu menyelesaikan tagihannya tetapi seringkali terlambat beberapa hari. Rasio bayar berkisar 60% s/d 90%.
                    <br><strong>Rekomendasi Tindakan:</strong> Kirimkan notifikasi WA otomatis tepat pada pagi hari penagihan dan lakukan pendekatan personal.
                </p>
            </div>

            <div style="background-color: #fff1f1; padding: var(--space-md); border-radius: var(--radius-md); border: 1px solid #ffdad6;">
                <h4 style="color: #c92323; font-size: 0.9375rem; font-weight: 700;">🔴 Berisiko (Tunggakan & Kredit Macet)</h4>
                <p style="font-size: 0.8125rem; color: #871414; margin-top: 4px; line-height: 1.5;">
                    Pedagang sangat jarang membayar, memiliki banyak hari belum terbayar, dan menumpuk saldo tunggakan yang tinggi.
                    <br><strong>Rekomendasi Tindakan:</strong> Berikan SP-1, terapkan pembatasan pembayaran bertahap, atau lakukan inspeksi langsung oleh petugas keamanan/pengelola pasar.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filter & List of Segmented Merchants -->
<div class="card" style="margin-top: var(--space-md);">
    <h3 class="card-title">Daftar Detail Hasil Pengelompokan Pedagang</h3>
    
    <!-- Inline Filter Table Form -->
    <form action="{{ route('segmentasi.index') }}" method="GET" class="filter-bar" style="margin-bottom: var(--space-md); background-color: var(--surface-container-lowest); padding: var(--space-sm) 0; border: none; border-bottom: 1px solid var(--outline-variant); border-radius: 0;">
        <div class="filter-item" style="min-width: 150px;">
            <label for="filter_blok" class="form-label" style="font-size: 0.75rem;">Blok Kios</label>
            <select name="blok_kios" id="filter_blok" class="form-control" onchange="this.form.submit()" style="min-height: 38px; padding: 4px var(--space-sm); font-size: 0.875rem;">
                <option value="">Semua Blok</option>
                @foreach($blokList as $blok)
                    <option value="{{ $blok }}" {{ request('blok_kios') == $blok ? 'selected' : '' }}>{{ $blok }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item" style="min-width: 150px;">
            <label for="filter_kategori" class="form-label" style="font-size: 0.75rem;">Kategori Usaha</label>
            <select name="kategori_usaha" id="filter_kategori" class="form-control" onchange="this.form.submit()" style="min-height: 38px; padding: 4px var(--space-sm); font-size: 0.875rem;">
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

        <div class="filter-item" style="min-width: 150px;">
            <label for="filter_segmen" class="form-label" style="font-size: 0.75rem;">Segmen AI</label>
            <select name="label_segmen" id="filter_segmen" class="form-control" onchange="this.form.submit()" style="min-height: 38px; padding: 4px var(--space-sm); font-size: 0.875rem;">
                <option value="">Semua Segmen</option>
                <option value="Pembayar Rajin" {{ request('label_segmen') == 'Pembayar Rajin' ? 'selected' : '' }}>Pembayar Rajin</option>
                <option value="Cukup Rajin" {{ request('label_segmen') == 'Cukup Rajin' ? 'selected' : '' }}>Cukup Rajin</option>
                <option value="Berisiko" {{ request('label_segmen') == 'Berisiko' ? 'selected' : '' }}>Berisiko</option>
            </select>
        </div>
        
        <div style="flex-shrink: 0;">
            <a href="{{ route('segmentasi.index') }}" class="btn btn-secondary btn-sm" style="min-height: 38px; display: inline-flex; align-items: center;">Reset Filter</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No Kios</th>
                    <th>Pedagang / Usaha</th>
                    <th>Segmen AI</th>
                    <th>Rasio Tepat Bayar</th>
                    <th>Rata-rata Terlambat</th>
                    <th>Total Tunggakan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($segmentations as $seg)
                    @php
                        $snapshot = $seg->fitur_snapshot;
                        $tunggakan = isset($snapshot['total_tunggakan']) ? $snapshot['total_tunggakan'] : 0.00;
                        $ratio = isset($snapshot['rasio_ketepatan_bayar']) ? $snapshot['rasio_ketepatan_bayar'] * 100 : 0.00;
                        $delay = isset($snapshot['rata_rata_keterlambatan_hari']) ? $snapshot['rata_rata_keterlambatan_hari'] : 0.0;
                        
                        $label = strtolower($seg->label_segmen);
                        $segmentClass = 'badge-segment-lainnya';
                        if (str_contains($label, 'rajin') && !str_contains($label, 'cukup')) {
                            $segmentClass = 'badge-segment-rajin';
                        } elseif (str_contains($label, 'cukup')) {
                            $segmentClass = 'badge-segment-cukup_rajin';
                        } elseif (str_contains($label, 'berisiko')) {
                            $segmentClass = 'badge-segment-berisiko';
                        }
                    @endphp
                    <tr>
                        <td class="numeric-display" style="font-weight: 700;">{{ $seg->pedagang->no_kios }}</td>
                        <td>
                            <div style="font-weight: 600; color: var(--on-surface);">{{ $seg->pedagang->nama_pedagang }}</div>
                            <div style="font-size: 0.75rem; color: var(--outline);">Usaha: {{ $seg->pedagang->nama_usaha ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $segmentClass }}">
                                {{ $seg->label_segmen }}
                            </span>
                        </td>
                        <td class="numeric-display" style="font-weight: 600;">{{ number_format($ratio, 1) }}%</td>
                        <td class="numeric-display">{{ number_format($delay, 1) }} Hari</td>
                        <td class="numeric-display" style="font-weight: 700; color: {{ $tunggakan > 0 ? 'var(--error)' : 'var(--secondary)' }}">
                            Rp {{ number_format($tunggakan, 0, ',', '.') }}
                        </td>
                        <td>
                            <a href="{{ route('pedagang.show', $seg->pedagang_id) }}" class="btn btn-secondary btn-sm" style="min-height: 32px; padding: 0.25rem 0.5rem;">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--outline); padding: var(--space-lg);">
                            Data segmentasi pedagang kosong. Jalankan analisis ulang terlebih dahulu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@if(count($scatterData) > 0)
<!-- Load ApexCharts from CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scatterData = {!! json_encode($scatterData) !!};

        // Group data points by segment name
        const rajinSeries = scatterData.filter(d => d.segment === 'Pembayar Rajin').map(d => ({ x: d.x, y: d.y, label: d.label, ratio: d.ratio, tunggakan: d.tunggakan }));
        const cukupSeries = scatterData.filter(d => d.segment === 'Cukup Rajin').map(d => ({ x: d.x, y: d.y, label: d.label, ratio: d.ratio, tunggakan: d.tunggakan }));
        const berisikoSeries = scatterData.filter(d => d.segment === 'Berisiko').map(d => ({ x: d.x, y: d.y, label: d.label, ratio: d.ratio, tunggakan: d.tunggakan }));

        const options = {
            series: [
                {
                    name: 'Pembayar Rajin',
                    data: rajinSeries
                },
                {
                    name: 'Cukup Rajin',
                    data: cukupSeries
                },
                {
                    name: 'Berisiko',
                    data: berisikoSeries
                }
            ],
            chart: {
                height: 350,
                type: 'scatter',
                zoom: {
                    enabled: true,
                    type: 'xy'
                },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#0d8a43', '#a67200', '#c92323'], // Match badges colors
            xaxis: {
                tickAmount: 10,
                labels: {
                    formatter: function(val) {
                        return parseFloat(val).toFixed(2);
                    }
                },
                title: {
                    text: 'Principal Component 1 (PC1)',
                    style: {
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                tickAmount: 7,
                title: {
                    text: 'Principal Component 2 (PC2)',
                    style: {
                        fontWeight: 600
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            },
            markers: {
                size: 8,
                hover: {
                    size: 10
                }
            },
            tooltip: {
                custom: function({series, seriesIndex, dataPointIndex, w}) {
                    const data = w.globals.initialSeries[seriesIndex].data[dataPointIndex];
                    return '<div class="chart-tooltip" style="padding: 10px; background: #ffffff; border: 1px solid var(--outline-variant); border-radius: var(--radius-default); box-shadow: var(--shadow-md);">' +
                        '<div style="font-weight: 700; color: var(--on-surface); font-size: 0.875rem;">' + data.label + '</div>' +
                        '<div style="font-size: 0.75rem; color: var(--outline); margin-top: 4px;">Segmen: <strong>' + w.globals.seriesNames[seriesIndex] + '</strong></div>' +
                        '<div style="font-size: 0.75rem; color: var(--on-surface-variant); margin-top: 2px;">Rasio Tepat: <strong>' + data.ratio.toFixed(1) + '%</strong></div>' +
                        '<div style="font-size: 0.75rem; color: var(--error); margin-top: 2px;">Arrears: <strong>Rp ' + new Intl.NumberFormat('id-ID').format(data.tunggakan) + '</strong></div>' +
                        '</div>';
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#clusterScatterPlot"), options);
        chart.render();
    });
</script>
@endif

<script>
    // Loading overlay on train form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('train-form');
        const submitBtn = document.getElementById('train-submit-btn');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="animation: spin 1s linear infinite; margin-right: 6px;">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor"></path>
                    </svg>
                    Memproses Analisis...
                `;
            });
        }
    });
</script>

<style>
    /* Spin animation definition */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        display: inline-block;
    }
</style>
@endsection
