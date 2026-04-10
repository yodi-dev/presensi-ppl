<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Rekap Presensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Dashboard Dosen / Guru 👨‍🏫</h2>
                <p class="text-muted mt-1">Selamat datang, <?= session()->get('nama') ?></p>
            </div>
            <div>
                <a href="<?= base_url('guru/ubahpassword') ?>" class="btn btn-outline-primary me-2">Ubah Password</a>
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if (session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('pesan') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Rekap Presensi Hari Ini</h5>
                <span class="badge bg-primary fs-6"><?= date('d F Y', strtotime($tanggal)) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%" class="text-start">Nama Mahasiswa</th>
                                <th width="10%">Status</th>
                                <th width="15%">Jam Masuk</th>
                                <th width="15%">Jam Pulang</th>
                                <th width="25%">Keterangan</th>
                                <th width="10%">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($presensi)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">Belum ada data mahasiswa.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($presensi as $key => $row): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td class="text-start fw-bold"><?= $row['nama'] ?></td>

                                        <td>
                                            <?php if ($row['status'] == 'hadir'): ?>
                                                <span class="badge bg-success">Hadir</span>
                                            <?php elseif ($row['status'] == 'izin'): ?>
                                                <span class="badge bg-info">Izin</span>
                                            <?php elseif ($row['status'] == 'sakit'): ?>
                                                <span class="badge bg-warning text-dark">Sakit</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum Absen</span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= $row['jam_masuk'] ? $row['jam_masuk'] : '-' ?></td>
                                        <td><?= $row['jam_keluar'] ? $row['jam_keluar'] : '-' ?></td>

                                        <td class="text-start text-muted small">
                                            <?= $row['keterangan'] ? $row['keterangan'] : '-' ?>
                                        </td>

                                        <td>
                                            <?php if ($row['status'] == 'hadir' && $row['latitude'] && $row['longitude']): ?>
                                                <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-success">
                                                    📍 Cek Map
                                                </a>
                                            <?php else: ?>
                                                -
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>