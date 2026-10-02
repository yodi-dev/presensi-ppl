<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['setting_key', 'setting_value', 'updated_at'];
    protected $useTimestamps    = false;

    /**
     * Cache internal request agar tidak melakukan query berulang
     */
    protected static array $cache = [];

    /**
     * Ambil nilai pengaturan berdasarkan key.
     */
    public static function getSetting(string $key, $default = null)
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        try {
            $model = new self();
            $row = $model->where('setting_key', $key)->first();
            $val = $row ? $row['setting_value'] : $default;
            self::$cache[$key] = $val;
            return $val;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Simpan atau perbarui nilai pengaturan.
     */
    public static function setSetting(string $key, $value): bool
    {
        try {
            $model = new self();
            $existing = $model->where('setting_key', $key)->first();
            $data = [
                'setting_key'   => $key,
                'setting_value' => (string) $value,
                'updated_at'    => date('Y-m-d H:i:s')
            ];

            if ($existing) {
                $res = $model->update($existing['id'], $data);
            } else {
                $res = (bool) $model->insert($data);
            }

            self::$cache[$key] = (string) $value;
            return (bool) $res;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ambil seluruh pengaturan dalam bentuk associative array.
     */
    public static function getAllSettings(): array
    {
        try {
            $model = new self();
            $rows = $model->findAll();
            $result = [];
            foreach ($rows as $row) {
                $result[$row['setting_key']] = $row['setting_value'];
                self::$cache[$row['setting_key']] = $row['setting_value'];
            }
            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
