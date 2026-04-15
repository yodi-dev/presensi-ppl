<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .report-card {
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

    .table-custom {
        margin-bottom: 0;
    }

    /* Warna Header Utama: Navy Blue */
    .table-custom thead tr:first-child th {
        background-color: #003366;
        color: #ffffff;
        font-weight: 500;
        border-color: #002244;
        padding: 1rem;
        vertical-align: middle;
    }

    /* Warna Sub-Header: Light Gray biar clean */
    .table-custom thead tr:nth-child(2) th {
        background-color: #f4f6f9;
        color: #333;
        font-weight: 600;
        border-color: #dee2e6;
        padding: 0.75rem;
    }

    .table-custom tbody td {
        padding: 0.8rem;
        vertical-align: middle;
    }

    .filter-wrapper {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4 mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>Laporan Piket KBM</h3>
            <p class="text-muted mt-1 mb-0">Rekapitulasi total kehadiran mahasiswa piket KBM</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('guru') ?>" class="btn btn-outline-secondary rounded-pill shadow-sm px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white pt-4 pb-2 border-0">
            <h5 class="fw-bold"><i class="bi bi-journal-text me-2"></i> Laporan Piket KBM</h5>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-custom text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Nama Mahasiswa</th>
                            <th>Jurusan</th>
                            <th class="text-center">Bukti Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataPiket)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data presensi piket.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($dataPiket as $row) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= date('H:i', strtotime($row['waktu'])) ?> WIB</td>
                                    <td class="fw-bold"><?= $row['nama'] ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $row['jurusan'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info text-white"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalFoto<?= $row['id'] ?>">
                                            <i class="bi bi-image"></i> Lihat Bukti
                                        </button>

                                        <div class="modal fade" id="modalFoto<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title fs-6 text-start">Bukti Piket: <?= $row['nama'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-0">
                                                        <img src="<?= base_url('uploads/piket/' . $row['foto_bukti']) ?>"
                                                            alt="Bukti Piket" class="img-fluid w-100">
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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