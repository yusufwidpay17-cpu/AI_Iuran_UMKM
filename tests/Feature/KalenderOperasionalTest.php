<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pedagang;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Models\KalenderOperasional;
use App\Models\AuditLog;
use App\Services\KalenderOperasionalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KalenderOperasionalTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;
    protected $pedagang1;
    protected $pedagang2;
    protected $userPedagang1;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(KalenderOperasionalService::class);

        // Fetch or create an admin user
        $this->adminUser = User::where('role', 'superadmin')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::create([
                'id' => (string) Str::uuid(),
                'nama' => 'Test Admin',
                'username' => 'testadmin_' . Str::random(5),
                'email' => 'admin_' . Str::random(5) . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'superadmin',
                'status_aktivasi' => 'aktif',
            ]);
        }

        // Get an existing tarif or create one
        $tarif = Tarif::first();
        if (!$tarif) {
            $tarif = Tarif::create([
                'id' => (string) Str::uuid(),
                'jenis_pedagang' => 'Kios Tetap',
                'nominal' => 5000,
                'keterangan' => 'Tarif Harian Pasar',
            ]);
        }

        // Create 2 test merchants
        $this->pedagang1 = Pedagang::create([
            'id' => (string) Str::uuid(),
            'nama_pedagang' => 'Pedagang Test Alpha',
            'no_ktp' => '320101' . rand(1000000000, 9999999999),
            'no_telepon' => '0812' . rand(10000000, 99999999),
            'nama_usaha' => 'Toko Alpha',
            'no_kios' => 'TST-01',
            'blok_kios' => 'Blok A',
            'status_pedagang' => 'aktif',
            'tarif_id' => $tarif->id,
            'saldo_deposit' => 0,
            'tanggal_daftar' => now()->toDateString(),
        ]);

        $this->pedagang2 = Pedagang::create([
            'id' => (string) Str::uuid(),
            'nama_pedagang' => 'Pedagang Test Beta',
            'no_ktp' => '320102' . rand(1000000000, 9999999999),
            'no_telepon' => '0813' . rand(10000000, 99999999),
            'nama_usaha' => 'Toko Beta',
            'no_kios' => 'TST-02',
            'blok_kios' => 'Blok B',
            'status_pedagang' => 'aktif',
            'tarif_id' => $tarif->id,
            'saldo_deposit' => 0,
            'tanggal_daftar' => now()->toDateString(),
        ]);

        // User for pedagang 1
        $this->userPedagang1 = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'User Pedagang Alpha',
            'username' => 'pedagang_alpha_' . Str::random(5),
            'email' => 'alpha_' . Str::random(5) . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'pedagang',
            'pedagang_id' => $this->pedagang1->id,
            'status_aktivasi' => 'aktif',
        ]);
    }

    /**
     * Skenario 1: Hari operasional menghasilkan tagihan sesuai tarif.
     */
    public function test_01_hari_operasional_menghasilkan_tagihan_sesuai_tarif()
    {
        $testDate = '2028-01-10';
        KalenderOperasional::whereDate('tanggal_mulai', '<=', $testDate)
            ->whereDate('tanggal_selesai', '>=', $testDate)
            ->delete();

        $result = $this->service->generateBillsForDate($testDate);

        $this->assertTrue($result['success']);
        $tagihanAlpha = Tagihan::where('pedagang_id', $this->pedagang1->id)
            ->where('tanggal_tagihan', $testDate)
            ->first();

        $this->assertNotNull($tagihanAlpha);
        $this->assertEquals(5000, (float) $tagihanAlpha->nominal_tagihan);
        $this->assertEquals('belum_bayar', $tagihanAlpha->status_bayar);
    }

    /**
     * Skenario 2: Hari bebas iuran tidak menghasilkan tagihan baru.
     */
    public function test_02_hari_bebas_iuran_tidak_menghasilkan_tagihan_baru()
    {
        $testDate = '2028-01-11';
        KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $testDate,
            'tanggal_selesai' => $testDate,
            'kategori' => 'libur_pasar',
            'alasan' => 'Pasar Libur Mingguan',
            'kebijakan_iuran' => 'bebas_iuran',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        $result = $this->service->generateBillsForDate($testDate);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Bebas Iuran', $result['message']);

        $count = Tagihan::where('tanggal_tagihan', $testDate)->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Skenario 3: Catatan petugas tidak melakukan penagihan tidak otomatis menghapus kewajiban
     * jika admin memilih kewajiban tetap berlaku.
     */
    public function test_03_catatan_penagihan_diliburkan_tidak_menghapus_kewajiban_jika_kewajiban_tetap_berlaku()
    {
        $testDate = '2028-01-12';
        KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $testDate,
            'tanggal_selesai' => $testDate,
            'kategori' => 'kegiatan_pesantren',
            'alasan' => 'Ada Pengajian Akbar di Pesantren',
            'kebijakan_iuran' => 'kewajiban_tetap_berlaku',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        $this->assertTrue($this->service->isPenagihanDiliburkan($testDate));
        $this->assertFalse($this->service->isBebasIuran($testDate));

        // Generate bills
        $result = $this->service->generateBillsForDate($testDate);
        $this->assertTrue($result['success']);

        $tagihan = Tagihan::where('pedagang_id', $this->pedagang1->id)
            ->where('tanggal_tagihan', $testDate)
            ->first();

        $this->assertNotNull($tagihan);
        $this->assertEquals(5000, (float) $tagihan->nominal_tagihan);
        $this->assertEquals('belum_bayar', $tagihan->status_bayar);
    }

    /**
     * Skenario 4: Membuka halaman berkali-kali tidak membuat tagihan atau notifikasi ganda.
     */
    public function test_04_membuka_halaman_berkali_kali_tidak_membuat_tagihan_ganda()
    {
        $testDate = '2028-01-13';

        // Hit input endpoint 3 times
        $this->actingAs($this->adminUser)->get(route('tagihan.input', ['tanggal' => $testDate]));
        $firstCount = Tagihan::where('tanggal_tagihan', $testDate)->count();

        $this->actingAs($this->adminUser)->get(route('tagihan.input', ['tanggal' => $testDate]));
        $secondCount = Tagihan::where('tanggal_tagihan', $testDate)->count();

        $this->actingAs($this->adminUser)->get(route('tagihan.input', ['tanggal' => $testDate]));
        $thirdCount = Tagihan::where('tanggal_tagihan', $testDate)->count();

        $this->assertEquals($firstCount, $secondCount);
        $this->assertEquals($secondCount, $thirdCount);
    }

    /**
     * Skenario 5: Tagihan lama yang sah tetap tercatat sebagai tunggakan.
     */
    public function test_05_tagihan_lama_yang_sah_tetap_tercatat_sebagai_tunggakan()
    {
        $oldDate = '2028-01-01';
        $today = '2028-01-14';

        // Create an unpaid bill on oldDate
        Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'tarif_id' => $this->pedagang1->tarif_id,
            'tanggal_tagihan' => $oldDate,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 5000,
            'nominal_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'user_id' => $this->adminUser->id,
        ]);

        // today is bebas_iuran
        KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $today,
            'tanggal_selesai' => $today,
            'kategori' => 'libur_pasar',
            'alasan' => 'Libur',
            'kebijakan_iuran' => 'bebas_iuran',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        // Tunggakan merchant 1 should still include the old unpaid bill
        $tunggakan = Tagihan::where('pedagang_id', $this->pedagang1->id)
            ->whereIn('status_bayar', ['belum_bayar', 'bayar_sebagian'])
            ->sum('nominal_tagihan');

        $this->assertGreaterThanOrEqual(5000, $tunggakan);
    }

    /**
     * Skenario 6: Tanggal operasional berikutnya kembali mengikuti aturan normal.
     */
    public function test_06_tanggal_operasional_berikutnya_kembali_mengikuti_aturan_normal()
    {
        $holiday = '2028-01-15';
        $nextDay = '2028-01-16';

        KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $holiday,
            'tanggal_selesai' => $holiday,
            'kategori' => 'libur_nasional',
            'alasan' => 'Libur Nasional',
            'kebijakan_iuran' => 'bebas_iuran',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        $this->assertTrue($this->service->isBebasIuran($holiday));
        $this->assertFalse($this->service->isBebasIuran($nextDay));

        $res = $this->service->generateBillsForDate($nextDay);
        $this->assertTrue($res['success']);

        $billNextDay = Tagihan::where('pedagang_id', $this->pedagang1->id)
            ->where('tanggal_tagihan', $nextDay)
            ->first();
        $this->assertNotNull($billNextDay);
        $this->assertEquals('belum_bayar', $billNextDay->status_bayar);
    }

    /**
     * Skenario 7: Rentang tanggal diproses secara konsisten.
     */
    public function test_07_rentang_tanggal_diproses_secara_konsisten()
    {
        $startDate = '2028-02-01';
        $endDate = '2028-02-03';

        KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'kategori' => 'kegiatan_pesantren',
            'alasan' => 'Haul Akbar 3 Hari',
            'kebijakan_iuran' => 'bebas_iuran',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        $this->assertTrue($this->service->isBebasIuran('2028-02-01'));
        $this->assertTrue($this->service->isBebasIuran('2028-02-02'));
        $this->assertTrue($this->service->isBebasIuran('2028-02-03'));
        $this->assertFalse($this->service->isBebasIuran('2028-02-04'));
    }

    /**
     * Skenario 8: Perubahan kalender tidak mengubah pembayaran, cicilan, deposit, atau kuitansi yang sudah sah.
     */
    public function test_08_perubahan_kalender_tidak_mengubah_pembayaran_sah()
    {
        $date = '2028-03-01';

        // Create a paid bill
        $paidBill = Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'tarif_id' => $this->pedagang1->tarif_id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 5000,
            'nominal_dibayar' => 5000,
            'status_bayar' => 'lunas',
            'metode_bayar' => 'tunai',
            'waktu_bayar' => now(),
            'no_bukti' => 'STR-TEST-001',
            'petugas_id' => $this->adminUser->id,
        ]);

        $record = KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $date,
            'tanggal_selesai' => $date,
            'kategori' => 'alasan_lain',
            'alasan' => 'Penetapan susulan',
            'kebijakan_iuran' => 'bebas_iuran',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        // Run correction
        $result = $this->service->koreksiTagihanHistoris($record, $this->adminUser->id);

        $freshPaidBill = Tagihan::find($paidBill->id);
        // Ensure paid bill is untouched
        $this->assertEquals('lunas', $freshPaidBill->status_bayar);
        $this->assertEquals(5000, (float) $freshPaidBill->nominal_dibayar);
        $this->assertEquals('STR-TEST-001', $freshPaidBill->no_bukti);
    }

    /**
     * Skenario 9: Koreksi tagihan historis memerlukan otorisasi dan meninggalkan audit trail.
     */
    public function test_09_koreksi_tagihan_historis_meninggalkan_audit_trail()
    {
        $date = '2028-04-01';

        $unpaidBill = Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'tarif_id' => $this->pedagang1->tarif_id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 5000,
            'nominal_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'petugas_id' => $this->adminUser->id,
        ]);

        $record = KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $date,
            'tanggal_selesai' => $date,
            'kategori' => 'libur_pasar',
            'alasan' => 'Pasar Libur',
            'kebijakan_iuran' => 'bebas_iuran',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        $res = $this->service->koreksiTagihanHistoris($record, $this->adminUser->id);
        $this->assertTrue($res['success']);
        $this->assertGreaterThanOrEqual(1, $res['corrected_count']);

        $freshUnpaid = Tagihan::find($unpaidBill->id);
        $this->assertEquals('dibatalkan', $freshUnpaid->status_bayar);
        $this->assertEquals(0, (float) $freshUnpaid->nominal_tagihan);

        // Verify audit log exists
        $audit = AuditLog::where('user_id', $this->adminUser->id)
            ->where('aksi', 'KOREKSI_TAGIHAN_KALENDER')
            ->latest('created_at')
            ->first();
        $this->assertNotNull($audit);
    }

    /**
     * Skenario 10: Pedagang hanya dapat melihat data miliknya sendiri.
     */
    public function test_10_pedagang_hanya_dapat_melihat_data_miliknya_sendiri()
    {
        $date = '2028-05-10';

        Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'tarif_id' => $this->pedagang1->tarif_id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 5000,
            'nominal_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'petugas_id' => $this->adminUser->id,
        ]);

        Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang2->id,
            'tarif_id' => $this->pedagang2->tarif_id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 5000,
            'nominal_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
            'petugas_id' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->userPedagang1)->get(route('pedagang.tagihan'));
        $response->assertStatus(200);
        $response->assertSee('Toko Alpha');
        $response->assertDontSee('Toko Beta');
    }

    /**
     * Skenario 11: Dashboard, Portal Pedagang, dan Laporan konsisten mengabaikan tagihan dibatalkan.
     */
    public function test_11_dashboard_dan_portal_mengabaikan_tagihan_dibatalkan()
    {
        $date = '2028-06-01';

        // Create a dibatalkan bill
        Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'tarif_id' => $this->pedagang1->tarif_id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 0,
            'nominal_dibayar' => 0,
            'status_bayar' => 'dibatalkan',
            'catatan' => '[BEBAS IURAN]',
            'petugas_id' => $this->adminUser->id,
        ]);

        // Check merchant tunggakan page
        $res = $this->actingAs($this->userPedagang1)->get(route('pedagang.tunggakan'));
        $res->assertStatus(200);
        // Should not treat 'dibatalkan' as pending arrears
        $res->assertDontSee('Menunggu Pembayaran');
    }

    /**
     * Skenario 12: Input manual dan generate tidak dapat melewati aturan operasional bebas iuran.
     */
    public function test_12_input_manual_dan_generate_dicegah_pada_hari_bebas_iuran()
    {
        $date = '2028-07-01';

        KalenderOperasional::create([
            'id' => (string) Str::uuid(),
            'tanggal_mulai' => $date,
            'tanggal_selesai' => $date,
            'kategori' => 'libur_pasar',
            'alasan' => 'Renovasi Pasar',
            'kebijakan_iuran' => 'bebas_iuran',
            'status' => 'aktif',
            'created_by' => $this->adminUser->id,
        ]);

        // Attempt batch generate
        $genRes = $this->actingAs($this->adminUser)->post(route('tagihan.generate'), [
            'tanggal_generate' => $date,
        ]);
        $genRes->assertSessionHas('error');

        // Attempt manual bill store
        $manualRes = $this->actingAs($this->adminUser)->post(route('tagihan.store-manual'), [
            'pedagang_id' => $this->pedagang1->id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'status_pembayaran' => 'lunas',
            'nominal_tagihan' => 5000,
            'nominal_dibayar' => 5000,
            'metode_bayar' => 'tunai',
        ]);
        $manualRes->assertSessionHas('error');
    }

    /**
     * Skenario 13: Konfigurasi zona waktu aplikasi mengikuti Asia/Jakarta.
     */
    public function test_13_konfigurasi_zona_waktu_sesuai_asia_jakarta()
    {
        $this->assertEquals('Asia/Jakarta', config('app.timezone'));
    }

    /**
     * Skenario 14: Laporan tunggakan dan rekapitulasi tidak menghitung tagihan dibatalkan.
     */
    public function test_14_laporan_tunggakan_tidak_menghitung_tagihan_dibatalkan()
    {
        $date = '2028-08-01';

        Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'tarif_id' => $this->pedagang1->tarif_id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 0,
            'nominal_dibayar' => 0,
            'status_bayar' => 'dibatalkan',
            'catatan' => '[BEBAS IURAN]',
            'petugas_id' => $this->adminUser->id,
        ]);

        $res = $this->actingAs($this->adminUser)->get(route('laporan.index', [
            'jenis' => 'tunggakan',
            'start_date' => $date,
            'end_date' => $date,
        ]));

        $res->assertStatus(200);
        $res->assertViewHas('summary', function ($summary) {
            return $summary['total_tunggakan'] === 0 && $summary['belum_count'] === 0;
        });
    }

    /**
     * Skenario 15: Detail pedagang tidak menghitung tagihan dibatalkan sebagai tunggakan.
     */
    public function test_15_pedagang_show_tunggakan_tidak_menghitung_tagihan_dibatalkan()
    {
        $date = '2028-08-02';

        Tagihan::create([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'tarif_id' => $this->pedagang1->tarif_id,
            'tanggal_tagihan' => $date,
            'jenis_iuran' => 'Iuran Harian Pasar',
            'nominal_tagihan' => 0,
            'nominal_dibayar' => 0,
            'status_bayar' => 'dibatalkan',
            'catatan' => '[BEBAS IURAN]',
            'petugas_id' => $this->adminUser->id,
        ]);

        $res = $this->actingAs($this->adminUser)->get(route('pedagang.show', $this->pedagang1->id));

        $res->assertStatus(200);
        $res->assertViewHas('totalTunggakan', 0);
        $res->assertViewHas('riwayatTunggakan', function ($riwayat) {
            return $riwayat->where('status_bayar', 'dibatalkan')->count() === 0;
        });
    }
}
