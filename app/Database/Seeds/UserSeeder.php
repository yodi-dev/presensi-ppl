<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'febriyana',
                'nama'     => 'Ibu Febriyana, S.T.',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'guru'
            ],
            [
                'username' => 'jumari',
                'nama'     => 'Bapak Jumari, S.Pd.T., M.Eng.',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'guru'
            ],
            [
                'username' => 'awan',
                'nama'     => 'Yodi Irawan',
                'password' => password_hash('secret12', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
            [
                'username' => 'fajar',
                'nama'     => 'Fajar Alamsyah',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
            [
                'username' => 'ina',
                'nama'     => 'Inarotul Qolbiyah',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
            [
                'username' => 'zul',
                'nama'     => 'Zulfikar Wismanda P.N.R',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
            [
                'username' => 'adi',
                'nama'     => 'Adi Chandra Winata',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
            [
                'username' => 'adit',
                'nama'     => 'Nur Aditya Romadhon',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
            [
                'username' => 'arkan',
                'nama'     => 'Arkan Bhanu Kurniadi',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
            [
                'username' => 'rayhan',
                'nama'     => 'Rayhan Pangestu Wibowo',
                'password' => password_hash('12345', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa'
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
