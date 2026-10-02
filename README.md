# Presensi PPL

Sistem informasi presensi digital dan dokumentasi piket KBM (Kegiatan Belajar Mengajar) untuk mahasiswa Praktik Pengalaman Lapangan (PPL) berbasis web menggunakan CodeIgniter 4.

---

## Tentang Aplikasi

Aplikasi ini dikembangkan untuk memudahkan pemantauan kehadiran serta kegiatan mahasiswa PPL secara transparan dan akurat. Sistem ini memisahkan hak akses antara Guru Pamong/Pembimbing dan Mahasiswa PPL, dilengkapi pelacakan koordinat lokasi saat absen serta dokumentasi piket berbasis kamera langsung dengan watermark waktu.

---

## Fitur Utama

### Mahasiswa PPL
* **Presensi Datang & Pulang**: Pencatatan waktu masuk dan keluar otomatis dilengkapi validasi koordinat GPS radius sekolah (Geofencing Formula Haversine) dan pencegahan absen pulang sebelum jam kerja selesai.
* **Kebijakan Keterlambatan**: Deteksi otomatis status terlambat jika absensi melewati jam masuk maksimal (07:15 WIB).
* **Pengajuan Izin & Sakit**: Formulir pengajuan izin/sakit dengan lampiran bukti surat/dokumen (opsional saat pengajuan awal dan dapat disusulkan kemudian).
* **Riwayat Presensi Mandiri**: Monitoring rekapitulasi kehadiran per bulan, rincian status, dan fasilitas unggah bukti susulan.
* **Presensi Piket KBM**: Dokumentasi kegiatan piket menggunakan kamera perangkat secara langsung, dilengkapi watermark waktu dan identitas mahasiswa pada foto bukti.
* **Ganti Password**: Pengelolaan keamanan akun mandiri.

### Guru Pamong / Pembimbing
* **Dashboard Rekap Harian**: Pemantauan kehadiran seluruh mahasiswa per tanggal dengan filter jurusan (Informatika, PJOK, BK, TL, TO).
* **Verifikasi Titik Lokasi**: Tautan langsung ke Google Maps berdasarkan koordinat GPS saat mahasiswa melakukan absensi datang.
* **Pembaruan Status Presensi**: Fasilitas perubahan status kehadiran (Hadir, Terlambat, Izin, Sakit, Alpa) dan peninjauan bukti surat izin/sakit.
* **Laporan Bulanan & Ekspor Excel**: Rekapitulasi akumulasi kehadiran bulanan per mahasiswa lengkap dengan persentase kehadiran, format cetak PDF, dan unduhan file Excel (.xls) native.
* **Laporan Piket KBM**: Rekapitulasi dokumentasi kegiatan piket harian beserta penampil bukti foto.

### Administrator (Superadmin)
* **Manajemen Pengguna (CRUD)**: Pengelolaan akun pengguna (Mahasiswa, Guru, Admin), pembuatan akun baru dengan hashing bcrypt, edit data jurusan/role, dan penghapusan akun.
* **Reset Password Pengguna**: Fasilitas reset sandi pengguna yang lupa password.
* **Filter & Pencarian Pengguna**: Pencarian cepat berdasarkan nama/username serta penyaringan berbasis peran dan jurusan.

### Keamanan Sistem
* **Autentikasi & Otorisasi Rute**: Penggunaan `AuthFilter` dan `RoleFilter` berbasis middleware untuk membatasi akses URL berdasarkan peran (Admin, Guru, Mahasiswa).
* **Validasi GPS Geofencing**: Penolakan presensi di luar radius sekolah yang telah ditentukan (konfigurasi di `app/Config/Presensi.php`).
* **Proteksi File Upload**: Validasi MIME type, verifikasi binary gambar, whitelist ekstensi, dan pembatasan eksekusi skrip PHP di folder upload via `.htaccess`.
* **Proteksi SQL Injection & XSS**: Penggunaan parameterized query/escaping pada seluruh filter dan sanitasi output tampilan menggunakan `esc()`.
* **Proteksi CSRF**: Penerapan token CSRF pada seluruh permintaan POST state-changing.

---

## Teknologi yang Digunakan

* **Backend**: PHP 8.2+ / CodeIgniter 4
* **Database**: MySQL
* **Frontend**: Bootstrap 5.3, Bootstrap Icons, SweetAlert2
* **Web API Browser**: HTML5 Geolocation API, HTML5 Canvas API, MediaDevices Camera API

---

## Persyaratan Sistem

* PHP versi 8.2 atau lebih tinggi
* Ekstensi PHP: `intl`, `mbstring`, `json`, `mysqli`, `fileinfo`
* Web Server (Apache via Laragon / XAMPP)
* MySQL Database Server

---

## Panduan Instalasi Lokal

### 1. Clone Repositori
```bash
git clone https://github.com/yodi-dev/presensi-ppl.git
cd presensi-ppl
```

### 2. Konfigurasi Environment
Salin file konfigurasi contoh `.env.example` menjadi `.env`:
```bash
copy .env.example .env
```
Sesuaikan pengaturan database dan URL dasar aplikasi pada file `.env`:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = db_presensi
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

### 3. Setup Database
1. Buat database baru di MySQL dengan nama `db_presensi`.
2. Pastikan tabel `users`, `presensi`, `piket_kbm`, dan `settings` telah tersedia.
3. Jalankan DatabaseSeeder untuk mengisi data awal administrator, guru, mahasiswa, dan pengaturan sekolah:
```bash
php spark db:seed DatabaseSeeder
```

### 4. Menjalankan Aplikasi
Jalankan server pengembangan bawaan CodeIgniter:
```bash
php spark serve
```
Buka browser dan akses alamat: `http://localhost:8080` (atau via virtual host Laragon).

---

## Akun Default Pengujian

| Peran | Username | Password Default | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin123` | Administrator Sistem |
| **Guru** | `febriyana` | `secret` | Akun Pembimbing |
| **Guru** | `jumari` | `secret` | Akun Pembimbing |
| **Mahasiswa** | `awan` | `secret` | Mahasiswa Informatika |
| **Mahasiswa** | `fajar` | `secret` | Mahasiswa Informatika |
| **Mahasiswa** | `latifah` | `secret` | Mahasiswa BK |

---

## Pengujian Otomatis

Aplikasi dilengkapi dengan test suite mandiri untuk memverifikasi keamanan dan fungsionalitas sistem.

Jalankan pengujian melalui terminal:
```bash
php tests/runner.php
```

Pengujian (41 skenario pengujian) mencakup:
1. Verifikasi penolakan akses tamu pada rute terproteksi (`AuthFilter`).
2. Verifikasi pemisahan hak akses Guru, Mahasiswa, dan Admin (`RoleFilter`).
3. Pengujian keamanan upload bukti piket (validasi binary, ekstensi, dan `.htaccess`).
4. Pengujian netralisasi injeksi SQL pada parameter tanggal dan bulan/tahun.
5. Pengujian sanitasi output terhadap potensi XSS.
6. Verifikasi ketiadaan kredensial sensitif pada repositori.
7. Pengujian validasi GPS Geofencing (Formula Haversine & radius perimeter sekolah).
8. Pengujian kebijakan jam kerja & deteksi status keterlambatan serta pembatasan checkout dini.
9. Pengujian hak akses Admin dan pencegahan self-delete akun admin.
10. Pengujian format unduhan Excel (.xls) dan whitelist status presensi.
11. Pengujian pengaturan geofencing dinamis, toleransi bypass, dan SettingModel.

---

## Struktur Direktori Utama

```
presensi-ppl/
├── app/
│   ├── Config/          # Konfigurasi aplikasi, presensi GPS, filter, routing
│   ├── Controllers/     # Controller Auth, Admin, Guru, dan Mahasiswa
│   ├── Database/        # Seeder dan migrasi database
│   ├── Filters/         # Filter middleware AuthFilter & RoleFilter
│   ├── Models/          # Model data UserModel, PresensiModel, PiketModel
│   └── Views/           # Template antarmuka (admin, auth, guru, mahasiswa, layout)
├── public/
│   ├── uploads/         # Direktori upload foto bukti (dilindungi .htaccess)
│   ├── favicon.png      # Ikon favicon web
│   └── index.php        # Front controller aplikasi
├── tests/
│   ├── runner.php       # Test runner otomatis mandiri (34 tests)
│   └── unit/            # Unit testing standar
├── .env.example         # Template konfigurasi environment bersih
├── .gitignore           # Konfigurasi pengabaian file sensitif
└── README.md            # Dokumentasi proyek
```

---

## Lisensi

Proyek ini dikembangkan untuk kebutuhan internal institusi dan praktik mahasiswa PPL.
