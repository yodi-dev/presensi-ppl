<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .mobile-dashboard-card {
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(15, 81, 50, 0.05);
    }

    .clock-display {
        font-size: 3.25rem;
        font-weight: 700;
        color: #0f5132;
        letter-spacing: 2px;
        line-height: 1.1;
    }

    .btn-absen {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 700;
        transition: all 0.25s ease;
        border: 4px solid transparent;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-absen:hover:not(:disabled) {
        transform: scale(1.05);
    }

    .btn-datang {
        background: linear-gradient(135deg, #0f5132, #10b981);
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
    }

    .btn-pulang {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(217, 119, 6, 0.25);
    }

    .btn-absen:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        box-shadow: none;
        cursor: not-allowed;
        border-color: #e2e8f0;
    }

    .operational-pill {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        font-size: 0.8rem;
        border-radius: 0.75rem;
        padding: 0.5rem 0.85rem;
    }

    .status-badge-soft {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">

            <!-- Sambutan & Identitas Singkat -->
            <div class="mb-3 text-start">
                <span class="text-muted small">Selamat datang,</span>
                <h4 class="fw-bold text-dark mb-0"><?= esc(session()->get('nama')) ?></h4>
            </div>

            <!-- Banner Jam Operasional Resmi Skagata -->
            <div class="operational-pill d-flex align-items-center justify-content-between mb-3 shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-5 text-success"></i>
                    <div>
                        <div class="fw-bold lh-1" style="font-size: 0.78rem;">Jam Masuk Maksimal: <?= esc(substr($config->jamMasukMax ?? '07:15:00', 0, 5)) ?> WIB</div>
                        <div class="text-muted lh-1 mt-1" style="font-size: 0.72rem;">Jam Pulang Minimal: <?= esc(substr($config->jamPulangMin ?? '15:00:00', 0, 5)) ?> WIB</div>
                    </div>
                </div>
                <span class="badge bg-white text-success border border-success-subtle fw-semibold">
                    <?= esc($config->schoolRadius ?? 100) ?>m
                </span>
            </div>

            <!-- Card Utama Absensi -->
            <div class="card mobile-dashboard-card text-center p-4 mb-3">
                <div class="text-muted text-uppercase fw-semibold small mb-1">Waktu Server Presensi</div>
                <div class="clock-display mb-1" id="clock">00:00:00</div>
                <div class="text-secondary small fw-medium mb-4"><?= date('l, d F Y') ?></div>

                <!-- Tombol Aksi Datang & Pulang -->
                <div class="d-flex justify-content-center gap-4 mb-4">
                    <form id="formDatang" action="<?= base_url('mahasiswa/datang') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <button type="button" id="btnDatang" class="btn btn-absen btn-datang"
                            onclick="prosesAbsen('formDatang', 'btnDatang', 'DATANG')"
                            <?= ($presensi_hari_ini && $presensi_hari_ini['jam_masuk']) ? 'disabled' : '' ?>>
                            <i class="bi bi-box-arrow-in-right fs-4 mb-1"></i>
                            <span>Datang</span>
                        </button>
                    </form>

                    <form id="formPulang" action="<?= base_url('mahasiswa/pulang') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <button type="button" id="btnPulang" class="btn btn-absen btn-pulang"
                            onclick="prosesAbsen('formPulang', 'btnPulang', 'PULANG')"
                            <?= (!$presensi_hari_ini || $presensi_hari_ini['jam_keluar'] || !in_array($presensi_hari_ini['status'], ['hadir', 'terlambat'], true)) ? 'disabled' : '' ?>>
                            <i class="bi bi-box-arrow-right fs-4 mb-1"></i>
                            <span>Pulang</span>
                        </button>
                    </form>
                </div>

                <!-- Opsi Izin / Sakit jika belum ada presensi -->
                <?php if (!$presensi_hari_ini): ?>
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-link text-success text-decoration-none fw-medium" data-bs-toggle="modal" data-bs-target="#modalIzin">
                            <i class="bi bi-envelope-paper me-1"></i> Berhalangan hadir? Ajukan Izin / Sakit
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Status Presensi Hari Ini -->
                <div class="pt-3 border-top">
                    <div class="text-muted fw-semibold small mb-2">Ringkasan Hari Ini</div>
                    <?php if ($presensi_hari_ini): ?>
                        <?php if (in_array($presensi_hari_ini['status'], ['hadir', 'terlambat'], true)): ?>
                            <div class="row g-2 justify-content-center">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border">
                                        <div class="text-muted small">Jam Masuk</div>
                                        <div class="fw-bold <?= $presensi_hari_ini['status'] === 'terlambat' ? 'text-warning' : 'text-success' ?> fs-5">
                                            <?= esc($presensi_hari_ini['jam_masuk'] ?: '--:--') ?>
                                        </div>
                                        <?php if ($presensi_hari_ini['status'] === 'terlambat'): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.7rem;">Terlambat</span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Tepat Waktu</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border">
                                        <div class="text-muted small">Jam Pulang</div>
                                        <div class="fw-bold text-dark fs-5">
                                            <?= esc($presensi_hari_ini['jam_keluar'] ?: '--:--') ?>
                                        </div>
                                        <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">
                                            <?= $presensi_hari_ini['jam_keluar'] ? 'Selesai' : 'Belum Selesai' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-3 rounded bg-light border text-center">
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle status-badge-soft text-uppercase">
                                    <?= esc($presensi_hari_ini['status']) ?>
                                </span>
                                <div class="small text-muted mt-2">Keterangan: <?= esc($presensi_hari_ini['keterangan']) ?></div>
                                <?php if (!empty($presensi_hari_ini['bukti_surat'])): ?>
                                    <div class="mt-2">
                                        <a href="<?= base_url('uploads/surat/' . esc($presensi_hari_ini['bukti_surat'])) ?>" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-3">
                                            <i class="bi bi-paperclip me-1"></i> Bukti Surat Terlampir
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-muted small py-2 bg-light rounded border border-dashed">
                            Belum ada catatan presensi hari ini.
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Tautan Cepat Navigasi Tambahan Bawah (Ergonomis Ponsel) -->
            <div class="row g-2">
                <div class="col-6">
                    <a href="<?= base_url('mahasiswa/piket') ?>" class="btn btn-light border w-100 text-start p-3 rounded-4 shadow-sm text-decoration-none">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-camera-video text-success fs-4"></i>
                            <div>
                                <div class="fw-bold text-dark small lh-1">Piket KBM</div>
                                <span class="text-muted" style="font-size: 0.72rem;">Dokumentasi harian</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= base_url('mahasiswa/riwayat') ?>" class="btn btn-light border w-100 text-start p-3 rounded-4 shadow-sm text-decoration-none">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                            <div>
                                <div class="fw-bold text-dark small lh-1">Riwayat Saya</div>
                                <span class="text-muted" style="font-size: 0.72rem;">Rekapitulasi bulanan</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Pengajuan Izin/Sakit -->
<div class="modal fade" id="modalIzin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Pengajuan Izin / Sakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('mahasiswa/izin_sakit') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body text-start p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori Status</label>
                        <select name="status" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Izin atau Sakit --</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan / Alasan Lengkap</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan izin atau kondisi sakit Anda..." required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Berkas Bukti Surat <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="file" name="bukti_surat" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf">
                        <div class="form-text small">Bisa dilampirkan susulan melalui menu Riwayat. Maksimal 2MB.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skagata rounded-pill px-4">Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // --- Jam Digital Presisi ---
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('clock');
        if (el) el.textContent = `${hours}:${minutes}:${seconds}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // --- Geolocation Haversine Trigger ---
    function prosesAbsen(formId, btnId, textAwal) {
        const form = document.getElementById(formId);
        const btn = document.getElementById(btnId);

        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span><span style="font-size: 0.75rem;">GPS...</span>';
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
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Lokasi Diperlukan',
                        text: 'Sistem membutuhkan koordinat GPS untuk memverifikasi kehadiran di area SMKN 3 Yogyakarta. Pastikan izin lokasi aktif.',
                        confirmButtonColor: '#0f5132'
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
                text: 'Browser Anda tidak mendukung layanan geolokasi GPS.',
                confirmButtonColor: '#0f5132'
            });
            btn.innerHTML = textAwal;
            btn.disabled = false;
        }
    }
</script>
<?= $this->endSection() ?>