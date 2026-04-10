<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Jadikan halaman login sebagai halaman utama saat web dibuka
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');

// Rute untuk proses form dan logout
$routes->post('/auth/proses_login', 'Auth::proses_login');
$routes->get('/auth/logout', 'Auth::logout');

$routes->get('/guru', 'Guru::index');
$routes->get('/mahasiswa', 'Mahasiswa::index');

$routes->post('/mahasiswa/datang', 'Mahasiswa::datang');
$routes->post('/mahasiswa/pulang', 'Mahasiswa::pulang');
$routes->post('/mahasiswa/izin_sakit', 'Mahasiswa::izin_sakit');

$routes->get('/ubah_password', 'Mahasiswa::ubah_password');
$routes->post('/proses_ubah_password', 'Mahasiswa::prosesUbahPassword');

$routes->get('guru/ubahpassword', 'Guru::ubahPassword');
$routes->post('guru/prosesubahpassword', 'Guru::prosesUbahPassword');

$routes->get('guru/laporan', 'Guru::laporan');
