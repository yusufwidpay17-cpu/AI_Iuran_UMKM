# Spesifikasi Sistem Pencatatan Tagihan Sewa Pasar

## Ringkasan Proyek

Bangun sistem web (PWA) untuk mencatat tagihan sewa harian pedagang pasar. Sistem dipakai oleh petugas pemungut di lapangan (HP), pengelola pasar (PC/tablet), dan pimpinan (terima laporan). Skala: 50–200 pedagang aktif.

---

## Role Pengguna

| Role | Akses |
|---|---|
| `superadmin` | Semua fitur + kelola petugas + konfigurasi sistem |
| `admin` | Kelola pedagang, lihat semua tagihan, buat laporan |
| `petugas` | Input tagihan harian, lihat daftar tagihan hari ini |

---

## Modul 1: Manajemen Pedagang

### Data yang disimpan per pedagang:
- `id` — UUID primary key
- `nama_pedagang` — string, wajib
- `no_ktp` — string, unik
- `no_telepon` — string
- `nama_usaha` — string
- `kategori_usaha` — enum: `kuliner`, `pakaian`, `elektronik`, `sayuran`, `buah`, `sembako`, `lainnya`
- `no_kios` — string, unik (mis: A-01, B-12)
- `blok_kios` — string
- `tanggal_daftar` — date
- `status_pedagang` — enum: `aktif`, `nonaktif`, `sementara_tutup`
- `status_legal` — enum: `legal`, `ilegal`, `proses_izin`
- `foto_ktp` — URL/path file
- `foto_izin_usaha` — URL/path file
- `catatan` — text bebas

### Fitur halaman daftar pedagang:
- Tabel dengan kolom: No Kios, Nama, Usaha, Status, Legal, Aksi
- Filter: status_pedagang, status_legal, blok_kios, kategori_usaha
- Search: nama pedagang, no kios, nama usaha
- Badge warna status: Aktif = hijau, Nonaktif = abu, Tutup sementara = kuning
- Badge legal: Legal = biru, Ilegal = merah, Proses = oranye
- Tombol: Tambah Pedagang, Edit, Detail, Nonaktifkan

### Fitur form tambah/edit pedagang:
- Upload foto KTP dan foto izin usaha
- Auto-generate nomor kios berdasarkan blok yang dipilih
- Validasi: no_kios unik, no_ktp unik

---

## Modul 2: Tagihan Harian

### Data yang disimpan per tagihan:
- `id` — UUID
- `pedagang_id` — FK ke tabel pedagang
- `tarif_id` — FK ke tabel tarif
- `petugas_id` — FK ke tabel petugas (yang input)
- `tanggal_tagihan` — date (default: hari ini)
- `nominal_tagihan` — decimal (ambil dari tarif)
- `nominal_dibayar` — decimal
- `status_bayar` — enum: `lunas`, `belum_bayar`, `bayar_sebagian`
- `waktu_bayar` — timestamp
- `metode_bayar` — enum: `tunai`, `transfer`, `qris`
- `no_bukti` — string (no struk/referensi transfer)
- `catatan` — text

### Alur input tagihan oleh petugas:
1. Petugas buka halaman "Input Tagihan Hari Ini"
2. Sistem tampilkan daftar SEMUA pedagang aktif untuk tanggal hari ini
3. Setiap baris: nama, no kios, nominal tagihan, tombol "Catat Bayar" / badge "Sudah Bayar"
4. Tap "Catat Bayar" → modal kecil muncul: isi nominal dibayar, metode bayar, no bukti (opsional)
5. Submit → status berubah jadi `lunas`, baris berubah warna hijau
6. Pedagang yang tidak ketemu/tutup: petugas bisa tandai "Tidak Ada" (status tetap `belum_bayar` dengan catatan)

### Aturan bisnis tagihan:
- Tagihan dibuat otomatis setiap hari untuk semua pedagang berstatus `aktif`
- Pedagang `nonaktif` tidak dapat tagihan
- Jika pedagang bayar lebih dari nominal: selisih dicatat sebagai uang muka tagihan berikutnya
- Jika bayar kurang: status `bayar_sebagian`, sisa jadi tunggakan
- Tunggakan otomatis carry-over ke hari berikutnya

### Fitur monitoring dashboard tagihan:
- Ringkasan hari ini: total tagihan, sudah bayar, belum bayar, nominal masuk, nominal tunggak
- Progress bar: persentase lunas vs belum bayar
- Daftar yang BELUM bayar (diurutkan: blok, no kios)
- Highlight merah jika tunggak > 3 hari
- Filter by: tanggal, blok, status bayar, petugas

---

## Modul 3: Tarif

- `id`, `nama_tarif`, `kategori`, `nominal_harian`, `berlaku_mulai`, `aktif`
- Admin dapat buat tarif berbeda per kategori usaha
- Tarif bisa diubah; tarif lama tetap tersimpan di histori tagihan
- Pedagang dihubungkan ke satu tarif aktif

---

## Modul 4: Laporan Harian Otomatis

### Konten laporan harian:
```
LAPORAN HARIAN PASAR — [NAMA PASAR]
Tanggal: [DD/MM/YYYY]
Dibuat: [HH:MM] oleh [nama petugas]

RINGKASAN:
- Total pedagang aktif  : XXX
- Sudah bayar           : XXX (XX%)
- Belum bayar           : XXX (XX%)
- Bayar sebagian        : XXX

KEUANGAN:
- Total tagihan hari ini : Rp XXX.XXX
- Total masuk            : Rp XXX.XXX
- Total tunggak          : Rp XXX.XXX

DAFTAR BELUM BAYAR:
[No Kios] [Nama] [Nominal] [Tunggak ke-N hari]
...

DAFTAR PEDAGANG ILEGAL (jika ada):
[No Kios] [Nama] [Status]
```

### Pengiriman laporan:
- Otomatis dikirim pada jam yang ditentukan admin (mis: 17.00 setiap hari)
- Channel: WhatsApp via Fonnte/Wablas API, atau Email via SMTP
- Admin bisa tambah multiple nomor/email penerima
- Tombol "Kirim Sekarang" untuk kirim manual
- Log pengiriman tersimpan: kapan, ke siapa, status (terkirim/gagal)

### Export:
- Export PDF rekap harian — bisa diunduh atau dikirim attachment
- Export Excel untuk rekap mingguan/bulanan
- Filter export: range tanggal, blok, status pedagang

---

## Modul 5: Dashboard Utama

Tampilan card ringkasan:
- Total pedagang aktif / nonaktif / ilegal
- Tagihan hari ini: lunas vs belum bayar (donut chart)
- Total pendapatan bulan ini vs bulan lalu
- Grafik pendapatan 7 hari terakhir (bar chart)
- Daftar 5 pedagang dengan tunggakan terlama

---

## Spesifikasi Teknis

### Stack yang direkomendasikan:
- **Frontend**: Next.js 14 (App Router), Tailwind CSS, shadcn/ui
- **Backend**: Next.js API Routes atau Express.js terpisah
- **Database**: PostgreSQL dengan Prisma ORM
- **Auth**: NextAuth.js atau JWT
- **Storage foto**: Cloudinary (free tier cukup) atau MinIO self-hosted
- **Notifikasi WA**: Fonnte API (fonnte.com) — murah, mudah
- **Hosting**: VPS Ubuntu + Nginx + PM2, atau Railway/Render

### Keamanan:
- Semua endpoint terproteksi JWT
- Log semua aksi: siapa input apa kapan
- Backup database harian otomatis
- HTTPS wajib

### PWA (Mobile-first):
- Bisa diakses via browser HP tanpa install
- Tampilan responsive, tombol besar untuk layar sentuh
- Offline-capable untuk lihat data (sync saat online)

---

## Alur User Journey Utama

### Petugas pagi hari:
```
Login → Buka "Tagihan Hari Ini" → Keliling kios → 
Tap "Catat Bayar" per pedagang → Selesai
```

### Admin siang hari:
```
Login → Dashboard → Lihat progress → 
Cek pedagang belum bayar → Hubungi via telpon
```

### Laporan sore hari:
```
Sistem otomatis rekap → 17.00 kirim ke WhatsApp pimpinan → 
Pimpinan terima laporan tanpa perlu login
```

---

## Prompt untuk Antigravity / AI Agent Builder

Gunakan spesifikasi ini sebagai konteks lengkap. Mulai dari:
1. Setup database schema (lihat ERD)
2. Buat CRUD pedagang + upload foto
3. Buat alur input tagihan harian (mobile-first)
4. Buat dashboard monitoring
5. Integrasi laporan otomatis + notifikasi WA

