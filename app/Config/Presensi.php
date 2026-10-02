<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Presensi extends BaseConfig
{
    /**
     * Nama Sekolah / Institusi
     */
    public string $schoolName = 'SMK Negeri 2 Yogyakarta';

    /**
     * Titik Koordinat Pusat Sekolah / Tempat PPL
     */
    public float $schoolLatitude = -7.7956;
    public float $schoolLongitude = 110.3695;

    /**
     * Radius toleransi kehadiran dalam satuan meter (default: 100 meter)
     */
    public int $schoolRadius = 100;

    /**
     * Batas waktu kehadiran normal.
     * Jika absen datang setelah jam ini, status otomatis 'terlambat'.
     */
    public string $jamMasukMax = '07:15:00';

    /**
     * Jam pulang resmi minimal.
     * Mahasiswa tidak diizinkan absen pulang sebelum jam ini.
     */
    public string $jamPulangMin = '15:00:00';

    /**
     * Status aktif Geofencing (jika false, radius tidak diperiksa/mode toleransi).
     */
    public bool $geofenceActive = true;

    public function __construct()
    {
        parent::__construct();

        // Ambil konfigurasi dinamis dari database bila tersedia
        try {
            if (class_exists(\App\Models\SettingModel::class)) {
                $settings = \App\Models\SettingModel::getAllSettings();
                if (!empty($settings)) {
                    if (isset($settings['school_name'])) $this->schoolName = (string) $settings['school_name'];
                    if (isset($settings['school_latitude']) && is_numeric($settings['school_latitude'])) $this->schoolLatitude = (float) $settings['school_latitude'];
                    if (isset($settings['school_longitude']) && is_numeric($settings['school_longitude'])) $this->schoolLongitude = (float) $settings['school_longitude'];
                    if (isset($settings['school_radius']) && is_numeric($settings['school_radius'])) $this->schoolRadius = (int) $settings['school_radius'];
                    if (isset($settings['jam_masuk_max'])) $this->jamMasukMax = (string) $settings['jam_masuk_max'];
                    if (isset($settings['jam_pulang_min'])) $this->jamPulangMin = (string) $settings['jam_pulang_min'];
                    if (isset($settings['geofence_active'])) $this->geofenceActive = ($settings['geofence_active'] === '1' || $settings['geofence_active'] === 'true');
                }
            }
        } catch (\Throwable $e) {
            // Gunakan nilai default properti di atas
        }
    }

    /**
     * Hitung jarak antara dua koordinat GPS menggunakan formula Haversine (hasil dalam meter).
     */
    public static function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
