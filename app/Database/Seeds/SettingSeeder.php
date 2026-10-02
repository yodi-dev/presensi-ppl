<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('settings')->truncate();

        $now = date('Y-m-d H:i:s');
        $data = [
            [
                'setting_key'   => 'school_name',
                'setting_value' => 'SMK Negeri 2 Yogyakarta',
                'updated_at'    => $now
            ],
            [
                'setting_key'   => 'school_latitude',
                'setting_value' => '-7.795600',
                'updated_at'    => $now
            ],
            [
                'setting_key'   => 'school_longitude',
                'setting_value' => '110.369500',
                'updated_at'    => $now
            ],
            [
                'setting_key'   => 'school_radius',
                'setting_value' => '100',
                'updated_at'    => $now
            ],
            [
                'setting_key'   => 'jam_masuk_max',
                'setting_value' => '07:15:00',
                'updated_at'    => $now
            ],
            [
                'setting_key'   => 'jam_pulang_min',
                'setting_value' => '15:00:00',
                'updated_at'    => $now
            ],
            [
                'setting_key'   => 'geofence_active',
                'setting_value' => '1',
                'updated_at'    => $now
            ],
        ];

        $this->db->table('settings')->insertBatch($data);
    }
}
