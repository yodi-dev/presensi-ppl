<?php

namespace App\Controllers;

use App\Models\PresensiModel;
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
        $tanggalHariIni = date('Y-m-d');

        // Mengambil semua user dengan role 'mahasiswa' dan di-JOIN dengan tabel presensi hari ini
        $builder->select('users.id, users.nama, presensi.status, presensi.jam_masuk, presensi.jam_keluar, presensi.keterangan, presensi.latitude, presensi.longitude');
        $builder->join('presensi', "presensi.user_id = users.id AND presensi.tanggal = '$tanggalHariIni'", 'left');
        $builder->where('users.role', 'mahasiswa');

        $data = [
            'tanggal'  => $tanggalHariIni,
            'presensi' => $builder->get()->getResultArray()
        ];

        return view('guru', $data); // atau 'guru/index' tergantung struktur foldermu
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
