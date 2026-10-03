<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedagang;
use App\Models\Tagihan;
use App\Models\FiturPolaBayar;
use App\Models\MlModel;
use App\Models\SegmentasiPedagang;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SegmentasiController extends Controller
{
    /**
     * Display AI Segmentations Dashboard
     */
    public function index(Request $request)
    {
        // 1. Fetch active ML model metadata
        $activeModel = MlModel::where('is_active', true)->first();

        // 2. Fetch segmentations mapping
        $query = SegmentasiPedagang::with(['pedagang.tarif', 'model']);

        // Join pedagang for sorting/filtering
        $query->join('pedagang', 'segmentasi_pedagang.pedagang_id', '=', 'pedagang.id')
              ->select('segmentasi_pedagang.*');

        // Apply filters
        if ($request->filled('blok_kios')) {
            $query->where('pedagang.blok_kios', $request->input('blok_kios'));
        }
        if ($request->filled('kategori_usaha')) {
            $query->where('pedagang.kategori_usaha', $request->input('kategori_usaha'));
        }
        if ($request->filled('label_segmen')) {
            $query->where('segmentasi_pedagang.label_segmen', $request->input('label_segmen'));
        }

        $segmentations = $query->orderBy('pedagang.no_kios', 'asc')->get();

        // Calculate distribution stats
        $distribution = [
            'rajin' => $segmentations->where('label_segmen', 'Pembayar Rajin')->count(),
            'cukup' => $segmentations->where('label_segmen', 'Cukup Rajin')->count(),
            'berisiko' => $segmentations->where('label_segmen', 'Berisiko')->count(),
            'total' => $segmentations->count(),
        ];

        // Format data for ApexCharts/Chart.js scatter plot
        $scatterData = [];
        foreach ($segmentations as $seg) {
            $snapshot = $seg->fitur_snapshot;
            if (isset($snapshot['pc1']) && isset($snapshot['pc2'])) {
                $scatterData[] = [
                    'x' => (float)$snapshot['pc1'],
                    'y' => (float)$snapshot['pc2'],
                    'label' => $seg->pedagang->nama_pedagang . ' (' . $seg->pedagang->no_kios . ')',
                    'segment' => $seg->label_segmen,
                    'tunggakan' => isset($snapshot['total_tunggakan']) ? $snapshot['total_tunggakan'] : 0,
                    'ratio' => isset($snapshot['rasio_ketepatan_bayar']) ? $snapshot['rasio_ketepatan_bayar'] * 100 : 0,
                ];
            }
        }

        $blokList = Pedagang::whereNotNull('blok_kios')
            ->where('blok_kios', '!=', '')
            ->distinct()
            ->pluck('blok_kios');

        return view('segmentasi.index', compact(
            'activeModel', 
            'segmentations', 
            'distribution', 
            'scatterData', 
            'blokList'
        ));
    }

    /**
     * Re-run feature extraction and execute K-Means training script
     */
    public function train(Request $request)
    {
        $startDate = $request->input('periode_awal', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('periode_akhir', Carbon::today()->format('Y-m-d'));

        // Phase 1: Feature Extraction from tagihan harian
        $pedagangs = Pedagang::where('status_pedagang', 'aktif')->get();
        $batchTime = now();

        if ($pedagangs->isEmpty()) {
            return back()->with('error', 'Tidak ada pedagang aktif untuk dianalisis.');
        }

        foreach ($pedagangs as $pdg) {
            // Compute payment statistics for the merchant
            $stats = Tagihan::where('pedagang_id', $pdg->id)
                ->whereBetween('tanggal_tagihan', [$startDate, $endDate])
                ->selectRaw("
                    COUNT(*) as total_hari,
                    SUM(CASE WHEN status_bayar = 'lunas' THEN 1 ELSE 0 END) as jml_lunas,
                    SUM(CASE WHEN status_bayar = 'bayar_sebagian' THEN 1 ELSE 0 END) as jml_sebagian,
                    SUM(CASE WHEN status_bayar = 'belum_bayar' THEN 1 ELSE 0 END) as jml_belum,
                    SUM(CASE WHEN status_bayar = 'lunas' AND waktu_bayar IS NOT NULL AND DATE(waktu_bayar) > tanggal_tagihan THEN 1 ELSE 0 END) as jml_terlambat,
                    SUM(
                        CASE 
                            WHEN status_bayar = 'lunas' AND waktu_bayar IS NOT NULL THEN 
                                GREATEST(0, DATEDIFF(waktu_bayar, tanggal_tagihan))
                            WHEN status_bayar = 'bayar_sebagian' AND waktu_bayar IS NOT NULL THEN 
                                GREATEST(0, DATEDIFF(waktu_bayar, tanggal_tagihan))
                            WHEN status_bayar = 'belum_bayar' AND tanggal_tagihan < ? THEN 
                                GREATEST(0, DATEDIFF(?, tanggal_tagihan))
                            ELSE 0 
                        END
                    ) as total_delay_days,
                    SUM(CASE WHEN status_bayar = 'lunas' AND (waktu_bayar IS NULL OR DATE(waktu_bayar) <= tanggal_tagihan) THEN 1 ELSE 0 END) as jml_tepat_waktu,
                    SUM(nominal_tagihan) as total_tagihan,
                    SUM(nominal_dibayar) as total_dibayar
                ", [$endDate, $endDate])
                ->first();

            if ($stats && $stats->total_hari > 0) {
                $totalHari = $stats->total_hari;
                $rasioKetepatan = $stats->jml_tepat_waktu / $totalHari;
                $avgKeterlambatan = $stats->total_delay_days / $totalHari;
                $tunggakan = max(0, $stats->total_tagihan - $stats->total_dibayar);

                // Update or create features snapshot
                FiturPolaBayar::updateOrCreate(
                    [
                        'pedagang_id' => $pdg->id,
                        'periode_awal' => $startDate,
                        'periode_akhir' => $endDate,
                    ],
                    [
                        'total_hari_tagihan' => $totalHari,
                        'jumlah_lunas' => $stats->jml_lunas,
                        'jumlah_terlambat' => $stats->jml_terlambat,
                        'jumlah_bayar_sebagian' => $stats->jml_sebagian,
                        'rata_rata_keterlambatan_hari' => $avgKeterlambatan,
                        'rasio_ketepatan_bayar' => $rasioKetepatan,
                        'total_nominal_tagihan' => $stats->total_tagihan,
                        'total_nominal_dibayar' => $stats->total_dibayar,
                        'total_tunggakan' => $tunggakan,
                        'dihitung_pada' => $batchTime,
                    ]
                );
            }
        }

        // Phase 2: Execute K-Means Python Script
        $pythonScript = base_path('ml/train_kmeans.py');
        
        // Execute the script and catch error logs
        $output = [];
        $returnVar = 0;
        $command = "python \"$pythonScript\" 2>&1";
        
        exec($command, $output, $returnVar);
        
        $outputLog = implode("\n", $output);

        if ($returnVar !== 0) {
            // Write to audit log as failed
            AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => Auth::id(),
                'aksi' => 'TRAIN_AI_FAILED',
                'deskripsi' => 'Gagal melatih model AI. Error: ' . substr($outputLog, 0, 500),
                'created_at' => now(),
            ]);

            return back()->with('error', 'Gagal melatih model AI. Kode keluar: ' . $returnVar . ". Log:\n" . $outputLog);
        }

        // Write to audit log as success
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'aksi' => 'TRAIN_AI_SUCCESS',
            'deskripsi' => 'Berhasil memperbarui segmentasi pedagang menggunakan K-Means untuk periode ' . $startDate . ' s/d ' . $endDate,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Model AI K-Means berhasil dilatih ulang! Segmentasi pedagang diperbarui.');
    }

    /**
     * Cek Segmentasi Pedagang Individual
     */
    public function cek(Request $request)
    {
        // 1. Fetch all active merchants for dropdown
        $pedagangs = Pedagang::where('status_pedagang', 'aktif')
            ->orderBy('nama_pedagang', 'asc')
            ->get();

        $selectedPedagang = null;
        $features = null;
        $segment = null;
        $explanation = '';

        // 2. If a merchant is selected, fetch their features and segment info
        if ($request->filled('pedagang_id')) {
            $pedagangId = $request->input('pedagang_id');
            $selectedPedagang = Pedagang::with('tarif')->find($pedagangId);

            if ($selectedPedagang) {
                // Fetch latest payment features
                $features = FiturPolaBayar::where('pedagang_id', $pedagangId)
                    ->orderBy('dihitung_pada', 'desc')
                    ->first();

                // Fetch latest cluster segment label
                $segment = SegmentasiPedagang::where('pedagang_id', $pedagangId)
                    ->orderBy('tanggal_analisis', 'desc')
                    ->first();

                if ($segment) {
                    $label = $segment->label_segmen;
                    if ($label === 'Pembayar Rajin') {
                        $explanation = 'Pedagang ini memiliki rekam jejak pembayaran yang luar biasa. Hampir seluruh tagihan dibayar tepat waktu (rasio ketepatan bayar mendekati 100%), rata-rata keterlambatan mendekati 0 hari, dan tidak ada tunggakan yang menumpuk. Pedagang sangat disiplin dan terpercaya.';
                    } elseif ($label === 'Cukup Rajin') {
                        $explanation = 'Pedagang ini dikategorikan cukup disiplin. Sebagian besar tagihan dilunasi, namun seringkali mengalami keterlambatan pembayaran berkisar antara 2 hingga 5 hari atau melakukan pembayaran sebagian terlebih dahulu sebelum dilunasi. Ada sedikit potensi tunggakan tetapi masih dalam batas wajar.';
                    } elseif ($label === 'Berisiko') {
                        $explanation = 'Pedagang ini memiliki risiko tinggi. Rasio ketepatan bayar sangat rendah, rata-rata keterlambatan pembayaran sangat panjang, atau menunggak banyak tagihan tanpa kejelasan pembayaran. Diperlukan perhatian khusus atau pendekatan persuasif untuk menyelesaikan tunggakan.';
                    } else {
                        $explanation = 'Pedagang belum dikelompokkan ke dalam kategori perilaku utama.';
                    }
                } else {
                    $explanation = 'Pedagang belum memiliki label segmentasi AI karena belum diikutsertakan dalam pelatihan model K-Means terbaru. Silakan jalankan analisis segmentasi ulang pada menu dashboard.';
                }
            }
        }

        return view('segmentasi.cek', compact(
            'pedagangs',
            'selectedPedagang',
            'features',
            'segment',
            'explanation'
        ));
    }
}
