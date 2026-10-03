<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pedagang;
use App\Models\Lapak;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenisLaporan = $request->input('jenis', 'harian');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $pedagangId = $request->input('pedagang_id');
        $kodeLapak = $request->input('kode_lapak');
        $statusBayar = $request->input('status_bayar');

        $query = Tagihan::with(['pedagang.tarif', 'petugas']);

        if ($startDate) {
            $query->whereDate('tanggal_tagihan', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_tagihan', '<=', $endDate);
        }
        if ($pedagangId) {
            $query->where('pedagang_id', $pedagangId);
        }
        if ($kodeLapak) {
            $query->whereHas('pedagang', function($q) use ($kodeLapak) {
                $q->where('no_kios', $kodeLapak);
            });
        }
        if ($statusBayar) {
            $query->where('status_bayar', $statusBayar);
        }

        if ($jenisLaporan === 'tunggakan') {
            $query->where('status_bayar', '!=', 'lunas');
        } elseif ($jenisLaporan === 'pembayaran') {
            $query->where('nominal_dibayar', '>', 0);
        }

        $tagihans = $query->orderBy('tanggal_tagihan', 'desc')->get();

        // Summary Calculations
        $summary = [
            'total_tagihan' => $tagihans->sum('nominal_tagihan'),
            'total_dibayar' => $tagihans->sum('nominal_dibayar'),
            'total_tunggakan' => $tagihans->sum('nominal_tagihan') - $tagihans->sum('nominal_dibayar'),
            'jumlah_transaksi' => $tagihans->count(),
            'lunas_count' => $tagihans->where('status_bayar', 'lunas')->count(),
            'sebagian_count' => $tagihans->where('status_bayar', 'bayar_sebagian')->count(),
            'belum_count' => $tagihans->where('status_bayar', 'belum_bayar')->count(),
        ];

        $pedagangs = Pedagang::orderBy('nama_pedagang', 'asc')->get();
        $lapaks = Lapak::orderBy('kode_lapak', 'asc')->get();

        return view('laporan.index', compact(
            'tagihans',
            'summary',
            'jenisLaporan',
            'startDate',
            'endDate',
            'pedagangId',
            'kodeLapak',
            'statusBayar',
            'pedagangs',
            'lapaks'
        ));
    }

    public function cetak(Request $request)
    {
        $jenisLaporan = $request->input('jenis', 'harian');
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $pedagangId = $request->input('pedagang_id');
        $kodeLapak = $request->input('kode_lapak');
        $statusBayar = $request->input('status_bayar');

        $query = Tagihan::with(['pedagang.tarif', 'petugas']);

        if ($startDate) {
            $query->whereDate('tanggal_tagihan', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_tagihan', '<=', $endDate);
        }
        if ($pedagangId) {
            $query->where('pedagang_id', $pedagangId);
        }
        if ($kodeLapak) {
            $query->whereHas('pedagang', function($q) use ($kodeLapak) {
                $q->where('no_kios', $kodeLapak);
            });
        }
        if ($statusBayar) {
            $query->where('status_bayar', $statusBayar);
        }

        if ($jenisLaporan === 'tunggakan') {
            $query->where('status_bayar', '!=', 'lunas');
        } elseif ($jenisLaporan === 'pembayaran') {
            $query->where('nominal_dibayar', '>', 0);
        }

        $tagihans = $query->orderBy('tanggal_tagihan', 'asc')->get();

        $summary = [
            'total_tagihan' => $tagihans->sum('nominal_tagihan'),
            'total_dibayar' => $tagihans->sum('nominal_dibayar'),
            'total_tunggakan' => $tagihans->sum('nominal_tagihan') - $tagihans->sum('nominal_dibayar'),
            'jumlah_transaksi' => $tagihans->count(),
        ];

        return view('laporan.cetak', compact('tagihans', 'summary', 'jenisLaporan', 'startDate', 'endDate'));
    }
}
