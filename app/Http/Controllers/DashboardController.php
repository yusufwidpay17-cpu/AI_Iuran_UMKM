<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedagang;
use App\Models\Tagihan;
use App\Models\Lapak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. Merchant & Lapak Operational Stats
        $totalPedagang = Pedagang::count();
        $pedagangAktif = Pedagang::where('status_pedagang', 'aktif')->count();
        $pedagangNonaktif = Pedagang::where('status_pedagang', 'nonaktif')->count();
        $totalLapak = Lapak::count();
        if ($totalLapak == 0) {
            $totalLapak = Pedagang::whereNotNull('no_kios')->count();
        }

        // 2. Today's Bill Status
        $todayBills = Tagihan::with('pedagang')->where('tanggal_tagihan', $today)->get();
        
        $sudahBayarHariIniCount = $todayBills->where('status_bayar', 'lunas')->count();
        $belumBayarHariIniCount = $todayBills->where('status_bayar', '!=', 'lunas')->count();
        
        $targetIuranHariIni = $todayBills->sum('nominal_tagihan');
        $totalIuranTerkumpulHariIni = $todayBills->sum('nominal_dibayar');
        $sisaBelumBayarHariIni = $targetIuranHariIni - $totalIuranTerkumpulHariIni;

        $persenPelunasanHariIni = $targetIuranHariIni > 0 
            ? round(($totalIuranTerkumpulHariIni / $targetIuranHariIni) * 100, 1) 
            : 0;

        // 3. Arrears & Monthly Income
        $allUnpaidBills = Tagihan::where('status_bayar', '!=', 'lunas')->get();
        $totalTunggakan = $allUnpaidBills->sum(function($b) {
            return $b->nominal_tagihan - $b->nominal_dibayar;
        });

        $startThisMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endThisMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $incomeThisMonth = Tagihan::whereBetween('tanggal_tagihan', [$startThisMonth, $endThisMonth])->sum('nominal_dibayar');

        // 4. Section B: Pedagang Belum Bayar Hari Ini (with days arrears and total arrears)
        $unpaidMerchantsToday = Tagihan::with('pedagang')
            ->where('tanggal_tagihan', $today)
            ->where('status_bayar', '!=', 'lunas')
            ->get()
            ->map(function($tgh) {
                // Calculate total days & amount menunggak for this merchant
                $merchantUnpaid = Tagihan::where('pedagang_id', $tgh->pedagang_id)
                    ->where('status_bayar', '!=', 'lunas')
                    ->get();
                
                $tgh->total_tunggakan_pedagang = $merchantUnpaid->sum(function($item) {
                    return $item->nominal_tagihan - $item->nominal_dibayar;
                });
                $tgh->jumlah_hari_menunggak = $merchantUnpaid->count();
                return $tgh;
            });

        // 5. Section C: Top 5 Merchants with Largest Arrears
        $topArrears = Pedagang::select(
                'pedagang.id',
                'pedagang.nama_pedagang',
                'pedagang.no_kios',
                'pedagang.blok_kios',
                DB::raw('SUM(tagihan.nominal_tagihan - tagihan.nominal_dibayar) as total_tunggakan')
            )
            ->join('tagihan', 'pedagang.id', '=', 'tagihan.pedagang_id')
            ->where('tagihan.status_bayar', '!=', 'lunas')
            ->groupBy('pedagang.id', 'pedagang.nama_pedagang', 'pedagang.no_kios', 'pedagang.blok_kios')
            ->orderByDesc('total_tunggakan')
            ->limit(5)
            ->get();

        // 6. Section D: Recent Transactions
        $recentTransactions = Tagihan::with(['pedagang', 'petugas'])
            ->where('nominal_dibayar', '>', 0)
            ->orderBy('waktu_bayar', 'desc')
            ->limit(6)
            ->get();

        // 7. Section E: Charts (7 days & Monthly)
        $sevenDaysAgo = Carbon::today()->subDays(6)->format('Y-m-d');
        $raw7DaysData = Tagihan::selectRaw('tanggal_tagihan, SUM(nominal_dibayar) as total_dibayar')
            ->whereBetween('tanggal_tagihan', [$sevenDaysAgo, $today])
            ->groupBy('tanggal_tagihan')
            ->orderBy('tanggal_tagihan', 'asc')
            ->get();

        $chart7Labels = [];
        $chart7Values = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->isoFormat('D MMM');
            $match = $raw7DaysData->firstWhere('tanggal_tagihan', $dateStr);
            
            $chart7Labels[] = $label;
            $chart7Values[] = $match ? (float)$match->total_dibayar : 0.00;
        }

        // Monthly income chart (last 6 months)
        $chartMonthLabels = [];
        $chartMonthValues = [];
        for ($m = 5; $m >= 0; $m--) {
            $month = Carbon::now()->subMonths($m);
            $startM = $month->copy()->startOfMonth()->format('Y-m-d');
            $endM = $month->copy()->endOfMonth()->format('Y-m-d');
            $val = Tagihan::whereBetween('tanggal_tagihan', [$startM, $endM])->sum('nominal_dibayar');

            $chartMonthLabels[] = $month->isoFormat('MMM YYYY');
            $chartMonthValues[] = (float)$val;
        }

        return view('dashboard.index', compact(
            'totalPedagang',
            'pedagangAktif',
            'pedagangNonaktif',
            'totalLapak',
            'sudahBayarHariIniCount',
            'belumBayarHariIniCount',
            'targetIuranHariIni',
            'totalIuranTerkumpulHariIni',
            'sisaBelumBayarHariIni',
            'persenPelunasanHariIni',
            'totalTunggakan',
            'incomeThisMonth',
            'unpaidMerchantsToday',
            'topArrears',
            'recentTransactions',
            'chart7Labels',
            'chart7Values',
            'chartMonthLabels',
            'chartMonthValues'
        ));
    }
}
