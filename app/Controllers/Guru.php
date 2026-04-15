<?php

namespace App\Controllers;

class Guru extends BaseController
{
    public function index()
    {
        // Pastikan hanya guru yang bisa akses
        if (session()->get('role') != 'guru') {
            return redirect()->to('/auth');
        }

        $db      = \Config\Database::connect();
        $builder = $db->table('users');

        // Ambil input filter dari URL
        $tanggalFilter = $this->request->getGet('tanggal');
        $jurusan       = $this->request->getGet('jurusan');

        // Default tanggal hari ini jika tidak ada filter
        $tanggalPilih = $tanggalFilter ? $tanggalFilter : date('Y-m-d');

        // 1. Pilih kolom (tambahkan users.jurusan agar bisa kita tampilkan di tabel)
        $builder->select('users.id, users.nama, users.jurusan, presensi.status, presensi.jam_masuk, presensi.jam_keluar, presensi.keterangan, presensi.latitude, presensi.longitude');

        // 2. JOIN dengan tabel presensi berdasarkan tanggal yang dipilih
        $builder->join('presensi', "presensi.user_id = users.id AND presensi.tanggal = '$tanggalPilih'", 'left');

        // 3. Filter dasar: Hanya role mahasiswa
        $builder->where('users.role', 'mahasiswa');

        // --- 🛠️ PENYESUAIAN DISINI SOB ---
        // 4. Filter tambahan: Jika jurusan dipilih, saring datanya
        if (!empty($jurusan)) {
            $builder->where('users.jurusan', $jurusan);
        }
        // ---------------------------------

        $data = [
            'tanggal'          => $tanggalPilih,
            'presensi'         => $builder->get()->getResultArray(), // Data yang sudah difilter
            'jurusan_terpilih' => $jurusan
        ];

        return view('guru/index', $data);
    }

    public function update_status($userId, $status)
    {
        $tanggal = $this->request->getGet('tgl');
        $presensiModel = new \App\Models\PresensiModel(); // Pastikan model ini ada

        // Cek apakah sudah ada datanya di tanggal tersebut
        $existing = $presensiModel->where(['user_id' => $userId, 'tanggal' => $tanggal])->first();

        $data = [
            'user_id' => $userId,
            'tanggal' => $tanggal,
            'status'  => $status,
            'keterangan' => 'Diupdate manual oleh Guru'
        ];

        if ($existing) {
            $presensiModel->update($existing['id'], $data);
        } else {
            $presensiModel->insert($data);
        }

        return redirect()->back()->with('pesan', 'Status berhasil diupdate!');
    }

    public function laporan()
    {
        // 1. Ambil request bulan dan tahun dari form filter (atau set default bulan/tahun sekarang)
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $db      = \Config\Database::connect();
        $builder = $db->table('users');

        // 2. Query super power untuk menghitung total masing-status per mahasiswa
        $laporan = $builder->select("
                users.id, 
                users.nama,
                SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN presensi.status = 'izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN presensi.status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN presensi.status = 'alpa' THEN 1 ELSE 0 END) as total_alpa
            ")
            // Join ke tabel presensi berdasarkan ID user dan filter bulan/tahun
            ->join('presensi', "presensi.user_id = users.id AND MONTH(presensi.tanggal) = '$bulan' AND YEAR(presensi.tanggal) = '$tahun'", 'left')
            // Pastikan cuma nampilin user yang role-nya mahasiswa
            ->where('users.role', 'mahasiswa')
            // Group by ID mahasiswa biar datanya ter-rekap per orang
            ->groupBy('users.id')
            ->get()
            ->getResultArray();


        // 3. Kirim data ke view
        $data = [
            'laporan'     => $laporan,
            'bulan_pilih' => $bulan,
            'tahun_pilih' => $tahun,
        ];

        return view('guru/laporan', $data);
    }

    public function laporanPiket()
    {
        $piketModel = new \App\Models\PiketModel();

        $data = [
            'title' => 'Laporan Presensi Piket KBM',
            // Ingat fungsi sakti getPiketWithUser() yang kita bikin di Step 1? 
            // Sekarang saatnya dia bekerja nge-JOIN tabel piket dan users!
            'dataPiket' => $piketModel->getPiketWithUser()
        ];

        return view('guru/laporan_piket', $data);
    }
}
