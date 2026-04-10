<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    /* Kita timpa background body khusus untuk login */
    body {
        background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-card {
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .login-header {
        background-color: #003366;
        color: white;
        padding: 2.5rem 1.5rem;
        text-align: center;
    }

    .login-body {
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
        border-radius: 0.5rem 0 0 0.5rem;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-right: none;
        color: #6c757d;
    }

    .form-control.border-start-0 {
        border-radius: 0 0.5rem 0.5rem 0;
        border-left: none;
    }

    .btn-login {
        background-color: #003366;
        border-color: #003366;
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background-color: #002244;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container w-100">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">

            <div class="card login-card border-0">
                <div class="login-header">
                    <i class="bi bi-geo-alt-fill fs-1 mb-2 d-block"></i>
                    <h4 class="fw-bold mb-0">Presensi PPL</h4>
                    <p class="text-white-50 mb-0 small mt-1">Sistem Absensi Digital</p>
                </div>

                <div class="login-body">
                    <form action="<?= base_url('auth/proses_login') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold small">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login btn-primary w-100 text-white mt-2">
                            MASUK <i class="bi bi-box-arrow-in-right ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>