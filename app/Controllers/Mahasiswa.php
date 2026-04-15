<?php

namespace App\Controllers;

use App\Models\PresensiModel;

class Mahasiswa extends BaseController
{
    public function index()
    {
        if (session()->get('role') != 'mahasiswa') {
            return redirect()->to('/auth');
        }

        $presensiModel = new PresensiModel();
        $userId = session()->get('id_user');
        $tanggalHariIni = date('Y-m-d');

        $presensiHariIni = $presensiModel->where('user_id', $userId)
            ->where('tanggal', $tanggalHariIni)
            ->first();

        $data = [
            'presensi_hari_ini' => $presensiHariIni
        ];

        return view('mahasiswa/index', $data);
    }

    public function datang()
    {
        $presensiModel = new PresensiModel();
        $userId = session()->get('id_user');
        $tanggalHariIni = date('Y-m-d');

        $cek = $presensiModel->where('user_id', $userId)->where('tanggal', $tanggalHariIni)->first();

        if (!$cek) {
            $presensiModel->insert([
                'user_id'   => $userId,
                'tanggal'   => $tanggalHariIni,
                'jam_masuk' => date('H:i:s'),
                'status' => 'hadir',
                // Data ini sekarang pasti masuk karena view-nya udah bener
                'latitude'  => $this->request->getPost('latitude'),
                'longitude' => $this->request->getPost('longitude')
            ]);
            session()->setFlashdata('pesan', 'Berhasil absen datang! Semangat belajarnya.');
        } else {
            session()->setFlashdata('error', 'Kamu sudah absen datang hari ini!');
        }

        return redirect()->to('/mahasiswa');
    }

    public function pulang()
    {
        $presensiModel = new PresensiModel();
        $userId = session()->get('id_user');
        $tanggalHariIni = date('Y-m-d');

        $cek = $presensiModel->where('user_id', $userId)->where('tanggal', $tanggalHariIni)->first();

        if ($cek && empty($cek['jam_keluar']) && $cek['status'] == 'hadir') {

            $presensiModel->update($cek['id'], [
                'jam_keluar' => date('H:i:s'),
                // 'lat_keluar'  => $this->request->getPost('latitude'),
                // 'long_keluar' => $this->request->getPost('longitude')
            ]);
            session()->setFlashdata('pesan', 'Berhasil absen pulang! Hati-hati di jalan.');
        } else {
            // Ubah pesan errornya biar lebih jelas
            session()->setFlashdata('error', 'Tidak bisa absen pulang (belum datang, sudah pulang, atau status izin/sakit).');
        }

        return redirect()->to('/mahasiswa');
    }

    public function izin_sakit()
    {
        // dd($_POST);
        $presensiModel = new PresensiModel();
        $userId = session()->get('id_user');
        $tanggalHariIni = date('Y-m-d');

        // Cek dulu, jangan sampai double input
        $cek = $presensiModel->where('user_id', $userId)->where('tanggal', $tanggalHariIni)->first();

        if (!$cek) {
            $presensiModel->insert([
                'user_id'    => $userId,
                'tanggal'    => $tanggalHariIni,
                'status'     => $this->request->getPost('status'), // 'izin' atau 'sakit'
                'keterangan' => $this->request->getPost('keterangan'),
                'jam_masuk'  => date('H:i:s'), // Tetap catat waktu dia lapor
            ]);
            session()->setFlashdata('pesan', 'Keterangan izin/sakit berhasil dikirim.');
        } else {
            session()->setFlashdata('error', 'Kamu sudah mengisi daftar hadir hari ini!');
        }

        return redirect()->to('/mahasiswa');
    }
}
