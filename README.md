# SimpeLajar — Sistem Manajemen Dokumen Akademik

## Description

SimpeLajar adalah aplikasi web fullstack untuk **manajemen dokumen mutu perkuliahan** di lingkungan perguruan tinggi. Aplikasi ini memfasilitasi proses pengumpulan, validasi, dan pelaporan dokumen akademik (RPS, Kontrak Kuliah, Soal UTS/UAS, Nilai, dsb.) yang diorganisir per mata kuliah, per semester, dan per tahap pengumpulan.

Terdapat dua peran utama:
- **Dosen** — mengunggah dokumen mata kuliah yang mereka ampu.
- **GKMP** (Gugus Kendali Mutu Prodi) — mengelola seluruh data master, mengvalidasi dokumen, dan mengekspor laporan.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Language | PHP 8.2 |
| Framework | Laravel 12 |
| Auth Scaffolding | Laravel Breeze |
| Database | MySQL (via PDO) |
| ORM | Eloquent |
| Frontend Templating | Blade |
| CSS Framework | Tailwind CSS 3 |
| JS Framework | Alpine.js 3 |
| Build Tool | Vite 7 (via `laravel-vite-plugin`) |
| Excel Export | Maatwebsite/Laravel Excel 3.1 |
| PDF | barryvdh/laravel-dompdf 3.1 |
| Deployment | Docker + Nginx |

---

## Features

Fitur-fitur berikut seluruhnya berdasarkan kode yang ada:

### GKMP (Admin)
- **Manajemen Semester** — CRUD semester (Ganjil/Genap), set semester aktif (hanya satu aktif pada satu waktu).
- **Manajemen Mata Kuliah** — CRUD mata kuliah (nama, kode), lihat detail per mata kuliah.
- **Manajemen Penugasan Dosen** — Menugaskan dosen ke mata kuliah per semester beserta lokasi kelas dan status (`Penanggung Jawab` / `Pemateri`).
- **Manajemen Tahap** — Membuat tahap pengumpulan dokumen per semester, dipetakan ke `KategoriTahap` yang sudah didefinisikan (Tahap 1–5), beserta deadline.
- **Manajemen User** — CRUD user (dosen dan GKMP), aktifasi/nonaktifasi akun, reset password.
- **Validasi Dokumen** — Review dokumen yang diunggah dosen: approve, minta revisi (beserta komentar), dan bulk approve.
- **Laporan** — Lihat dan ekspor laporan kelengkapan dokumen seluruh mata kuliah dalam format Excel (.xlsx), dapat difilter per semester dan per tahap.
- **Dashboard GKMP** — Statistik agregat: total mata kuliah aktif, total dokumen, jumlah pending/revisi/approved.

### Dosen
- **Dashboard Dosen** — Melihat daftar mata kuliah yang diampu pada semester aktif beserta tahap yang sedang berjalan.
- **Unggah Dokumen** — Mengunggah file dokumen per mata kuliah, tahap, dan jenis dokumen (mapping ke jenis yang diwajibkan oleh tahap).
- **Revisi Dokumen** — Mengunggah ulang dokumen yang dikembalikan untuk revisi; riwayat revisi tersimpan via `parent_id`.
- **Lihat Status Dokumen** — Melihat status dokumen (pending / revisi / approved) beserta komentar dari GKMP.
- **Ubah Password** — Mengganti password sendiri.

### Umum (Kedua Peran)
- **Autentikasi** — Login/logout berbasis session (email + password), menggunakan scaffolding Laravel Breeze.
- **Edit Profil** — Update nama, email, dan password.
- **Role-Based Access Control** — Akses dikontrol oleh `RoleMiddleware` via route; peran `dosen` dan `gkmp` memiliki izin yang berbeda.

---

## User Roles

Aplikasi memiliki **2 role** yang disimpan sebagai `enum('dosen', 'gkmp')` di kolom `users.role` dan divalidasi oleh `RoleMiddleware` pada setiap request.

### GKMP (`role = gkmp`) — Administrator Mutu

GKMP adalah **pengelola sistem** yang mengatur seluruh data master dan proses validasi dokumen. Semua route write-nya dilindungi `middleware('role:gkmp')` dan sebagian besar controller-nya memiliki cek ganda via `abort_unless(auth()->user()?->isGkmp(), 403)` di constructor.

| Akses | Keterangan |
|---|---|
| Dashboard | Statistik agregat: total MK, total dokumen, jumlah pending/revisi/approved |
| Kelola Semester | CRUD + set satu semester aktif |
| Kelola Mata Kuliah | CRUD kode & nama MK |
| Kelola Dosen | Tugaskan dosen ke MK per semester, atur lokasi & status (`Penanggung Jawab` / `Pemateri`) |
| Kelola Tahap | Buat tahap pengumpulan dokumen per semester beserta deadline |
| Kelola User | CRUD semua user, toggle aktif/nonaktif |
| Validasi Dokumen | Approve, minta revisi + komentar, bulk approve |
| Laporan | Lihat & ekspor Excel kelengkapan dokumen per semester/tahap |
| Lihat Dokumen | Semua dokumen dari semua dosen |

### Dosen (`role = dosen`) — Pengampu Mata Kuliah

Dosen adalah **kontributor dokumen**. Data yang ditampilkan secara otomatis difilter berdasarkan `dosen_id` di tabel pivot `dosen_mata_kuliah`, sehingga dosen hanya dapat melihat dan mengelola data yang relevan dengan dirinya.

| Akses | Keterangan |
|---|---|
| Dashboard | Daftar MK yang diampu di semester aktif + tahap yang sedang berjalan |
| Upload Dokumen | Hanya untuk MK yang ditugaskan ke dirinya |
| Lihat Dokumen | Hanya dokumen dari MK yang ia ampu |
| Revisi Dokumen | Upload ulang jika dokumen dikembalikan oleh GKMP |
| Ubah Password | Halaman `/ubah-password` (eksklusif untuk Dosen) |
| Edit Profil | Update nama, email, password |

### Mekanisme Enforcement

```php
// RoleMiddleware.php — dieksekusi di setiap request pada route yang dilindungi
if (!in_array($request->user()->role, $roles)) {
    abort(403, 'Akses tidak diizinkan.');
}

// Cek ganda di constructor controller (contoh: SemesterController)
abort_unless(auth()->user()?->isGkmp(), 403);

// Helper method di User model
public function isDosen(): bool { return $this->role === 'dosen'; }
public function isGkmp(): bool  { return $this->role === 'gkmp'; }
```

### Flowchart Akses & Alur Kerja per Role

```mermaid
flowchart TD
    START([User Akses Aplikasi]) --> AUTH{Sudah Login?}
    AUTH -- Belum --> LOGIN[Halaman Login]
    LOGIN --> CRED[Input Email + Password]
    CRED --> VERIFY{Kredensial Valid?}
    VERIFY -- Tidak --> LOGIN
    VERIFY -- Ya --> ROLE{Cek Role User}

    ROLE -- role = gkmp --> GKMP_DASH[Dashboard GKMP\nStatistik dokumen & MK aktif]
    ROLE -- role = dosen --> DOSEN_DASH[Dashboard Dosen\nDaftar MK & Tahap Aktif]

    %% GKMP Branch
    GKMP_DASH --> GKMP_MENU[Menu GKMP]
    GKMP_MENU --> G1[Kelola Semester\nCRUD + Set Aktif]
    GKMP_MENU --> G2[Kelola Mata Kuliah\nCRUD MK]
    GKMP_MENU --> G3[Kelola Dosen\nTugaskan Dosen ke MK\nper Semester + Lokasi + Status]
    GKMP_MENU --> G4[Kelola Tahap\nCRUD Tahap + Deadline\nper Semester]
    GKMP_MENU --> G5[Kelola User\nCRUD User + Toggle Aktif]
    GKMP_MENU --> G6[Validasi Dokumen\nApprove / Revisi / Bulk Approve]
    GKMP_MENU --> G7[Laporan\nLihat & Export Excel\nFilter Semester & Tahap]
    GKMP_MENU --> SHARED_DOKUMEN[Lihat Dokumen\nSemua MK & Dosen]

    %% Dosen Branch
    DOSEN_DASH --> DOSEN_MENU[Menu Dosen]
    DOSEN_MENU --> D1[Upload Dokumen\nPilih MK + Tahap + Jenis\nUnggah File]
    D1 --> STATUS{Status Dokumen}
    STATUS -- pending --> WAIT[Menunggu Review GKMP]
    STATUS -- revisi --> REVISI[Upload Ulang\nDokumen Baru sbg Revisi]
    STATUS -- approved --> DONE[Dokumen Diterima]
    DOSEN_MENU --> D2[Lihat Dokumen\nHanya MK yang diampu]
    DOSEN_MENU --> D3[Ubah Password]
    DOSEN_MENU --> SHARED_PROFILE[Edit Profil]

    GKMP_MENU --> SHARED_PROFILE

    %% Validation loop
    WAIT --> G6
    G6 --> STATUS

    %% Style
    classDef gkmpStyle fill:#0f172a,color:#fff,stroke:#0f172a
    classDef dosenStyle fill:#1e40af,color:#fff,stroke:#1e40af
    classDef sharedStyle fill:#374151,color:#fff,stroke:#374151
    classDef decisionStyle fill:#f59e0b,color:#000,stroke:#d97706

    class GKMP_DASH,GKMP_MENU,G1,G2,G3,G4,G5,G6,G7 gkmpStyle
    class DOSEN_DASH,DOSEN_MENU,D1,D2,D3 dosenStyle
    class SHARED_DOKUMEN,SHARED_PROFILE sharedStyle
    class AUTH,VERIFY,ROLE,STATUS decisionStyle
```

---

## Project Structure

```
simpelajar/
├── app/
│   ├── Exports/
│   │   └── LaporanExport.php          # Konfigurasi ekspor Excel (styling, header)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  # Controllers autentikasi (dari Breeze)
│   │   │   ├── DashboardController    # Dashboard berbeda untuk GKMP dan Dosen
│   │   │   ├── DokumenController      # Upload, lihat, hapus dokumen; API endpoint AJAX
│   │   │   ├── DosenController        # CRUD penugasan dosen ke mata kuliah
│   │   │   ├── LaporanController      # Tampil laporan & trigger ekspor Excel
│   │   │   ├── MataKuliahController   # CRUD mata kuliah
│   │   │   ├── MataKuliahDosenController # Kelola dosen per mata kuliah
│   │   │   ├── PasswordChangeController  # Ganti password (Dosen)
│   │   │   ├── ProfileController      # Edit profil
│   │   │   ├── SemesterController     # CRUD semester & set-active
│   │   │   ├── TahapController        # CRUD tahap (dipetakan ke KategoriTahap)
│   │   │   ├── UserManagementController # CRUD user, toggle aktif
│   │   │   └── ValidasiController     # Approve/revisi dokumen, bulk approve
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php     # Cek role user (dosen|gkmp), abort 403 jika tidak sesuai
│   │   │   └── TrustProxies.php
│   │   └── Requests/                  # Form Request classes
│   ├── Models/
│   │   ├── Dokumen.php                # Model dokumen; status badge/label accessor; revisi chain
│   │   ├── KategoriTahap.php          # Master kategori tahap; jenis_dokumen disimpan sebagai JSON
│   │   ├── MataKuliah.php             # Model mk; progressTahap() dan overallProgress() method
│   │   ├── Semester.php               # Model semester; getActive() static method
│   │   ├── Tahap.php                  # Instance tahap per semester; accessor dari KategoriTahap
│   │   └── User.php                   # isDosen()/isGkmp() helper; relasi mataKuliah (BelongsToMany)
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   └── app.php                        # Entry konfigurasi Laravel 12; register RoleMiddleware
├── database/
│   ├── migrations/                    # 18 file migrasi, merepresentasikan evolusi skema DB
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── TahapSeeder.php            # Seed KategoriTahap (5 tahap), Semester, dan Tahap awal
│       └── UserSeeder.php             # Seed user demo: 1 GKMP, 2 Dosen, beberapa MataKuliah
├── resources/
│   ├── css/                           # CSS entry (Tailwind)
│   ├── js/                            # JS entry (Alpine.js, Axios)
│   └── views/
│       ├── layouts/                   # Layout utama (app.blade.php)
│       ├── components/                # Reusable Blade components
│       ├── dashboard/                 # View dashboard (gkmp.blade.php, dosen.blade.php)
│       ├── dokumen/                   # View daftar, form upload, detail dokumen
│       ├── dosen/                     # View manajemen penugasan dosen
│       ├── laporan/                   # View laporan kelengkapan dokumen
│       ├── mata-kuliah/               # View CRUD mata kuliah
│       ├── semester/                  # View CRUD semester
│       ├── tahap/                     # View CRUD tahap
│       ├── users/                     # View manajemen user
│       └── validasi/                  # View review dan validasi dokumen
├── routes/
│   ├── web.php                        # Semua route web, dikelompokkan per middleware
│   └── auth.php                       # Route autentikasi (Breeze)
├── docker-compose.yml                 # Layanan: app (php-fpm) + webserver (nginx:alpine), port 8085
├── Dockerfile                         # Image PHP 8.2-fpm + ekstensi: pdo_mysql, gd, mbstring, dll.
└── composer.json                      # Dependensi PHP; script `setup` dan `dev`
```

---

## Application Flow

### 1. Entry Point
```
HTTP Request
    → public/index.php
    → bootstrap/app.php          (konfigurasi framework, register middleware)
    → routes/web.php             (dispatch ke controller)
    → Controller@method
    → View (Blade)               → Response ke browser
```

### 2. Autentikasi
1. User mengakses `/` → redirect ke `/dashboard`.
2. Jika belum login → redirect ke halaman login (Laravel Breeze).
3. Login dengan email + password → session dibuat.
4. Seluruh route di bawah `middleware('auth')` membutuhkan session aktif.

### 3. Alur Kerja Inti (Document Management Cycle)

```
GKMP                                          Dosen
 │                                              │
 ├─ Buat Semester (aktifkan 1 semester)         │
 ├─ Buat Tahap per Semester                     │
 │    └─ Pilih KategoriTahap + set deadline     │
 ├─ Tambah Mata Kuliah                          │
 ├─ Tugaskan Dosen ke Mata Kuliah               │
 │    (per semester, lokasi, status_dosen)       │
 │                                              │
 │                              ┌───────────────┤
 │                              │ Upload Dokumen │
 │                              │ per MK + Tahap │
 │                              │ + Jenis Dok.   │
 │                              └───────────────┤
 │                                    ↓         │
 ├─ Validasi Dokumen (pending list)             │
 │    ├─ Approve → is_current=true, status=approved
 │    └─ Revisi  → komentar dikirim ke Dosen    │
 │                                    ↓         │
 │                              ┌───────────────┤
 │                              │ Upload Ulang   │
 │                              │ (membuat revisi│
 │                              │  baru, parent  │
 │                              │  = dok lama)   │
 │                              └───────────────┤
 │                                              │
 ├─ Lihat Laporan & Export Excel                │
```

### 4. Role-Based Access Control
- Route di bawah `middleware('role:gkmp')` hanya bisa diakses oleh user dengan `role = gkmp`.
- Route `/dokumen/create` hanya bisa diakses `role:dosen`.
- Cek ganda dilakukan di `__construct()` beberapa controller (`abort_unless`).

### 5. Revisi Dokumen
- Ketika Dosen upload ulang dokumen yang berstatus `revisi`, record baru dibuat dengan `parent_id` menunjuk ke dokumen sebelumnya.
- Dokumen lama masih tersimpan di DB (`is_current = false`); dokumen baru berstatus `pending`.
- Ketika GKMP approve dokumen revisi: parent di-set `is_current = false`, revisi baru di-set `is_current = true`.

---

## Database Structure

### Tabel Utama

#### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | string | |
| email | string unique | |
| password | string (hashed) | |
| role | enum: `dosen`, `gkmp` | |
| is_active | boolean | default `true` |

#### `semester`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_semester | string | contoh: "Ganjil 2025" |
| tahun_ajaran | string(9) | format: `YYYY/YYYY` |
| tipe | enum: `ganjil`, `genap` | |
| is_active | boolean | hanya satu boleh aktif |

#### `mata_kuliah`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_mk | string | |
| kode_mk | string unique | |

#### `dosen_mata_kuliah` *(pivot)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| dosen_id | FK → users | |
| mata_kuliah_id | FK → mata_kuliah | |
| semester_id | FK → semester | nullable |
| lokasi | string | kelas/ruang mengajar |
| status_dosen | string | `Penanggung Jawab` atau `Pemateri` |
> Constraint unique: `(dosen_id, mata_kuliah_id, semester_id)`

#### `kategori_tahap`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_tahap | string | contoh: "Tahap 1" |
| deskripsi | text | |
| urutan | tinyint unique | urutan tampil |
| jenis_dokumen | JSON | daftar jenis dokumen yang wajib diunggah |

**Data bawaan (seeder):** Tahap 1–5 dengan jenis dokumen: RPS, Kontrak Kuliah, Materi 1–15, Soal UTS, Soal UAS, Nilai UTS, Nilai UAS, DNA, Portofolio, LJU, dsb.

#### `tahap`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| semester_id | FK → semester | |
| kategori_tahap_id | FK → kategori_tahap | |
| deadline | datetime | |

#### `dokumen`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| mata_kuliah_id | FK → mata_kuliah | |
| tahap_id | FK → tahap | |
| jenis_dokumen | string | salah satu dari jenis di `kategori_tahap.jenis_dokumen` |
| nama_file | string | nama file asli |
| file_path | string | path di storage |
| uploaded_by | FK → users | |
| status | enum: `pending`, `revisi`, `approved` | default `pending` |
| komentar | text | komentar GKMP saat revisi |
| parent_id | FK → dokumen nullable | menunjuk dokumen versi sebelumnya |
| is_current | boolean | hanya satu versi aktif per kategori |

### Relasi Antar Tabel

```
users  ──<  dosen_mata_kuliah  >──  mata_kuliah
                                        │
                                        └──<  dokumen
                                                 │
semester  ──<  tahap  >──  kategori_tahap       │
                │                               │
                └──────────────────────── dokumen.tahap_id

dokumen  ──self──  dokumen (parent_id: revisi chain)
```

---

## Installation

### Prerequisites
- PHP 8.2+, Composer, Node.js, MySQL (atau Docker)

### Dengan Docker

```bash
# Clone repositori
git clone <repo-url>
cd simpelajar

# Copy environment file
cp .env.example .env

# Edit .env: sesuaikan DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# (Jika menggunakan Docker Compose, pastikan DB_HOST mengarah ke container MySQL)

# Build dan jalankan container
docker-compose up -d

# Install dependensi dan setup (di dalam container app)
docker exec -it simpelajar_app bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run build
```

### Tanpa Docker (Local)

```bash
git clone <repo-url>
cd simpelajar

# Install dependensi
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env, lalu:
php artisan migrate
php artisan db:seed

# Build aset frontend
npm run build

# Jalankan development server
php artisan serve
```

Atau gunakan script shortcut dari `composer.json`:

```bash
composer setup   # install + key:generate + migrate + npm install + npm run build
composer dev     # php artisan serve + queue + pail + vite (concurrently)
```

---

## Usage

### Akun Default (setelah `db:seed`)

| Role | Email | Password |
|---|---|---|
| GKMP | gkmp@simpelajar.id | password |
| Dosen | budi@simpelajar.id | password |
| Dosen | siti@simpelajar.id | password |

### Alur Penggunaan Dasar

**Sebagai GKMP:**
1. Login → Dashboard menampilkan statistik dokumen dan daftar mata kuliah semester aktif.
2. Buat Semester → aktifkan semester yang sedang berjalan.
3. Buat Mata Kuliah → isi kode dan nama.
4. Tugaskan Dosen ke Mata Kuliah via menu **Kelola Dosen**.
5. Buat Tahap per semester → pilih KategoriTahap dan set deadline.
6. Menu **Validasi** → review dokumen berstatus `pending`, approve atau kembalikan untuk revisi.
7. Menu **Laporan** → filter per semester/tahap, lalu klik **Export** untuk unduh Excel.

**Sebagai Dosen:**
1. Login → Dashboard menampilkan mata kuliah yang diampu dan tahap yang sedang aktif.
2. Menu **Dokumen** → klik **Upload Dokumen**, pilih mata kuliah, semester, tahap, dan unggah file per jenis dokumen.
3. Pantau status dokumen (pending / revisi / approved). Jika `revisi`, upload ulang file yang sudah diperbaiki.
4. Menu **Ubah Password** untuk mengganti password.

### URL Akses (Docker)
Aplikasi berjalan di `http://localhost:8085` sesuai konfigurasi `docker-compose.yml`.

---

