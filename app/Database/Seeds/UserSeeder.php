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
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'guru',
                'jurusan' => null,
            ],
            [
                'username' => 'jumari',
                'nama'     => 'Bapak Jumari, S.Pd.T., M.Eng.',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'guru',
                'jurusan' => null,
            ],
            [
                'username' => 'awan',
                'nama'     => 'Yodi Irawan',
                'password' => password_hash('secret12', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'fajar',
                'nama'     => 'Fajar Alamsyah',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'ina',
                'nama'     => 'Inarotul Qolbiyah',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'zul',
                'nama'     => 'Zulfikar Wismanda P.N.R',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'adi',
                'nama'     => 'Adi Chandra Winata',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'adit',
                'nama'     => 'Nur Aditya Romadhon',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'arkan',
                'nama'     => 'Arkan Bhanu Kurniadi',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'rayhan',
                'nama'     => 'Rayhan Pangestu Wibowo',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'latifah',
                'nama'     => 'Nur Latifah',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'BK'
            ],
            [
                'username' => 'asih',
                'nama'     => 'Nur Asih Wiji Astuti',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'BK'
            ],
            [
                'username' => 'erin',
                'nama'     => 'Erin Mustikawati',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'BK'
            ],
            [
                'username' => 'dimas',
                'nama'     => 'Dimas Surya Mahendra',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'BK'
            ],
            [
                'username' => 'fikriy',
                'nama'     => 'Fikriy Abbad Fauzan',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'TL'
            ],
            [
                'username' => 'khoerul',
                'nama'     => 'Muhammad Khoerul Umam',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'TL'
            ],
            [
                'username' => 'sendy',
                'nama'     => 'Sendy Diaz Erlangga',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'TO'
            ],
            [
                'username' => 'nuraini',
                'nama'     => 'Nuraini Eka Putri',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'TO'
            ],
            [
                'username' => 'firman',
                'nama'     => 'Firmansyah Arya Pangestu',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'TO'
            ],
            [
                'username' => 'afeb',
                'nama'     => 'Afeb Chesa Arianto',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
            [
                'username' => 'panji',
                'nama'     => 'Panji Agung Nugrobo',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
            [
                'username' => 'prasetyo',
                'nama'     => 'Adi Prasetyo',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
            [
                'username' => 'putri',
                'nama'     => 'Putri Diang Pawestri',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
            [
                'username' => 'fauzan',
                'nama'     => 'Muhammad Fauzan Arrasyid',
                'password' => password_hash('secret', PASSWORD_BCRYPT),
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
