<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pedagang;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Services\KalenderOperasionalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PedagangTransaksiIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;
    protected $tarif;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::where('role', 'superadmin')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::create([
                'id' => (string) Str::uuid(),
                'nama' => 'Superadmin Test',
                'username' => 'superadmin_' . Str::random(5),
                'email' => 'admin_' . Str::random(5) . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'superadmin',
                'status_aktivasi' => 'aktif',
            ]);
        }

        $this->tarif = Tarif::where('aktif', 1)->first();
        if (!$this->tarif) {
            $this->tarif = Tarif::create([
                'id' => (string) Str::uuid(),
                'nama_tarif' => 'Tarif Kuliner Test',
                'kategori' => 'kuliner',
                'nominal_harian' => 5000,
                'berlaku_mulai' => '2026-01-01',
                'aktif' => 1,
            ]);
        }
    }

    /** @test */
    public function pendaftaran_pedagang_baru_otomatis_terhubung_ke_transaksi_pembayaran_besok_harinya()
    {
        $today = Carbon::today()->format('Y-m-d');
        $besok = Carbon::today()->addDay()->format('Y-m-d');
        $uniqueNo = rand(1000, 9999);

        $response = $this->actingAs($this->adminUser)->post(route('pedagang.store'), [
            'nama_pedagang' => 'Pedagang Baru ' . $uniqueNo,
            'no_ktp' => '351' . rand(1000000000000, 9999999999999),
            'no_telepon' => '0812' . rand(10000000, 99999999),
            'nama_usaha' => 'Kios Berkah ' . $uniqueNo,
            'kategori_usaha' => 'kuliner',
            'blok_kios' => 'Blok A',
            'no_kios' => 'TEST-' . $uniqueNo,
            'tanggal_daftar' => $today,
            'status_pedagang' => 'aktif',
            'status_legal' => 'legal',
            'tarif_id' => $this->tarif->id,
            'saldo_deposit' => 0,
        ]);

        $response->assertRedirect(route('pedagang.index'));
        $response->assertSessionHas('success');

        // Pastikan pedagang tersimpan
        $pedagang = Pedagang::where('no_kios', 'TEST-' . $uniqueNo)->first();
        $this->assertNotNull($pedagang);

        // Pastikan TIDAK ADA tagihan pada hari ini (hari pendaftaran)
        $tagihanHariIni = Tagihan::where('pedagang_id', $pedagang->id)
            ->where('tanggal_tagihan', $today)
            ->first();
        $this->assertNull($tagihanHariIni, 'Pedagang baru tidak boleh langsung ditagih pada hari pendaftaran.');

        // Pastikan tagihan otomatis dibuat untuk BESOK HARINYA (jatuh tempo H+1)
        $tagihanBesok = Tagihan::where('pedagang_id', $pedagang->id)
            ->where('tanggal_tagihan', $besok)
            ->first();
        $this->assertNotNull($tagihanBesok, 'Tagihan perdana harus langsung terbit untuk jatuh tempo besok harinya.');
        $this->assertEquals((float) $this->tarif->nominal_harian, (float) $tagihanBesok->nominal_tagihan);
        $this->assertEquals('belum_bayar', $tagihanBesok->status_bayar);
        $this->assertEquals(0, (float) $tagihanBesok->nominal_dibayar);

        // Verifikasi muncul di menu Transaksi Pembayaran saat filter tanggal besok dibuka
        $responseInput = $this->actingAs($this->adminUser)->get(route('tagihan.input', ['tanggal' => $besok]));
        $responseInput->assertStatus(200);
        $responseInput->assertSee('Pedagang Baru ' . $uniqueNo);
        $responseInput->assertSee('TEST-' . $uniqueNo);
    }

    /** @test */
    public function pendaftaran_pedagang_dengan_deposit_otomatis_memotong_deposit_untuk_jatuh_tempo_besok()
    {
        $today = Carbon::today()->format('Y-m-d');
        $besok = Carbon::today()->addDay()->format('Y-m-d');
        $uniqueNo = rand(1000, 9999);
        $nominalTarif = (float) $this->tarif->nominal_harian;

        $response = $this->actingAs($this->adminUser)->post(route('pedagang.store'), [
            'nama_pedagang' => 'Pedagang Deposit ' . $uniqueNo,
            'no_ktp' => '352' . rand(1000000000000, 9999999999999),
            'no_telepon' => '0813' . rand(10000000, 99999999),
            'nama_usaha' => 'Kios Deposit ' . $uniqueNo,
            'kategori_usaha' => 'kuliner',
            'blok_kios' => 'Blok B',
            'no_kios' => 'DEP-' . $uniqueNo,
            'tanggal_daftar' => $today,
            'status_pedagang' => 'aktif',
            'status_legal' => 'legal',
            'tarif_id' => $this->tarif->id,
            'saldo_deposit' => $nominalTarif * 2, // Deposit 2 hari
        ]);

        $response->assertRedirect(route('pedagang.index'));

        $pedagang = Pedagang::where('no_kios', 'DEP-' . $uniqueNo)->first();
        $this->assertNotNull($pedagang);

        // Tagihan besok harinya otomatis lunas terpotong deposit
        $tagihanBesok = Tagihan::where('pedagang_id', $pedagang->id)
            ->where('tanggal_tagihan', $besok)
            ->first();
        $this->assertNotNull($tagihanBesok);
        $this->assertEquals('lunas', $tagihanBesok->status_bayar);
        $this->assertEquals($nominalTarif, (float) $tagihanBesok->nominal_dibayar);
        $this->assertNotNull($tagihanBesok->no_bukti);

        // Sisa deposit berkurang
        $pedagang->refresh();
        $this->assertEquals($nominalTarif, (float) $pedagang->saldo_deposit);
    }
}
