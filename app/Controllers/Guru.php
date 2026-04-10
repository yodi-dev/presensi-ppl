<?php

namespace App\Controllers;

use App\Models\UserModel;

class Guru extends BaseController
{
    public function index()
    {
        // Pastikan hanya guru yang bisa akses
        if (session()->get('role') != 'guru') {
            return redirect()->to('/auth');
        }

        $db      = \Config\Database::connect();
        $builder = $db->table('users');

        $tanggalFilter = $this->request->getGet('tanggal');

        $tanggalPilih = $tanggalFilter ? $tanggalFilter : date('Y-m-d');

        // Mengambil semua user dengan role 'mahasiswa' dan di-JOIN dengan tabel presensi hari ini
        $builder->select('users.id, users.nama, presensi.status, presensi.jam_masuk, presensi.jam_keluar, presensi.keterangan, presensi.latitude, presensi.longitude');
        $builder->join('presensi', "presensi.user_id = users.id AND presensi.tanggal = '$tanggalPilih'", 'left');
        $builder->where('users.role', 'mahasiswa');

        $data = [
            'tanggal'  => $tanggalPilih,
            'presensi' => $builder->get()->getResultArray()
        ];

        return view('guru', $data); // atau 'guru/index' tergantung struktur foldermu
    }

    public function laporan()
    {
        if (session()->get('role') != 'guru') {
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();

        // Tangkap request filter, kalau kosong pakai bulan dan tahun saat ini
        $bulanFilter = $this->request->getGet('bulan') ?: date('m');
        $tahunFilter = $this->request->getGet('tahun') ?: date('Y');

        $builder = $db->table('users');

        // Trik Query: Hitung total masing-masing status langsung dari database
        $builder->select("
            users.id, 
            users.nama,
            SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
            SUM(CASE WHEN presensi.status = 'izin' THEN 1 ELSE 0 END) as total_izin,
            SUM(CASE WHEN presensi.status = 'sakit' THEN 1 ELSE 0 END) as total_sakit
        ");

        // JOIN dengan kondisi filter Bulan dan Tahun
        $builder->join('presensi', "presensi.user_id = users.id AND MONTH(presensi.tanggal) = '$bulanFilter' AND YEAR(presensi.tanggal) = '$tahunFilter'", 'left');

        $builder->where('users.role', 'mahasiswa');
        $builder->groupBy('users.id, users.nama'); // Kelompokkan berdasarkan mahasiswa

        $data = [
            'bulan_pilih' => $bulanFilter,
            'tahun_pilih' => $tahunFilter,
            'laporan'     => $builder->get()->getResultArray()
        ];

        return view('laporan', $data); // Arahkan ke file view baru
    }

    // Fungsi untuk menampilkan form ubah password Guru
    public function ubahPassword()
    {
        // Proteksi, pastikan yang akses beneran guru
        if (session()->get('role') != 'guru') {
            return redirect()->to('/auth');
        }

        return view('guru/ubah_password'); // Asumsi view-nya ditaruh di dalam folder 'guru'
    }

    // Fungsi untuk memproses perubahan password
    public function prosesUbahPassword()
    {
        $userModel = new UserModel();
        $userId = session()->get('id_user');

        $passLama = $this->request->getPost('password_lama');
        $passBaru = $this->request->getPost('password_baru');
        $passKonfirm = $this->request->getPost('konfirmasi_password');

        $user = $userModel->find($userId);

        // Validasi password lama
        if (!password_verify($passLama, $user['password'])) {
            session()->setFlashdata('error', 'Password lama salah!');
            return redirect()->to('/guru/ubahpassword'); // Balik ke halaman ubah password guru
        }

        // Validasi password baru & konfirmasi
        if ($passBaru !== $passKonfirm) {
            session()->setFlashdata('error', 'Password baru dan konfirmasi tidak cocok!');
            return redirect()->to('/guru/ubahpassword');
        }

        // Update password baru
        $userModel->update($userId, [
            'password' => password_hash($passBaru, PASSWORD_DEFAULT)
        ]);

        session()->setFlashdata('pesan', 'Password berhasil diubah! Silakan ingat password baru Anda.');
        return redirect()->to('/guru'); // Balikin ke dashboard guru
    }
}
