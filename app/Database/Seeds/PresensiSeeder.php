<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PresensiSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('presensi')->truncate();
        $this->db->table('piket_kbm')->truncate();
        $this->db->enableForeignKeyChecks();

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $twoDaysAgo = date('Y-m-d', strtotime('-2 days'));

        // Query ID mahasiswa yang baru saja di-seed
        $mahasiswa = $this->db->table('users')
            ->where('role', 'mahasiswa')
            ->get()
            ->getResultArray();

        if (empty($mahasiswa)) {
            return;
        }

        $presensiData = [];
        $piketData = [];

        foreach ($mahasiswa as $idx => $mhs) {
            $userId = (int) $mhs['id'];

            // Presensi Hari Ini (dengan variasi status untuk demo/testing)
            if ($idx % 5 === 0) {
                // Tepat Waktu (Hadir)
                $presensiData[] = [
                    'user_id'    => $userId,
                    'tanggal'    => $today,
                    'status'     => 'hadir',
                    'jam_masuk'  => '07:05:12',
                    'jam_keluar' => null,
                    'latitude'   => '-7.795620',
                    'longitude'  => '110.369510',
                    'keterangan' => 'Hadir tepat waktu'
                ];
            } elseif ($idx % 5 === 1) {
                // Terlambat
                $presensiData[] = [
                    'user_id'    => $userId,
                    'tanggal'    => $today,
                    'status'     => 'terlambat',
                    'jam_masuk'  => '07:22:45',
                    'jam_keluar' => null,
                    'latitude'   => '-7.795590',
                    'longitude'  => '110.369480',
                    'keterangan' => 'Terlambat karena macet'
                ];
            } elseif ($idx % 5 === 2) {
                // Izin
                $presensiData[] = [
                    'user_id'    => $userId,
                    'tanggal'    => $today,
                    'status'     => 'izin',
                    'jam_masuk'  => '06:55:00',
                    'jam_keluar' => null,
                    'latitude'   => null,
                    'longitude'  => null,
                    'keterangan' => 'Izin menghadiri bimbingan skripsi di kampus'
                ];
            } elseif ($idx % 5 === 3) {
                // Sakit
                $presensiData[] = [
                    'user_id'    => $userId,
                    'tanggal'    => $today,
                    'status'     => 'sakit',
                    'jam_masuk'  => '06:40:00',
                    'jam_keluar' => null,
                    'latitude'   => null,
                    'longitude'  => null,
                    'keterangan' => 'Demam dan flu, istirahat dokter'
                ];
            }
            // ($idx % 5 === 4) dibiarkan belum absen untuk testing

            // Presensi Kemarin (Data Selesai Masuk & Pulang)
            $presensiData[] = [
                'user_id'    => $userId,
                'tanggal'    => $yesterday,
                'status'     => ($idx % 4 === 1) ? 'terlambat' : 'hadir',
                'jam_masuk'  => ($idx % 4 === 1) ? '07:20:10' : '07:02:40',
                'jam_keluar' => '15:15:30',
                'latitude'   => '-7.795610',
                'longitude'  => '110.369520',
                'keterangan' => 'Presensi harian'
            ];

            // Presensi 2 Hari Lalu
            $presensiData[] = [
                'user_id'    => $userId,
                'tanggal'    => $twoDaysAgo,
                'status'     => 'hadir',
                'jam_masuk'  => '07:08:15',
                'jam_keluar' => '15:20:00',
                'latitude'   => '-7.795600',
                'longitude'  => '110.369500',
                'keterangan' => null
            ];
        }

        if (!empty($presensiData)) {
            $this->db->table('presensi')->insertBatch($presensiData);
        }

        // Demo Data Piket KBM
        $firstStudentId = (int) $mahasiswa[0]['id'];
        $secondStudentId = isset($mahasiswa[1]) ? (int) $mahasiswa[1]['id'] : $firstStudentId;

        $piketData = [
            [
                'user_id'    => $firstStudentId,
                'tanggal'    => $today,
                'waktu'      => '07:30:00',
                'foto_bukti' => 'piket_demo_1.jpg'
            ],
            [
                'user_id'    => $secondStudentId,
                'tanggal'    => $yesterday,
                'waktu'      => '08:00:15',
                'foto_bukti' => 'piket_demo_2.jpg'
            ]
        ];

        $this->db->table('piket_kbm')->insertBatch($piketData);
    }
}
