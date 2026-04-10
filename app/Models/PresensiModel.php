<?php

namespace App\Models;

use CodeIgniter\Model;

class PresensiModel extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id';
    // Kolom yang diizinkan untuk diisi
    protected $allowedFields    = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'latitude',
        'longitude',
        // 'lat_masuk',     // (kalau kamu pakai opsi 4 kolom lokasi)
        // 'long_masuk',    // (kalau kamu pakai opsi 4 kolom lokasi)
        // 'lat_keluar',    // (kalau kamu pakai opsi 4 kolom lokasi)
        // 'long_keluar',   // (kalau kamu pakai opsi 4 kolom lokasi)
        'status',        // TAMBAHKAN INI
        'keterangan'     // TAMBAHKAN INI
    ];

    public function getPresensiLengkap()
    {
        return $this->select('presensi.*, users.nama')
            ->join('users', 'users.id = presensi.user_id')
            ->orderBy('presensi.tanggal', 'DESC')
            ->orderBy('presensi.jam_masuk', 'DESC')
            ->findAll();
    }
}
