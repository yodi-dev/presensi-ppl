<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - Presensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Halo, <?= session()->get('nama') ?>!</h2>
            <div>
                <a href="<?= base_url('ubah_password') ?>" class="btn btn-success">Ubah Password</a>
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('pesan') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 text-center p-5 mt-4">
            <h4 class="mb-4">Silakan Lakukan Presensi Hari Ini</h4>
            <h5 class="text-primary mb-4"><?= date('d F Y') ?></h5>

            <div class="d-flex justify-content-center gap-4">
                <form id="formDatang" action="<?= base_url('mahasiswa/datang') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <button type="button" id="btnDatang" class="btn btn-success btn-lg px-5 py-3 fw-bold shadow"
                        onclick="prosesAbsen('formDatang', 'btnDatang', 'DATANG')"
                        <?= ($presensi_hari_ini && $presensi_hari_ini['jam_masuk']) ? 'disabled' : '' ?>>
                        DATANG
                    </button>
                </form>

                <form id="formPulang" action="<?= base_url('mahasiswa/pulang') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <button type="button" id="btnPulang" class="btn btn-warning btn-lg px-5 py-3 fw-bold shadow text-white"
                        onclick="prosesAbsen('formPulang', 'btnPulang', 'PULANG')"
                        <?= (!$presensi_hari_ini || $presensi_hari_ini['jam_keluar'] || $presensi_hari_ini['status'] != 'hadir') ? 'disabled' : '' ?>>
                        PULANG
                    </button>
                </form>
            </div>

            <div class="mt-4">
                <?php if (!$presensi_hari_ini): ?>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalIzin">
                        Izin atau Sakit? Klik di sini
                    </button>
                <?php endif; ?>
            </div>

            <div class="modal fade" id="modalIzin" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Form Izin / Sakit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= base_url('mahasiswa/izin_sakit') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="modal-body text-start">
                                <div class="mb-3">
                                    <label class="form-label">Pilih Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="izin">Izin</option>
                                        <option value="sakit">Sakit</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Keterangan / Alasan</label>
                                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Demam tinggi / Ada urusan keluarga" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Kirim Keterangan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-muted border-top pt-4">
                <?php if ($presensi_hari_ini): ?>
                    <div class="row justify-content-center">
                        <?php if ($presensi_hari_ini['status'] == 'hadir'): ?>
                            <div class="col-4">
                                <p class="mb-1">Jam Masuk</p>
                                <h5 class="text-success"><?= $presensi_hari_ini['jam_masuk'] ?: '-' ?></h5>
                            </div>
                            <div class="col-4">
                                <p class="mb-1">Jam Pulang</p>
                                <h5 class="text-warning"><?= $presensi_hari_ini['jam_keluar'] ?: '-' ?></h5>
                            </div>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="mb-1">Status Hari Ini</p>
                                <h5 class="text-info"><?= strtoupper($presensi_hari_ini['status']) ?></h5>
                                <p class="small">Keterangan: <?= $presensi_hari_ini['keterangan'] ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p>Kamu belum melakukan presensi kedatangan hari ini.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi utama untuk memproses presensi berbasis lokasi
        function prosesAbsen(formId, btnId, textAwal) {
            const form = document.getElementById(formId);
            const btn = document.getElementById(btnId);

            // Ubah tampilan tombol jadi loading
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Melacak...';
            btn.disabled = true;

            if (navigator.geolocation) {
                // Maksa pakai akurasi tinggi (GPS)
                const opsi = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Kalau diizinkan: isi input hidden, lalu submit formnya otomatis!
                        form.querySelector('input[name="latitude"]').value = position.coords.latitude;
                        form.querySelector('input[name="longitude"]').value = position.coords.longitude;
                        form.submit();
                    },
                    function(error) {
                        // Kalau ditolak/gagal: kembalikan tombol ke semula dan kasih tau user
                        alert('PRESENSI GAGAL: Sistem membutuhkan akses lokasi. Tolong izinkan akses lokasi (Allow) di browser Anda.');
                        btn.innerHTML = textAwal;
                        btn.disabled = false;
                    },
                    opsi
                );
            } else {
                alert('Browser kamu tidak mendukung fitur lokasi.');
                btn.innerHTML = textAwal;
                btn.disabled = false;
            }
        }
    </script>
</body>

</html>