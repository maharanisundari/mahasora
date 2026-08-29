# NusantaraStore — Sistem Pemesanan Layanan Berbasis Web
**Seleksi Teaching Factory (TeFa) — Kompetensi Keahlian RPL SMKN 1 Katapang**

> Client Brief: Usaha jasa dengan pencatatan manual kesulitan mengelola layanan, pelanggan, pemesanan, dan status pekerjaan. Aplikasi web terstruktur menggantikan proses manual.

## 1. Tech Stack (Wajib H — Sesuai Instruksi)
| Komponen | Teknologi | Keterangan |
|---|---|---|
| Framework | **Laravel 13** (`laravel/framework ^13.17`) | MVC, Eloquent ORM, Routing, Middleware |
| Database | **MySQL 8 (XAMPP)** (`nusantarastore`) | Persisten, migrasi + seeder |
| Template Engine | **Blade** | `resources/views/*.blade.php` |
| CSS Framework | **Tailwind CSS 4** (`@tailwindcss/vite ^4.0.0`) | Via Vite `resources/css/app.css` + `@vite` |
| Bahasa | PHP 8.3+ | Validasi `validate()`, error handling `abort()` |

Tidak ada library/package tambahan di luar stack wajib (cek `composer.json` hanya `laravel/framework`, `laravel/tinker`; `package.json` hanya `tailwindcss`, `vite`, `laravel-vite-plugin`).

## 2. Ringkasan 7 Fitur Minimum (Poin D)
| No | Modul | Spesifikasi Minimum (Instruksi) | Implementasi di NusantaraStore | Route / File |
|---|---|---|---|---|
| 1 | **Manajemen Layanan** | CRUD layanan (tambah, lihat, ubah, hapus) | Admin CRUD penuh + katalog publik | `GET /admin/services` (`ServiceController@index`), `GET /` katalog + `GET /services/{id}` detail (`ServiceController@catalog/show`) — `resources/views/admin/services/*`, `catalog/*` |
| 2 | **Manajemen Pelanggan** | Mengelola profil & informasi pelanggan | Admin `users.role=customer` CRUD (nama, email, phone, address, avatar, bio, status) + pelanggan update profil sendiri | `GET /admin/customers` (`CustomerController@index`), `GET /profile` (`ProfileController@edit/update`), `resources/views/profile/edit.blade.php` |
| 3 | **Transaksi Pemesanan** | Membuat pemesanan baru berdasar layanan | Customer checkout pilih layanan → sistem hitung `total_price=service.price`, terbitkan `order_code=TRX-YYYYMMDD-001` (`Order::generateOrderCode()`), set `order_type online/offline`, status awal `pending`, simpan `orders`+`order_statuses`. Admin juga input offline WA | `GET /checkout/{service}` + `POST /orders` (`OrderController@store`), `GET/POST /admin/orders/create` (`adminStore`) |
| 4 | **Monitoring & Detail** | Daftar pesanan + rincian tiap pesanan | Admin `Monitoring Pesanan` tabel + detail; Customer `Pesanan Saya` + detail | `GET /admin/orders` (`adminIndex`), `GET /admin/orders/{order}` (`adminShow`), `GET /my-orders` (`myOrders`), `GET /my-orders/{order}` (`myShow`) |
| 5 | **Status Pemesanan** | Ubah & update progres pekerjaan | Enum `pending → diproses → selesai` (+ `dibatalkan`), histori `order_statuses.updated_by` | `PATCH /admin/orders/{order}/status` (`updateStatus`), `resources/views/admin/orders/show.blade.php` |
| 6 | **Pencarian & Filter** | Cari / saring data pesanan/layanan | Layanan: `?q` (nama/deskripsi); Pesanan: `?q` (kode/nama/layanan) + `?status` + `?type` | `ServiceController@catalog/index`, `OrderController@adminIndex/myOrders` |
| 7 | **Ringkasan Informasi** | Dashboard ringkasan | Dashboard admin: total pesanan, total pendapatan (sum `selesai`), jumlah pelanggan, total layanan, rekap status, grafik 7 hari (Tailwind bar), daftar terbaru | `GET /admin/dashboard` (`DashboardController@index`), `resources/views/admin/dashboard.blade.php` |

Semua validasi input: `validate(['service_name'=>'required', 'price'=>'required|numeric', 'email'=>'required|email|unique', ...])`. Error handling: `abort(403)`, flash `session('success')`/`$errors`, `findOrFail`.

## 3. Perencanaan (Poin E — Sebelum Implementasi)

### 3.1 Analisis Kebutuhan
Lihat `konsep_web.txt:8-16` — 7 modul di atas. Non-fungsional: MySQL persisten, validasi, responsive Tailwind, auth `admin/customer`.

### 3.2 Flow Sistem (Flowchart)
`konsep_web.txt:20-29`
- **A. Pelanggan Online:** Buka Website → Cek Login? → Cari & Pilih Layanan → Form Checkout → Sistem (hitung total, kode TRX, pending, save) → Pesanan Saya → Selesai.
- **B. Admin Offline WA:** Login Dashboard → Terima WA → Cek Pelanggan Terdaftar? → Input Profil jika belum → Input Transaksi → Pending & Save.
- **C. Admin Update:** Login → Ringkasan → Pilih Layanan/Pelanggan/Monitoring → Aksi CRUD / Update Status `Pending→Diproses→Selesai` → Tampilkan Hasil.

### 3.3 ERD / Skema Database
`konsep_web.txt:33-72` + `database/migrations/*.php`
```
users (id PK, name, email UQ, password, phone 20, address TEXT, role ENUM admin/customer, avatar, bio, customer_status ENUM active/inactive, timestamps)
  1 ── N orders (id PK, order_code VARCHAR50 UQ TRX-..., user_id FK, service_id FK, total_price DECIMAL12,2, order_type ENUM online/offline, notes TEXT NULL, timestamps)
services (id PK, service_name, description TEXT, price DECIMAL12,2, image, timestamps)
  1 ── N orders
orders 1 ── N order_statuses (id PK, order_id FK, status ENUM pending/diproses/selesai/dibatalkan, updated_by FK users.id, created_at)
```

### 3.4 Arsitektur & Struktur Project (MVC Laravel)
```
ecommerce/
├── app/Http/Controllers/ AuthController, ServiceController, CustomerController, OrderController, DashboardController, ProfileController
├── app/Models/ User, Service, Order, OrderStatus (+ RoleMiddleware)
├── app/Http/Middleware/RoleMiddleware.php (alias 'role')
├── database/migrations/ 0001_..._users, 2026_..._services, orders, order_statuses
├── database/seeders/DatabaseSeeder.php (admin + 3 customer + 6 layanan + 5 pesanan)
├── routes/web.php (guest/auth/role:admin)
├── resources/views/layouts/app.blade.php (@vite), admin.blade.php, catalog/*, admin/*, orders/*, profile/*, auth/*
├── resources/css/app.css (@import tailwindcss), vite.config.js (@tailwindcss/vite)
└── public/build/ (vite build)
```

### 3.5 Time Schedule
| Tahap | Fase SDLC (Poin D) | Estimasi | Luaran |
|---|---|---|---|
| 1 | Client/Problem | 0.5 hari | Brief usaha jasa manual → `konsep_web.txt:1` |
| 2 | Analysis | 0.5 hari | 7 kebutuhan minimum |
| 3 | Planning | 0.5 hari | Tech stack Laravel/MySQL/Blade/Tailwind, time schedule ini |
| 4 | Design | 1 hari | Flowchart, ERD, arsitektur MVC |
| 5 | Development | 3 hari | Migrasi, Model, Controller, Blade, Auth, CRUD, Checkout |
| 6 | Testing | 0.5 hari | Uji manual login, CRUD, checkout, filter, dashboard |
| 7 | Build/Deployment | 0.5 hari | `npm run build`, `php artisan serve`, MySQL dump |
| 8 | Revision | 0.5 hari | Hapus Chart.js/FA, ganti ke Tailwind murni, MySQL migrasi |
| **Total** |  | **7 hari** |  |

## 4. Cara Menjalankan (Dokumentasi Teknis)

### Prasyarat
- PHP 8.3+, Composer 2.10, Node 22, MySQL (XAMPP), Git

### Instalasi
```bash
git clone <repo-url> "web ecommerce"
cd "web ecommerce"
composer install
copy .env.example .env   # atau pakai .env yang ada (APP_NAME=NusantaraStore, DB_DATABASE=nusantarastore)
php artisan key:generate
# Buat DB MySQL
mysql -u root -e "CREATE DATABASE nusantarastore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed   # atau migrate:fresh --seed
php artisan storage:link
npm install
npm run build
php artisan serve --port=8000
# buka http://localhost:8000
```

### Akun Demo (Seeder)
- **Admin:** `admin@nusa.test` / `password` → `/admin/dashboard`
- **Customer:** `budi@nusa.test` / `password`, `siti@nusa.test` / `password`, `andi@nusa.test` / `password` → `/` katalog → `Checkout`
- Semua password `password` (hash `bcrypt`).

### Database / Data
- Migrasi: `database/migrations/*`
- Seeder: `database/seeders/DatabaseSeeder.php`
- Dump: `database/nusantarastore.sql` (export via `mysqldump -u root nusantarastore > database/nusantarastore.sql`) — sertakan jika diminta penguji.

### Build
- `public/build/manifest.json` + `public/build/assets/*` hasil `vite build` — wajib ada saat `php artisan serve` tanpa `vite dev`.

## 5. Checklist Instruksi H & F
- [x] Laravel, MySQL, Blade, Tailwind (tanpa library tambahan yang tidak dapat dipertanggungjawabkan — FA/Chart.js dihapus)
- [x] 7 fitur minimum berfungsi, validasi & error handling, responsive (`grid-cols-1 lg:grid-cols-4`, `flex-col lg:flex-row`), persisten MySQL
- [x] Runnable: `php artisan serve` + `npm run build`
- [x] Git repository: `git init`, commit history (lihat `git log --oneline`)
- [x] Dokumentasi README ini + `konsep_web.txt`

## 6. Catatan Penting
- Aplikasi menyesuaikan perubahan client (mis. hapus library ekstra → Tailwind murni).
- Peserta mampu mempertanggungjawabkan tiap modul (controller `ServiceController.php:46 validate`, `Order.php:45 generateOrderCode`, `RoleMiddleware.php:11`).

---
© 2026 NusantaraStore — TeFa RPL SMKN 1 Katapang. Dibuat dengan Laravel & Tailwind CSS.
