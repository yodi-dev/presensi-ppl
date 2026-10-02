<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks sementara untuk pembersihan bersih
        $this->db->disableForeignKeyChecks();
        $this->db->table('users')->truncate();
        $this->db->enableForeignKeyChecks();

        $defaultPassword = password_hash('secret', PASSWORD_BCRYPT);
        $adminPassword   = password_hash('admin123', PASSWORD_BCRYPT);

        $data = [
            // 1. Akun Administrator Sistem
            [
                'username' => 'admin',
                'nama'     => 'Administrator Sistem',
                'password' => $adminPassword,
                'role'     => 'admin',
                'jurusan'  => null,
            ],

            // 2. Akun Guru Pamong / Pembimbing
            [
                'username' => 'febriyana',
                'nama'     => 'Ibu Febriyana, S.T.',
                'password' => $defaultPassword,
                'role'     => 'guru',
                'jurusan'  => null,
            ],
            [
                'username' => 'jumari',
                'nama'     => 'Bapak Jumari, S.Pd.T., M.Eng.',
                'password' => $defaultPassword,
                'role'     => 'guru',
                'jurusan'  => null,
            ],

            // 3. Akun Mahasiswa PPL - Jurusan Informatika
            [
                'username' => 'awan',
                'nama'     => 'Yodi Irawan',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'fajar',
                'nama'     => 'Fajar Alamsyah',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],
            [
                'username' => 'ina',
                'nama'     => 'Inarotul Qolbiyah',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'Informatika'
            ],

            // 4. Akun Mahasiswa PPL - Jurusan Bimbingan Konseling (BK)
            [
                'username' => 'latifah',
                'nama'     => 'Nur Latifah',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'BK'
            ],
            [
                'username' => 'asih',
                'nama'     => 'Nur Asih Wiji Astuti',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'BK'
            ],
            [
                'username' => 'dimas',
                'nama'     => 'Dimas Surya Mahendra',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'BK'
            ],

            // 5. Akun Mahasiswa PPL - Jurusan Teknik Listrik (TL)
            [
                'username' => 'fikriy',
                'nama'     => 'Fikriy Abbad Fauzan',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'TL'
            ],
            [
                'username' => 'khoerul',
                'nama'     => 'Muhammad Khoerul Umam',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'TL'
            ],

            // 6. Akun Mahasiswa PPL - Jurusan Teknik Otomotif (TO)
            [
                'username' => 'sendy',
                'nama'     => 'Sendy Diaz Erlangga',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'TO'
            ],
            [
                'username' => 'nuraini',
                'nama'     => 'Nuraini Eka Putri',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'TO'
            ],

            // 7. Akun Mahasiswa PPL - Jurusan PJOK
            [
                'username' => 'afeb',
                'nama'     => 'Afeb Chesa Arianto',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
            [
                'username' => 'panji',
                'nama'     => 'Panji Agung Nugroho',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
            [
                'username' => 'putri',
                'nama'     => 'Putri Diang Pawestri',
                'password' => $defaultPassword,
                'role'     => 'mahasiswa',
                'jurusan'  => 'PJOK'
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
