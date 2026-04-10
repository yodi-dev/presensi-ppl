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

    public function logout()
    {
        // Hancurkan session saat logout
        session()->destroy();
        return redirect()->to('/auth');
    }
}
