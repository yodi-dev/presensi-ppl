<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .app-card {
        border-radius: 1.5rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .clock-display {
        font-size: 3.5rem;
        font-weight: 700;
        color: #003366;
        letter-spacing: 2px;
        line-height: 1;
    }

    .btn-absen {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: 700;
        transition: all 0.3s ease;
        border: 4px solid transparent;
    }

    .btn-absen:hover:not(:disabled) {
        transform: scale(1.05);
    }

    .btn-datang {
        background: linear-gradient(135deg, #198754, #146c43);
        color: white;
        box-shadow: 0 10px 20px rgba(25, 135, 84, 0.3);
    }

    .btn-pulang {
        background: linear-gradient(135deg, #ffc107, #ffcd39);
        color: #000;
        box-shadow: 0 10px 20px rgba(255, 193, 7, 0.3);
    }

    .btn-absen:disabled {
        background: #e9ecef;
        color: #adb5bd;
        box-shadow: none;
        cursor: not-allowed;
        border-color: #dee2e6;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4 mb-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-person-circle text-primary me-2"></i>Halo, <?= session()->get('nama') ?>!</h3>
        <div class="d-flex gap-2">
            <a href="<?= base_url('mahasiswa/piket') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-list-check"></i> Piket
            </a>
            <a href="<?= base_url('ubah_password') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-key"></i> Password
            </a>
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card app-card text-center p-4 p-md-5">

                <h6 class="text-muted text-uppercase fw-bold mb-3">Waktu Saat Ini</h6>
                <div class="clock-display mb-1" id="clock">00:00:00</div>
                <h5 class="text-primary mb-5 fw-semibold"><?= date('d F Y') ?></h5>

                <div class="d-flex justify-content-center gap-3 gap-md-5 mb-4">

                    <form id="formDatang" action="<?= base_url('mahasiswa/datang') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <button type="button" id="btnDatang" class="btn btn-absen btn-datang"
                            onclick="prosesAbsen('formDatang', 'btnDatang', 'DATANG')"
                            <?= ($presensi_hari_ini && $presensi_hari_ini['jam_masuk']) ? 'disabled' : '' ?>>
                            DATANG
                        </button>
                    </form>

                    <form id="formPulang" action="<?= base_url('mahasiswa/pulang') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <button type="button" id="btnPulang" class="btn btn-absen btn-pulang"
                            onclick="prosesAbsen('formPulang', 'btnPulang', 'PULANG')"
                            <?= (!$presensi_hari_ini || $presensi_hari_ini['jam_keluar'] || $presensi_hari_ini['status'] != 'hadir') ? 'disabled' : '' ?>>
                            PULANG
                        </button>
                    </form>

                </div>

                <div>
                    <?php if (!$presensi_hari_ini): ?>
                        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalIzin">
                            <i class="bi bi-info-circle me-1"></i> Izin atau Sakit? Klik di sini
                        </button>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <h6 class="text-muted fw-bold mb-3">Status Hari Ini</h6>
                    <?php if ($presensi_hari_ini): ?>
                        <div class="row justify-content-center">
                            <?php if ($presensi_hari_ini['status'] == 'hadir'): ?>
                                <div class="col-5">
                                    <p class="mb-0 small text-muted">Masuk</p>
                                    <h5 class="text-success fw-bold"><?= $presensi_hari_ini['jam_masuk'] ?: '--:--' ?></h5>
                                </div>
                                <div class="col-2 border-end border-start"></div>
                                <div class="col-5">
                                    <p class="mb-0 small text-muted">Pulang</p>
                                    <h5 class="text-warning fw-bold"><?= $presensi_hari_ini['jam_keluar'] ?: '--:--' ?></h5>
                                </div>
                            <?php else: ?>
                                <div class="col-12">
                                    <span class="badge bg-info fs-6 px-4 py-2 text-uppercase"><?= $presensi_hari_ini['status'] ?></span>
                                    <p class="small text-muted mt-2 mb-0">Keterangan: <?= $presensi_hari_ini['keterangan'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary bg-light border-0 text-muted small py-2 mb-0">
                            Kamu belum melakukan presensi hari ini.
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalIzin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Form Izin / Sakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('mahasiswa/izin_sakit') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body text-start p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Status</label>
                        <select name="status" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Keterangan / Alasan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Demam tinggi / Ada urusan keluarga..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // --- Script Jam Digital ---
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
    }
    setInterval(updateClock, 1000);
    updateClock(); // Panggil sekali biar gak nunggu 1 detik pertama

    // --- Script Presensi Lokasi ---
    function prosesAbsen(formId, btnId, textAwal) {
        const form = document.getElementById(formId);
        const btn = document.getElementById(btnId);

        // Ubah tampilan tombol jadi loading
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        btn.disabled = true;

        if (navigator.geolocation) {
            const opsi = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    form.querySelector('input[name="latitude"]').value = position.coords.latitude;
                    form.querySelector('input[name="longitude"]').value = position.coords.longitude;
                    form.submit();
                },
                function(error) {
                    // Pakai SweetAlert biar UI tetap cantik kalau GPS gagal
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Lokasi Gagal!',
                        text: 'Sistem membutuhkan akses lokasi (GPS) untuk absen. Pastikan GPS aktif dan browser diizinkan mengakses lokasi.',
                        confirmButtonColor: '#003366'
                    });
                    btn.innerHTML = textAwal;
                    btn.disabled = false;
                },
                opsi
            );
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Didukung',
                text: 'Browser kamu tidak mendukung fitur lokasi.',
                confirmButtonColor: '#003366'
            });
            btn.innerHTML = textAwal;
            btn.disabled = false;
        }
    }
</script>
<?= $this->endSection() ?>