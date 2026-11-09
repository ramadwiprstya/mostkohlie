# Kopi Koh Lie — Sistem Manajemen Operasional, Inventaris & Kasir

Sistem informasi berbasis web untuk manajemen operasional kedai kopi **Kopi Koh Lie**. Sistem ini mengelola pencatatan rekapitulasi pesanan shift kasir, kalkulasi otomatis pemakaian bahan baku berdasarkan resep (*recipe-based inventory deduction*), buku besar mutasi stok (*audit trail*), pengadaan bahan baku (*procurement*), serta pembelian darurat (*emergency purchase*).

---

## 🛠️ Prasyarat Sistem (Prerequisites)

Sebelum memulai instalasi, pastikan komputer Anda telah terpasang:
* **PHP** `>= 8.2` (ekstensi aktif: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`)
* **Composer** `>= 2.x`
* **Node.js** `>= 18.x` & **NPM**
* **MySQL Server** (melalui XAMPP, Laragon, Docker, atau MySQL Service mandiri)

---

## 🚀 Panduan Instalasi & Setup Proyek

Ikuti langkah-langkah berikut secara berurutan:

### 1. Masuk ke Direktori Proyek
Buka terminal (PowerShell / Command Prompt / Git Bash) di direktori proyek:
```bash
cd Capstone-Project
```

### 2. Pasang Dependensi Backend (Composer)
```bash
composer install
```

### 3. Pasang Dependensi Frontend (NPM)
```bash
npm install
```

### 4. Salin & Konfigurasi File Environment (`.env`)
Salin file template konfigurasi lingkungan:
* **Windows (PowerShell / CMD):**
  ```bash
  copy .env.example .env
  ```
* **Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

Buka file `.env` dan pastikan konfigurasi database sesuai dengan pengaturan MySQL lokal Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kohlie
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Encryption Key
```bash
php artisan key:generate
```

### 6. Siapkan Database MySQL
Buka MySQL (misal via phpMyAdmin atau HeidiSQL) dan buat database baru bernama **`kohlie`**:
```sql
CREATE DATABASE kohlie;
```

### 7. Jalankan Migrasi Tabel & Data Seeder
Perintah ini akan membuat seluruh struktur tabel dan mengisi data awal (kategori, satuan, bahan baku, menu, master resep, dan akun demo):
```bash
php artisan migrate --seed
```

### 8. Buat Symlink Storage Publik
Diperlukan agar bukti struk belanja darurat dan foto menu dapat diakses publik:
```bash
php artisan storage:link
```

### 9. Build Aset Tampilan (Tailwind CSS, Alpine.js, Lucide Icons)
Pilih salah satu perintah berikut:
* **Untuk mode pengembangan (Hot Reloading):**
  ```bash
  npm run dev
  ```
* **Atau build sekali untuk produksi:**
  ```bash
  npm run build
  ```

### 10. Jalankan Server Lokal Laravel
Buka terminal baru dan jalankan server pengembangan:
```bash
php artisan serve
```

Aplikasi sekarang dapat diakses melalui peramban web di:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 👥 Kredensial Akun Default (Hasil Seeder)

| Peran (Role) | Email | Password | Wewenang Akses |
| :--- | :--- | :--- | :--- |
| **Owner (Admin)** | `owner@kopikohlie.com` | `kohlie123` | Akses penuh: Dashboard analitik, approval pengadaan, kelola pengguna, monitoring stok, laporan. |
| **Barista (Staf)** | `barista@kopikohlie.com` | `password` | Akses operasional: Input rekap kasir per shift, ajukan request pengadaan, catat belanja darurat. |

---

## 📂 Struktur Utama Proyek

```text
Capstone-Project/
├── app/
│   ├── Http/Controllers/    # Controller logika bisnis (OrderRecap, Procurement, dll)
│   └── Models/              # Eloquent Models & Relasi ORM (Ingredient, Menu, Recipe, dll)
├── database/
│   ├── migrations/          # Struktur skema tabel database
│   └── seeders/             # Data master awal & akun pengguna
├── resources/
│   ├── views/               # Template Blade UI (Alpine.js, Tailwind CSS)
│   ├── css/ & js/           # Source aset frontend & Vite compiler
├── routes/
│   └── web.php              # Definisi rute web & otorisasi role
├── ERD_Kopi_KohLie.png      # Entity Relationship Diagram (Visual 250 DPI)
├── Class_Diagram_Kopi_KohLie.png # UML Class Diagram Domain Model
└── README.md                # Dokumentasi proyek
```

---

## 🛠️ Perintah Troubleshooting

* **Membersihkan Cache Aplikasi & Konfigurasi:**
  ```bash
  php artisan optimize:clear
  ```
* **Mereset & Mengisi Ulang Database dari Awal (Hati-hati: Data terhapus):**
  ```bash
  php artisan migrate:fresh --seed
  ```
* **Jika Tampilan CSS / Icon Tidak Muncul:**
  ```bash
  npm run build
  php artisan view:clear
  ```
