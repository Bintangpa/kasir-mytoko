# MyTokoGue — Point of Sale (POS) & Retail Store Management

Aplikasi kasir (Point of Sale) dan manajemen toko ritel modern berbasis **Laravel 11**, dirancang untuk kecepatan transaksi kasir, pencetakan struk thermal (standar 80mm & 58mm), manajemen inventaris produk, kontrol hak akses berbasis peran (*Role-Based Access Control*), serta pelaporan keuangan *real-time*.

---

##  Struktur Folder & File Project

Berikut adalah struktur direktori dan file lengkap pada project **MyTokoGue**:

```text
mytokogue/
├── app/                                              # Kode logika inti aplikasi (MVC)
│   ├── Http/
│   │   ├── Controllers/                              # Pengendali alur request & response HTTP
│   │   │   ├── AuthController.php                    # Autentikasi pengguna (login, logout, session)
│   │   │   ├── Controller.php                        # Base Controller bawaan Laravel
│   │   │   ├── MidtransWebhookController.php         # Endpoint webhook notifikasi pembayaran Midtrans (SHA-512)
│   │   │   ├── TransactionReceiptController.php      # Render cetak struk thermal & pengaturan ukuran nota (80mm / 58mm)
│   │   │   ├── Admin/                                # Controller khusus peran Administrator
│   │   │   │   ├── AdminDashboardController.php      # Dashboard metrik penjualan & statistik performa toko
│   │   │   │   ├── CategoryController.php            # CRUD kategori barang toko
│   │   │   │   ├── ProductController.php             # CRUD produk, harga, barcode, dan manajemen stok
│   │   │   │   ├── ReportController.php              # Laporan penjualan dan filter analitik
│   │   │   │   └── UserController.php                # Manajemen akun kasir dan admin toko
│   │   │   ├── Kasir/                                # Controller khusus peran Kasir
│   │   │   │   ├── KasirDashboardController.php      # Dashboard ringkasan shift kasir yang sedang bertugas
│   │   │   │   ├── PosController.php                 # Mesin Kasir / Point of Sale & pemrosesan checkout
│   │   │   │   └── TransactionHistoryController.php  # Riwayat transaksi penjualan kasir
│   │   │   └── User/                                 # Controller khusus peran User / Pengguna
│   │   │       └── UserShopController.php            # Katalog produk (hanya lihat), pembayaran mandiri & riwayat
│   │   └── Middleware/
│   │       └── RoleMiddleware.php                    # Proteksi hak akses route berdasarkan peran (admin / kasir / user)
│   ├── Models/                                       # Model Eloquent ORM & relasi database
│   │   ├── Category.php                              # Model data kategori produk
│   │   ├── Product.php                               # Model data produk (kode/SKU, nama, harga, stok)
│   │   ├── Transaction.php                           # Model transaksi penjualan & nomor invoice unik
│   │   ├── TransactionItem.php                       # Model rincian barang per transaksi (qty, harga, subtotal)
│   │   └── User.php                                  # Model data akun pengguna (admin / kasir)
│   └── Providers/
│       └── AppServiceProvider.php                    # Konfigurasi bootstrap service provider Laravel
│
├── bootstrap/                                        # File inisialisasi & caching framework
│   ├── app.php                                       # Konfigurasi routing & registrasi middleware aplikasi
│   └── providers.php                                 # Daftar service provider terdaftar
│
├── config/                                           # Konfigurasi sistem Laravel
│   ├── app.php                                       # Pengaturan zona waktu, locale, nama aplikasi
│   ├── auth.php                                      # Pengaturan autentikasi & guard pengguna
│   ├── database.php                                  # Pengaturan koneksi database (MySQL / SQLite)
│   └── session.php                                   # Pengaturan masa aktif dan driver session
│
├── database/                                         # Manajemen skema dan data database
│   ├── migrations/                                   # File migrasi tabel database
│   │   ├── 0001_01_01_000000_create_users_table.php             # Tabel akun pengguna (users)
│   │   ├── 0001_01_01_000001_create_cache_table.php             # Tabel cache Laravel
│   │   ├── 0001_01_01_000002_create_jobs_table.php              # Tabel background jobs
│   │   ├── 2026_01_01_000010_create_categories_table.php        # Tabel kategori produk
│   │   ├── 2026_01_01_000020_create_products_table.php          # Tabel produk dan stok
│   │   ├── 2026_01_01_000030_create_transactions_table.php      # Tabel transaksi penjualan
│   │   ├── 2026_01_01_000040_create_transaction_items_table.php # Tabel rincian item transaksi
│   │   ├── 2026_01_01_000050_add_payment_gateway_fields_to_transactions_table.php # Field Midtrans VA & QRIS
│   │   ├── 2026_01_01_000060_add_indexes_and_backfill_transactions_table.php      # Index performa query & backfill
│   │   └── 2026_01_01_000070_add_image_blob_fields_to_products_table.php          # Field foto database produk (image_data, image_mime, image_size)
│   └── seeders/
│       └── DatabaseSeeder.php                        # Data awal demo (kategori retail, 10 produk, akun demo)
│
├── public/                                           # Direktori root web publik
│   ├── favicon.ico                                   # Ikon favicon aplikasi
│   ├── index.php                                     # Front controller utama penanganan request
│   ├── robots.txt                                    # Panduan perayapan search engine
│   └── .htaccess                                     # Konfigurasi URL rewriting Apache
│
├── resources/                                        # Aset sumber daya & tampilan antarmuka (UI)
│   ├── css/                                          # Berkas CSS sumber
│   ├── js/                                           # Berkas JavaScript sumber
│   └── views/                                        # Template antarmuka pengguna (Blade Views)
│       ├── layouts/
│       │   └── app.blade.php                         # Master layout (sidebar navigasi, topbar, alert flash)
│       ├── auth/
│       │   └── login.blade.php                       # Halaman login side-form kotak besar dengan ambient glow
│       ├── admin/                                    # Modul Antarmuka Administrator
│       │   ├── dashboard.blade.php                   # Dashboard metrik pendapatan & grafik tren 7 hari
│       │   ├── categories/
│       │   │   └── index.blade.php                   # Manajemen & form modal kategori produk
│       │   ├── products/
│       │   │   ├── index.blade.php                   # Daftar produk, pencarian SKU, & status stok
│       │   │   ├── create.blade.php                  # Form input data produk baru
│       │   │   └── edit.blade.php                    # Form perbarui data & stok produk
│       │   ├── reports/
│       │   │   └── index.blade.php                   # Laporan omzet, filter rentang tanggal & metode pembayaran
│       │   └── users/
│       │       └── index.blade.php                   # Manajemen akun user & kasir toko
│       ├── kasir/                                    # Modul Antarmuka Kasir
│       │   ├── dashboard.blade.php                   # Dashboard ringkasan shift harian kasir
│       │   ├── pos/
│       │   │   └── index.blade.php                   # Mesin kasir interaktif (katalog, keranjang, uang pas, checkout)
│       │   └── transactions/
│       │       └── index.blade.php                   # Riwayat transaksi kasir yang bertugas
│       ├── user/                                     # Modul Antarmuka User / Pengguna
│       │   ├── products/
│       │   │   └── index.blade.php                   # Katalog produk full-width (hanya lihat & tambah ke troli)
│       │   ├── cart/
│       │   │   └── index.blade.php                   # Halaman khusus keranjang belanja & pembayaran mandiri
│       │   └── transactions/
│       │       └── index.blade.php                   # Riwayat pembelian & bukti bayar pengguna
│       └── receipts/
│           └── print.blade.php                       # Preview struk thermal (80mm & 58mm) & printer setup
│
├── routes/                                           # Definisi endpoint URL aplikasi
│   ├── console.php                                   # Perintah interaktif artisan CLI
│   └── web.php                                       # Seluruh routing web aplikasi dan grup otorisasi middleware
│
├── tests/                                            # Pengujian otomatis aplikasi (Unit & Feature Testing)
│   ├── TestCase.php                                  # Base test case konfigurasi
│   ├── Feature/
│   │   ├── ExampleTest.php                           # Uji respons status halaman
│   │   └── RoleDashboardTest.php                     # Uji hak akses admin/kasir, proses checkout POS, & invoice unik
│   └── Unit/
│       └── ExampleTest.php                           # Uji unit PHP dasar
│
├── .editorconfig                                     # Standar format penulisan kode antar IDE
├── .env.example                                      # Contoh berkas konfigurasi environment aplikasi
├── artisan                                           # Antarmuka baris perintah (CLI) Laravel
├── composer.json                                     # Daftar dependensi PHP & paket eksternal
├── package.json                                      # Konfigurasi dependensi npm / build frontend
└── phpunit.xml                                       # Konfigurasi runner pengujian unit/fitur
```

---

##  Fitur Utama

1. **Mesin Kasir (Point of Sale)**
   - Desain 2 panel responsif (Katalog Produk & Keranjang Belanja).
   - Pencarian cepat instan (kode barang / barcode / nama produk).
   - Filter produk berdasarkan kategori dengan tab aktif.
   - Perhitungan otomatis subtotal, diskon, jumlah item, dan kembalian.
   - Pilihan metode pembayaran: **Tunai**, **QRIS**, dan **Transfer**.
   - Tombol nominal cepat (*Uang Pas, 10k, 20k, 50k, 100k*).

2. **Cetak Nota & Struk Thermal**
   - Format standar default **80 mm Roll** (lebar 72 mm area cetak aktif) yang panjang ke bawah mengikuti jumlah barang belanjaan.
   - Tersedia preset ukuran nota tambahan (**58 mm Roll**, **58 x 40 mm Label**, dan **Ukuran Kustom**).
   - Format struk profesional lengkap dengan barcode nomor invoice, rincian produk, subtotal, potongan harga, dan catatan kaki.

3. **Manajemen Produk & Stok**
   - Kode produk unik / SKU.
   - Indikator stok otomatis (*Habis, Sisa Sedikit, Tersedia*).
   - Penyesuaian stok otomatis berkurang setiap kali checkout kasir berhasil.

4. **Laporan & Analitik Penjualan**
   - Rekap omzet total, jumlah transaksi berhasil, dan rata-rata penjualan per transaksi.
   - Filter tanggal fleksibel (*Hari Ini, 7 Hari Terakhir, Bulan Ini, Rentang Tanggal Kustom*).
   - Filter berdasarkan metode pembayaran.

5. **Manajemen Pengguna & Keamanan**
   - Pemisahan hak akses berbasis peran (*Role-Based Access Control*):
     - **Admin**: Akses penuh ke seluruh fitur toko, produk, kategori, user, laporan, dan POS.
     - **Kasir**: Akses fokus ke POS penjualan, riwayat transaksi kasir bersangkutan, dan dashboard shift.
     - **User / Pengguna**: Hanya melihat katalog & detail produk (read-only), melakukan pembayaran non-tunai (QRIS & Virtual Account / VA), serta melihat riwayat pembelian dan struk digital miliknya sendiri (hanya lihat, tanpa cetak).
   - Keamanan terhadap benturan nomor invoice ganda (*lock / auto-retry generator*).

---

##  Palet Warna Desain Antarmuka

Antarmuka seluruh aplikasi dikembangkan menggunakan 5 warna harmonis:

| Warna | Kode HEX | Peran & Penggunaan |
|---|---|---|
| **Dark Obsidian** | `#0F0F0F` | Latar luar pop-up, bilah logo header, dark ambient card, teks primer kontras |
| **Dark Charcoal** | `#202020` | Latar belakang sidebar navigasi, surface elevated, tombol utilitas |
| **Electric Lime** | `#5DD62C` | Tombol Call-To-Action utama (Bayar, Simpan), active tab/pills, aksen brand |
| **Forest Olive** | `#337418` | State hover tombol lime, teks hijau kontras tinggi, ring aksen |
| **Off-White Soft** | `#F8F8F8` | Latar belakang canvas utama aplikasi, kartu produk, input field |

---

##  Akun Demo Bawaan

Saat seeder database dijalankan, akun demo berikut langsung siap digunakan:

| Peran | Alamat Email | Kata Sandi | Hak Akses |
|---|---|---|---|
| **Admin Toko** | `admin@mytoko.com` | `password` | Akses penuh (Dashboard Admin, Produk, Kategori, User, Laporan, POS) |
| **Kasir Utama** | `kasir@mytoko.com` | `password` | Akses khusus (POS Kasir, Shift Dashboard, Riwayat Transaksi Saya) |
| **Pengguna (User)** | `user@mytoko.com` | `password` | Lihat produk (read-only), lakukan pembayaran, dan lihat riwayat pembelian sendiri |

> **Catatan:** Pada halaman login telah disediakan tombol **1-Klik Akun Demo** untuk mengisi form secara instan tanpa perlu mengetik manual.

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Prasyarat Sistem
- PHP >= 8.2 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`.
- MySQL / MariaDB (XAMPP).
- Composer.

### 2. Langkah Pengaturan
```bash
# Salin konfigurasi environment
cp .env.example .env

# Pasang dependensi composer
composer install

# Generate application key
php artisan key:generate

# Konfigurasi database di .env (contoh MySQL XAMPP)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mytoko
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migrasi tabel dan data awal seeder
php artisan migrate:fresh --seed

# Jalankan server lokal
php artisan serve
```

Akses aplikasi melalui peramban: `http://127.0.0.1:8000`

---

##  Menjalankan Pengujian Otomatis

Seluruh modul dan hak akses telah diuji secara menyeluruh melalui PHPUnit:

```bash
php artisan test
```

Semua 9 tes unit & fitur lulus validasi:
- `ExampleTest` (Status halaman & responsivitas)
- `RoleDashboardTest` (Proteksi redirect guest, otorisasi peran kasir vs admin, integritas transaksi checkout, pembuatan nomor invoice unik, dan akses pengaturan struk 80mm).
