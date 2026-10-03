<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Presensi PPL' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png'); ?>">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --skagata-primary: #0f5132;
            --skagata-primary-hover: #0a3622;
            --skagata-mint: #10b981;
            --skagata-bg: #f8fafc;
            --skagata-text: #0f172a;
            --skagata-muted: #64748b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--skagata-bg);
            color: var(--skagata-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-skagata {
            background-color: var(--skagata-primary);
            box-shadow: 0 4px 20px rgba(15, 81, 50, 0.15);
        }

        .navbar-skagata .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .navbar-skagata .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .navbar-skagata .nav-link:hover,
        .navbar-skagata .nav-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .badge-role {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.35rem 0.65rem;
            border-radius: 2rem;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .main-content {
            flex: 1 0 auto;
        }

        .footer-skagata {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            color: var(--skagata-muted);
            font-size: 0.875rem;
            margin-top: auto;
        }

        .btn-skagata {
            background-color: var(--skagata-primary);
            color: #ffffff;
            border: none;
        }

        .btn-skagata:hover {
            background-color: var(--skagata-primary-hover);
            color: #ffffff;
        }
    </style>

    <?= $this->renderSection('styles'); ?>

</head>

<body>

    <?php if (session()->get('logged_in')): ?>
        <?php
        $role = session()->get('role');
        $namaUser = session()->get('nama') ?? 'Pengguna';
        $currentUri = service('uri')->getPath();

        $roleLabels = [
            'admin'       => 'Administrator',
            'guru'        => 'Guru Pamong',
            'guru_pamong' => 'Guru Pamong',
            'gtt'         => 'Guru Tidak Tetap',
            'mahasiswa'   => 'Mahasiswa Praktikan'
        ];
        $roleLabel = $roleLabels[$role] ?? ucfirst($role ?? '');
        ?>
        <nav class="navbar navbar-expand-lg navbar-dark navbar-skagata py-2">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url($role === 'admin' ? 'admin' : ($role === 'guru' ? 'guru' : 'mahasiswa')) ?>">
                    <span class="fs-4"><i class="bi bi-geo-alt-fill text-warning"></i></span>
                    <div class="lh-sm">
                        <span class="d-block fw-bold tracking-wide">SIPENSI SKAGATA</span>
                        <small class="d-block fw-normal text-white-50" style="font-size: 0.7rem;">SMK Negeri 3 Yogyakarta</small>
                    </div>
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                        <?php if ($role === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($currentUri, 'admin/pengaturan') === false && strpos($currentUri, 'admin') !== false ? 'active' : '' ?>" href="<?= base_url('admin') ?>">
                                    <i class="bi bi-people me-1"></i> Data Pengguna
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($currentUri, 'admin/pengaturan') !== false ? 'active' : '' ?>" href="<?= base_url('admin/pengaturan') ?>">
                                    <i class="bi bi-gear me-1"></i> Pengaturan Presensi
                                </a>
                            </li>
                        <?php elseif ($role === 'guru' || $role === 'guru_pamong'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($currentUri === 'guru' || $currentUri === 'guru/') ? 'active' : '' ?>" href="<?= base_url('guru') ?>">
                                    <i class="bi bi-calendar2-check me-1"></i> Presensi Harian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($currentUri, 'guru/laporan_piket') !== false ? 'active' : '' ?>" href="<?= base_url('guru/laporan_piket') ?>">
                                    <i class="bi bi-camera me-1"></i> Laporan Piket
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($currentUri, 'guru/laporan') !== false && strpos($currentUri, 'guru/laporan_piket') === false ? 'active' : '' ?>" href="<?= base_url('guru/laporan') ?>">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Laporan Bulanan
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($currentUri === 'mahasiswa' || $currentUri === 'mahasiswa/') ? 'active' : '' ?>" href="<?= base_url('mahasiswa') ?>">
                                    <i class="bi bi-house me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($currentUri, 'mahasiswa/piket') !== false ? 'active' : '' ?>" href="<?= base_url('mahasiswa/piket') ?>">
                                    <i class="bi bi-camera me-1"></i> Piket KBM
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($currentUri, 'mahasiswa/riwayat') !== false ? 'active' : '' ?>" href="<?= base_url('mahasiswa/riwayat') ?>">
                                    <i class="bi bi-clock-history me-1"></i> Riwayat Mandiri
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div class="d-flex align-items-center gap-3">
                        <span class="badge badge-role d-none d-md-inline-block">
                            <?= esc($roleLabel) ?>
                        </span>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                                <span class="fw-semibold text-truncate" style="max-width: 140px;"><?= esc($namaUser) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenuDropdown">
                                <li class="px-3 py-2 text-muted small border-bottom">
                                    Login sebagai: <strong><?= esc($namaUser) ?></strong>
                                    <br><span class="badge bg-light text-dark border mt-1"><?= esc($roleLabel) ?></span>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= base_url('ubah_password') ?>">
                                        <i class="bi bi-key text-muted me-2"></i> Ubah Password
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="<?= base_url('auth/logout') ?>">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="main-content">
        <?= $this->renderSection('content'); ?>
    </main>

    <footer class="footer-skagata py-3 text-center">
        <div class="container">
            <div class="small text-muted">
                Crafted with <span class="text-danger">❤️</span> by <a href="https://awanbeo.my.id" target="_blank" class="fw-semibold text-success text-decoration-none">awanbeo.my.id</a>
            </div>
            <div class="small text-muted mt-1 fw-medium" style="font-size: 0.8rem;">
                SMK Negeri 3 Yogyakarta
            </div>
        </div>
    </footer>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: <?= json_encode((string) session()->getFlashdata('error')) ?>,
                    confirmButtonColor: '#0f5132',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('pesan')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: <?= json_encode((string) session()->getFlashdata('pesan')) ?>,
                    confirmButtonColor: '#0f5132',
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?= $this->renderSection('scripts'); ?>

</body>

</html>