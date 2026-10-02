<?php

namespace App\Controllers;

use App\Models\PiketModel;
use App\Models\PresensiModel;
use Config\Presensi as PresensiConfig;

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

        $config = config('Presensi');

        $data = [
            'presensi_hari_ini' => $presensiHariIni,
            'config'            => $config,
            'title'             => 'Dashboard Mahasiswa - Presensi PPL'
        ];

        return view('mahasiswa/index', $data);
    }

    public function datang()
    {
        $presensiModel = new PresensiModel();
        $userId = session()->get('id_user');
        $tanggalHariIni = date('Y-m-d');
        $config = config('Presensi') ?? new PresensiConfig();

        $cek = $presensiModel->where('user_id', $userId)->where('tanggal', $tanggalHariIni)->first();

        if (!$cek) {
            $lat = $this->request->getPost('latitude');
            $long = $this->request->getPost('longitude');

            // 1. Validasi keberadaan dan format koordinat GPS
            if ($lat === null || $long === null || !is_numeric($lat) || !is_numeric($long)) {
                return redirect()->to('/mahasiswa')->with('error', 'Koordinat GPS tidak terdeteksi! Pastikan akses lokasi diizinkan di browser.');
            }

            $latFloat = (float) $lat;
            $longFloat = (float) $long;

            if ($latFloat < -90 || $latFloat > 90 || $longFloat < -180 || $longFloat > 180) {
                return redirect()->to('/mahasiswa')->with('error', 'Format koordinat lokasi tidak valid.');
            }

            // 2. Validasi Geofencing Radius Sekolah (Formula Haversine)
            $jarak = PresensiConfig::hitungJarak(
                $latFloat,
                $longFloat,
                $config->schoolLatitude,
                $config->schoolLongitude
            );

            if ($jarak > $config->schoolRadius) {
                $jarakBulat = round($jarak);
                return redirect()->to('/mahasiswa')->with('error', "Presensi ditolak! Anda berada di luar radius sekolah. Jarak Anda: {$jarakBulat} meter (Maksimal: {$config->schoolRadius} meter).");
            }

            // 3. Kebijakan Jam Masuk & Penentuan Keterlambatan
            $jamMasuk = date('H:i:s');
            $isTerlambat = ($jamMasuk > $config->jamMasukMax);
            $status = $isTerlambat ? 'terlambat' : 'hadir';

            $presensiModel->insert([
                'user_id'   => $userId,
                'tanggal'   => $tanggalHariIni,
                'jam_masuk' => $jamMasuk,
                'status'    => $status,
                'latitude'  => $latFloat,
                'longitude' => $longFloat
            ]);

            if ($isTerlambat) {
                session()->setFlashdata('pesan', "Absen datang tercatat! Anda tercatat TERLAMBAT (lewat {$config->jamMasukMax} WIB). Tetap semangat!");
            } else {
                session()->setFlashdata('pesan', 'Berhasil absen datang tepat waktu! Semangat belajarnya.');
            }
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
        $config = config('Presensi') ?? new PresensiConfig();

        $cek = $presensiModel->where('user_id', $userId)->where('tanggal', $tanggalHariIni)->first();

        // Validasi: hanya bisa pulang jika sudah absen datang (hadir/terlambat) dan belum absen pulang
        if ($cek && empty($cek['jam_keluar']) && in_array($cek['status'], ['hadir', 'terlambat'], true)) {
            $jamSekarang = date('H:i:s');

            // Batasi jam pulang minimal
            if ($jamSekarang < $config->jamPulangMin) {
                return redirect()->to('/mahasiswa')->with('error', "Belum waktu pulang resmi! Jam pulang minimal adalah {$config->jamPulangMin} WIB.");
            }

            $presensiModel->update($cek['id'], [
                'jam_keluar' => $jamSekarang
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

            if (mb_strlen($keterangan) > 500) {
                $keterangan = mb_substr($keterangan, 0, 500);
            }

            // Penanganan Unggah Bukti Surat (Opsional)
            $namaFileSurat = null;
            $fileSurat = $this->request->getFile('bukti_surat');

            if ($fileSurat && $fileSurat->isValid() && !$fileSurat->hasMoved()) {
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
                if (!in_array($fileSurat->getMimeType(), $allowedMimes, true)) {
                    return redirect()->to('/mahasiswa')->with('error', 'Format berkas bukti tidak didukung (hanya JPG, PNG, WEBP, atau PDF)!');
                }

                if ($fileSurat->getSizeByUnit('mb') > 2) {
                    return redirect()->to('/mahasiswa')->with('error', 'Ukuran berkas bukti maksimal 2MB!');
                }

                $folderSurat = FCPATH . 'uploads/surat/';
                if (!is_dir($folderSurat)) {
                    mkdir($folderSurat, 0755, true);
                }

                $namaFileSurat = 'surat_' . (int) $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileSurat->guessExtension();
                $fileSurat->move($folderSurat, $namaFileSurat);
            }

            $presensiModel->insert([
                'user_id'     => $userId,
                'tanggal'     => $tanggalHariIni,
                'status'      => $status,
                'keterangan'  => $keterangan,
                'jam_masuk'   => date('H:i:s'),
                'bukti_surat' => $namaFileSurat
            ]);

            session()->setFlashdata('pesan', 'Keterangan izin/sakit berhasil dikirim.' . ($namaFileSurat ? ' Berkas bukti terlampir.' : ''));
        } else {
            session()->setFlashdata('error', 'Kamu sudah mengisi daftar hadir hari ini!');
        }

        return redirect()->to('/mahasiswa');
    }

    public function riwayat()
    {
        $userId = session()->get('id_user');
        $bulanInput = $this->request->getGet('bulan');
        $tahunInput = $this->request->getGet('tahun');

        $bulan = (is_string($bulanInput) && preg_match('/^(0[1-9]|1[0-2])$/', $bulanInput)) ? $bulanInput : date('m');
        $tahun = (is_string($tahunInput) && preg_match('/^\d{4}$/', $tahunInput)) ? $tahunInput : date('Y');

        $presensiModel = new PresensiModel();

        $dataRiwayat = $presensiModel->where('user_id', $userId)
            ->where('MONTH(tanggal)', (int) $bulan)
            ->where('YEAR(tanggal)', (int) $tahun)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        // Hitung statistik
        $rekap = [
            'hadir'     => 0,
            'terlambat' => 0,
            'izin'      => 0,
            'sakit'     => 0,
            'alpa'      => 0
        ];

        foreach ($dataRiwayat as $item) {
            $st = $item['status'];
            if (isset($rekap[$st])) {
                $rekap[$st]++;
            }
        }

        $data = [
            'riwayat'     => $dataRiwayat,
            'rekap'       => $rekap,
            'bulan_pilih' => $bulan,
            'tahun_pilih' => $tahun,
            'title'       => 'Riwayat Presensi Mandiri - Mahasiswa'
        ];

        return view('mahasiswa/riwayat', $data);
    }

    public function uploadBuktiSusulan()
    {
        $userId = session()->get('id_user');
        $presensiId = $this->request->getPost('presensi_id');

        $presensiModel = new PresensiModel();
        $presensi = $presensiModel->where('id', $presensiId)->where('user_id', $userId)->first();

        if (!$presensi) {
            return redirect()->back()->with('error', 'Data presensi tidak ditemukan atau bukan milik Anda!');
        }

        if (!in_array($presensi['status'], ['izin', 'sakit'], true)) {
            return redirect()->back()->with('error', 'Berkas bukti surat hanya untuk status Izin atau Sakit!');
        }

        $fileSurat = $this->request->getFile('bukti_surat');
        if (!$fileSurat || !$fileSurat->isValid() || $fileSurat->hasMoved()) {
            return redirect()->back()->with('error', 'Pilih berkas bukti surat yang valid!');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($fileSurat->getMimeType(), $allowedMimes, true)) {
            return redirect()->back()->with('error', 'Format berkas tidak didukung (hanya JPG, PNG, WEBP, atau PDF)!');
        }

        if ($fileSurat->getSizeByUnit('mb') > 2) {
            return redirect()->back()->with('error', 'Ukuran berkas maksimal 2MB!');
        }

        $folderSurat = FCPATH . 'uploads/surat/';
        if (!is_dir($folderSurat)) {
            mkdir($folderSurat, 0755, true);
        }

        $namaFileSurat = 'surat_' . (int) $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileSurat->guessExtension();
        $fileSurat->move($folderSurat, $namaFileSurat);

        $presensiModel->update($presensiId, [
            'bukti_surat' => $namaFileSurat
        ]);

        return redirect()->back()->with('pesan', 'Berkas bukti surat berhasil disusulkan!');
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

        $sudahPiket = $piketModel->where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        if ($sudahPiket) {
            return redirect()->to('mahasiswa/piket')->with('error', 'Kamu sudah melakukan presensi piket hari ini!');
        }

        $base64_string = (string) $this->request->getPost('foto_base64');

        if (empty($base64_string)) {
            return redirect()->back()->with('error', 'Foto bukti tidak boleh kosong!');
        }

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

        if (strlen($image_binary) > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran gambar terlalu besar (maksimal 5MB)!');
        }

        $imageInfo = @getimagesizefromstring($image_binary);
        if ($imageInfo === false || empty($imageInfo['mime']) || !isset($allowedMimes[$imageInfo['mime']])) {
            return redirect()->back()->with('error', 'File yang dikirimkan terdeteksi bukan gambar asli!');
        }

        $extension = $allowedMimes[$imageInfo['mime']];
        $randomHash = bin2hex(random_bytes(4));
        $fileName = 'piket_' . (int) $userId . '_' . time() . '_' . $randomHash . '.' . $extension;

        $path = FCPATH . 'uploads/piket/';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if (file_put_contents($path . $fileName, $image_binary) === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan foto bukti di server.');
        }

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
