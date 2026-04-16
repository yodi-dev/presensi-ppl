<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .dashboard-card {
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

    .table-custom thead th {
        background-color: #003366;
        color: #ffffff;
        font-weight: 500;
        border-bottom: none;
        padding: 1rem;
        white-space: nowrap;
    }

    .table-custom tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .btn-action-top {
        border-radius: 0.5rem;
        font-weight: 500;
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
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-person-workspace text-primary me-2"></i>Dashboard Guru</h3>
            <p class="text-muted mt-1 mb-0">Selamat datang, <strong><?= session()->get('nama') ?></strong></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-primary btn-action-top dropdown-toggle" type="button" id="dropdownMenuLaporan" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Laporan
                </button>

                <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="dropdownMenuLaporan">
                    <li>
                        <a class="dropdown-item py-2" href="<?= base_url('guru/laporan_piket') ?>">
                            <i class="bi bi-camera text-primary me-2"></i> Laporan Piket
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?= base_url('guru/laporan') ?>">
                            <i class="bi bi-calendar-check text-success me-2"></i> Laporan Bulanan
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= base_url('ubah_password') ?>" class="btn btn-outline-secondary btn-action-top">
                <i class="bi bi-key me-1"></i> Ubah Password
            </a>
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger btn-action-top">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>

    <div class="card dashboard-card mt-4">

        <div class="card-header-custom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Rekap Presensi Harian</h5>
                <p class="text-muted small mb-0">
                    Menampilkan data untuk tanggal: <span class="fw-bold text-primary"><?= date('d F Y', strtotime($tanggal)) ?></span>
                </p>
            </div>

            <div class="d-flex flex-column flex-md-row gap-2">
                <form action="<?= base_url('guru') ?>" method="GET" class="d-flex flex-column flex-md-row gap-3 align-items-md-center">

                    <div class="filter-wrapper d-flex align-items-center gap-2">
                        <label for="jurusan" class="fw-bold text-muted small mb-0 text-nowrap"><i class="bi bi-funnel"></i> Jurusan:</label>
                        <select name="jurusan" id="jurusan" class="form-select form-select-sm border-0 shadow-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Jurusan --</option>
                            <option value="Informatika" <?= ($jurusan_terpilih == 'Informatika') ? 'selected' : '' ?>>Informatika</option>
                            <option value="PJOK" <?= ($jurusan_terpilih == 'PJOK') ? 'selected' : '' ?>>PJOK</option>
                            <option value="BK" <?= ($jurusan_terpilih == 'BK') ? 'selected' : '' ?>>BK</option>
                            <option value="TL" <?= ($jurusan_terpilih == 'TL') ? 'selected' : '' ?>>TL</option>
                            <option value="TO" <?= ($jurusan_terpilih == 'TO') ? 'selected' : '' ?>>TO</option>
                        </select>
                    </div>

                    <div class="filter-wrapper d-flex align-items-center gap-2">
                        <label for="tanggal" class="fw-bold text-muted small mb-0 text-nowrap"><i class="bi bi-calendar-event me-1"></i>Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control form-control-sm border-0 shadow-sm" style="width: auto;" value="<?= $tanggal ?>" required>

                        <button type="submit" class="btn btn-sm btn-primary shadow-sm"><i class="bi bi-search"></i> Cari</button>
                        <a href="<?= base_url('guru') ?>" class="btn btn-sm btn-light border shadow-sm" title="Reset ke hari ini"><i class="bi bi-arrow-clockwise"></i></a>
                    </div>

                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-custom text-center">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="22%" class="text-start">Nama Mahasiswa</th>
                        <th width="10%">Status</th>
                        <th width="12%">Jam Masuk</th>
                        <th width="12%">Jam Pulang</th>
                        <th width="25%" class="text-start">Keterangan</th>
                        <th width="14%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($presensi)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-black-50"></i>
                                Belum ada data presensi mahasiswa pada tanggal ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($presensi as $key => $row): ?>
                            <tr>
                                <td><span class="text-muted fw-semibold"><?= $key + 1 ?></span></td>
                                <td class="text-start fw-bold text-dark"><?= $row['nama'] ?></td>

                                <td>
                                    <?php if ($row['status'] == 'hadir'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Hadir</span>
                                    <?php elseif ($row['status'] == 'izin'): ?>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">Izin</span>
                                    <?php elseif ($row['status'] == 'sakit'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Sakit</span>
                                    <?php elseif ($row['status'] == 'alpa'): ?>
                                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-2 py-1">Alpa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Belum Absen</span>
                                    <?php endif; ?>
                                </td>

                                <td><span class="fw-semibold <?= $row['jam_masuk'] ? 'text-success' : 'text-muted' ?>"><?= $row['jam_masuk'] ? $row['jam_masuk'] : '--:--' ?></span></td>
                                <td><span class="fw-semibold <?= $row['jam_keluar'] ? 'text-warning' : 'text-muted' ?>"><?= $row['jam_keluar'] ? $row['jam_keluar'] : '--:--' ?></span></td>

                                <td class="text-start text-muted small">
                                    <?= $row['keterangan'] ? $row['keterangan'] : '<span class="text-black-50 fst-italic">Tidak ada keterangan</span>' ?>
                                </td>

                                <td>
                                    <?php if ($row['status'] == 'hadir' && $row['latitude']): ?>
                                        <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mb-1">
                                            <i class="bi bi-geo-alt"></i> Map
                                        </a>
                                    <?php endif; ?>

                                    <div class="dropdown d-inline">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-pencil-square"></i> Status
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="<?= base_url('guru/update_status/' . $row['id'] . '/hadir?tgl=' . $tanggal) ?>">Set Hadir</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('guru/update_status/' . $row['id'] . '/izin?tgl=' . $tanggal) ?>">Set Izin</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('guru/update_status/' . $row['id'] . '/sakit?tgl=' . $tanggal) ?>">Set Sakit</a></li>
                                            <li><a class="dropdown-item text-danger" href="<?= base_url('guru/update_status/' . $row['id'] . '/alpa?tgl=' . $tanggal) ?>">Set Alpa</a></li>
                                        </ul>
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
<?= $this->endSection() ?>