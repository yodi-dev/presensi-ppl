<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<style>
    .camera-card {
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(15, 81, 50, 0.05);
        overflow: hidden;
    }

    .camera-viewport {
        background-color: #000000;
        border-radius: 1rem;
        position: relative;
        overflow: hidden;
        min-height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #kamera,
    #hasil-foto {
        width: 100%;
        height: auto;
        max-height: 65vh;
        object-fit: cover;
        display: block;
    }

    .camera-switch-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.55);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s ease;
    }

    .camera-switch-btn:hover {
        background: rgba(0, 0, 0, 0.8);
        transform: rotate(180deg);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card camera-card">
                <div class="card-body p-4 text-center">

                    <?php if ($sudahPiket) : ?>
                        <!-- Keadaan Sudah Presensi Piket Hari Ini -->
                        <div class="py-4">
                            <div class="mb-3">
                                <i class="bi bi-shield-check text-success" style="font-size: 3.5rem;"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Presensi Piket KBM Tercatat</h5>
                            <p class="text-muted small">Anda telah mendokumentasikan kegiatan piket KBM hari ini di lingkungan SMKN 3 Yogyakarta.</p>

                            <div class="text-start small bg-light p-3 rounded-3 mb-4 border">
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Tanggal:</span>
                                    <span class="fw-semibold text-dark"><?= esc($dataPiket['tanggal']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-1">
                                    <span class="text-muted">Waktu:</span>
                                    <span class="fw-semibold text-dark"><?= esc($dataPiket['waktu']) ?> WIB</span>
                                </div>
                            </div>

                            <a href="<?= base_url('mahasiswa') ?>" class="btn btn-skagata w-100 rounded-pill py-2 shadow-sm">
                                <i class="bi bi-house me-1"></i> Kembali ke Dashboard
                            </a>
                        </div>

                    <?php else : ?>
                        <!-- Form Kamera Presensi Piket -->
                        <div class="text-start mb-3">
                            <h5 class="fw-bold text-dark mb-1">
                                <i class="bi bi-camera-video text-success me-2"></i>Presensi Piket KBM
                            </h5>
                            <p class="text-muted small mb-0">Arahkan kamera ke aktivitas piket KBM yang sedang Anda jalankan.</p>
                        </div>

                        <!-- Viewport Kamera dengan Flip Switcher -->
                        <div class="camera-viewport mb-3">
                            <button type="button" id="btn-flip" class="camera-switch-btn" title="Ganti Kamera Depan/Belakang">
                                <i class="bi bi-arrow-repeat fs-5"></i>
                            </button>
                            <video id="kamera" autoplay playsinline></video>
                            <img id="hasil-foto" style="display: none;" alt="Hasil Dokumentasi Piket" />
                        </div>

                        <!-- Tombol Jepret Foto -->
                        <button type="button" id="btn-jepret" class="btn btn-skagata btn-lg w-100 rounded-pill mb-2 py-3 shadow-sm fw-semibold">
                            <i class="bi bi-camera me-2"></i> Ambil Foto Piket
                        </button>

                        <!-- Tombol Ulangi Foto -->
                        <button type="button" id="btn-ulang" class="btn btn-outline-secondary w-100 rounded-pill mb-2 py-2" style="display: none;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Ambil Ulang Foto
                        </button>

                        <!-- Form Pengiriman Foto Hasil Kompresi -->
                        <form action="<?= base_url('mahasiswa/simpan-piket') ?>" method="POST" id="form-piket">
                            <?= csrf_field() ?>
                            <input type="hidden" name="foto_base64" id="foto_base64">
                            <button type="submit" id="btn-kirim" class="btn btn-success btn-lg w-100 rounded-pill shadow fw-semibold py-3" style="display: none; background-color: #10b981; border-color: #10b981;">
                                <i class="bi bi-send-check me-2"></i> Kirim Presensi Piket
                            </button>
                        </form>

                        <div class="mt-3">
                            <a href="<?= base_url('mahasiswa') ?>" class="btn btn-link text-muted text-decoration-none small">
                                <i class="bi bi-arrow-left me-1"></i> Batal dan Kembali
                            </a>
                        </div>
                    <?php endif; ?>

                    <canvas id="canvas-foto" style="display: none;"></canvas>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!$sudahPiket) : ?>
    <script>
        const kamera = document.getElementById('kamera');
        const canvas = document.getElementById('canvas-foto');
        const ctx = canvas.getContext('2d');
        const hasilFoto = document.getElementById('hasil-foto');
        const inputBase64 = document.getElementById('foto_base64');

        const btnJepret = document.getElementById('btn-jepret');
        const btnUlang = document.getElementById('btn-ulang');
        const btnKirim = document.getElementById('btn-kirim');
        const btnFlip = document.getElementById('btn-flip');

        const namaUser = <?= json_encode((string) (session()->get('nama') ?? 'Mahasiswa')) ?>;
        
        let currentFacingMode = 'environment'; // Default kamera belakang untuk foto aktivitas piket
        let currentStream = null;

        // Fungsi Memulai Kamera sesuai Facing Mode
        async function mulaiKamera(facingMode) {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: facingMode },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                });
                currentStream = stream;
                kamera.srcObject = stream;
            } catch (err) {
                console.warn("Gagal membuka kamera dengan facingMode ideal, mencoba default:", err);
                try {
                    const fallbackStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    currentStream = fallbackStream;
                    kamera.srcObject = fallbackStream;
                } catch (fallbackErr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Kamera Gagal',
                        text: 'Pastikan izin akses kamera telah diizinkan di browser Anda.',
                        confirmButtonColor: '#0f5132'
                    });
                }
            }
        }

        // Toggle Switch Kamera Depan / Belakang
        if (btnFlip) {
            btnFlip.addEventListener('click', function() {
                currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
                mulaiKamera(currentFacingMode);
            });
        }

        // Inisialisasi awal saat halaman dimuat
        mulaiKamera(currentFacingMode);

        // Ambil Foto & Kompresi Klien
        btnJepret.addEventListener('click', function() {
            // Kompresi resolusi: Maksimum lebar 1280px (proporsional)
            const maxWidth = 1280;
            const videoWidth = kamera.videoWidth || 640;
            const videoHeight = kamera.videoHeight || 480;

            let canvasWidth = videoWidth;
            let canvasHeight = videoHeight;

            if (videoWidth > maxWidth) {
                const ratio = maxWidth / videoWidth;
                canvasWidth = maxWidth;
                canvasHeight = videoHeight * ratio;
            }

            canvas.width = canvasWidth;
            canvas.height = canvasHeight;

            // Render stream kamera ke canvas
            ctx.drawImage(kamera, 0, 0, canvasWidth, canvasHeight);

            // Watermark Resmi Kedinasan Skagata
            const waktuSekarang = new Date();
            const formatTanggal = waktuSekarang.toLocaleDateString('id-ID', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            const formatJam = waktuSekarang.toLocaleTimeString('id-ID');

            // Bar pita gelap di bagian bawah
            const barHeight = Math.max(70, Math.floor(canvasHeight * 0.12));
            ctx.fillStyle = "rgba(15, 81, 50, 0.85)"; // Skagata Emerald transparan
            ctx.fillRect(0, canvasHeight - barHeight, canvasWidth, barHeight);

            // Teks Watermark
            const fontSize = Math.max(14, Math.floor(barHeight * 0.28));
            ctx.font = `bold ${fontSize}px sans-serif`;
            ctx.fillStyle = "#ffffff";

            ctx.fillText(`SMK NEGERI 3 YOGYAKARTA | PIKET KBM`, 16, canvasHeight - barHeight + fontSize + 4);
            ctx.font = `normal ${Math.max(12, Math.floor(fontSize * 0.85))}px sans-serif`;
            ctx.fillText(`${formatTanggal} - ${formatJam} WIB | ${namaUser}`, 16, canvasHeight - 12);

            // Kompresi JPEG dengan kualitas 0.75 (sangat hemat ukuran, jernih & terbaca)
            const dataURL = canvas.toDataURL('image/jpeg', 0.75);

            // Tampilkan preview hasil
            hasilFoto.src = dataURL;
            hasilFoto.style.display = "block";
            kamera.style.display = "none";
            if (btnFlip) btnFlip.style.display = "none";

            // Simpan ke input form
            inputBase64.value = dataURL;

            // Transisi tombol
            btnJepret.style.display = "none";
            btnUlang.style.display = "block";
            btnKirim.style.display = "block";
        });

        // Ulangi Foto
        btnUlang.addEventListener('click', function() {
            hasilFoto.style.display = "none";
            kamera.style.display = "block";
            if (btnFlip) btnFlip.style.display = "flex";

            btnJepret.style.display = "block";
            btnUlang.style.display = "none";
            btnKirim.style.display = "none";
            inputBase64.value = "";
        });
    </script>
<?php endif; ?>
<?= $this->endSection() ?>