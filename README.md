# SIPENSI SKAGATA
### Sistem Informasi Presensi & Piket KBM &bull; SMK Negeri 3 Yogyakarta

[![CI/Automated Tests](https://img.shields.io/badge/Tests-44%20Passed-success?style=flat-square&logo=php)](tests/runner.php)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?style=flat-square&logo=php)](https://www.php.net/)
[![Framework](https://img.shields.io/badge/Framework-CodeIgniter%204-firebrick?style=flat-square&logo=codeigniter)](https://codeigniter.com/)
[![Design System](https://img.shields.io/badge/Theme-Skagata%20Emerald-0f5132?style=flat-square)](PRD.md)
[![Sister App](https://img.shields.io/badge/Ecosystem-SIBENKA%20SKAGATA-10b981?style=flat-square)](https://github.com/yodi-dev)

---

## 📌 Tentang Aplikasi

**SIPENSI SKAGATA** *(Sistem Informasi Presensi & Piket Skagata)* adalah platform web presensi digital dan dokumentasi piket KBM (*Kegiatan Belajar Mengajar*) yang dirancang khusus untuk ekosistem **SMK Negeri 3 Yogyakarta (Skagata)**.

Aplikasi ini merupakan bagian dari ekosistem digital Skagata, bersanding bersama aplikasi saudara **SIBENKA SKAGATA** *(Sirkulasi Bengkel Skagata)*. Sistem ini melayani pencatatan kehadiran presisi untuk:
1. **Mahasiswa Praktikan (PK & PPG)** dari berbagai perguruan tinggi mitra (UNY, UAD, UST, dsb.) lintas jurusan.
2. **Guru Tidak Tetap (GTT)** di lingkungan SMKN 3 Yogyakarta.
3. **Guru Pamong / Pembimbing** untuk supervisi, monitoring, dan pengesahan kehadiran bulanan.
4. **Administrator (Superadmin)** untuk kalibrasi geofencing, jam dinas operasional, dan manajemen pengguna.

> 📖 **Spesifikasi Produk Lengkap**: Silakan baca dokumen resmi di [**PRD.md (Product Requirements Document v2.0)**](PRD.md).

---

## 🌟 Fitur Utama Sistem

### 📱 1. Mahasiswa Praktikan & Guru Tidak Tetap (Mobile-First)
* **Presensi Datang & Pulang Real-Time**: Pencatatan kehadiran akurat dengan penegakan radius GPS Geofencing (Formula Haversine) di area SMKN 3 Yogyakarta.
* **Banner Jam Kerja Dinamis**: Informasi batas jam masuk normal dan jam pulang minimal tampil elegan di layar utama ponsel.
* **Deteksi Keterlambatan Otomatis**: Deteksi status `terlambat` jika absen datang melewati batas toleransi masuk sekolah (default: `07:15 WIB`).
* **Pencegahan Checkout Dini**: Tombol pulang terkunci hingga jam dinas pulang tercapai (default: `15:00 WIB`).
* **Pengajuan Izin & Sakit**: Formulir izin/sakit dengan berkas bukti surat opsional yang dapat dilengkapi susulan kapan saja.
* **Riwayat Mandiri dengan Auto-Filter**: Rekapitulasi kehadiran bulanan dengan dropdown interaktif yang otomatis berganti tanpa tombol submit manual (pola interaksi konsisten dengan *SIBENKA*).
* **Dokumentasi Piket KBM**:
  - **Flip Kamera**: Pemilihan fleksibel antara kamera depan (`user`) dan kamera belakang (`environment`).
  - **Kompresi Gambar Sisi Klien**: Kompresi otomatis via HTML5 Canvas (resolusi maksimum 1280px, kualitas JPEG 75%) untuk menghemat kuota dan memori server.
  - **Watermark Kedinasan**: Stempel waktu otomatis, nama pengguna, dan identitas resmi SMKN 3 Yogyakarta.

### 👨‍🏫 2. Guru Pamong / Pembimbing
* **Monitoring Harian**: Verifikasi presensi harian per jurusan mahasiswa dengan filter reaktif instan.
* **Inspeksi Lokasi GPS**: Tautan langsung koordinat GPS presensi datang ke Google Maps.
* **Pembaruan Status Presensi**: Otoritas mengubah status (Hadir, Terlambat, Izin, Sakit, Alpa) dan meninjau berkas surat perizinan.
* **Laporan Bulanan Berkop Dinas Skagata**: Format laporan kedinasan cetak fisik / PDF dilengkapi **Kop Resmi SMK Negeri 3 Yogyakarta** dan lembar pengesahan tanda tangan ganda.
* **Unduhan Excel Native**: Ekspor rekapitulasi kehadiran bulanan dalam format `.xls` bersih dengan perhitungan persentase kehadiran efektif.
* **Laporan Piket KBM**: Rekapitulasi foto kegiatan piket harian beserta modal penampil foto beresolusi optimal.

### ⚙️ 3. Administrator (Superadmin)
* **Manajemen Pengguna (CRUD)**: Kelola akun Guru Pamong, GTT, dan Mahasiswa praktikan lengkap dengan atribut jurusan dan status keaktifan.
* **Auto-Filter Pengguna**: Pencarian nama/username dan penyaringan role/jurusan otomatis (*reactive on-change*).
* **Peta Geofencing Interaktif (Leaflet.js)**:
  - Titik pusat sekolah visual dengan *draggable marker* di Jl. R.W. Monginsidi No. 2, Jetis, Yogyakarta (`-7.780120`, `110.366450`).
  - Slider kalibrasi radius perimeter visual (10m s.d. 1000m) dengan lingkaran *Skagata Emerald*.
  - Tombol deteksi lokasi GPS perangkat admin secara langsung.
  - Toggle toleransi darurat (on/off geofence) jika terjadi gangguan sinyal GPS massal.
* **Pengaturan Jam Kerja Sekolah**: Konfigurasi dinamis jam masuk maksimal dan jam pulang minimal yang langsung tersinkronisasi ke seluruh sistem.
* **Reset Password & Profil Terpisah**: Fasilitas reset password bagi akun pengguna serta pengelolaan kredensial superadmin mandiri.

### 🛡️ 4. Keamanan Sistem & Audit Perlindungan
* **Role-Based Access Control (RBAC)**: Proteksi rute berbasis middleware [`AuthFilter`](app/Filters/AuthFilter.php) dan [`RoleFilter`](app/Filters/RoleFilter.php).
* **Proteksi File Upload Ketat**: Validasi MIME type, verifikasi keaslian binary gambar melalui `getimagesizefromstring()`, hashing nama file unik, dan blokir eksekusi skrip PHP di folder upload via `.htaccess`.
* **Anti SQL Injection & XSS**: Parameterized query / escaping di seluruh query builder dan sanitasi output HTML menggunakan `esc()`.
* **Proteksi CSRF**: Perlindungan CSRF token pada seluruh manipulasi data (POST / DELETE).

---

## 🎨 Desain Sistem: Skagata Emerald

Antarmuka SIPENSI SKAGATA dirancang dengan filosofi **elegan, minimalis, dan terukur**:

| Token Warna | Nilai Hex | Penggunaan |
| :--- | :---: | :--- |
| **Skagata Emerald** | `#0f5132` | Header navbar, tombol aksi primer, kartu sorotan |
| **Deep Forest** | `#0a3622` | Header tabel kedinasan, hover state tombol utama |
| **Vibrant Mint** | `#10b981` | Indikator Hadir, lingkaran aktif geofencing |
| **Amber Gold** | `#d97706` | Indikator Terlambat, aksen peringatan |
| **Slate Surface** | `#f8fafc` | Latar belakang lembut seluruh halaman |
| **Slate Text** | `#0f172a` | Tipografi judul dan teks utama |

Di bagian bawah seluruh halaman disematkan atribusi resmi:
> *Crafted with ❤️ by [awanbeo.my.id](https://awanbeo.my.id) &bull; SMK Negeri 3 Yogyakarta*

---

## 💻 Persyaratan Sistem

* **PHP**: Versi `8.2` atau lebih tinggi
* **Ekstensi PHP**: `intl`, `mbstring`, `json`, `mysqli`, `fileinfo`, `gd`
* **Web Server**: Apache (Laragon / XAMPP)
* **Database**: MySQL 5.7+ / MariaDB 10.4+
* **Browser**: Chrome, Firefox, Safari, atau Edge modern (dengan izin akses Kamera & Lokasi GPS)

---

## 🚀 Panduan Instalasi Lokal

### 1. Clone Repositori
```bash
git clone https://github.com/yodi-dev/sipensi-skagata.git
cd sipensi-skagata
```

### 2. Konfigurasi Environment
Salin file konfigurasi `.env.example` menjadi `.env`:
```bash
# Windows Command Prompt / PowerShell:
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

### 3. Setup Database & Seeder
Buat database baru di MySQL dengan nama `db_presensi`, lalu jalankan database seeder untuk menginisialisasi tabel dan data pengujian Skagata:
```bash
php spark db:seed DatabaseSeeder
```

### 4. Menjalankan Server Lokal
```bash
php spark serve
```
Akses aplikasi melalui peramban: `http://localhost:8080` (atau via virtual host Laragon: `http://presensi-ppl.test`).

---

## 🔑 Kredensial Akun Pengujian / Demo

| Peran | Username | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | Superadmin Sistem Skagata |
| **Guru Pamong** | `febriyana` | `secret` | Pembimbing Jurusan Informatika |
| **Guru Pamong** | `jumari` | `secret` | Pembimbing Jurusan Kejuruan |
| **Mahasiswa Praktikan** | `awan` | `secret` | Praktikan Pend. Teknik Informatika |
| **Mahasiswa Praktikan** | `fajar` | `secret` | Praktikan Pend. Teknik Informatika |
| **Mahasiswa Praktikan** | `latifah` | `secret` | Praktikan Bimbingan Konseling (BK) |
| **Mahasiswa Praktikan** | `dewi` | `secret` | Praktikan Pend. Jasmani (PJOK) |
| **Mahasiswa Praktikan** | `budi` | `secret` | Praktikan Pend. Teknik Elektro (TL) |

---

## 🧪 Pengujian Otomatis (Automated Test Suite)

SIPENSI SKAGATA dilengkapi dengan test runner otomatis mandiri yang menguji 44 skenario keamanan dan fungsionalitas:

```bash
php tests/runner.php
```

### Lingkup Pengujian (44 Skenario - 100% PASS):
1. **Proteksi Tamu (`AuthFilter`)**: Penolakan akses tamu tanpa sesi login.
2. **Pemisahan Peran (`RoleFilter`)**: Verifikasi batas kewenangan Mahasiswa, Guru, dan Admin.
3. **Keamanan File Upload**: Validasi format binary gambar palsu, pemblokiran ekstensi ganda, dan file `.htaccess`.
4. **Penanggulangan SQL Injection**: Sanitasi parameter filter tanggal dan integer casting bulan/tahun.
5. **Sanitasi XSS**: Uji netralisasi tag script dan atribut manipulasi HTML.
6. **Kebersihan Kredensial**: Pemeriksaan file `.gitignore` dan `.env.example`.
7. **Geofencing Haversine**: Verifikasi presisi radius GPS dan penolakan presensi di luar perimeter.
8. **Ketentuan Jam Kerja**: Validasi deteksi status terlambat dan pencegahan checkout dini.
9. **Manajemen Pengguna Admin**: Hak akses admin dan larangan self-delete akun admin.
10. **Laporan & Status Whitelist**: Integritas status kehadiran dan ekspor spreadsheet.
11. **Modul Pengaturan Dinamis**: Validasi model konfigurasi dan toggle toleransi darurat.
12. **Hari Kerja Efektif & Skagata Branding**: Pengecualian hari libur akhir pekan (Sabtu-Minggu) dan integritas identitas institusi SMKN 3 Yogyakarta.

---

## 📁 Struktur Direktori Proyek

```
presensi-ppl/
├── app/
│   ├── Config/          # Konfigurasi aplikasi, presensi GPS, routes, dan filter
│   ├── Controllers/     # Controller Admin, Auth, Guru, dan Mahasiswa
│   ├── Database/        # Seeder data pengguna, pengaturan, dan presensi
│   ├── Filters/         # Middleware AuthFilter dan RoleFilter
│   ├── Models/          # Model UserModel, PresensiModel, PiketModel, SettingModel
│   └── Views/           # Antarmuka tampilan (layout, admin, auth, guru, mahasiswa)
├── public/
│   ├── uploads/         # Direktori berkas surat & foto piket (dilindungi .htaccess)
│   ├── favicon.png      # Favicon web
│   └── index.php        # Front controller aplikasi
├── tests/
│   └── runner.php       # Test runner mandiri CodeIgniter 4 (44 tests)
├── .env.example         # Template konfigurasi environment bersih
├── .gitignore           # Konfigurasi pengabaian file sensitif
├── PRD.md               # Product Requirements Document (PRD) v2.0 Resmi
└── README.md            # Dokumentasi proyek utama
```

---

## 📄 Kredit & Lisensi

Dikembangkan dengan dedikasi untuk menunjang kegiatan operasional dan Praktik Pengalaman Lapangan di lingkungan **SMK Negeri 3 Yogyakarta**.

* **Institusi Tuan Rumah**: SMK Negeri 3 Yogyakarta
* **Lead Developer**: Yodi Irawan
* **Kredit Resmi**: *Crafted with ❤️ by [awanbeo.my.id](https://awanbeo.my.id)*
