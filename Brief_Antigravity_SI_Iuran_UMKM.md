# BRIEF PROYEK UNTUK ANTIGRAVITY
## Fitur AI Segmentasi Pola Bayar Pedagang — SI Iuran UMKM

---

## 1. KONTEKS PROYEK (WAJIB DIBACA DULU)

Saya sudah punya aplikasi web berjalan bernama **SI Iuran UMKM** (nama internal: "Nusantara SME Core" / "Amanah Ledger"), dibangun dengan **PHP native + PDO + HTML/JS vanilla** (bukan Laravel). Aplikasi ini mencatat tagihan sewa harian pedagang pasar/UMKM.

**Tugas ini BUKAN membuat aplikasi baru dari nol.** Tugas ini adalah:
1. **Migrasi tampilan/backend** dari PHP native ke **Laravel**.
2. **Menambahkan 1 fitur AI**: segmentasi/clustering pedagang berdasarkan pola bayar mereka, menggunakan algoritma **K-Means**.

Jangan membuat ulang konsep aplikasi, jangan mengganti alur bisnis yang sudah ada. Ikuti struktur data dan alur yang sudah didefinisikan di bawah ini.

---

## 2. BATASAN KERAS (JANGAN DILANGGAR)

- ❌ **DILARANG** menggunakan **Streamlit** untuk tampilan apa pun.
- ❌ **DILARANG** menggunakan **Flask** sebagai backend/API.
- ✅ **WAJIB** menggunakan **Laravel** (versi terbaru stabil) sebagai satu-satunya web framework untuk seluruh tampilan dan backend aplikasi (CRUD pedagang, tagihan, dashboard, DAN halaman hasil segmentasi AI).
- ✅ Proses **training model K-Means** boleh dan sebaiknya tetap pakai **Python (scikit-learn, pandas)** — tapi hanya sebagai **script terpisah/offline**, bukan sebagai web server (jadi bukan Flask, bukan Streamlit). Hasilnya (label cluster per pedagang) disimpan ke database, lalu **Laravel yang menampilkannya**.
- Database: **MySQL/MariaDB**, nama database `db_tagihan_pasar` (sudah ada, lihat skema di bawah).

---

## 3. SKEMA DATABASE YANG SUDAH ADA (JANGAN DIUBAH STRUKTURNYA)

Tabel-tabel ini sudah berjalan di sistem lama, migrasikan apa adanya ke Laravel migration:

- `users` (petugas/admin/superadmin)
- `tarif` (tarif sewa per kategori usaha)
- `pedagang` (data pedagang/UMKM: nama, no KTP, no kios, kategori usaha, status legal, dll)
- `tagihan` (tagihan harian per pedagang: nominal, status bayar, metode bayar)
- `log_pengiriman` (log notifikasi WA/email)
- `audit_log` (log aktivitas user)

Detail lengkap kolom & relasi ada di file `ERD_SI_Iuran_UMKM.mermaid` yang saya lampirkan/sertakan bersama brief ini.

---

## 4. TABEL BARU YANG PERLU DITAMBAHKAN (untuk fitur AI)

Tambahkan 3 tabel baru ini via Laravel migration:

### `fitur_pola_bayar`
Menyimpan hasil ekstraksi fitur per pedagang dari riwayat tabel `tagihan`, per periode analisis (misal per bulan). Kolom: `pedagang_id` (FK), `periode_awal`, `periode_akhir`, `total_hari_tagihan`, `jumlah_lunas`, `jumlah_terlambat`, `jumlah_bayar_sebagian`, `rata_rata_keterlambatan_hari`, `rasio_ketepatan_bayar`, `total_nominal_tagihan`, `total_nominal_dibayar`, `total_tunggakan`, `dihitung_pada`.

### `ml_model`
Menyimpan metadata setiap model K-Means yang sudah dilatih. Kolom: `nama_model`, `algoritma` (default "K-Means"), `jumlah_cluster`, `parameter` (json), `silhouette_score`, `path_model_pkl`, `path_scaler_pkl`, `periode_data_awal`, `periode_data_akhir`, `is_active`, `trained_at`.

### `segmentasi_pedagang`
Menyimpan hasil akhir clustering: setiap pedagang mendapat label cluster. Kolom: `pedagang_id` (FK), `ml_model_id` (FK), `cluster_index`, `label_segmen` (string, misal "Pembayar Rajin" / "Cukup Rajin" / "Berisiko"), `fitur_snapshot` (json), `tanggal_analisis`.

---

## 5. ALUR KERJA FITUR AI (IKUTI URUTAN INI)

Fitur ini mengikuti pola dari buku referensi *"Implementasi Algoritma Machine Learning Berbasis Web"* bab K-Means Clustering, tapi Laravel menggantikan peran Streamlit sebagai lapisan tampilan.

1. **Ekstraksi fitur** — ambil data dari tabel `tagihan`, hitung per pedagang: rasio ketepatan bayar, rata-rata keterlambatan (hari), total tunggakan, frekuensi bayar tepat waktu vs telat. Simpan ke `fitur_pola_bayar`.
2. **Training model (Python script terpisah)** — baca `fitur_pola_bayar` (via export CSV atau koneksi langsung ke DB pakai `mysql-connector`/`SQLAlchemy`), lakukan:
   - Normalisasi/scaling fitur (StandardScaler)
   - Cari jumlah cluster optimal (elbow method, biasanya 3 cluster: Rajin / Cukup Rajin / Berisiko)
   - Latih `KMeans` dari scikit-learn
   - Evaluasi dengan silhouette score
   - Simpan model (`pickle`) dan scaler
   - Simpan hasil label cluster per pedagang kembali ke database (tabel `ml_model` dan `segmentasi_pedagang`)
3. **Laravel menampilkan hasil**:
   - Halaman **Dashboard Segmentasi**: grafik scatter/distribusi cluster, jumlah pedagang per segmen, tabel pedagang dengan badge warna sesuai segmen (hijau=Rajin, kuning=Cukup Rajin, merah=Berisiko)
   - Filter by blok kios, kategori usaha, segmen
   - Detail per pedagang: histori bayar + segmen saat ini
   - (Opsional) Tombol "Jalankan Ulang Analisis" yang memanggil script Python via `Process` Laravel (`Symfony\Component\Process\Process` atau `shell_exec`), lalu Laravel membaca ulang hasilnya dari DB.

---

## 6. YANG PERLU DIBUATKAN ANTIGRAVITY

1. Setup project **Laravel** baru, koneksi ke database `db_tagihan_pasar`.
2. **Migration** untuk 6 tabel lama + 3 tabel baru sesuai ERD.
3. **Model Eloquent** untuk semua tabel beserta relasinya.
4. **Controller + View (Blade)** untuk: CRUD Pedagang, Input Tagihan Harian, Dashboard Utama, **Dashboard Segmentasi AI** (fitur baru).
5. **Script Python terpisah** (folder `ml/` di luar Laravel, atau di `storage/app/ml/`) untuk training K-Means sesuai alur di poin 5.
6. Desain visual mengikuti gaya di file `DESIGN.md` yang saya sertakan (warna hijau tua/emas, gaya korporat-minimalis, cocok untuk konteks pesantren/UMKM).

---

## 7. FILE PENDUKUNG YANG SAYA LAMPIRKAN

- `ERD_SI_Iuran_UMKM.mermaid` — diagram relasi lengkap semua tabel
- `schema.sql` — skema database PHP native yang sudah berjalan (referensi kolom persis)
- `DESIGN.md` — panduan desain visual (warna, tipografi, komponen)
- `spesifikasi_sistem_tagihan_pasar.md` — spesifikasi fungsional modul-modul yang sudah ada

Mohon baca semua file di atas sebelum mulai coding, agar hasilnya konsisten dengan sistem yang sudah ada.
