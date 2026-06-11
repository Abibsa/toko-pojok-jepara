# 🏪 Toko Pojok Jepara — E-Commerce Grosir Berbasis Laravel

> **Proyek UAS E-Commerce & Rekayasa Perangkat Lunak**  
> Program Studi Teknik Informatika — UNISNU Jepara | Tahun Akademik 2025/2026

Aplikasi E-Commerce berbasis web untuk Toko Grosir Pojok Jepara, dilengkapi fitur **Visibilitas Stok Real-Time**, sistem pemesanan **BOPIS (Buy Online Pick Up In Store)**, dan implementasi Algoritma **K-Means Clustering** untuk pengelompokan produk berdasarkan histori penjualan.

---

## 👥 Tim Pengembang

| Nama | NIM |
|------|-----|
| Muhammad Ashab Ibnu Abdul Aziz | 231240001399 |
| Muhammad Awalul Falah | 231240001414 |
| Raya Saputra | 231240001411 |

---

## 🔗 Repositori GitHub

> **Link GitHub:** [https://github.com/Abibsa/toko-pojok-jepara](https://github.com/Abibsa/toko-pojok-jepara)

---

## ✨ Fitur Utama

- 📦 **Visibilitas Stok Real-Time** — Stok produk diperbarui secara instan saat transaksi terjadi
- 🛒 **Keranjang Belanja & Checkout** — Alur pemesanan yang mudah dengan metode BOPIS
- ⏱️ **Countdown Timer** — Estimasi waktu persiapan pesanan ditampilkan kepada pelanggan
- 🔐 **Autentikasi Laravel Breeze** — Registrasi, Login, dan Logout yang aman
- 🤖 **K-Means Clustering** — Pengelompokan produk menjadi 3 cluster (Tinggi/Sedang/Rendah) berdasarkan frekuensi penjualan
- 📊 **Dasbor Administrator** — Kelola produk, pesanan, stok, dan laporan penjualan

---

## 💻 Prasyarat (Prerequisites)

Pastikan perangkat lunak berikut sudah terinstal:
- [PHP](https://www.php.net/) >= 8.1
- [Composer](https://getcomposer.org/)
- [Node.js & npm](https://nodejs.org/) >= 18

---

## 🚀 Cara Menjalankan Proyek

```bash
# 1. Clone repositori
git clone https://github.com/Abibsa/toko-pojok-jepara.git
cd toko-pojok-jepara

# 2. Instal dependensi PHP
composer install

# 3. Instal dependensi Node.js
npm install

# 4. Salin file environment
copy .env.example .env
```

> Proyek ini menggunakan **SQLite** secara default. File database sudah tersedia di `database/database.sqlite`.

```bash
# 5. Generate Application Key
php artisan key:generate

# 6. Jalankan migrasi database beserta data awal (seeder)
php artisan migrate --seed

> **Informasi Akun Default:**
> - **Admin:** `admin@tokopojok.com` / Password: `password`
> - **Customer:** `customer1@example.com` / Password: `password`

# 7. Jalankan server (buka 2 terminal berbeda)
# Terminal 1 — Backend:
php artisan serve

# Terminal 2 — Frontend (Vite):
npm run dev
```

Buka browser dan akses: **[http://localhost:8000](http://localhost:8000)**

---

## 📁 Struktur Folder Utama

```
toko-pojok-jepara/
├── app/
│   ├── Http/Controllers/       # Pengendali (Admin & Pelanggan)
│   ├── Models/                 # Model Eloquent (User, Product, Order, Stock, dll)
│   └── Services/               # Service Layer (KMeansService, StockService, dll)
├── database/migrations/        # Skema tabel database
├── resources/views/            # Blade Template (tampilan)
├── routes/web.php              # Definisi semua rute URL
├── laporan_lengkap/            # Laporan UAS (E-Commerce & RPL)
└── public/                     # Aset statis & entry point
```

---

## 🛠 Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Backend Framework | Laravel (PHP) |
| Frontend | Blade Templating, Tailwind CSS v4, Vite |
| Database | SQLite |
| Algoritma | K-Means Clustering |
| Autentikasi | Laravel Breeze |

---

## 📄 Laporan

| Dokumen | Deskripsi |
|---------|-----------|
| [`laporan_lengkap/UAS-E-COMMERCE-PROJECT AKHIR.docx`](laporan_lengkap/UAS-E-COMMERCE-PROJECT%20AKHIR.docx) | Laporan UAS Mata Kuliah E-Commerce |
| [`laporan_lengkap/UAS-rpl-PROJECT_AKHIR.docx`](laporan_lengkap/UAS-rpl-PROJECT_AKHIR.docx) | Laporan UAS Mata Kuliah RPL (termasuk UML) |

---

*© 2026 — Proyek UAS Teknik Informatika, UNISNU Jepara*
