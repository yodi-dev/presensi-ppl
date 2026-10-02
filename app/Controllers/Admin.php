<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Database;

class Admin extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $db = Database::connect();
        $builder = $db->table('users');

        // Parameter pencarian & filter
        $keyword = trim((string) $this->request->getGet('keyword'));
        $roleFilter = $this->request->getGet('role');
        $jurusanFilter = $this->request->getGet('jurusan');

        // Hitung statistik keseluruhan
        $totalUsers = (clone $builder)->countAllResults();
        $totalMahasiswa = (clone $builder)->where('role', 'mahasiswa')->countAllResults();
        $totalGuru = (clone $builder)->where('role', 'guru')->countAllResults();
        $totalAdmin = (clone $builder)->where('role', 'admin')->countAllResults();

        // Terapkan filter query
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('nama', $keyword)
                ->orLike('username', $keyword)
                ->groupEnd();
        }

        if (!empty($roleFilter) && in_array($roleFilter, ['mahasiswa', 'guru', 'admin'], true)) {
            $builder->where('role', $roleFilter);
        }

        if (!empty($jurusanFilter)) {
            $builder->where('jurusan', $jurusanFilter);
        }

        $users = $builder->orderBy('role', 'ASC')
            ->orderBy('nama', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'users'          => $users,
            'totalUsers'     => $totalUsers,
            'totalMahasiswa' => $totalMahasiswa,
            'totalGuru'      => $totalGuru,
            'totalAdmin'     => $totalAdmin,
            'keyword'        => $keyword,
            'role_terpilih'  => $roleFilter,
            'jurusan_pilih'  => $jurusanFilter,
            'title'          => 'Manajemen Pengguna - Admin Presensi PPL'
        ];

        return view('admin/index', $data);
    }

    public function tambahUser()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric|is_unique[users.username]',
            'nama'     => 'required|min_length[2]|max_length[100]',
            'role'     => 'required|in_list[mahasiswa,guru,admin]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            $errors = implode(' ', $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', $errors);
        }

        $username = trim((string) $this->request->getPost('username'));
        $nama     = trim(strip_tags((string) $this->request->getPost('nama')));
        $role     = (string) $this->request->getPost('role');
        $jurusan  = trim((string) $this->request->getPost('jurusan'));
        $password = (string) $this->request->getPost('password');

        $this->userModel->insert([
            'username' => $username,
            'nama'     => $nama,
            'role'     => $role,
            'jurusan'  => !empty($jurusan) ? $jurusan : null,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);

        return redirect()->to('/admin')->with('pesan', "Pengguna {$nama} ({$username}) berhasil ditambahkan!");
    }

    public function editUser()
    {
        $userId = $this->request->getPost('id');
        if (empty($userId) || !is_numeric($userId)) {
            return redirect()->back()->with('error', 'ID pengguna tidak valid!');
        }

        $existingUser = $this->userModel->find($userId);
        if (!$existingUser) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan!');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|alpha_numeric|is_unique[users.username,id,{$userId}]",
            'nama'     => 'required|min_length[2]|max_length[100]',
            'role'     => 'required|in_list[mahasiswa,guru,admin]'
        ];

        if (!$this->validate($rules)) {
            $errors = implode(' ', $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', $errors);
        }

        $username = trim((string) $this->request->getPost('username'));
        $nama     = trim(strip_tags((string) $this->request->getPost('nama')));
        $role     = (string) $this->request->getPost('role');
        $jurusan  = trim((string) $this->request->getPost('jurusan'));

        $this->userModel->update($userId, [
            'username' => $username,
            'nama'     => $nama,
            'role'     => $role,
            'jurusan'  => !empty($jurusan) ? $jurusan : null
        ]);

        return redirect()->to('/admin')->with('pesan', "Data pengguna {$nama} berhasil diperbarui!");
    }

    public function hapusUser()
    {
        $userId = $this->request->getPost('user_id');
        $currentAdminId = session()->get('id_user');

        if (empty($userId) || !is_numeric($userId)) {
            return redirect()->back()->with('error', 'ID pengguna tidak valid!');
        }

        if ((int) $userId === (int) $currentAdminId) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang aktif login!');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan!');
        }

        // Hapus data presensi dan piket terkait terlebih dahulu
        $db = Database::connect();
        $db->table('presensi')->where('user_id', $userId)->delete();
        $db->table('piket_kbm')->where('user_id', $userId)->delete();

        // Hapus pengguna
        $this->userModel->delete($userId);

        return redirect()->to('/admin')->with('pesan', "Pengguna {$user['nama']} ({$user['username']}) berhasil dihapus!");
    }

    public function resetPassword()
    {
        $userId = $this->request->getPost('user_id');
        $newPassword = $this->request->getPost('new_password');

        if (empty($userId) || !is_numeric($userId)) {
            return redirect()->back()->with('error', 'ID pengguna tidak valid!');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan!');
        }

        $passwordToSet = !empty($newPassword) ? (string) $newPassword : 'password123';

        if (strlen($passwordToSet) < 6) {
            return redirect()->back()->with('error', 'Password minimal 6 karakter!');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($passwordToSet, PASSWORD_BCRYPT)
        ]);

        return redirect()->to('/admin')->with('pesan', "Password untuk pengguna {$user['nama']} berhasil direset!");
    }
}
