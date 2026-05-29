# 💰 Keuangan Pribadi

Aplikasi web pencatatan keuangan pribadi untuk mengelola pemasukan dan pengeluaran. Dibangun menggunakan **Laravel 13** + **Blade** + **MySQL**.

## 📋 Fitur

### ✅ Milestone Minggu 1 (Selesai)
- **Authentication**: Register, Login, Logout
- **CRUD Kategori**: Tambah, Edit, Hapus kategori (Pemasukan & Pengeluaran)
- **Database Schema**: Tabel users, categories, transactions
- **Validasi Input**: Form request validation dengan pesan error Bahasa Indonesia

### 🔜 Milestone Minggu 2
- CRUD Transaksi (pemasukan & pengeluaran)
- Relasi database
- Dashboard dasar

### 🔜 Milestone Minggu 3
- Statistik & Chart bulanan
- Filter transaksi (tanggal & kategori)
- UI improvement & Bug fixing

### 🔜 Milestone Minggu 4
- Polishing & Final testing
- Presentasi final

## 🛠️ Teknologi

| Teknologi | Versi |
|-----------|-------|
| PHP | 8.3+ |
| Laravel | 13.x |
| Blade | - |
| MySQL | 8.x |
| Tailwind CSS | 4.x |
| Node.js | 24.x |

## 🚀 Instalasi

### Prasyarat
- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL (Laragon)

### Langkah-langkah

1. **Clone repository**
   ```bash
   git clone <url-repository>
   cd "Project magang"
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database**

   Edit file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=keuangan_pribadi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Buat database**

   Buat database `keuangan_pribadi` di MySQL (bisa melalui phpMyAdmin atau CLI).

6. **Jalankan migration & seeder**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Jalankan server**
   ```bash
   php artisan serve
   ```

9. Buka browser dan akses `http://localhost:8000`

## ⚠️ Catatan Penting

Pastikan Laragon menggunakan **PHP 8.3+** karena Laravel 13 membutuhkan minimum PHP 8.3.
Di Laragon, pilih PHP version `php-8.3.28-Win32-vs16-x64` melalui Menu > PHP > Version.

## 📁 Struktur Database

### Tabel `categories`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| user_id | bigint | FK → users.id |
| name | string(100) | Nama kategori |
| type | enum | 'income' atau 'expense' |

### Tabel `transactions`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary Key |
| user_id | bigint | FK → users.id |
| category_id | bigint (nullable) | FK → categories.id |
| type | enum | 'income' atau 'expense' |
| amount | decimal(15,2) | Jumlah transaksi |
| description | string(255) | Deskripsi (opsional) |
| transaction_date | date | Tanggal transaksi |

## 🔐 Demo Account

Setelah menjalankan seeder:
- **Email**: demo@example.com
- **Password**: password

## 👤 Author

Dibuat sebagai project magang.

## 📄 Lisensi

Project ini dibuat untuk keperluan pendidikan.
