<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function proses_login()
    {
        // dd($_POST);
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user di database berdasarkan username
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // Cek kecocokan password (sementara kita pakai teks biasa tanpa hash biar gampang testingnya)
            if (password_verify($password, $user['password'])) {

                // Jika benar, simpan data user ke Session
                $dataSession = [
                    'id_user'    => $user['id'],
                    'nama'       => $user['nama'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true
                ];
                $session->set($dataSession);

                // Arahkan ke halaman sesuai role
                if ($user['role'] == 'guru') {
                    return redirect()->to('/guru');
                } else {
                    return redirect()->to('/mahasiswa');
                }
            } else {
                $session->setFlashdata('error', 'Password salah, sob!');
                return redirect()->to('/auth');
            }
        } else {
            $session->setFlashdata('error', 'Username tidak ditemukan!');
            return redirect()->to('/auth');
        }
    }

    // --- METHOD UNTUK MENAMPILKAN VIEW ---
    public function ubahPasswordView()
    {
        // Pastikan user sudah login (cek session)
        if (!session()->get('nama')) {
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('auth/ubah_password');
    }

    // --- METHOD UNTUK MEMPROSES UBAH PASSWORD ---
    public function prosesUbahPassword()
    {
        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasiPassword = $this->request->getPost('konfirmasi_password');

        // 1. Validasi konfirmasi password
        if ($passwordBaru !== $konfirmasiPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok dengan password baru!');
        }

        // 2. Ambil data session
        $idUser = session()->get('id_user');
        $role   = session()->get('role'); // Masih butuh buat redirect nanti

        // 3. Panggil UserModel (Sekarang cukup panggil satu model ini aja)
        $userModel = new UserModel();

        // 4. Cari data user di database berdasarkan ID session
        $user = $userModel->find($idUser);

        // 5. Cek apakah password lama sesuai
        if (!password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama yang kamu masukkan salah!');
        }

        // 6. Enkripsi password baru dan Update ke database
        $userModel->update($idUser, [
            'password' => password_hash($passwordBaru, PASSWORD_DEFAULT)
        ]);

        // 7. Arahkan kembali ke dashboard yang sesuai berdasarkan role
        $redirectUrl = ($role === 'guru') ? '/guru' : '/mahasiswa';
        return redirect()->to($redirectUrl)->with('pesan', 'Mantap! Password berhasil diubah.');
    }

    public function logout()
    {
        // Hancurkan session saat logout
        session()->destroy();
        return redirect()->to('/auth');
    }
}
