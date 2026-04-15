<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3" role="alert">
                    <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 text-center">

                    <?php if ($sudahPiket) : ?>
                        <div class="py-4">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold">Anda Sudah Presensi Piket</h4>
                            <p class="text-muted">Data piket KBM Anda untuk hari ini sudah tersimpan di sistem.</p>

                            <hr>
                            <div class="text-start small bg-light p-3 rounded-3 mb-4">
                                <div><strong>Waktu:</strong> <?= $dataPiket['waktu'] ?></div>
                                <div><strong>Tanggal:</strong> <?= $dataPiket['tanggal'] ?></div>
                            </div>

                            <a href="<?= base_url('mahasiswa') ?>" class="btn btn-primary btn-lg w-100 rounded-pill">
                                <i class="bi bi-house-door me-2"></i> Kembali ke Dashboard
                            </a>
                        </div>

                    <?php else : ?>
                        <h4 class="fw-bold mb-3">📸 Presensi Piket KBM</h4>
                        <p class="text-muted small">Silakan ambil foto bukti piket di lokasi KBM.</p>

                        <div class="bg-dark rounded-3 mb-3 d-flex justify-content-center align-items-center" style="min-height: 300px;">
                            <video id="kamera" autoplay playsinline style="width: 100%; height: auto; max-height: 60vh;"></video>
                            <img id="hasil-foto" style="display: none; width: 100%; height: auto; max-height: 60vh;" />
                        </div>

                        <button type="button" id="btn-jepret" class="btn btn-primary btn-lg w-100 rounded-pill mb-2">
                            <i class="bi bi-camera me-2"></i> Jepret Foto
                        </button>

                        <button type="button" id="btn-ulang" class="btn btn-outline-secondary w-100 rounded-pill mb-3" style="display: none;">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Ulangi
                        </button>

                        <form action="<?= base_url('mahasiswa/simpan-piket') ?>" method="POST" id="form-piket">
                            <?= csrf_field() ?>
                            <input type="hidden" name="foto_base64" id="foto_base64">
                            <button type="submit" id="btn-kirim" class="btn btn-success btn-lg w-100 rounded-pill shadow" style="display: none;">
                                <i class="bi bi-send me-2"></i> Kirim Presensi
                            </button>
                        </form>

                        <a href="<?= base_url('mahasiswa/dashboard') ?>" class="btn btn-link text-decoration-none mt-3">
                            <i class="bi bi-arrow-left"></i> Batal dan Kembali
                        </a>
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

        // Ambil nama user dari session PHP untuk di-watermark
        const namaUser = "<?= session()->get('nama') ?? 'Mahasiswa' ?>";

        // 1. Fungsi menyalakan kamera HP (kamera belakang kalau ada)
        async function mulaiKamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    } // Prioritas kamera belakang
                });
                kamera.srcObject = stream;
            } catch (err) {
                alert("Gagal mengakses kamera! Pastikan izin kamera sudah diberikan.");
                console.error(err);
            }
        }

        // Jalankan kamera saat halaman dibuka
        mulaiKamera();

        // 2. Fungsi saat tombol Jepret ditekan
        btnJepret.addEventListener('click', function() {
            // Tentukan resolusi kompresi (biar hemat hosting, kita set lebar 640px aja)
            const lebarCanvas = 640;
            const rasio = kamera.videoHeight / kamera.videoWidth;
            const tinggiCanvas = lebarCanvas * rasio;

            canvas.width = lebarCanvas;
            canvas.height = tinggiCanvas;

            // Gambar video ke canvas
            ctx.drawImage(kamera, 0, 0, lebarCanvas, tinggiCanvas);

            // --- PROSES WATERMARK ---
            const waktuSekarang = new Date();
            const teksWaktu = waktuSekarang.toLocaleString('id-ID');

            // 1. Buat pita background hitam transparan full dari kiri ke kanan
            ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
            // format: fillRect(x, y, lebar, tinggi)
            ctx.fillRect(0, tinggiCanvas - 80, lebarCanvas, 80);

            // 2. Tulis teks di atas pita tersebut
            ctx.font = "bold 24px Arial"; // Font digedein dikit biar jelas
            ctx.fillStyle = "white";

            // Teks Jam (posisi Y = tinggiCanvas - 45)
            ctx.fillText("🕒 " + teksWaktu, 15, tinggiCanvas - 45);

            // Teks Nama User (posisi Y = tinggiCanvas - 15)
            ctx.fillText("👤 " + namaUser, 15, tinggiCanvas - 15);

            // --- PROSES KOMPRESI & CONVERT ---
            // Ubah canvas jadi file JPG dengan kualitas 0.7 (70%), super hemat size!
            const dataURL = canvas.toDataURL('image/jpeg', 0.7);

            // Tampilkan hasil dan sembunyikan kamera live
            hasilFoto.src = dataURL;
            hasilFoto.style.display = "block";
            kamera.style.display = "none";

            // Masukkan data gambar ke input form
            inputBase64.value = dataURL;

            // Atur tombol
            btnJepret.style.display = "none";
            btnUlang.style.display = "block";
            btnKirim.style.display = "block";
        });

        // 3. Fungsi saat tombol Ulang ditekan
        btnUlang.addEventListener('click', function() {
            hasilFoto.style.display = "none";
            kamera.style.display = "block";

            btnJepret.style.display = "block";
            btnUlang.style.display = "none";
            btnKirim.style.display = "none";
            inputBase64.value = "";
        });
    </script>
<?php endif; ?>
<?= $this->endSection() ?>