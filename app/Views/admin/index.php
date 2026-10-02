<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .admin-card {
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

    .stat-card {
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
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

    <!-- Top Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-people text-success me-2"></i>Manajemen Pengguna &amp; Akun
            </h4>
            <p class="text-muted small mt-1 mb-0">Kelola akun Guru Pamong, GTT, dan Mahasiswa Praktikan di SMKN 3 Yogyakarta</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-skagata btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
            </button>
            <a href="<?= base_url('admin/pengaturan') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-gear me-1"></i> Pengaturan Presensi
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-white p-3 border-start border-4 border-success">
                <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Total Pengguna</small>
                <h3 class="fw-bold text-success mb-0 mt-1"><?= $totalUsers ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-white p-3 border-start border-4 border-info">
                <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Mahasiswa Praktikan</small>
                <h3 class="fw-bold text-info mb-0 mt-1"><?= $totalMahasiswa ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-white p-3 border-start border-4 border-warning">
                <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Guru Pamong / GTT</small>
                <h3 class="fw-bold text-warning mb-0 mt-1"><?= $totalGuru ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card bg-white p-3 border-start border-4 border-dark">
                <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Administrator</small>
                <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalAdmin ?></h3>
            </div>
        </div>
    </div>

    <!-- Daftar Pengguna Card -->
    <div class="card admin-card">
        <div class="card-header-custom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-person-lines-fill me-2 text-success"></i>Daftar Akun Pengguna</h6>
                <small class="text-muted">Total terfilter: <?= count($users) ?> orang</small>
            </div>

            <!-- Auto-Filter Form (onchange submit ala Sibenka) -->
            <form action="<?= base_url('admin') ?>" method="GET" class="filter-wrapper d-flex align-items-center flex-wrap gap-2 m-0">
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0" placeholder="Cari nama / username..." value="<?= esc($keyword) ?>">
                </div>

                <div class="input-group input-group-sm" style="width: auto;">
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Role --</option>
                        <option value="mahasiswa" <?= ($role_terpilih === 'mahasiswa') ? 'selected' : '' ?>>Mahasiswa</option>
                        <option value="guru" <?= ($role_terpilih === 'guru') ? 'selected' : '' ?>>Guru</option>
                        <option value="admin" <?= ($role_terpilih === 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="input-group input-group-sm" style="width: auto;">
                    <select name="jurusan" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Jurusan --</option>
                        <option value="Informatika" <?= ($jurusan_pilih === 'Informatika') ? 'selected' : '' ?>>Informatika</option>
                        <option value="PJOK" <?= ($jurusan_pilih === 'PJOK') ? 'selected' : '' ?>>PJOK</option>
                        <option value="BK" <?= ($jurusan_pilih === 'BK') ? 'selected' : '' ?>>BK</option>
                        <option value="TL" <?= ($jurusan_pilih === 'TL') ? 'selected' : '' ?>>TL</option>
                        <option value="TO" <?= ($jurusan_pilih === 'TO') ? 'selected' : '' ?>>TO</option>
                    </select>
                </div>

                <a href="<?= base_url('admin') ?>" class="btn btn-sm btn-light border text-muted py-0 px-2" title="Reset filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-custom text-center mb-0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="26%" class="text-start">Nama Lengkap</th>
                        <th width="18%">Username</th>
                        <th width="12%">Role</th>
                        <th width="16%">Jurusan Mahasiswa</th>
                        <th width="23%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 text-muted"></i>
                                Tidak ada data pengguna yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $key => $row): ?>
                            <tr>
                                <td><span class="text-muted"><?= esc($key + 1) ?></span></td>
                                <td class="text-start fw-bold text-dark"><?= esc($row['nama']) ?></td>
                                <td><code><?= esc($row['username']) ?></code></td>
                                <td>
                                    <?php if ($row['role'] === 'admin'): ?>
                                        <span class="badge bg-dark-subtle text-dark border border-dark px-2 py-1">Admin</span>
                                    <?php elseif ($row['role'] === 'guru'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Guru Pamong</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Mahasiswa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($row['jurusan']) ? '<span class="badge bg-light text-dark border">' . esc($row['jurusan']) . '</span>' : '<span class="text-muted">-</span>' ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2 me-1"
                                        onclick="bukaModalEdit(<?= (int) $row['id'] ?>, '<?= esc($row['username']) ?>', '<?= esc(addslashes($row['nama'])) ?>', '<?= esc($row['role']) ?>', '<?= esc($row['jurusan'] ?? '') ?>')">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 me-1"
                                        onclick="bukaModalReset(<?= (int) $row['id'] ?>, '<?= esc(addslashes($row['nama'])) ?>')">
                                        <i class="bi bi-key"></i> Reset
                                    </button>
                                    <?php if ((int) $row['id'] !== (int) session()->get('id_user')): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2"
                                            onclick="konfirmasiHapus(<?= (int) $row['id'] ?>, '<?= esc(addslashes($row['nama'])) ?>')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
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

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/tambah-user') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body text-start p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Ahmad Fauzi, S.Pd." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: ahmad123" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Role / Hak Akses</label>
                        <select name="role" class="form-select" id="tambahRoleSelect" onchange="toggleJurusanField('tambahRoleSelect', 'tambahJurusanWrapper')" required>
                            <option value="mahasiswa" selected>Mahasiswa Praktikan</option>
                            <option value="guru">Guru Pamong / GTT</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3" id="tambahJurusanWrapper">
                        <label class="form-label fw-semibold small">Jurusan Mahasiswa</label>
                        <select name="jurusan" class="form-select">
                            <option value="">-- Pilih Jurusan Mahasiswa --</option>
                            <option value="Informatika">Informatika</option>
                            <option value="PJOK">PJOK</option>
                            <option value="BK">BK</option>
                            <option value="TL">TL</option>
                            <option value="TO">TO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password Awal</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skagata rounded-pill px-4">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Edit Data Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/edit-user') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="editUserId">
                <div class="modal-body text-start p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="nama" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username</label>
                        <input type="text" name="username" id="editUsername" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Role / Hak Akses</label>
                        <select name="role" id="editRole" class="form-select" onchange="toggleJurusanField('editRole', 'editJurusanWrapper')" required>
                            <option value="mahasiswa">Mahasiswa Praktikan</option>
                            <option value="guru">Guru Pamong / GTT</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3" id="editJurusanWrapper">
                        <label class="form-label fw-semibold small">Jurusan Mahasiswa</label>
                        <select name="jurusan" id="editJurusan" class="form-select">
                            <option value="">-- Pilih Jurusan Mahasiswa --</option>
                            <option value="Informatika">Informatika</option>
                            <option value="PJOK">PJOK</option>
                            <option value="BK">BK</option>
                            <option value="TL">TL</option>
                            <option value="TO">TO</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skagata rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Reset Password Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/reset-password') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="resetUserId">
                <div class="modal-body text-start p-4">
                    <p class="text-muted small mb-3">
                        Reset password untuk akun: <strong id="resetUserNama" class="text-dark"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Tersembunyi untuk Hapus User via POST + CSRF -->
<form id="formHapusUser" action="<?= base_url('admin/hapus-user') ?>" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="user_id" id="hapusUserId">
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleJurusanField(roleSelectId, wrapperId) {
        const role = document.getElementById(roleSelectId).value;
        const wrapper = document.getElementById(wrapperId);
        if (role === 'mahasiswa') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }

    function bukaModalEdit(id, username, nama, role, jurusan) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editUsername').value = username;
        document.getElementById('editNama').value = nama;
        document.getElementById('editRole').value = role;
        document.getElementById('editJurusan').value = jurusan;
        toggleJurusanField('editRole', 'editJurusanWrapper');

        const modal = new bootstrap.Modal(document.getElementById('modalEditUser'));
        modal.show();
    }

    function bukaModalReset(id, nama) {
        document.getElementById('resetUserId').value = id;
        document.getElementById('resetUserNama').innerText = nama;

        const modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
        modal.show();
    }

    function konfirmasiHapus(id, nama) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            html: `Akun <strong>${nama}</strong> dan seluruh riwayat presensi terkait akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Akun!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('hapusUserId').value = id;
                document.getElementById('formHapusUser').submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>
