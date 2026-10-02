<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .report-card {
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(15, 81, 50, 0.05);
        background: #ffffff;
        overflow: hidden;
    }

    .card-header-custom {
        background-color: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .table-custom thead th {
        background-color: #0f5132;
        color: #ffffff;
        font-weight: 600;
        border-bottom: none;
        padding: 0.9rem;
        white-space: nowrap;
        font-size: 0.85rem;
    }

    .table-custom tbody td {
        padding: 0.85rem;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .filter-wrapper {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.4rem 0.75rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-camera text-success me-2"></i>Laporan Piket KBM
            </h4>
            <p class="text-muted small mt-1 mb-0">Dokumentasi kegiatan piket KBM mahasiswa praktikan di SMKN 3 Yogyakarta</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('guru') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="card report-card">
        <div class="card-header-custom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h6 class="fw-bold text-dark mb-1">Daftar Foto Piket KBM</h6>
                <p class="text-muted small mb-0">Menampilkan rekaman piket pada tanggal: <span class="fw-semibold text-success"><?= esc(date('d F Y', strtotime($tanggal))) ?></span></p>
            </div>

            <!-- Auto-Filter Form (onchange submit ala Sibenka) -->
            <form action="<?= base_url('guru/laporan_piket') ?>" method="GET" class="filter-wrapper d-flex align-items-center flex-wrap gap-2 m-0">
                <label for="tanggal" class="fw-semibold text-muted small mb-0"><i class="bi bi-calendar-event me-1"></i>Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control form-control-sm border-0 bg-transparent shadow-none" value="<?= esc($tanggal) ?>" onchange="this.form.submit()" required>
                <a href="<?= base_url('guru/laporan_piket') ?>" class="btn btn-sm btn-light border text-muted py-0 px-2" title="Reset ke hari ini">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </form>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-custom text-center mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="12%">Waktu</th>
                            <th width="30%" class="text-start">Nama Mahasiswa</th>
                            <th width="18%">Jurusan Asal</th>
                            <th width="20%">Dokumentasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataPiket)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-camera-video-off fs-1 d-block mb-2 text-muted"></i>
                                    Belum ada dokumentasi piket KBM pada tanggal ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($dataPiket as $row) : ?>
                                <tr>
                                    <td><span class="text-muted fw-semibold"><?= esc($no++) ?></span></td>
                                    <td><?= esc(date('d M Y', strtotime($row['tanggal']))) ?></td>
                                    <td class="fw-semibold text-success"><?= esc(date('H:i', strtotime($row['waktu']))) ?> WIB</td>
                                    <td class="text-start fw-bold text-dark"><?= esc($row['nama']) ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= esc($row['jurusan']) ?></span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalFoto<?= (int) $row['id'] ?>">
                                            <i class="bi bi-image me-1"></i> Lihat Foto
                                        </button>

                                        <!-- Modal Foto Bukti -->
                                        <div class="modal fade" id="modalFoto<?= (int) $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                                                    <div class="modal-header border-0 pb-2">
                                                        <h6 class="modal-title fw-bold text-dark">
                                                            <i class="bi bi-camera text-success me-1"></i> Bukti Piket: <?= esc($row['nama']) ?>
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-0 text-center bg-dark">
                                                        <img src="<?= base_url('uploads/piket/' . esc($row['foto_bukti'])) ?>"
                                                            alt="Bukti Piket KBM" class="img-fluid w-100" style="max-height: 70vh; object-fit: contain;">
                                                    </div>
                                                    <div class="modal-footer border-0 py-2 bg-light d-flex justify-content-between">
                                                        <small class="text-muted"><?= esc($row['tanggal']) ?> &bull; <?= esc($row['waktu']) ?> WIB</small>
                                                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>