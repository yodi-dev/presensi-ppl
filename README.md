# Presensi PPL

Sistem informasi presensi digital dan dokumentasi piket KBM (Kegiatan Belajar Mengajar) untuk mahasiswa Praktik Pengalaman Lapangan (PPL) berbasis web menggunakan CodeIgniter 4.

---

## Tentang Aplikasi

Aplikasi ini dikembangkan untuk memudahkan pemantauan kehadiran serta kegiatan mahasiswa PPL secara transparan dan akurat. Sistem ini memisahkan hak akses antara Guru Pamong/Pembimbing dan Mahasiswa PPL, dilengkapi pelacakan koordinat lokasi saat absen serta dokumentasi piket berbasis kamera langsung dengan watermark waktu.

---

## Fitur Utama

### Mahasiswa PPL
* **Presensi Datang & Pulang**: Pencatatan waktu masuk dan keluar otomatis dilengkapi pengambilan titik koordinat GPS (latitude & longitude).
* **Pengajuan Izin & Sakit**: Formulir pengajuan keterangan ketidakhadiran dengan alasan yang tercatat di sistem.
* **Presensi Piket KBM**: Dokumentasi kehadiran piket menggunakan kamera perangkat secara langsung, dilengkapi watermark waktu dan nama mahasiswa pada foto bukti.
* **Ganti Password**: Pengelolaan keamanan akun mandiri.

### Guru Pamong / Pembimbing
* **Dashboard Rekap Harian**: Pemantauan kehadiran seluruh mahasiswa per tanggal dengan filter jurusan (Informatika, PJOK, BK, TL, TO).
* **Verifikasi Titik Lokasi**: Tautan langsung ke Google Maps berdasarkan koordinat GPS saat mahasiswa melakukan absensi datang.
* **Pembaruan Status Presensi**: Fasilitas perubahan status kehadiran (Hadir, Izin, Sakit, Alpa) yang dilengkapi konfirmasi SweetAlert dan proteksi token CSRF.
* **Laporan Bulanan**: Rekapitulasi akumulasi kehadiran bulanan per mahasiswa lengkap dengan persentase kehadiran serta tampilan cetak ramah cetak (print-ready layout).
* **Laporan Piket KBM**: Rekapitulasi dokumentasi kegiatan piket harian beserta penampil bukti foto.

### Keamanan Sistem
* **Autentikasi & Otorisasi Rute**: Penggunaan `AuthFilter` dan `RoleFilter` berbasis middleware untuk membatasi akses URL berdasarkan peran pengguna.
* **Proteksi File Upload**: Validasi MIME type, verifikasi binary gambar, whitelist ekstensi, dan pembatasan eksekusi skrip PHP di folder upload via `.htaccess`.
* **Proteksi SQL Injection & XSS**: Penggunaan parameterized query/escaping pada seluruh filter dan sanitasi output tampilan menggunakan `esc()`.
* **Proteksi CSRF**: Penerapan token CSRF pada seluruh permintaan POST.

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
2. Pastikan tabel `users`, `presensi`, dan `piket_kbm` telah diimpor ke database.
3. Jalankan Seeder akun bawaan jika diperlukan:
```bash
php spark db:seed UserSeeder
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
| **Guru** | `febriyana` | `secret` | Akun Pembimbing |
| **Guru** | `jumari` | `secret` | Akun Pembimbing |
| **Mahasiswa** | `awan` | `secret12` | Mahasiswa Informatika |
| **Mahasiswa** | `fajar` | `secret` | Mahasiswa Informatika |
| **Mahasiswa** | `latifah` | `secret` | Mahasiswa BK |

---

## Pengujian Otomatis

Aplikasi dilengkapi dengan test suite mandiri untuk memverifikasi keamanan dan fungsionalitas sistem.

Jalankan pengujian melalui terminal:
```bash
php tests/runner.php
```

Pengujian mencakup:
1. Verifikasi penolakan akses tamu pada rute terproteksi (`AuthFilter`).
2. Verifikasi pemisahan hak akses Guru dan Mahasiswa (`RoleFilter`).
3. Pengujian keamanan upload bukti piket (validasi binary, ekstensi, dan `.htaccess`).
4. Pengujian netralisasi injeksi SQL pada parameter tanggal dan bulan/tahun.
5. Pengujian sanitasi output terhadap potensi XSS.
6. Verifikasi ketiadaan kredensial sensitif pada repositori.

---

## Struktur Direktori Utama

```
presensi-ppl/
├── app/
│   ├── Config/          # Konfigurasi aplikasi, filter, dan routing
│   ├── Controllers/     # Controller Auth, Guru, dan Mahasiswa
│   ├── Database/        # Seeder dan migrasi database
│   ├── Filters/         # Filter middleware AuthFilter & RoleFilter
│   ├── Models/          # Model data UserModel, PresensiModel, PiketModel
│   └── Views/           # Template antarmuka (auth, guru, mahasiswa, layout)
├── public/
│   ├── uploads/         # Direktori upload foto bukti (dilindungi .htaccess)
│   ├── favicon.png      # Ikon favicon web
│   └── index.php        # Front controller aplikasi
├── tests/
│   ├── runner.php       # Test runner otomatis mandiri
│   └── unit/            # Unit testing standar
├── .env.example         # Template konfigurasi environment bersih
├── .gitignore           # Konfigurasi pengabaian file sensitif
└── README.md            # Dokumentasi proyek
```

---

## Lisensi

Proyek ini dikembangkan untuk kebutuhan internal institusi dan praktik mahasiswa PPL.
