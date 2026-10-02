<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Jadikan halaman login sebagai halaman utama saat web dibuka
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/proses_login', 'Auth::proses_login');
// Route yang membutuhkan autentikasi umum
$routes->get('/ubah_password', 'Auth::ubahPasswordView', ['filter' => 'auth']);
$routes->post('/auth/proses_ubah_password', 'Auth::prosesUbahPassword', ['filter' => 'auth']);
$routes->get('/auth/logout', 'Auth::logout');

// Rute khusus Guru
$routes->group('guru', ['filter' => ['auth', 'role:guru']], static function ($routes) {
    $routes->get('/', 'Guru::index');
    $routes->get('laporan', 'Guru::laporan');
    $routes->get('laporan/export-excel', 'Guru::exportExcel');
    $routes->get('laporan_piket', 'Guru::laporanPiket');
    $routes->post('update-status', 'Guru::update_status');
});

// Rute khusus Mahasiswa
$routes->group('mahasiswa', ['filter' => ['auth', 'role:mahasiswa']], static function ($routes) {
    $routes->get('/', 'Mahasiswa::index');
    $routes->post('datang', 'Mahasiswa::datang');
    $routes->post('pulang', 'Mahasiswa::pulang');
    $routes->post('izin_sakit', 'Mahasiswa::izin_sakit');
    $routes->get('riwayat', 'Mahasiswa::riwayat');
    $routes->post('upload-bukti-susulan', 'Mahasiswa::uploadBuktiSusulan');
    $routes->get('piket', 'Mahasiswa::piket');
    $routes->post('simpan-piket', 'Mahasiswa::simpanPiket');
});

// Rute khusus Admin
$routes->group('admin', ['filter' => ['auth', 'role:admin']], static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->post('tambah-user', 'Admin::tambahUser');
    $routes->post('edit-user', 'Admin::editUser');
    $routes->post('hapus-user', 'Admin::hapusUser');
    $routes->post('reset-password', 'Admin::resetPassword');
});
