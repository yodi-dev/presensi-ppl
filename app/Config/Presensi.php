<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Presensi extends BaseConfig
{
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
