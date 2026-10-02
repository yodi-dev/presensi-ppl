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

    .table-custom {
        margin-bottom: 0;
    }

    .table-custom thead tr:first-child th {
        background-color: #0f5132;
        color: #ffffff;
        font-weight: 600;
        border-color: #0a3622;
        padding: 0.9rem;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .table-custom thead tr:nth-child(2) th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 600;
        border-color: #e2e8f0;
        padding: 0.7rem;
        font-size: 0.8rem;
    }

    .table-custom tbody td {
        padding: 0.8rem;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .filter-wrapper {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.4rem 0.75rem;
    }

    /* Area Kop Surat & Tanda Tangan Cetak (Hanya tampil saat print) */
    .print-only-header,
    .print-only-signature {
        display: none;
    }

    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt;
        }

        .btn,
        .filter-wrapper,
        .navbar-skagata,
        .footer-skagata,
        nav,
        footer {
            display: none !important;
        }

        .report-card {
            box-shadow: none !important;
            border: none !important;
        }

        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .print-only-header {
            display: block !important;
            margin-bottom: 20px;
        }

        .print-only-signature {
            display: block !important;
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .kop-surat-border {
            border-bottom: 3px double #000000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .table-custom {
            font-size: 10pt !important;
            width: 100% !important;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #000000 !important;
            color: #000000 !important;
            padding: 5px !important;
        }

        .table-custom thead tr:first-child th {
            background-color: #e2e8f0 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact;
        }

        .table-custom thead tr:nth-child(2) th {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact;
        }

        .table-responsive {
            overflow: visible !important;
        }

        @page {
            size: landscape;
            margin: 1.2cm;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Top Action Bar (Sembunyi saat Cetak) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Laporan Bulanan Presensi
            </h4>
            <p class="text-muted small mt-1 mb-0">Rekapitulasi resmi kehadiran mahasiswa praktikan &amp; GTT berbasis 5 hari kerja efektif</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('guru') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="<?= base_url('guru/laporan/export-excel?bulan=' . esc($bulan_pilih) . '&tahun=' . esc($tahun_pilih)) ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel
            </a>
            <button onclick="window.print()" class="btn btn-skagata btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Kop Resmi SMK Negeri 3 Yogyakarta (Hanya Muncul Saat Print / Cetak PDF) -->
    <div class="print-only-header text-center">
        <div class="lh-sm">
            <h6 class="text-uppercase mb-0 fw-semibold" style="letter-spacing: 1px; font-size: 11pt;">Pemerintah Daerah Daerah Istimewa Yogyakarta</h6>
            <h6 class="text-uppercase mb-0 fw-semibold" style="letter-spacing: 1px; font-size: 11pt;">Dinas Pendidikan, Pemuda, dan Olahraga</h6>
            <h4 class="text-uppercase mb-0 fw-bold mt-1" style="letter-spacing: 1.5px; font-size: 15pt;">SMK NEGERI 3 YOGYAKARTA</h4>
            <p class="mb-0 small text-muted" style="font-size: 9pt;">Jl. R.W. Monginsidi No. 2, Jetis, Yogyakarta 55233 | Telp: (0274) 513507 | Laman: smkn3jogja.sch.id</p>
        </div>
        <div class="kop-surat-border mt-2"></div>
        <h5 class="fw-bold text-uppercase mt-2 mb-1" style="font-size: 12pt;">REKAPITULASI PRESENSI MAHASISWA PRAKTIKAN</h5>
        <p class="small text-muted mb-0" style="font-size: 10pt;">
            Periode: <strong><?= date('F', mktime(0, 0, 0, (int)$bulan_pilih, 10)) ?> <?= esc($tahun_pilih) ?></strong> &bull; Basis 5 Hari Kerja Efektif (Senin - Jumat)
        </p>
    </div>

    <div class="card report-card">

        <div class="card-header-custom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-funnel me-2 text-success"></i>Filter Periode Presensi</h6>

            <!-- Auto-Filter Form (Otomatis reload saat bulan / tahun berubah) -->
            <form action="<?= base_url('guru/laporan') ?>" method="GET" class="filter-wrapper d-flex align-items-center flex-wrap gap-2 m-0">

                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar-month"></i></span>
                    <select name="bulan" class="form-select border-start-0" onchange="this.form.submit()" required>
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

                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                    <select name="tahun" class="form-select border-start-0" onchange="this.form.submit()" required>
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

            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-custom text-center mb-0">
                    <thead>
                        <tr>
                            <th width="5%" rowspan="2" class="align-middle border-bottom-0">No</th>
                            <th width="32%" rowspan="2" class="align-middle text-start border-bottom-0">Nama Lengkap Mahasiswa</th>
                            <th colspan="6" class="border-bottom-0 border-start text-center">Akumulasi Kehadiran (Hari)</th>
                        </tr>
                        <tr>
                            <th class="border-start">Hadir</th>
                            <th>Terlambat</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpa</th>
                            <th>% Efektif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2 text-muted"></i>
                                    Tidak ada data presensi pada periode bulan ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan as $key => $row): ?>
                                <tr>
                                    <td><span class="text-muted"><?= esc($key + 1) ?></span></td>
                                    <td class="text-start fw-bold text-dark"><?= esc($row['nama']) ?></td>

                                    <td class="fw-semibold text-success"><?= $row['total_hadir'] ?></td>
                                    <td class="fw-semibold text-warning-emphasis"><?= $row['total_terlambat'] ?></td>
                                    <td class="fw-semibold text-info-emphasis"><?= $row['total_izin'] ?></td>
                                    <td class="fw-semibold text-secondary-emphasis"><?= $row['total_sakit'] ?></td>
                                    <td class="fw-semibold text-danger"><?= $row['total_alpa'] ?></td>

                                    <td class="fw-bold bg-light">
                                        <?php
                                        $total_masuk = $row['total_hadir'] + $row['total_terlambat'] + $row['total_izin'] + $row['total_sakit'] + $row['total_alpa'];
                                        $total_hadir_efektif = $row['total_hadir'] + $row['total_terlambat'];
                                        $persen = ($total_masuk > 0) ? ($total_hadir_efektif / $total_masuk) * 100 : 0;
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

    <!-- Lembar Tanda Tangan Kedinasan (Hanya Muncul Saat Print / PDF) -->
    <div class="print-only-signature">
        <table style="width: 100%; border: none; font-size: 10pt;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                    Mengetahui,<br>
                    <strong>Koordinator PK / PPL Perguruan Tinggi</strong>
                    <br><br><br><br><br>
                    (......................................................)<br>
                    NIP / NIDN.
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                    Yogyakarta, <?= date('d F Y') ?><br>
                    <strong>Guru Pamong Pembimbing SMKN 3 Yogyakarta</strong>
                    <br><br><br><br><br>
                    <strong><?= esc(session()->get('nama')) ?></strong><br>
                    NIP. ..................................................
                </td>
            </tr>
        </table>
    </div>

</div>
<?= $this->endSection() ?>