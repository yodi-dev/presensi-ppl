<?php

namespace App\Controllers;

use App\Models\PiketModel;
use App\Models\PresensiModel;
use Config\Database;

class Guru extends BaseController
{
    public function index()
    {
        $db      = Database::connect();
        $builder = $db->table('users');

        // Ambil input filter dari URL dengan validasi format
        $tanggalFilter = $this->request->getGet('tanggal');
        $jurusan       = $this->request->getGet('jurusan');

        $tanggalPilih = (is_string($tanggalFilter) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalFilter))
            ? $tanggalFilter
            : date('Y-m-d');

        // 1. Pilih kolom mahasiswa & presensi
        $builder->select('users.id, users.nama, users.jurusan, presensi.status, presensi.jam_masuk, presensi.jam_keluar, presensi.keterangan, presensi.latitude, presensi.longitude');

        // 2. JOIN dengan tabel presensi dengan parameterized escaping (aman dari SQL Injection)
        $builder->join('presensi', 'presensi.user_id = users.id AND presensi.tanggal = ' . $db->escape($tanggalPilih), 'left');

        // 3. Filter dasar: Hanya role mahasiswa
        $builder->where('users.role', 'mahasiswa');

        // 4. Filter tambahan jurusan jika dipilih
        if (!empty($jurusan)) {
            $builder->where('users.jurusan', $jurusan);
        }

        $data = [
            'tanggal'          => $tanggalPilih,
            'presensi'         => $builder->get()->getResultArray(),
            'jurusan_terpilih' => $jurusan,
            'title'            => 'Dashboard Guru - Presensi PPL'
        ];

        return view('guru/index', $data);
    }

    public function update_status($userId = null, $status = null)
    {
        // Ambil parameter dari POST (didukung fallback GET untuk keamanan transisi)
        $userId  = $this->request->getPost('user_id') ?? $userId;
        $status  = $this->request->getPost('status') ?? $status;
        $tanggal = $this->request->getPost('tanggal') ?? $this->request->getGet('tgl');

        $tanggalPilih = (is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal))
            ? $tanggal
            : date('Y-m-d');

        $allowedStatus = ['hadir', 'izin', 'sakit', 'alpa'];
        if (!in_array($status, $allowedStatus, true)) {
            return redirect()->back()->with('error', 'Status presensi tidak valid!');
        }

        if (empty($userId) || !is_numeric($userId)) {
            return redirect()->back()->with('error', 'ID Mahasiswa tidak valid!');
        }

        $presensiModel = new PresensiModel();
        $existing = $presensiModel->where(['user_id' => (int) $userId, 'tanggal' => $tanggalPilih])->first();

        $data = [
            'user_id'    => (int) $userId,
            'tanggal'    => $tanggalPilih,
            'status'     => $status,
            'keterangan' => 'Diupdate manual oleh Guru (' . (session()->get('nama') ?? 'Guru') . ')'
        ];

        if ($existing) {
            $presensiModel->update($existing['id'], $data);
        } else {
            $presensiModel->insert($data);
        }

        return redirect()->back()->with('pesan', 'Status presensi berhasil diupdate!');
    }

    public function laporan()
    {
        $bulanInput = $this->request->getGet('bulan');
        $tahunInput = $this->request->getGet('tahun');

        $bulan = (is_string($bulanInput) && preg_match('/^(0[1-9]|1[0-2])$/', $bulanInput)) ? $bulanInput : date('m');
        $tahun = (is_string($tahunInput) && preg_match('/^\d{4}$/', $tahunInput)) ? $tahunInput : date('Y');

        $db      = Database::connect();
        $builder = $db->table('users');

        $bulanEscaped = (int) $bulan;
        $tahunEscaped = (int) $tahun;

        $laporan = $builder->select("
                users.id, 
                users.nama,
                SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN presensi.status = 'izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN presensi.status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN presensi.status = 'alpa' THEN 1 ELSE 0 END) as total_alpa
            ")
            ->join('presensi', "presensi.user_id = users.id AND MONTH(presensi.tanggal) = {$bulanEscaped} AND YEAR(presensi.tanggal) = {$tahunEscaped}", 'left')
            ->where('users.role', 'mahasiswa')
            ->groupBy('users.id')
            ->get()
            ->getResultArray();

        $data = [
            'laporan'     => $laporan,
            'bulan_pilih' => $bulan,
            'tahun_pilih' => $tahun,
            'title'       => 'Laporan Bulanan - Presensi PPL'
        ];

        return view('guru/laporan', $data);
    }

    public function laporanPiket()
    {
        $piketModel = new PiketModel();

        $tanggalFilter = $this->request->getGet('tanggal');
        $tanggalPilih = (is_string($tanggalFilter) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalFilter))
            ? $tanggalFilter
            : date('Y-m-d');

        $data = [
            'tanggal'   => $tanggalPilih,
            'dataPiket' => $piketModel->getPiketWithFilter($tanggalPilih),
            'title'     => 'Laporan Piket KBM - Presensi PPL'
        ];

        return view('guru/laporan_piket', $data);
    }
}
