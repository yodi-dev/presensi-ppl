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

    public function piket()
    {
        $piketModel = new \App\Models\PiketModel();
        $userId = session()->get('id_user');
        $today = date('Y-m-d');

        // Cari data piket user ini untuk hari ini
        $sudahPiket = $piketModel->where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        $data = [
            'title'      => 'Presensi Piket KBM',
            'sudahPiket' => !empty($sudahPiket), // Akan bernilai true jika data ada
            'dataPiket'  => $sudahPiket
        ];

        return view('mahasiswa/piket', $data);
    }

    public function simpanPiket()
    {
        // 1. Ambil data Base64 dari form
        $base64_string = $this->request->getPost('foto_base64');

        if (empty($base64_string)) {
            return redirect()->back()->with('error', 'Foto bukti tidak boleh kosong!');
        }

        // 2. Pisahkan header "data:image/jpeg;base64," dari data inti fotonya
        $image_parts = explode(";base64,", $base64_string);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1] ?? 'jpeg'; // Default ke jpeg
        $image_base64 = base64_decode($image_parts[1]);

        // 3. Buat nama file unik (Format: piket_idUser_timestamp.jpeg)
        $userId = session()->get('id_user'); // Pastikan ini sesuai dengan nama session id user kamu
        $fileName = 'piket_' . $userId . '_' . time() . '.' . $image_type;

        // 4. Tentukan lokasi folder penyimpanan (public/uploads/piket/)
        $path = FCPATH . 'uploads/piket/';

        // Bikin foldernya otomatis kalau belum ada
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // 5. Simpan file gambar ke dalam folder tersebut
        file_put_contents($path . $fileName, $image_base64);

        // 6. Simpan data presensi ke Database
        $piketModel = new \App\Models\PiketModel();

        $dataPiket = [
            'user_id'    => $userId,
            'tanggal'    => date('Y-m-d'),
            'waktu'      => date('H:i:s'),
            'foto_bukti' => $fileName // Kita cuma simpan nama filenya aja, bukan full gambarnya
        ];

        $piketModel->insert($dataPiket);

        // 7. Redirect kembali dengan pesan sukses
        return redirect()->to('mahasiswa/piket')->with('success', 'Presensi Piket KBM berhasil disimpan!');
    }
}
