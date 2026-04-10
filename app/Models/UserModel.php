<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // Nama tabel di database
    protected $table = 'users';

    // Primary key tabel
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi/diambil
    protected $allowedFields = ['username', 'nama', 'password', 'role'];
}
