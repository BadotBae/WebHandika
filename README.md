# 🛒 Handika Helmet Store (Web Toko Handika)

Aplikasi *E-Commerce* modern berbasis web untuk katalogisasi, manajemen stok multi-varian, dan transaksi penjualan helm berstandar SNI/DOT. Dilengkapi dengan panel kontrol administrator, pelacakan pesanan, verifikasi pembayaran, serta rekapitulasi laporan penjualan otomatis.

---

## 📌 1. Deskripsi Aplikasi

**Handika Helmet Store** dikembangkan menggunakan arsitektur **Model-View-Controller (MVC)** dengan framework **CodeIgniter 4** dan basis data **MySQL 8.0**. Sistem ini dirancang untuk mengatasi permasalahan pada toko helm fisik konvensional, seperti keterbatasan jangkauan pemasaran, kerumitan pencatatan stok multi-varian (kombinasi warna dan ukuran M/L/XL), serta rekapitulasi keuangan manual yang rentan kesalahan manusia (*human error*).

Aplikasi ini mengintegrasikan alur belanja konsumen secara menyeluruh (dari pemilihan varian helm, perhitungan ongkos kirim ekspedisi, simulasi berbagai kanal pembayaran modern) hingga pengawasan penuh oleh pemilik toko melalui *Executive Dashboard* dan laporan penjualan terfilter.

---

## ✨ 2. Fitur Utama

Sistem menerapkan **Role-Based Access Control (RBAC)** dengan 2 kelompok peran:

### 🛍️ A. Fitur Pelanggan (Customer)
* **Autentikasi & Registrasi**: Pendaftaran akun aman dengan validasi email `@gmail.com`, enkripsi sandi Bcrypt, serta sanitasi data masukan.
* **Katalog Produk Dinamis**:
  * Filter kategori helm (*Half Face*, *Full Face*, *Retro*).
  * Pencarian instan berdasarkan nama produk.
  * Filter rentang harga dan pengurutan dinamis (*Terpopuler*, *Harga Terendah*, *Harga Tertinggi*, *Rating Tertinggi*).
* **Sistem Wishlist (Favorit)**: Menyimpan produk impian dengan tombol *heart* interaktif; otomatis terhapus saat produk dimasukkan ke keranjang.
* **Smart Cart (Multi-Varian)**: Keranjang belanja yang mengelompokkan item berdasarkan `ID Produk | Warna | Ukuran` dengan validasi stok gudang secara *real-time*.
* **Checkout & Ekspedisi**:
  * Pilihan kurir lengkap (JNE REG, JNE YES, SiCepat BEST, AnterAja, GoSend Instant, GrabExpress) dengan kalkulasi ongkir otomatis.
  * Pilihan metode bayar: *Cash on Delivery* (COD), Virtual Account Bank (BCA, BRI, Mandiri), dan E-Wallet / QRIS dinamis.
* **Riwayat Pesanan & Auto-Restock**:
  * Monitoring status transaksi (*unpaid*, *diproses*, *dikirim*, *selesai*).
  * Konfirmasi penerimaan barang.
  * Fitur pembatalan pesanan sebelum barang dikirim dengan **pengembalian stok varian secara otomatis** (*atomic rollback*).

### 🛠️ B. Fitur Administrator
* **Executive Dashboard**:
  * Ringkasan KPI: Omset lunas hari ini, omset bulan ini, dan total transaksi harian.
  * Notifikasi Dini Stok Kritis (*Low Stock Alert* $\le 5\text{ unit}$).
  * Grafik visual tren penjualan 7 hari terakhir.
  * Daftar 5 produk terlaris (*Top Selling*) dan 5 transaksi terkini.
* **Manajemen Produk & Multi-Varian (CRUD)**: Kelola data helm, upload foto produk, serta penambahan variasi warna, ukuran, dan stok dinamis tak terbatas.
* **Verifikasi Pembayaran Masuk**: Antrean persetujuan (*Approve*) atau penolakan (*Reject*) transaksi masuk.
* **Kelola Pengiriman & Logistik**: Pembaruan status pengiriman berkala (*belum dikirim* $\rightarrow$ *diproses* $\rightarrow$ *dikirim* $\rightarrow$ *selesai*).
* **Manajemen Pengguna (User Management)**: Kelola pengguna terdaftar, perubahan hak akses peran, serta proteksi penghapusan akun mandiri (*self-delete protection*).

### 📊 C. Fitur Laporan Penjualan (Reporting)
* Penyaringan data transaksi berdasarkan **rentang tanggal** (*Start Date* hingga *End Date*).
* Penyaringan berdasarkan **metode pembayaran**.
* Kalkulasi otomatis total omset pendapatan dan volume transaksi sukses.
* Tampilan ramah cetak (*Print-ready view*) untuk arsip pertanggungjawaban fisik.

---

## 💻 3. Teknologi yang Digunakan (Tech Stack)

| Komponen Layer | Teknologi / Library | Keterangan |
| :--- | :--- | :--- |
| **Backend Language** | PHP 8.2 (Object-Oriented) | Logika komputasi server |
| **Backend Framework** | CodeIgniter 4 (CI4) | Framework MVC, routing, dan ORM Query Builder |
| **Database** | MySQL 8.0 (InnoDB) | Relational Database, ACID Transaction, Foreign Key CASCADE |
| **Frontend Framework**| Bootstrap 5.3 & CSS3 | Desain antarmuka responsif (Mobile & Desktop) |
| **Interactivity** | Vanilla JavaScript (ES6) & SweetAlert2 | Modal dialog varian dan notifikasi modern |
| **Icons & Typography** | Font Awesome 6 & Google Fonts (Outfit) | Aset visual dan tipografi |
| **Web Server Local** | Apache 2.4 (Laragon Suite) | Server lokal dengan modul *rewrite* aktif |
| **Tunneling / Hosting**| Cloudflare Tunnel (`cloudflared`) | Terowongan publik dengan enkripsi HTTPS / TLS 1.3 |

---

## 🌐 4. URL Aplikasi

* **URL Publik (Online / Siap Diakses Dosen)**:  
  👉 **[https://saturn-charm-emerald-ali.trycloudflare.com](https://saturn-charm-emerald-ali.trycloudflare.com)**  
  *(Akses menggunakan HTTPS resmi Cloudflare, aktif dari laptop pengembang tanpa port forwarding)*.
* **URL Lokal (Development)**:  
  👉 `http://webhandika.test` atau `http://localhost:8080`

### 🔑 Kredensial Akun Pengujian (Demo):
| Peran (Role) | Email | Kata Sandi | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@gmail.com` | `admin123` | Akses penuh dashboard, kelola katalog, verifikasi, dan laporan |
| **Customer** | `user@gmail.com` | `user123` | Akses belanja, wishlist, keranjang, checkout, dan riwayat |

---

## 🚀 5. Cara Instalasi & Menjalankan Secara Lokal

### Prasyarat Sistem:
1. **PHP**: Versi 8.2 atau lebih tinggi (ekstensi `intl`, `mbstring`, `mysqlnd`, `json`, `curl` aktif).
2. **Database**: MySQL 8.0 / MariaDB.
3. **Web Server**: Apache 2.4 (direkomendasikan menggunakan **Laragon** di Windows).
4. **Composer**: Versi 2.x.

### Langkah-langkah Instalasi:

1. **Clone atau Letakkan Repositori**:
   Pindahkan repositori ke dalam direktori root server lokal Anda (misal di Laragon: `d:\laragon\www\WebHandika`).

2. **Setup Database**:
   * Buka phpMyAdmin, HeidiSQL, atau terminal MySQL.
   * Buat database baru bernama `toko_handika`:
     ```sql
     CREATE DATABASE toko_handika CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   * Import file database yang telah disediakan di root proyek:
     ```bash
     mysql -u root -p toko_handika < toko_handika.sql
     ```

3. **Konfigurasi Environment Variable**:
   Salin file `env` menjadi `.env` (atau periksa file `.env` yang sudah ada):
   ```bash
   cp env .env
   ```
   Sesuaikan parameter database pada `.env` (lihat panduan Bagian 6 di bawah).

4. **Jalankan Aplikasi**:
   * **Menggunakan Laragon (Direkomendasikan)**:
     Cukup klik **Start All** pada Laragon, lalu buka peramban ke alamat:  
     `http://webhandika.test`
   * **Menggunakan CLI CodeIgniter Spark**:
     ```bash
     php spark serve --port 8080
     ```
     Buka peramban ke: `http://localhost:8080`

5. **Menjalankan Terowongan Hosting Online (Cloudflare Tunnel)**:
   Cukup klik dua kali file:
   📁 `START_HOSTING.bat`  
   *(Aplikasi akan langsung memiliki URL publik HTTPS yang dapat diakses dari mana saja)*.

---

## ⚙️ 6. Konfigurasi Environment Variable (`.env`)

Berikut adalah template konfigurasi `.env` aman (tanpa memuat kredensial rahasia produksi):

```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# APP SETTINGS
# (Biarkan app.baseURL kosong/terkomentari agar sistem otomatis
# mendeteksi domain lokal maupun domain Cloudflare Tunnel HTTPS)
#--------------------------------------------------------------------
# app.baseURL = 'http://localhost:8080'
app.forceGlobalSecureRequests = false
app.sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler'
app.sessionCookieName = 'ci_session'
app.sessionExpiration = 7200

#--------------------------------------------------------------------
# DATABASE CONFIGURATION
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = toko_handika
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port     = 3306
database.default.charset  = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci
```

---

## 🤖 7. Ringkasan Penggunaan AI Agent

Dalam proses pengembangan dan penyempurnaan proyek ini, **AI Agent (Antigravity)** digunakan sebagai mitra kolaborasi teknis (*pair programmer*) pada sejumlah tahapan krusial:

1. **Rekayasa Arsitektur Database & DDL**:
   * Menganalisis kebutuhan data aplikasi dari kode Controller/Model, lalu merancang dan mengeksekusi skema SQL relasional [toko_handika.sql](toko_handika.sql) lengkap dengan 6 tabel berelasi, integritas *Foreign Key CASCADE*, dan *initial seeding* data helm.
2. **Debugging Lingkungan & Kompatibilitas Runtime**:
   * Mengidentifikasi konflik versi PHP (PHP 8.1 CLI vs PHP 8.2 Laragon) serta mengonfigurasi `auto.WebHandika.test.conf` Apache agar mendukung *ServerAlias* domain publik secara dinamis.
3. **Konfigurasi Dynamic Base URL**:
   * Melakukan refactoring pada `app/Config/App.php` agar secara cerdas mendeteksi *header* `HTTP_HOST` dan `HTTP_X_FORWARDED_PROTO`, sehingga tautan aset (CSS, gambar helm, rute login) tidak rusak (*broken link*) saat diakses melalui tunnel HTTPS publik.
4. **Automated Tunnel Deployment**:
   * Mengunduh dan mengonfigurasi daemon **Cloudflare Tunnel (`cloudflared`)** secara otomatis di lingkungan Laragon lokal serta membuat skrip starter `START_HOSTING.bat` untuk kemudahan pengujian jarak jauh oleh dosen.
5. **Penyusunan Dokumentasi & Panduan Akademis**:
   * Menghasilkan dokumentasi lengkap penjelasan sistem, analisis keamanan, serta menghasilkan berkas laporan formal [Penjelasan_Aplikasi_Toko_Handika.docx](Penjelasan_Aplikasi_Toko_Handika.docx) secara terotomasi.

---

## 🛡️ 8. Hasil Self-Testing Keamanan (Security Verification)

Pengujian keamanan mandiri (*self-testing*) telah dilakukan pada alur-alur krusial sistem dengan hasil sebagai berikut:

| Skenario Pengujian | Target / Vektor Uji | Metodologi Uji | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :---: |
| **SQL Injection (SQLi)** | Form Login, Filter Kategori, & Query Pencarian | Memasukkan payload `' OR '1'='1` dan `'; DROP TABLE users; --` pada kolom input | **AMAN**: Input diperlakukan murni sebagai literal string oleh Query Builder & Parameter Binding (`?`). Tidak ada query yang terinjeksi. | ✅ **PASSED** |
| **Cross-Site Scripting (XSS)** | Input Nama Produk, Deskripsi, dan Alamat Pelanggan | Memasukkan payload `<script>alert('XSS-Test')</script>` | **AMAN**: Seluruh output dinamis di-escape dengan fungsi `esc()`, karakter dikonversi menjadi entitas teks `&lt;script&gt;` tanpa eksekusi skrip di browser. | ✅ **PASSED** |
| **Authentication & RBAC Bypass** | Endpoint `/admin/*` (Dashboard, Orders, Verification, Laporan) | Mengakses URL panel admin langsung tanpa sesi login atau dengan akun role Customer (`role_id = 2`) | **AMAN**: Fungsi `cekAdmin()` mencegat permintaan dan langsung mengalihkan (*redirect*) ke halaman login. | ✅ **PASSED** |
| **Password Storage Security** | Tabel `users` (Kolom `password`) | Memeriksa format penyimpanan kata sandi di database | **AMAN**: Kata sandi tersimpan dalam format hash satu arah **Bcrypt** ber-salt (`$2y$10$...`) menggunakan `PASSWORD_DEFAULT`. | ✅ **PASSED** |
| **Data Transaction Integrity (ACID)** | Alur Checkout Keranjang & Pembatalan Pesanan | Menguji pembatalan order di tengah jalan serta pembatalan pesanan oleh user | **AMAN**: Didukung blok transaksi database (`transBegin`, `transCommit`, `transRollback`). Stok helm varian kembali sinkron tanpa inkonsistensi data. | ✅ **PASSED** |
| **Network Transport Security** | Komunikasi Antara Pengguna dan Server | Akses melalui tautan terowongan Cloudflare | **AMAN**: Enkripsi TLS 1.3 / HTTPS aktif penuh, mencegah potensi serangan *Man-in-the-Middle* (MitM) pada Wi-Fi publik. | ✅ **PASSED** |

---

## 👨‍💻 Pengembang
* **Nama Proyek**: Handika Helmet Store (Web Toko Handika)
* **Tahun Pengembangan**: 2026
* **Status**: Siap Uji & Presentasi Sidang Dosen
