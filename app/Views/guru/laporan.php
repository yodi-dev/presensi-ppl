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

    @media print {

        /* Sembunyikan elemen non-cetak */
        .btn,
        .filter-wrapper,
        header,
        footer,
        .nav {
            display: none !important;
        }

        /* Hilangkan jarak dan shadow card */
        .report-card {
            box-shadow: none !important;
            border: none !important;
        }

        /* Maksimalkan lebar layar */
        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Styling tabel saat di-print */
        .table-custom {
            font-size: 12px;
            /* Perkecil font biar muat banyak kolom */
            width: 100% !important;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #000 !important;
            color: #000 !important;
            padding: 5px !important;
            /* Kurangi padding biar gak mekar */
        }

        /* Set warna hitam putih untuk print */
        .table-custom thead tr:first-child th {
            background-color: #e0e0e0 !important;
            -webkit-print-color-adjust: exact;
        }

        .table-custom thead tr:nth-child(2) th {
            background-color: #f5f5f5 !important;
            -webkit-print-color-adjust: exact;
        }

        /* Matikan scroll */
        .table-responsive {
            overflow: visible !important;
        }

        tr {
            page-break-inside: avoid;
        }

        @page {
            size: landscape;
            margin: 1cm;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4 mb-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>Laporan Bulanan</h3>
            <p class="text-muted mt-1 mb-0">Rekapitulasi total kehadiran mahasiswa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('guru') ?>" class="btn btn-outline-secondary rounded-pill shadow-sm px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="bi bi-printer me-1"></i> Cetak PDF
            </button>
        </div>
    </div>

    <div class="card report-card mt-4">

        <div class="card-header-custom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-funnel me-2"></i>Filter Laporan</h5>

            <form action="<?= base_url('guru/laporan') ?>" method="GET" class="filter-wrapper d-flex align-items-center flex-wrap gap-2 m-0">

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
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3 text-muted"></i></span>
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

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-custom text-center mb-0">
                    <thead>
                        <tr>
                            <th width="5%" rowspan="2" class="align-middle border-bottom-0">No</th>
                            <th width="35%" rowspan="2" class="align-middle text-start border-bottom-0">Nama Mahasiswa</th>
                            <th colspan="5" class="border-bottom-0 border-start text-center">Total Kehadiran</th>
                        </tr>
                        <tr>
                            <th class="border-start">Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpa</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2 text-black-50"></i>
                                    Data laporan pada bulan dan tahun ini tidak ditemukan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan as $key => $row): ?>
                                <tr>
                                    <td><span class="text-muted"><?= $key + 1 ?></span></td>
                                    <td class="text-start fw-bold text-dark"><?= $row['nama'] ?></td>

                                    <td class="fw-semibold text-success"><?= $row['total_hadir'] ?></td>
                                    <td class="fw-semibold text-info-emphasis"><?= $row['total_izin'] ?></td>
                                    <td class="fw-semibold text-warning-emphasis"><?= $row['total_sakit'] ?></td>
                                    <td class="fw-semibold text-danger"><?= $row['total_alpa'] ?></td>

                                    <td class="fw-bold bg-light">
                                        <?php
                                        $total_masuk = $row['total_hadir'] + $row['total_izin'] + $row['total_sakit'] + $row['total_alpa'];
                                        $persen = ($total_masuk > 0) ? ($row['total_hadir'] / $total_masuk) * 100 : 0;
                                        echo number_format($persen, 0) . '%';
                                        ?>
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