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

    public function pengaturan()
    {
        $config = config('Presensi') ?? new \Config\Presensi();
        $settings = \App\Models\SettingModel::getAllSettings();

        $data = [
            'school_name'      => $settings['school_name'] ?? $config->schoolName,
            'school_latitude'  => (float) ($settings['school_latitude'] ?? $config->schoolLatitude),
            'school_longitude' => (float) ($settings['school_longitude'] ?? $config->schoolLongitude),
            'school_radius'    => (int) ($settings['school_radius'] ?? $config->schoolRadius),
            'jam_masuk_max'    => $settings['jam_masuk_max'] ?? $config->jamMasukMax,
            'jam_pulang_min'   => $settings['jam_pulang_min'] ?? $config->jamPulangMin,
            'geofence_active'  => isset($settings['geofence_active']) ? ($settings['geofence_active'] === '1' || $settings['geofence_active'] === 'true') : $config->geofenceActive,
            'title'            => 'Pengaturan Lokasi & Jam Presensi - Admin'
        ];

        return view('admin/pengaturan', $data);
    }

    public function simpanPengaturan()
    {
        $schoolName = trim(strip_tags((string) $this->request->getPost('school_name')));
        $lat = $this->request->getPost('school_latitude');
        $lng = $this->request->getPost('school_longitude');
        $radius = $this->request->getPost('school_radius');
        $jamMasuk = trim((string) $this->request->getPost('jam_masuk_max'));
        $jamPulang = trim((string) $this->request->getPost('jam_pulang_min'));
        $geofenceActive = $this->request->getPost('geofence_active') ? '1' : '0';

        // Validasi input
        if (empty($schoolName) || mb_strlen($schoolName) < 3) {
            return redirect()->back()->withInput()->with('error', 'Nama institusi/sekolah minimal 3 karakter!');
        }

        if (!is_numeric($lat) || (float) $lat < -90 || (float) $lat > 90) {
            return redirect()->back()->withInput()->with('error', 'Latitude harus berupa angka valid antara -90 dan 90!');
        }

        if (!is_numeric($lng) || (float) $lng < -180 || (float) $lng > 180) {
            return redirect()->back()->withInput()->with('error', 'Longitude harus berupa angka valid antara -180 dan 180!');
        }

        if (!is_numeric($radius) || (int) $radius < 10 || (int) $radius > 5000) {
            return redirect()->back()->withInput()->with('error', 'Radius harus berupa angka antara 10 sampai 5000 meter!');
        }

        // Format jam HH:MM atau HH:MM:SS
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $jamMasuk)) {
            return redirect()->back()->withInput()->with('error', 'Format Jam Masuk Maksimal tidak valid (HH:MM atau HH:MM:SS)!');
        }
        if (strlen($jamMasuk) === 5) {
            $jamMasuk .= ':00';
        }

        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $jamPulang)) {
            return redirect()->back()->withInput()->with('error', 'Format Jam Pulang Minimal tidak valid (HH:MM atau HH:MM:SS)!');
        }
        if (strlen($jamPulang) === 5) {
            $jamPulang .= ':00';
        }

        // Simpan ke SettingModel
        \App\Models\SettingModel::setSetting('school_name', $schoolName);
        \App\Models\SettingModel::setSetting('school_latitude', (string) ((float) $lat));
        \App\Models\SettingModel::setSetting('school_longitude', (string) ((float) $lng));
        \App\Models\SettingModel::setSetting('school_radius', (string) ((int) $radius));
        \App\Models\SettingModel::setSetting('jam_masuk_max', $jamMasuk);
        \App\Models\SettingModel::setSetting('jam_pulang_min', $jamPulang);
        \App\Models\SettingModel::setSetting('geofence_active', $geofenceActive);

        return redirect()->to('/admin/pengaturan')->with('pesan', 'Pengaturan lokasi presensi dan jam kerja berhasil disimpan!');
    }
}
