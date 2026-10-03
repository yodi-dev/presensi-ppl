<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    body {
        background: linear-gradient(135deg, #f0fdf4 0%, #e2e8f0 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .login-container {
        flex: 1 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }

    .login-card {
        border-radius: 1.25rem;
        box-shadow: 0 10px 30px rgba(15, 81, 50, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        background: #ffffff;
    }

    .login-header {
        background: linear-gradient(135deg, #0f5132, #0a3622);
        color: white;
        padding: 2.25rem 1.5rem;
        text-align: center;
    }

    .login-body {
        padding: 2.25rem;
        background-color: #ffffff;
    }

    .form-control {
        border-radius: 0.5rem;
        padding: 0.7rem 1rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.95rem;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: #10b981;
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.2);
    }

    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: none;
        color: #64748b;
    }

    .form-control.border-start-0 {
        border-radius: 0 0.5rem 0.5rem 0;
        border-left: none;
    }

    .btn-login {
        background-color: #0f5132;
        border-color: #0f5132;
        border-radius: 2rem;
        padding: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.25s ease;
    }

    .btn-login:hover {
        background-color: #0a3622;
        border-color: #0a3622;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(15, 81, 50, 0.25);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">

                <div class="card login-card border-0">
                    <div class="login-header text-center">
                        <div class="mb-2">
                            <img src="<?= base_url('logo-skagata.png') ?>" alt="Logo SMK Negeri 3 Yogyakarta" style="width: 76px; height: 76px; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));">
                        </div>
                        <span class="text-white-50 text-uppercase fw-medium d-block mb-1" style="letter-spacing: 1.5px; font-size: 0.75rem;">Selamat Datang di</span>
                        <h4 class="fw-bold mb-0 text-white" style="letter-spacing: 0.5px;">SIPENSI SKAGATA</h4>
                        <p class="text-white-50 mb-0 small mt-1" style="font-size: 0.8rem;">Sistem Informasi Presensi &amp; Piket KBM</p>
                    </div>

                    <div class="login-body">
                        <form action="<?= base_url('auth/proses_login') ?>" method="POST">
                            <?= csrf_field() ?>

                            <div class="mb-3">
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

                            <button type="submit" class="btn btn-login btn-success w-100 text-white mt-2">
                                MASUK SISTEM <i class="bi bi-box-arrow-in-right ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>