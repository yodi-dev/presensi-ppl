<?php

namespace App\Controllers;

use App\Models\PiketModel;
use App\Models\PresensiModel;

class Mahasiswa extends BaseController
{
    public function index()
    {
        $presensiModel = new PresensiModel();
        $userId = session()->get('id_user');
        $tanggalHariIni = date('Y-m-d');

        $presensiHariIni = $presensiModel->where('user_id', $userId)
            ->where('tanggal', $tanggalHariIni)
            ->first();

        $data = [
            'presensi_hari_ini' => $presensiHariIni,
            'title'             => 'Dashboard Mahasiswa - Presensi PPL'
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
            $lat = $this->request->getPost('latitude');
            $long = $this->request->getPost('longitude');

            // Validasi format angka koordinat
            $latitudeValid = is_numeric($lat) && $lat >= -90 && $lat <= 90 ? (float) $lat : null;
            $longitudeValid = is_numeric($long) && $long >= -180 && $long <= 180 ? (float) $long : null;

            $presensiModel->insert([
                'user_id'   => $userId,
                'tanggal'   => $tanggalHariIni,
                'jam_masuk' => date('H:i:s'),
                'status'    => 'hadir',
                'latitude'  => $latitudeValid,
                'longitude' => $longitudeValid
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

        if ($cek && empty($cek['jam_keluar']) && $cek['status'] === 'hadir') {
            $presensiModel->update($cek['id'], [
                'jam_keluar' => date('H:i:s')
            ]);
            session()->setFlashdata('pesan', 'Berhasil absen pulang! Hati-hati di jalan.');
        } else {
            session()->setFlashdata('error', 'Tidak bisa absen pulang (belum datang, sudah pulang, atau status izin/sakit).');
        }

        return redirect()->to('/mahasiswa');
    }

    public function izin_sakit()
    {
        $presensiModel = new PresensiModel();
        $userId = session()->get('id_user');
        $tanggalHariIni = date('Y-m-d');

        $cek = $presensiModel->where('user_id', $userId)->where('tanggal', $tanggalHariIni)->first();

        if (!$cek) {
            $status = $this->request->getPost('status');
            $keterangan = trim(strip_tags((string) $this->request->getPost('keterangan')));

            if (!in_array($status, ['izin', 'sakit'], true)) {
                return redirect()->to('/mahasiswa')->with('error', 'Pilihan status tidak valid!');
            }

            if (empty($keterangan)) {
                return redirect()->to('/mahasiswa')->with('error', 'Alasan keterangan wajib diisi!');
            }

            // Batasi panjang keterangan
            if (mb_strlen($keterangan) > 500) {
                $keterangan = mb_substr($keterangan, 0, 500);
            }

            $presensiModel->insert([
                'user_id'    => $userId,
                'tanggal'    => $tanggalHariIni,
                'status'     => $status,
                'keterangan' => $keterangan,
                'jam_masuk'  => date('H:i:s'),
            ]);
            session()->setFlashdata('pesan', 'Keterangan izin/sakit berhasil dikirim.');
        } else {
            session()->setFlashdata('error', 'Kamu sudah mengisi daftar hadir hari ini!');
        }

        return redirect()->to('/mahasiswa');
    }

    public function piket()
    {
        $piketModel = new PiketModel();
        $userId = session()->get('id_user');
        $today = date('Y-m-d');

        $sudahPiket = $piketModel->where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        $data = [
            'title'      => 'Presensi Piket KBM',
            'sudahPiket' => !empty($sudahPiket),
            'dataPiket'  => $sudahPiket
        ];

        return view('mahasiswa/piket', $data);
    }

    public function simpanPiket()
    {
        $userId = session()->get('id_user');
        $piketModel = new PiketModel();
        $today = date('Y-m-d');

        // Pastikan tidak dobel submit piket di hari yang sama
        $sudahPiket = $piketModel->where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        if ($sudahPiket) {
            return redirect()->to('mahasiswa/piket')->with('error', 'Kamu sudah melakukan presensi piket hari ini!');
        }

        // 1. Ambil data Base64 dari form
        $base64_string = (string) $this->request->getPost('foto_base64');

        if (empty($base64_string)) {
            return redirect()->back()->with('error', 'Foto bukti tidak boleh kosong!');
        }

        // 2. Validasi struktur Header Data URL Base64 dan whitelist MIME
        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!preg_match('/^data:(image\/(jpeg|jpg|png|webp));base64,(.+)$/i', $base64_string, $matches)) {
            return redirect()->back()->with('error', 'Format data gambar tidak valid atau tidak didukung!');
        }

        $detectedMime = strtolower($matches[1]);
        if (!isset($allowedMimes[$detectedMime])) {
            return redirect()->back()->with('error', 'Tipe file tidak diizinkan. Hanya foto JPG, PNG, atau WEBP!');
        }

        $rawBase64 = $matches[3];
        $image_binary = base64_decode($rawBase64, true);

        if ($image_binary === false) {
            return redirect()->back()->with('error', 'Gagal memproses data gambar!');
        }

        // 3. Batasi ukuran file (Maksimal 5MB)
        if (strlen($image_binary) > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran gambar terlalu besar (maksimal 5MB)!');
        }

        // 4. Verifikasi binary data benar-benar merupakan gambar valid (bukan script menyamar)
        $imageInfo = @getimagesizefromstring($image_binary);
        if ($imageInfo === false || empty($imageInfo['mime']) || !isset($allowedMimes[$imageInfo['mime']])) {
            return redirect()->back()->with('error', 'File yang dikirimkan terdeteksi bukan gambar asli!');
        }

        // 5. Buat nama file unik dan aman: piket_{userId}_{timestamp}_{random}.{ext}
        $extension = $allowedMimes[$imageInfo['mime']];
        $randomHash = bin2hex(random_bytes(4));
        $fileName = 'piket_' . (int) $userId . '_' . time() . '_' . $randomHash . '.' . $extension;

        // 6. Tentukan folder penyimpanan yang aman
        $path = FCPATH . 'uploads/piket/';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        // Simpan file
        if (file_put_contents($path . $fileName, $image_binary) === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan foto bukti di server.');
        }

        // 7. Simpan data presensi ke Database
        $dataPiket = [
            'user_id'    => (int) $userId,
            'tanggal'    => $today,
            'waktu'      => date('H:i:s'),
            'foto_bukti' => $fileName
        ];

        $piketModel->insert($dataPiket);

        return redirect()->to('mahasiswa/piket')->with('success', 'Presensi Piket KBM berhasil disimpan!');
    }
}
