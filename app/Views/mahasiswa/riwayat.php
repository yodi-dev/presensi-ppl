<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .history-card {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header-custom {
        background-color: #ffffff;
        border-bottom: 2px solid #f8f9fa;
        padding: 1.5rem;
    }

    .stat-card {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .table-custom thead th {
        background-color: #003366;
        color: #ffffff;
        font-weight: 500;
        border-bottom: none;
        padding: 0.85rem;
        white-space: nowrap;
    }

    .table-custom tbody td {
        padding: 0.85rem;
        vertical-align: middle;
    }

    .filter-wrapper {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4 mb-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Presensi Mandiri</h3>
            <p class="text-muted mt-1 mb-0">Catatan riwayat kehadiran <strong><?= esc(session()->get('nama')) ?></strong></p>
        </div>
        <div>
            <a href="<?= base_url('mahasiswa') ?>" class="btn btn-outline-secondary rounded-pill shadow-sm px-4">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Statistik Ringkas Bulan Terpilih -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card stat-card bg-white p-3 text-center border-start border-4 border-success">
                <small class="text-muted fw-semibold">Hadir</small>
                <h3 class="fw-bold text-success mb-0 mt-1"><?= $rekap['hadir'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card stat-card bg-white p-3 text-center border-start border-4 border-warning">
                <small class="text-muted fw-semibold">Terlambat</small>
                <h3 class="fw-bold text-warning mb-0 mt-1"><?= $rekap['terlambat'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card stat-card bg-white p-3 text-center border-start border-4 border-info">
                <small class="text-muted fw-semibold">Izin</small>
                <h3 class="fw-bold text-info mb-0 mt-1"><?= $rekap['izin'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card stat-card bg-white p-3 text-center border-start border-4 border-secondary">
                <small class="text-muted fw-semibold">Sakit</small>
                <h3 class="fw-bold text-secondary mb-0 mt-1"><?= $rekap['sakit'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card stat-card bg-white p-3 text-center border-start border-4 border-danger">
                <small class="text-muted fw-semibold">Alpa</small>
                <h3 class="fw-bold text-danger mb-0 mt-1"><?= $rekap['alpa'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card stat-card bg-white p-3 text-center border-start border-4 border-primary">
                <small class="text-muted fw-semibold">Total Rekap</small>
                <h3 class="fw-bold text-primary mb-0 mt-1"><?= array_sum($rekap) ?></h3>
            </div>
        </div>
    </div>

    <!-- Card Riwayat dan Filter -->
    <div class="card history-card">
        <div class="card-header-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar3 me-2"></i>Daftar Presensi</h5>
            </div>

            <form action="<?= base_url('mahasiswa/riwayat') ?>" method="GET" class="filter-wrapper d-flex align-items-center flex-wrap gap-2 m-0">
                <div class="input-group input-group-sm shadow-sm" style="width: auto;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-month text-muted"></i></span>
                    <select name="bulan" class="form-select border-start-0" required>
                        <?php
                        $namaBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                        foreach ($namaBulan as $angka => $nama):
                        ?>
                            <option value="<?= $angka ?>" <?= ($bulan_pilih == $angka) ? 'selected' : '' ?>>
                                <?= $nama ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group input-group-sm shadow-sm" style="width: auto;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                    <select name="tahun" class="form-select border-start-0" required>
                        <?php
                        $tahunSekarang = date('Y');
                        for ($t = $tahunSekarang; $t >= 2023; $t--):
                        ?>
                            <option value="<?= $t ?>" <?= ($tahun_pilih == $t) ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-sm btn-primary shadow-sm px-3">
                    <i class="bi bi-search me-1"></i> Tampilkan
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-custom text-center mb-0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="12%">Status</th>
                        <th width="12%">Jam Masuk</th>
                        <th width="12%">Jam Pulang</th>
                        <th width="24%" class="text-start">Keterangan</th>
                        <th width="20%">Bukti Surat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-black-50"></i>
                                Belum ada data presensi pada bulan ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $key => $row): ?>
                            <tr>
                                <td><span class="text-muted"><?= esc($key + 1) ?></span></td>
                                <td class="fw-semibold text-dark"><?= esc(date('d M Y', strtotime($row['tanggal']))) ?></td>

                                <td>
                                    <?php if ($row['status'] === 'hadir'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Hadir</span>
                                    <?php elseif ($row['status'] === 'terlambat'): ?>
                                        <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1">Terlambat</span>
                                    <?php elseif ($row['status'] === 'izin'): ?>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">Izin</span>
                                    <?php elseif ($row['status'] === 'sakit'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Sakit</span>
                                    <?php elseif ($row['status'] === 'alpa'): ?>
                                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-2 py-1">Alpa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><?= esc($row['status']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td><span class="fw-semibold <?= !empty($row['jam_masuk']) ? ($row['status'] === 'terlambat' ? 'text-warning' : 'text-success') : 'text-muted' ?>"><?= esc($row['jam_masuk'] ?: '--:--') ?></span></td>
                                <td><span class="fw-semibold <?= !empty($row['jam_keluar']) ? 'text-warning' : 'text-muted' ?>"><?= esc($row['jam_keluar'] ?: '--:--') ?></span></td>

                                <td class="text-start text-muted small">
                                    <?= !empty($row['keterangan']) ? esc($row['keterangan']) : '<span class="text-black-50 fst-italic">-</span>' ?>
                                </td>

                                <td>
                                    <?php if (!empty($row['bukti_surat'])): ?>
                                        <a href="<?= base_url('uploads/surat/' . esc($row['bukti_surat'])) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-file-earmark-check me-1"></i> Lihat Bukti
                                        </a>
                                    <?php elseif (in_array($row['status'], ['izin', 'sakit'], true)): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" onclick="bukaModalSusulan(<?= (int) $row['id'] ?>, '<?= esc($row['tanggal']) ?>')">
                                            <i class="bi bi-upload me-1"></i> Upload Susulan
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Upload Bukti Susulan -->
<div class="modal fade" id="modalSusulan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Upload Bukti Susulan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('mahasiswa/upload-bukti-susulan') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="presensi_id" id="susulanPresensiId">
                <div class="modal-body text-start p-4">
                    <p class="text-muted small mb-3">
                        Mengunggah berkas surat dokter atau keterangan izin untuk presensi tanggal: <strong id="susulanTanggalText" class="text-dark"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Berkas Bukti</label>
                        <input type="file" name="bukti_surat" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
                        <div class="form-text">Maksimal 2MB. Format didukung: JPG, PNG, WEBP, atau PDF.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Unggah Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function bukaModalSusulan(presensiId, tanggal) {
        document.getElementById('susulanPresensiId').value = presensiId;
        document.getElementById('susulanTanggalText').innerText = tanggal;
        const modal = new bootstrap.Modal(document.getElementById('modalSusulan'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
