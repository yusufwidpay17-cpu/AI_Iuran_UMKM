<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ExportHelper;
use App\Models\Pedagang;
use App\Models\PendaftaranPedagang;
use App\Models\Lapak;
use App\Models\Tagihan;

class ExportExcelController extends Controller
{
    public function pedagang(Request $request)
    {
        $query = Pedagang::with('tarif');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_pedagang', 'like', "%{$search}%")
                  ->orWhere('no_kios', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status_pedagang')) {
            $query->where('status_pedagang', $request->input('status_pedagang'));
        }

        $pedagangs = $query->orderBy('no_kios', 'asc')->get();

        $headers = ['No Kios', 'Blok', 'Nama Pedagang', 'No KTP', 'No Telepon', 'Nama Usaha', 'Kategori', 'Tanggal Daftar', 'Status', 'Saldo Deposit'];
        $rows = [];

        foreach ($pedagangs as $p) {
            $rows[] = [
                $p->no_kios,
                $p->blok_kios,
                $p->nama_pedagang,
                $p->no_ktp,
                $p->no_telepon ?? '-',
                $p->nama_usaha ?? '-',
                ucfirst($p->kategori_usaha),
                $p->tanggal_daftar,
                ucfirst($p->status_pedagang),
                number_format($p->saldo_deposit, 2, ',', '.')
            ];
        }

        return ExportHelper::downloadCsv('Data_Pedagang', $headers, $rows);
    }

    public function pendaftaran(Request $request)
    {
        $query = PendaftaranPedagang::with('tarif');
        if ($request->filled('status')) {
            $query->where('status_pendaftaran', $request->input('status'));
        }

        $pendaftarans = $query->orderBy('created_at', 'desc')->get();

        $headers = ['Nama Calon', 'No KTP', 'No HP', 'No Kios', 'Blok', 'Kategori Usaha', 'Tanggal Daftar', 'Status Pendaftaran', 'Catatan'];
        $rows = [];

        foreach ($pendaftarans as $p) {
            $rows[] = [
                $p->nama_calon,
                $p->no_ktp,
                $p->no_telepon ?? '-',
                $p->no_kios,
                $p->blok_kios,
                ucfirst($p->kategori_usaha),
                $p->tanggal_daftar,
                str_replace('_', ' ', ucfirst($p->status_pendaftaran)),
                $p->catatan ?? '-'
            ];
        }

        return ExportHelper::downloadCsv('Data_Pendaftaran_Pedagang', $headers, $rows);
    }

    public function lapak(Request $request)
    {
        $lapaks = Lapak::with(['pedagang', 'tarif'])->orderBy('kode_lapak', 'asc')->get();

        $headers = ['Kode Lapak', 'Blok Kios', 'Nama Pedagang', 'Kategori', 'Status Lapak', 'Tanggal Mulai', 'Keterangan'];
        $rows = [];

        foreach ($lapaks as $l) {
            $rows[] = [
                $l->kode_lapak,
                $l->blok_kios,
                $l->pedagang ? $l->pedagang->nama_pedagang : 'Kosong',
                ucfirst($l->kategori),
                ucfirst($l->status_lapak),
                $l->tanggal_mulai ?? '-',
                $l->keterangan ?? '-'
            ];
        }

        return ExportHelper::downloadCsv('Data_Lapak', $headers, $rows);
    }

    public function tagihan(Request $request)
    {
        // Parse target date (or default to today)
        $targetDateStr = $request->input('tanggal', $request->input('start_date', now()->format('Y-m-d')));
        $targetDate = \Carbon\Carbon::parse($targetDateStr);

        $year = $targetDate->year;
        $month = $targetDate->month;
        $daysInMonth = $targetDate->daysInMonth;

        $monthsIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulanIndo = $monthsIndo[$month] ?? $targetDate->format('F');

        // Title Header Rows matching user's manual paper document structure
        $titleRows = [
            ['DAFTAR TAGIHAN LAPAK PASAR - AMANAHLEDGER'],
            ['SI IURAN UMKM PASAR / RETRIBUSI HARIAN PEDAGANG'],
            ['Bulan: ' . $namaBulanIndo . ' ' . $year],
            [] // Empty separator row
        ];

        // Column Headers: NO, NO KIOS, NAMA PEDAGANG, NAMA USAHA, [1..daysInMonth], TOTAL LUNAS, TOTAL DIBAYAR (RP)
        $headers = ['NO', 'NO KIOS', 'NAMA PEDAGANG', 'NAMA USAHA'];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $headers[] = (string) $d;
        }
        $headers[] = 'TOTAL LUNAS (HARI)';
        $headers[] = 'TOTAL TERKUMPUL (RP)';

        // Fetch Active Merchants
        $pedagangs = Pedagang::where('status_pedagang', 'aktif')
            ->orderBy('blok_kios', 'asc')
            ->orderBy('no_kios', 'asc')
            ->get();

        // Fetch all bills for that month
        $tagihansRaw = Tagihan::whereYear('tanggal_tagihan', $year)
            ->whereMonth('tanggal_tagihan', $month)
            ->get();

        $tagihansGrouped = [];
        foreach ($tagihansRaw as $t) {
            $dayNum = (int) \Carbon\Carbon::parse($t->tanggal_tagihan)->format('j');
            $tagihansGrouped[$t->pedagang_id . '_' . $dayNum] = $t;
        }

        $rows = [];
        $dailyTotals = array_fill(1, $daysInMonth, 0);
        $no = 1;

        foreach ($pedagangs as $p) {
            $row = [
                $no++,
                $p->no_kios ?? '-',
                $p->nama_pedagang,
                $p->nama_usaha ?? ucfirst($p->kategori_usaha)
            ];

            $lunasCount = 0;
            $totalDibayarPedagang = 0;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $key = $p->id . '_' . $d;
                if (isset($tagihansGrouped[$key])) {
                    $t = $tagihansGrouped[$key];
                    if ($t->nominal_dibayar > 0) {
                        $row[] = number_format($t->nominal_dibayar, 0, ',', '.');
                        if ($t->status_bayar === 'lunas') {
                            $lunasCount++;
                        }
                        $totalDibayarPedagang += $t->nominal_dibayar;
                        $dailyTotals[$d] += $t->nominal_dibayar;
                    } elseif (str_contains($t->catatan ?? '', '[TIDAK BERDAGANG]')) {
                        $row[] = 'Tutup';
                    } else {
                        $row[] = '-';
                    }
                } else {
                    $row[] = '-';
                }
            }

            $row[] = $lunasCount . ' Hari';
            $row[] = number_format($totalDibayarPedagang, 0, ',', '.');
            $rows[] = $row;
        }

        // Summary Row at the Bottom
        $summaryRow = ['TOTAL HARIAN TERKUMPUL (RP)', '', '', ''];
        $grandTotalBulan = 0;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $summaryRow[] = number_format($dailyTotals[$d], 0, ',', '.');
            $grandTotalBulan += $dailyTotals[$d];
        }
        $summaryRow[] = '-';
        $summaryRow[] = number_format($grandTotalBulan, 0, ',', '.');

        $rows[] = []; // empty row spacer
        $rows[] = $summaryRow;

        $filename = 'Daftar_Tagihan_Lapak_' . str_replace(' ', '_', $namaBulanIndo) . '_' . $year;

        return ExportHelper::downloadCsv($filename, $headers, $rows, $titleRows);
    }

    public function riwayat(Request $request)
    {
        $query = Tagihan::with(['pedagang', 'petugas'])->where('nominal_dibayar', '>', 0);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_tagihan', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_tagihan', '<=', $request->input('end_date'));
        }

        $transaksis = $query->orderBy('waktu_bayar', 'desc')->get();

        $headers = ['Waktu Bayar', 'No Bukti', 'Kode Kios', 'Nama Pedagang', 'Nominal Dibayar', 'Metode Bayar', 'Status Bayar', 'Petugas'];
        $rows = [];

        foreach ($transaksis as $t) {
            $rows[] = [
                $t->waktu_bayar ? $t->waktu_bayar->format('Y-m-d H:i:s') : '-',
                $t->no_bukti ?? '-',
                $t->pedagang ? $t->pedagang->no_kios : '-',
                $t->pedagang ? $t->pedagang->nama_pedagang : '-',
                number_format($t->nominal_dibayar, 0, ',', '.'),
                strtoupper($t->metode_bayar ?? 'TUNAI'),
                str_replace('_', ' ', ucfirst($t->status_bayar)),
                $t->petugas ? $t->petugas->nama : 'Sistem'
            ];
        }

        return ExportHelper::downloadCsv('Riwayat_Transaksi_Pembayaran', $headers, $rows);
    }

    public function laporan(Request $request)
    {
        return $this->tagihan($request);
    }

    public function detailPedagang($id)
    {
        $pedagang = Pedagang::with(['tagihan.petugas'])->findOrFail($id);

        $headers = ['Tanggal Tagihan', 'Jenis Iuran', 'Nominal Tagihan', 'Nominal Dibayar', 'Status Bayar', 'Waktu Bayar', 'Metode Bayar', 'No Bukti', 'Catatan'];
        $rows = [];

        foreach ($pedagang->tagihan->sortByDesc('tanggal_tagihan') as $t) {
            $rows[] = [
                $t->tanggal_tagihan ? $t->tanggal_tagihan->format('Y-m-d') : '-',
                $t->jenis_iuran ?? 'Iuran Harian',
                number_format($t->nominal_tagihan, 0, ',', '.'),
                number_format($t->nominal_dibayar, 0, ',', '.'),
                str_replace('_', ' ', ucfirst($t->status_bayar)),
                $t->waktu_bayar ? $t->waktu_bayar->format('Y-m-d H:i:s') : '-',
                $t->metode_bayar ? strtoupper($t->metode_bayar) : '-',
                $t->no_bukti ?? '-',
                $t->catatan ?? '-'
            ];
        }

        $filename = 'Riwayat_Pedagang_' . str_replace(' ', '_', $pedagang->nama_pedagang);
        return ExportHelper::downloadCsv($filename, $headers, $rows);
    }
}
