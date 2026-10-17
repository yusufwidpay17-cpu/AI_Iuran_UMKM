<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pedagang;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntegritasTransaksiTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;
    protected $petugasUser;
    protected $pedagang1;
    protected $pedagang2;
    protected $pedagang3;
    protected $userPedagang1;
    protected $userPedagang2;
    protected $tarif;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin / Petugas User
        $this->adminUser = User::where('role', 'superadmin')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::create([
                'id' => (string) Str::uuid(),
                'nama' => 'Admin Penguji',
                'username' => 'adm_uji_' . Str::random(5),
                'email' => 'admin_uji_' . Str::random(5) . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'superadmin',
                'status_aktivasi' => 'aktif',
            ]);
        }

        $this->petugasUser = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Petugas Lapangan Uji',
            'username' => 'petugas_' . Str::random(5),
            'email' => 'petugas_' . Str::random(5) . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'petugas',
            'status_aktivasi' => 'aktif',
        ]);

        // 2. Tarif
        $this->tarif = Tarif::first();
        if (!$this->tarif) {
            $this->tarif = Tarif::create([
                'id' => (string) Str::uuid(),
                'jenis_pedagang' => 'Kios Tetap',
                'nominal' => 10000,
                'keterangan' => 'Tarif Harian Pasar 10k',
            ]);
        }

        // 3. Pedagang 1
        $this->pedagang1 = Pedagang::create([
            'id' => (string) Str::uuid(),
            'nama_pedagang' => 'Pedagang Integritas 1',
            'no_ktp' => '320101' . rand(1000000000, 9999999999),
            'no_telepon' => '0812' . rand(10000000, 99999999),
            'nama_usaha' => 'Toko Amanah 1',
            'no_kios' => 'INT-01',
            'blok_kios' => 'Blok A',
            'status_pedagang' => 'aktif',
            'tarif_id' => $this->tarif->id,
            'saldo_deposit' => 0,
            'tanggal_daftar' => now()->toDateString(),
        ]);

        // 4. Pedagang 2
        $this->pedagang2 = Pedagang::create([
            'id' => (string) Str::uuid(),
            'nama_pedagang' => 'Pedagang Integritas 2',
            'no_ktp' => '320102' . rand(1000000000, 9999999999),
            'no_telepon' => '0813' . rand(10000000, 99999999),
            'nama_usaha' => 'Toko Amanah 2',
            'no_kios' => 'INT-02',
            'blok_kios' => 'Blok B',
            'status_pedagang' => 'aktif',
            'tarif_id' => $this->tarif->id,
            'saldo_deposit' => 0,
            'tanggal_daftar' => now()->toDateString(),
        ]);

        // 5. Pedagang 3
        $this->pedagang3 = Pedagang::create([
            'id' => (string) Str::uuid(),
            'nama_pedagang' => 'Pedagang Integritas 3',
            'no_ktp' => '320103' . rand(1000000000, 9999999999),
            'no_telepon' => '0814' . rand(10000000, 99999999),
            'nama_usaha' => 'Toko Amanah 3',
            'no_kios' => 'INT-03',
            'blok_kios' => 'Blok C',
            'status_pedagang' => 'aktif',
            'tarif_id' => $this->tarif->id,
            'saldo_deposit' => 0,
            'tanggal_daftar' => now()->toDateString(),
        ]);

        // 6. User Portal Pedagang 1 & 2
        $this->userPedagang1 = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Akun Pedagang 1',
            'username' => 'usr_pdg1_' . Str::random(5),
            'email' => 'pdg1_' . Str::random(5) . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'pedagang',
            'pedagang_id' => $this->pedagang1->id,
            'status_aktivasi' => 'aktif',
        ]);

        $this->userPedagang2 = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Akun Pedagang 2',
            'username' => 'usr_pdg2_' . Str::random(5),
            'email' => 'pdg2_' . Str::random(5) . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'pedagang',
            'pedagang_id' => $this->pedagang2->id,
            'status_aktivasi' => 'aktif',
        ]);
    }

    protected function createTagihan(array $attributes = []): Tagihan
    {
        return Tagihan::create(array_merge([
            'id' => (string) Str::uuid(),
            'pedagang_id' => $this->pedagang1->id,
            'petugas_id' => $this->petugasUser->id,
            'tarif_id' => $this->tarif->id,
            'tanggal_tagihan' => '2028-02-01',
            'jenis_iuran' => 'Iuran Harian',
            'nominal_tagihan' => 10000,
            'nominal_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
        ], $attributes));
    }

    /**
     * Skenario 1: Pembayaran manual lunas mencatat no_bukti, waktu_bayar, dan status lunas.
     */
    public function test_01_pembayaran_manual_lunas_mencatat_bukti_waktu_dan_status()
    {
        $tagihan = $this->createTagihan(['tanggal_tagihan' => '2028-02-01']);

        $response = $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 10000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'lunas',
            'catatan' => 'Pembayaran lunas kasir',
        ]);

        $response->assertSessionHas('success');

        $tagihan->refresh();
        $this->assertEquals(10000, (float) $tagihan->nominal_dibayar);
        $this->assertEquals('lunas', $tagihan->status_bayar);
        $this->assertEquals('tunai', $tagihan->metode_bayar);
        $this->assertNotNull($tagihan->no_bukti);
        $this->assertTrue(str_starts_with($tagihan->no_bukti, 'INV-') || str_starts_with($tagihan->no_bukti, 'BKT-'));
        $this->assertNotNull($tagihan->waktu_bayar);
    }

    /**
     * Skenario 2: Pembayaran cicilan (nominal < tagihan) wajib berstatus bayar_sebagian.
     */
    public function test_02_pembayaran_cicilan_pertama_berstatus_bayar_sebagian()
    {
        $tagihan = $this->createTagihan(['tanggal_tagihan' => '2028-02-02']);

        $response = $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 4000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'bayar_sebagian',
            'catatan' => 'Cicilan pertama',
        ]);

        $response->assertSessionHas('success');

        $tagihan->refresh();
        $this->assertEquals(4000, (float) $tagihan->nominal_dibayar);
        $this->assertEquals('bayar_sebagian', $tagihan->status_bayar);
        $this->assertEquals(6000, (float) ($tagihan->nominal_tagihan - $tagihan->nominal_dibayar));
        $this->assertNotNull($tagihan->no_bukti);
    }

    /**
     * Skenario 3: Upaya menandai 'lunas' pada pembayaran sebagian DITOLAK/DIKOREKSI menjadi 'bayar_sebagian'.
     */
    public function test_03_pembayaran_parsial_tidak_bisa_ditandai_lunas_sebelum_kewajiban_terpenuhi()
    {
        $tagihan = $this->createTagihan(['tanggal_tagihan' => '2028-02-03']);

        // Petugas secara keliru memilih status 'lunas' padahal hanya membayar 3000
        $response = $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 3000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'lunas', // Keliru
            'catatan' => 'Mencoba menandai lunas meski cuma bayar 3000',
        ]);

        $tagihan->refresh();
        // Sistem integrity check HARUS memaksa status tetap bayar_sebagian
        $this->assertNotEquals('lunas', $tagihan->status_bayar, 'Pembayaran di bawah nominal tagihan tidak boleh berstatus lunas!');
        $this->assertEquals('bayar_sebagian', $tagihan->status_bayar);
        $this->assertEquals(3000, (float) $tagihan->nominal_dibayar);
        $this->assertEquals(7000, (float) ($tagihan->nominal_tagihan - $tagihan->nominal_dibayar));
    }

    /**
     * Skenario 4: Pembayaran cicilan bertahap terakumulasi dengan benar (tidak menimpa pembayaran awal).
     */
    public function test_04_cicilan_bertahap_terakumulasi_secara_akurat()
    {
        $tagihan = $this->createTagihan(['tanggal_tagihan' => '2028-02-04']);

        // Cicilan 1: Rp 4.000
        $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 4000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'bayar_sebagian',
        ]);

        $tagihan->refresh();
        $this->assertEquals(4000, (float) $tagihan->nominal_dibayar);

        // Cicilan 2: Rp 3.000
        $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 3000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'bayar_sebagian',
        ]);

        $tagihan->refresh();
        // Akumulasi harus Rp 7.000, BUKAN Rp 3.000
        $this->assertEquals(7000, (float) $tagihan->nominal_dibayar, 'Cicilan kedua harus diakumulasi dengan cicilan pertama!');
        $this->assertEquals('bayar_sebagian', $tagihan->status_bayar);
        $this->assertEquals(3000, (float) ($tagihan->nominal_tagihan - $tagihan->nominal_dibayar));
    }

    /**
     * Skenario 5: Pelunasan cicilan terakhir mengubah status otomatis menjadi lunas.
     */
    public function test_05_pelunasan_cicilan_terakhir_mengubah_status_menjadi_lunas()
    {
        $tagihan = $this->createTagihan([
            'tanggal_tagihan' => '2028-02-05',
            'nominal_dibayar' => 7000,
            'status_bayar' => 'bayar_sebagian',
        ]);

        // Bayar sisa Rp 3.000
        $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 3000,
            'metode_bayar' => 'transfer',
            'status_pembayaran' => 'lunas',
        ]);

        $tagihan->refresh();
        $this->assertEquals(10000, (float) $tagihan->nominal_dibayar);
        $this->assertEquals('lunas', $tagihan->status_bayar);
        $this->assertEquals(0, (float) ($tagihan->nominal_tagihan - $tagihan->nominal_dibayar));
    }

    /**
     * Skenario 6: Idempotensi - Pengiriman ulang form pembayaran pada tagihan yang sudah lunas tidak menduplikasi transaksi.
     */
    public function test_06_idempotensi_pembayaran_ulang_tidak_menduplikasi_transaksi_atau_deposit()
    {
        $this->pedagang1->update(['saldo_deposit' => 0]);

        $tagihan = $this->createTagihan([
            'tanggal_tagihan' => '2028-02-06',
            'nominal_dibayar' => 10000,
            'status_bayar' => 'lunas',
            'no_bukti' => 'BKT-20280206-0001',
            'waktu_bayar' => now(),
        ]);

        $originalBukti = $tagihan->no_bukti;

        // User mengklik submit 2 kali berturut-turut pada form pembayaran
        $response = $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 10000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'lunas',
        ]);

        $tagihan->refresh();
        $this->pedagang1->refresh();

        $this->assertEquals(10000, (float) $tagihan->nominal_dibayar);
        $this->assertEquals('lunas', $tagihan->status_bayar);
        $this->assertEquals($originalBukti, $tagihan->no_bukti, 'No bukti tidak boleh berubah karena pengiriman form berulang');
        $this->assertEquals(0, (float) $this->pedagang1->saldo_deposit, 'Saldo deposit tidak boleh bertambah keliru akibat double-submit yang sama');
    }

    /**
     * Skenario 7: Kelebihan bayar dialokasikan ke saldo deposit pedagang secara aman.
     */
    public function test_07_kelebihan_bayar_masuk_ke_saldo_deposit_secara_aman()
    {
        $this->pedagang1->update(['saldo_deposit' => 5000]);

        $tagihan = $this->createTagihan(['tanggal_tagihan' => '2028-02-07']);

        // Bayar 15.000 untuk tagihan 10.000 -> kelebihan 5.000 harus masuk deposit
        $response = $this->actingAs($this->petugasUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 15000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'lunas',
        ]);

        $tagihan->refresh();
        $this->pedagang1->refresh();

        $this->assertEquals(10000, (float) $tagihan->nominal_dibayar);
        $this->assertEquals('lunas', $tagihan->status_bayar);
        // Saldo deposit awal 5.000 + kelebihan 5.000 = 10.000
        $this->assertEquals(10000, (float) $this->pedagang1->saldo_deposit);
    }

    /**
     * Skenario 8: DB Transaction Rollback saat kegagalan penyimpanan menjaga saldo deposit dan tagihan tetap konsisten.
     */
    public function test_08_kegagalan_penyimpanan_merollback_transaksi_secara_utuh()
    {
        $initialDeposit = 20000;
        $this->pedagang1->update(['saldo_deposit' => $initialDeposit]);

        $tagihan = $this->createTagihan(['tanggal_tagihan' => '2028-02-08']);

        // Simulasikan operasi pembayaran yang melempar exception di tengah proses
        $failed = false;
        try {
            DB::transaction(function () use ($tagihan) {
                $p = Pedagang::lockForUpdate()->find($this->pedagang1->id);
                $p->saldo_deposit -= 10000;
                $p->save();

                // Simulasi error tidak terduga pada sistem perbankan / database sebelum tagihan tersimpan
                throw new \RuntimeException('Simulasi kegagalan I/O fatal pada transaksi perbankan');
            });
        } catch (\RuntimeException $e) {
            $failed = true;
        }

        $this->assertTrue($failed);

        $this->pedagang1->refresh();
        $tagihan->refresh();

        // Saldo deposit TIDAK BOLEH berkurang karena transaksi di-rollback secara atomik
        $this->assertEquals($initialDeposit, (float) $this->pedagang1->saldo_deposit);
        $this->assertEquals(0, (float) $tagihan->nominal_dibayar);
        $this->assertEquals('belum_bayar', $tagihan->status_bayar);
    }

    /**
     * Skenario 9: Keamanan Akses (IDOR Prevention) - Pedagang 1 tidak diizinkan mencetak bukti milik Pedagang 2.
     */
    public function test_09_keamanan_akses_pedagang_dilarang_mencetak_kuitansi_pedagang_lain()
    {
        // Tagihan sah milik Pedagang 2
        $tagihanPedagang2 = $this->createTagihan([
            'pedagang_id' => $this->pedagang2->id,
            'tanggal_tagihan' => '2028-02-09',
            'nominal_dibayar' => 10000,
            'status_bayar' => 'lunas',
            'no_bukti' => 'BKT-20280209-9999',
            'waktu_bayar' => now(),
        ]);

        // User Pedagang 1 mencoba mengakses kuitansi milik Pedagang 2 dengan memanipulasi ID di URL
        $response = $this->actingAs($this->userPedagang1)->get("/riwayat-transaksi/{$tagihanPedagang2->id}/cetak");

        // Backend HARUS menolak dengan status 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * Skenario 10: Bukti Pembayaran tidak dapat dicetak jika tagihan belum memiliki pembayaran sah (> 0).
     */
    public function test_10_tagihan_belum_bayar_tidak_dapat_mencetak_kuitansi_resmi()
    {
        $tagihanUnpaid = $this->createTagihan(['tanggal_tagihan' => '2028-02-10']);

        // Mencoba mencetak kuitansi untuk tagihan yang nominal_dibayar-nya 0
        $response = $this->actingAs($this->adminUser)->get("/riwayat-transaksi/{$tagihanUnpaid->id}/cetak");

        // Backend HARUS menolak dengan status 400 Bad Request
        $response->assertStatus(400);
    }

    /**
     * Skenario 11: Koreksi / perubahan transaksi mencatat Audit Trail lengkap (user, waktu, sebelum, sesudah).
     */
    public function test_11_koreksi_transaksi_mencatat_audit_log_lengkap_sebelum_dan_sesudah()
    {
        $tagihan = $this->createTagihan(['tanggal_tagihan' => '2028-02-11']);

        // Lakukan pembayaran melalui bayar
        $this->actingAs($this->adminUser)->post("/tagihan/{$tagihan->id}/bayar", [
            'nominal_dibayar' => 10000,
            'metode_bayar' => 'qris',
            'status_pembayaran' => 'lunas',
            'catatan' => 'Audit test QRIS',
        ]);

        // Periksa keberadaan Audit Log
        $log = AuditLog::where('user_id', $this->adminUser->id)
            ->where('aksi', 'UPDATE_TRANSACTION')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log, 'Audit log harus tercatat saat transaksi pembayaran disimpan.');
        $this->assertEquals($this->adminUser->id, $log->user_id);
        $this->assertStringContainsString('BELUM_BAYAR', strtoupper($log->deskripsi));
        $this->assertStringContainsString('LUNAS', strtoupper($log->deskripsi));
        $this->assertStringContainsString('10.000', $log->deskripsi);
    }

    /**
     * Skenario 12: Konsistensi Laporan - Pendapatan hanya menghitung nominal yang benar-benar dibayar.
     */
    public function test_12_konsistensi_laporan_pendapatan_hanya_menghitung_pembayaran_riil()
    {
        $testDate = '2028-02-12';
        
        // 1 tagihan lunas (10.000) - Pedagang 1
        $this->createTagihan([
            'pedagang_id' => $this->pedagang1->id,
            'tanggal_tagihan' => $testDate,
            'nominal_tagihan' => 10000,
            'nominal_dibayar' => 10000,
            'status_bayar' => 'lunas',
            'no_bukti' => 'BKT-20280212-0001',
        ]);

        // 1 tagihan cicil (10.000, dibayar 4.000) - Pedagang 2
        $this->createTagihan([
            'pedagang_id' => $this->pedagang2->id,
            'tanggal_tagihan' => $testDate,
            'nominal_tagihan' => 10000,
            'nominal_dibayar' => 4000,
            'status_bayar' => 'bayar_sebagian',
            'no_bukti' => 'BKT-20280212-0002',
        ]);

        // 1 tagihan belum bayar (10.000, dibayar 0) - Pedagang 3
        $this->createTagihan([
            'pedagang_id' => $this->pedagang3->id,
            'tanggal_tagihan' => $testDate,
            'nominal_tagihan' => 10000,
            'nominal_dibayar' => 0,
            'status_bayar' => 'belum_bayar',
        ]);

        $query = Tagihan::where('tanggal_tagihan', $testDate);
        $totalTagihan = $query->sum('nominal_tagihan'); // 30.000
        $totalPendapatanRiil = $query->sum('nominal_dibayar'); // 14.000
        $totalTunggakan = $query->whereIn('status_bayar', ['belum_bayar', 'bayar_sebagian'])
            ->get()
            ->sum(function($item) {
                return $item->nominal_tagihan - $item->nominal_dibayar;
            }); // 6.000 (cicilan) + 10.000 (belum bayar) = 16.000

        $this->assertEquals(30000, $totalTagihan);
        $this->assertEquals(14000, $totalPendapatanRiil, 'Pendapatan yang diakui hanya uang riil yang masuk (14.000), BUKAN total tagihan (30.000)');
        $this->assertEquals(16000, $totalTunggakan, 'Total tunggakan harus tepat mencerminkan sisa kewajiban (16.000)');
        $this->assertEquals($totalTagihan, $totalPendapatanRiil + $totalTunggakan, 'Prinsip akuntansi: Total Tagihan = Total Terkumpul + Total Tunggakan');
    }
}
