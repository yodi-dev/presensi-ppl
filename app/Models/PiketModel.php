<?php

namespace App\Models;

use CodeIgniter\Model;

class PiketModel extends Model
{
    protected $table            = 'piket_kbm';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom yang diizinkan untuk diisi
    protected $allowedFields    = [
        'user_id',
        'tanggal',
        'waktu',
        'foto_bukti'
    ];

    // Fungsi tambahan untuk ngambil data piket sekalian sama nama & jurusan mahasiswanya
    public function getPiketWithUser()
    {
        return $this->select('piket_kbm.*, users.nama, users.jurusan')
            ->join('users', 'users.id = piket_kbm.user_id')
            ->orderBy('piket_kbm.tanggal', 'DESC')
            ->orderBy('piket_kbm.waktu', 'DESC')
            ->findAll();
    }
}
