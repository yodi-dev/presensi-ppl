<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .password-card {
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: none;
        overflow: hidden;
    }

    .password-header {
        background-color: #003366;
        color: white;
        padding: 2.5rem 1.5rem;
        text-align: center;
    }

    .password-body {
        padding: 2.5rem;
        background-color: #ffffff;
    }

    .form-control {
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: #003366;
        box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.25);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #6c757d;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .btn-primary-custom {
        background-color: #003366;
        border-color: #003366;
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        background-color: #002244;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
    }

    .btn-outline-custom {
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-weight: 600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-10 col-md-8 col-lg-6 col-xl-5">

            <div class="card password-card">
                <div class="password-header">
                    <i class="bi bi-shield-lock-fill fs-1 d-block mb-2"></i>
                    <h4 class="fw-bold mb-0">Ubah Password</h4>
                    <p class="text-white-50 small mb-0 mt-1">Pastikan akun kamu selalu aman</p>
                </div>

                <div class="password-body">

                    <form action="<?= base_url('proses_ubah_password') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold small">Password Lama</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="bi bi-key"></i></span>
                                <input type="password" name="password_lama" id="passLama" class="form-control border-start-0 border-end-0" placeholder="Masukkan password saat ini" required>
                                <span class="input-group-text border-start-0 cursor-pointer toggle-password" data-target="passLama">
                                    <i class="bi bi-eye-slash"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold small">Password Baru</label>
                            <div class="input-group mb-1">
                                <span class="input-group-text border-end-0"><i class="bi bi-shield-plus"></i></span>
                                <input type="password" name="password_baru" id="passBaru" class="form-control border-start-0 border-end-0" placeholder="Minimal 6 karakter" required minlength="6">
                                <span class="input-group-text border-start-0 cursor-pointer toggle-password" data-target="passBaru">
                                    <i class="bi bi-eye-slash"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label text-muted fw-semibold small">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="bi bi-shield-check"></i></span>
                                <input type="password" name="konfirmasi_password" id="passKonfirm" class="form-control border-start-0 border-end-0" placeholder="Ulangi password baru" required minlength="6">
                                <span class="input-group-text border-start-0 cursor-pointer toggle-password" data-target="passKonfirm">
                                    <i class="bi bi-eye-slash"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary-custom text-white">
                                <i class="bi bi-save me-2"></i> Simpan Password
                            </button>
                            <a href="<?= base_url('mahasiswa') ?>" class="btn btn-outline-secondary btn-outline-custom">
                                <i class="bi bi-arrow-left me-2"></i> Batal / Kembali
                            </a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ambil target input dari data-target
                const targetId = this.getAttribute('data-target');
                const inputElement = document.getElementById(targetId);
                const iconElement = this.querySelector('i');

                // Toggle tipe input dan ganti icon
                if (inputElement.type === "password") {
                    inputElement.type = "text";
                    iconElement.classList.remove('bi-eye-slash');
                    iconElement.classList.add('bi-eye');
                    iconElement.classList.add('text-primary'); // Kasih warna biru pas aktif
                } else {
                    inputElement.type = "password";
                    iconElement.classList.remove('bi-eye');
                    iconElement.classList.add('bi-eye-slash');
                    iconElement.classList.remove('text-primary');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>