<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan - Presensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Laporan Bulanan</h2>
            <a href="<?= base_url('guru') ?>" class="btn btn-outline-secondary">⬅️ Kembali ke Dashboard</a>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0 fw-bold">Filter Laporan</h5>

                <form action="<?= base_url('guru/laporan') ?>" method="GET" class="d-flex align-items-center mt-2 mt-md-0 gap-2">

                    <select name="bulan" class="form-select form-select-sm" required>
                        <?php
                        $namaBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                        foreach ($namaBulan as $angka => $nama):
                        ?>
                            <option value="<?= $angka ?>" <?= ($bulan_pilih == $angka) ? 'selected' : '' ?>>
                                <?= $nama ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="tahun" class="form-select form-select-sm" required>
                        <?php
                        $tahunSekarang = date('Y');
                        for ($t = $tahunSekarang; $t >= 2023; $t--):
                        ?>
                            <option value="<?= $t ?>" <?= ($tahun_pilih == $t) ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <button type="submit" class="btn btn-sm btn-primary px-3">Tampilkan</button>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" rowspan="2" class="align-middle">No</th>
                                <th width="35%" rowspan="2" class="align-middle text-start">Nama Mahasiswa</th>
                                <th colspan="3">Total Kehadiran</th>
                            </tr>
                            <tr>
                                <th class="bg-success text-white">Hadir</th>
                                <th class="bg-info text-white">Izin</th>
                                <th class="bg-warning text-dark">Sakit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($laporan)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">Data tidak ditemukan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($laporan as $key => $row): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td class="text-start fw-bold"><?= $row['nama'] ?></td>

                                        <td><span class="badge bg-success px-3 py-2 fs-6"><?= $row['total_hadir'] ?></span></td>
                                        <td><span class="badge bg-info px-3 py-2 fs-6"><?= $row['total_izin'] ?></span></td>
                                        <td><span class="badge bg-warning text-dark px-3 py-2 fs-6"><?= $row['total_sakit'] ?></span></td>
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