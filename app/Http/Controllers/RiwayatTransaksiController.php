<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pedagang;

class RiwayatTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Tagihan::with(['pedagang.tarif', 'petugas'])
            ->where('nominal_dibayar', '>', 0);

        // Jika user adalah pedagang, hanya tampilkan riwayat pembayaran miliknya sendiri
        if ($user && $user->role === 'pedagang') {
            $query->where('pedagang_id', $user->pedagang_id);
        }

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

        $periode = $request->input('periode');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Pilihan Periode Praktis
        if ($periode === 'hari_ini') {
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        } elseif ($periode === '7_hari') {
            $startDate = now()->subDays(6)->toDateString();
            $endDate = now()->toDateString();
        } elseif ($periode === 'bulan_ini') {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        } elseif ($periode === 'semua') {
            $startDate = null;
            $endDate = null;
        }

        // Filter berdasarkan tanggal pembayaran (waktu_bayar)
        if (!empty($startDate)) {
            $query->where(function($q) use ($startDate) {
                $q->whereDate('waktu_bayar', '>=', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('waktu_bayar')
                          ->whereDate('tanggal_tagihan', '>=', $startDate);
                  });
            });
        }
        if (!empty($endDate)) {
            $query->where(function($q) use ($endDate) {
                $q->whereDate('waktu_bayar', '<=', $endDate)
                  ->orWhere(function($sub) use ($endDate) {
                      $sub->whereNull('waktu_bayar')
                          ->whereDate('tanggal_tagihan', '<=', $endDate);
                  });
            });
        }

        if ($request->filled('pedagang_id') && (!$user || $user->role !== 'pedagang')) {
            $query->where('pedagang_id', $request->input('pedagang_id'));
        }
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->input('metode_bayar'));
        }
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->input('status_bayar'));
        }

        if ($request->has('reset')) {
            return redirect()->route('riwayat.index');
        }

        // Transaksi terbaru tampil paling atas berdasarkan waktu transaksi
        $transaksis = $query->orderByRaw("COALESCE(waktu_bayar, CONCAT(tanggal_tagihan, ' 00:00:00')) desc")
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Jika pedagang, tidak perlu kirim daftar seluruh pedagang
        $pedagangs = ($user && $user->role === 'pedagang') 
            ? collect([]) 
            : Pedagang::orderBy('nama_pedagang', 'asc')->get();

        return view('riwayat.index', compact('transaksis', 'pedagangs', 'startDate', 'endDate', 'periode'));
    }

    public function cetak($id)
    {
        $user = auth()->user();
        $tagihan = Tagihan::with(['pedagang.tarif', 'petugas'])->findOrFail($id);

        // Keamanan: cegah pedagang mencetak kuitansi pedagang lain
        if ($user && $user->role === 'pedagang' && $tagihan->pedagang_id !== $user->pedagang_id) {
            abort(403, 'Akses Ditolak: Anda tidak diizinkan mencetak bukti pembayaran milik pedagang lain.');
        }

        // Integritas kuitansi: Bukti pembayaran hanya boleh dicetak jika tagihan sudah memiliki nominal pembayaran sah (> 0)
        if ((float) $tagihan->nominal_dibayar <= 0) {
            abort(400, 'Permintaan Ditolak: Tagihan ini belum memiliki pembayaran sah sehingga bukti pembayaran belum dapat dicetak.');
        }

        return view('riwayat.cetak', compact('tagihan'));
    }
}
