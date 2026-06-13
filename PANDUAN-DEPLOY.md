# Panduan Deploy Lumière API — Laravel 12 + PHP 8.2 + MySQL

---

## Daftar Isi
1. [Persiapan (Lokal)](#1-persiapan-lokal)
2. [Instalasi Laravel & Salin File Project](#2-instalasi-laravel--salin-file-project)
3. [Konfigurasi .env](#3-konfigurasi-env)
4. [Migrasi Database & Inisialisasi Data](#4-migrasi-database--inisialisasi-data)
5. [Menjalankan Lokal (Development)](#5-menjalankan-lokal-development)
6. [Deploy ke Hostinger (Shared Hosting)](#6-deploy-ke-hostinger-shared-hosting)
7. [Konfigurasi React untuk Produksi](#7-konfigurasi-react-untuk-produksi)
8. [Endpoint API Lengkap](#8-endpoint-api-lengkap)

---

## 1. Persiapan (Lokal)

Pastikan sudah terinstall:
- **PHP 8.2+** → `php -v`
- **Composer** → `composer -V`  (https://getcomposer.org)
- **MySQL 5.7+ / 8.0+** (lokal: XAMPP, Laragon, atau MySQL langsung)
- **Node.js 18+** + npm/pnpm
- **Git** (opsional)

---

## 2. Instalasi Laravel & Salin File Project

```bash
# 1. Buat project Laravel 12 baru
composer create-project laravel/laravel lumiere-api "^12.0"

# 2. Masuk ke folder
cd lumiere-api

# 3. Install Laravel Sanctum (untuk token auth)
composer require laravel/sanctum

# 4. Publish konfigurasi Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Setelah itu **copy semua file dari zip ini** ke dalam folder `lumiere-api/`:

```
Salin folder/file berikut (timpa yang sudah ada):
  app/Http/Controllers/    → ke lumiere-api/app/Http/Controllers/
  app/Models/              → ke lumiere-api/app/Models/
  database/migrations/     → ke lumiere-api/database/migrations/
  routes/api.php           → ke lumiere-api/routes/api.php
  routes/web.php           → ke lumiere-api/routes/web.php
  config/cors.php          → ke lumiere-api/config/cors.php
  config/auth.php          → ke lumiere-api/config/auth.php
  bootstrap/app.php        → ke lumiere-api/bootstrap/app.php
```

---

## 3. Konfigurasi .env

```bash
# Copy file contoh
cp .env.example .env

# Generate app key
php artisan key:generate
```

Edit file `.env`, sesuaikan bagian ini:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lumiere_db       # <-- nama database MySQL kamu
DB_USERNAME=root             # <-- username MySQL
DB_PASSWORD=                 # <-- password MySQL

# Domain React yang boleh akses API (pisahkan koma)
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000

# Setup key untuk inisialisasi data pertama kali
SETUP_KEY=lumiere-setup-2024
```

---

## 4. Migrasi Database & Inisialisasi Data

```bash
# Buat database dulu di MySQL
mysql -u root -p -e "CREATE DATABASE lumiere_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan migrasi (buat semua tabel)
php artisan migrate

# Buat symlink untuk storage (agar upload gambar bisa diakses publik)
php artisan storage:link
```

**Inisialisasi data awal** dilakukan via browser/Postman setelah server berjalan:

```
POST http://localhost:8000/api/admin/seed
Body JSON: { "setupKey": "lumiere-setup-2024" }
```

Atau buka di browser React: `http://localhost:5173/admin/setup`  
Masukkan setup key: **lumiere-setup-2024**

Akun admin default yang dibuat:
- Username: **admin**
- Password: **lumiere2024**

---

## 5. Menjalankan Lokal (Development)

**Terminal 1 — Laravel API:**
```bash
cd lumiere-api
php artisan serve
# API berjalan di: http://localhost:8000
```

**Terminal 2 — React:**
```bash
cd lumiere-react
cp .env.example .env
# Edit .env: VITE_API_URL=http://localhost:8000/api
npm install
npm run dev
# React berjalan di: http://localhost:5173
```

---

## 6. Deploy ke Hostinger (Shared Hosting)

### A. Upload Laravel API

Hostinger shared hosting mendukung PHP 8.2. Caranya:

1. **Di cPanel/hPanel Hostinger**, buat subdomain baru:  
   Contoh: `api.namadomain.com`  
   Arahkan **Document Root** ke: `public_html/lumiere-api/public`

2. **Upload file** via File Manager atau FTP:
   - Upload seluruh folder `lumiere-api/` ke `public_html/lumiere-api/`
   - Pastikan folder `public/` ada di dalamnya

3. **Buat database MySQL di hPanel:**
   - Masuk ke `Databases → MySQL Databases`
   - Buat database: misalnya `u123456_lumiere`
   - Buat user dan hubungkan ke database tersebut
   - Catat: host, username, password, nama database

4. **Edit file `.env`** di server (via File Manager):
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://api.namadomain.com

   DB_HOST=localhost
   DB_DATABASE=u123456_lumiere
   DB_USERNAME=u123456_lumiere
   DB_PASSWORD=password_kamu

   CORS_ALLOWED_ORIGINS=https://namadomain.com,https://www.namadomain.com

   SETUP_KEY=ganti-dengan-key-rahasia-kamu
   ```

5. **Jalankan perintah via SSH** (aktifkan SSH di hPanel terlebih dahulu):
   ```bash
   cd ~/public_html/lumiere-api
   composer install --optimize-autoloader --no-dev
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   ```

   Jika tidak bisa SSH, gunakan **hPanel Terminal** (Hosting → Advanced → Terminal).

6. **Konfigurasi `.htaccess`** di `public/` (sudah ada bawaan Laravel, pastikan file ini ada):
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ index.php [QSA,L]
   </IfModule>
   ```

### B. Upload React (Static Build)

1. **Build React untuk produksi:**
   ```bash
   cd lumiere-react
   # Edit .env untuk produksi:
   echo "VITE_API_URL=https://api.namadomain.com/api" > .env
   npm run build
   # Hasil build ada di folder dist/
   ```

2. **Upload isi folder `dist/`** ke `public_html/` di Hostinger  
   (semua file HTML, JS, CSS langsung di dalam `public_html/`)

3. **Tambahkan `.htaccess`** di `public_html/` untuk SPA routing:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteBase /
       RewriteRule ^index\.html$ - [L]
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule . /index.html [L]
   </IfModule>
   ```

4. **Inisialisasi data** — buka di browser:
   `https://namadomain.com/admin/setup`  
   Masukkan setup key yang kamu set di `.env`

---

## 7. Konfigurasi React untuk Produksi

File `.env` untuk React production:
```env
VITE_API_URL=https://api.namadomain.com/api
```

Build:
```bash
npm run build
```

---

## 8. Endpoint API Lengkap

### Public (tanpa auth)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/products` | Daftar semua produk |
| GET | `/api/products/{id}` | Detail produk |
| GET | `/api/testimonials` | Testimoni aktif |
| GET | `/api/gallery` | Galeri aktif |
| GET | `/api/homepage-content` | Konten homepage |
| POST | `/api/admin/login` | Login admin → dapat token |
| POST | `/api/admin/seed` | Inisialisasi data awal |

### Admin (Bearer Token wajib di header)
```
Authorization: Bearer <token>
```
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/admin/stats` | Statistik (jumlah produk, testimoni) |
| GET/POST | `/api/admin/products` | List / Tambah produk |
| PUT/DELETE | `/api/admin/products/{id}` | Edit / Hapus produk |
| GET/POST | `/api/admin/testimonials` | List / Tambah testimoni |
| PUT/DELETE | `/api/admin/testimonials/{id}` | Edit / Hapus testimoni |
| GET/POST | `/api/admin/gallery` | List / Tambah galeri |
| PUT/DELETE | `/api/admin/gallery/{id}` | Edit / Hapus galeri |
| POST | `/api/admin/homepage-content` | Simpan konten homepage |
| POST | `/api/admin/upload` | Upload gambar (multipart/form-data) |

---

## Troubleshooting

**CORS Error** → Pastikan `CORS_ALLOWED_ORIGINS` di `.env` backend berisi domain React yang benar.

**500 Error di Hostinger** → Cek `storage/logs/laravel.log`, biasanya karena:
- Permission folder `storage/` dan `bootstrap/cache/` belum 775
- `.env` belum diisi lengkap

**Token tidak valid** → Pastikan tidak ada spasi extra di header Authorization.

**Upload gambar gagal** → Pastikan `php artisan storage:link` sudah dijalankan.

---

*Dibuat untuk project LUMIÈRE Beauty — Laravel 12 + React + MySQL*




<!-- DEPLOY REACT FRONT END di HOSTINGER SHARED HOSTING -->
 #1. ** modif : src\lib\api.ts **
replace :
const VITE_API_URL = import.meta.env.VITE_API_URL as string | undefined;

export const api = axios.create({
  baseURL: VITE_API_URL || "/api",
});

jadi :
export const api = axios.create({
  baseURL: "/api",
});

#2.buat file baru : src\vite-env.d.ts
isi : 
/// <reference types="vite/client" />

#3.buat zip folder projek frontend react
#4.pilih node.js web app
#upload
#framework: react;nodeversion24x;npm run build;npm;dist;VITE_API_URL=>isidengandomainapi.;
#jika deploy sukses , cek browser.
#untuk akses admin modif .htacess :
RewriteEngine On
RewriteBase /
RewriteRule ^index\.html$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.html [L]
#  