<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pedagang;

class RiwayatTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with(['pedagang.tarif', 'petugas'])
            ->where('nominal_dibayar', '>', 0);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhereHas('pedagang', function($qp) use ($search) {
                      $qp->where('nama_pedagang', 'like', "%{$search}%")
                        ->orWhere('no_kios', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_tagihan', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_tagihan', '<=', $request->input('end_date'));
        }
        if ($request->filled('pedagang_id')) {
            $query->where('pedagang_id', $request->input('pedagang_id'));
        }
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->input('metode_bayar'));
        }
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->input('status_bayar'));
        }

        $transaksis = $query->orderBy('waktu_bayar', 'desc')->paginate(15)->withQueryString();
        $pedagangs = Pedagang::orderBy('nama_pedagang', 'asc')->get();

        return view('riwayat.index', compact('transaksis', 'pedagangs'));
    }

    public function cetak($id)
    {
        $tagihan = Tagihan::with(['pedagang.tarif', 'petugas'])->findOrFail($id);
        return view('riwayat.cetak', compact('tagihan'));
    }
}
