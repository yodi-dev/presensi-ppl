<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Jadikan halaman login sebagai halaman utama saat web dibuka
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/proses_login', 'Auth::proses_login');
$routes->get('/ubah_password', 'Auth::ubahPasswordView');
$routes->post('/auth/proses_ubah_password', 'Auth::prosesUbahPassword');
$routes->get('/auth/logout', 'Auth::logout');

$routes->get('/guru', 'Guru::index');
$routes->get('guru/laporan', 'Guru::laporan');
$routes->get('guru/laporan_piket', 'Guru::laporanPiket');
$routes->get('/guru/update_status/(:num)/(:segment)', 'Guru::update_status/$1/$2');

$routes->get('/mahasiswa', 'Mahasiswa::index');
$routes->post('/mahasiswa/datang', 'Mahasiswa::datang');
$routes->post('/mahasiswa/pulang', 'Mahasiswa::pulang');
$routes->post('/mahasiswa/izin_sakit', 'Mahasiswa::izin_sakit');
$routes->get('/mahasiswa/piket', 'Mahasiswa::piket');
$routes->post('/mahasiswa/simpan-piket', 'Mahasiswa::simpanPiket');
