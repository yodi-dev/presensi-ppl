<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .settings-card {
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

    #map {
        height: 380px;
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        z-index: 1;
    }

    .form-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f5132;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.4rem;
        margin-bottom: 1.25rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Top Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-geo-alt text-success me-2"></i>Pengaturan Lokasi &amp; Jam Presensi
            </h4>
            <p class="text-muted small mt-1 mb-0">Kalibrasi koordinat GPS radius sekolah dan kebijakan waktu presensi SMKN 3 Yogyakarta</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('admin') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Data Pengguna
            </a>
        </div>
    </div>

    <form action="<?= base_url('admin/pengaturan/simpan') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="row g-4">

            <!-- Kolom Kiri: Peta Interaktif Leaflet -->
            <div class="col-12 col-lg-7">
                <div class="card settings-card h-100">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Peta Geofencing Perimeter Skagata</h6>
                            <small class="text-muted">Geser pin marker atau klik pada peta untuk menentukan titik pusat</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm" onclick="deteksiLokasiSaya()">
                            <i class="bi bi-crosshair me-1"></i> Lokasi Saya
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div id="map"></div>
                        <div class="mt-3 p-3 bg-light rounded text-muted small border">
                            <i class="bi bi-info-circle text-success me-1"></i>
                            Lingkaran hijau menunjukkan <strong>radius perimeter absensi resmi</strong>. Mahasiswa atau GTT yang berada di luar batas lingkaran ini akan otomatis ditolak saat melakukan presensi datang.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Form Konfigurasi -->
            <div class="col-12 col-lg-5">
                <div class="card settings-card">
                    <div class="card-header-custom">
                        <h6 class="mb-0 fw-bold text-dark">Parameter Konfigurasi Operasional</h6>
                    </div>
                    <div class="card-body p-4">

                        <!-- Section Institusi -->
                        <div class="form-section-title">Informasi Institusi</div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Sekolah</label>
                            <input type="text" name="school_name" class="form-control" value="<?= esc($school_name) ?>" required>
                        </div>

                        <!-- Section Koordinat & Radius -->
                        <div class="form-section-title mt-4">Geofencing &amp; Titik Koordinat</div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Latitude</label>
                                <input type="text" name="school_latitude" id="school_latitude" class="form-control form-control-sm" value="<?= esc($school_latitude) ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Longitude</label>
                                <input type="text" name="school_longitude" id="school_longitude" class="form-control form-control-sm" value="<?= esc($school_longitude) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                                <span>Radius Presensi (Meter)</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" id="radiusLabel"><?= esc($school_radius) ?> meter</span>
                            </label>
                            <input type="range" name="school_radius" id="school_radius" class="form-range" min="10" max="1000" step="5" value="<?= esc($school_radius) ?>" oninput="updateRadius(this.value)">
                            <div class="d-flex justify-content-between text-muted small" style="font-size: 0.72rem;">
                                <span>10m</span>
                                <span>100m (Default)</span>
                                <span>500m</span>
                                <span>1000m</span>
                            </div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded border">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="geofence_active" id="geofence_active" value="1" <?= $geofence_active ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold text-dark ms-2 small" for="geofence_active">
                                    Aktifkan Penegakan Radius Ketat
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                Jika dinonaktifkan, presensi dapat dilakukan tanpa batasan jarak (mode toleransi sinyal GPS darurat).
                            </small>
                        </div>

                        <!-- Section Jam Presensi -->
                        <div class="form-section-title mt-4">Ketentuan Jam Kerja Operasional</div>
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Jam Masuk Normal</label>
                                <input type="time" name="jam_masuk_max" class="form-control" value="<?= esc(substr($jam_masuk_max, 0, 5)) ?>" required>
                                <small class="text-muted" style="font-size: 0.7rem;">Lewat waktu ini = Terlambat</small>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Jam Pulang Minimal</label>
                                <input type="time" name="jam_pulang_min" class="form-control" value="<?= esc(substr($jam_pulang_min, 0, 5)) ?>" required>
                                <small class="text-muted" style="font-size: 0.7rem;">Sebelum jam ini = Cegah checkout</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-skagata w-100 py-2 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Konfigurasi
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let currentLat = <?= json_encode((float) $school_latitude) ?>;
    let currentLng = <?= json_encode((float) $school_longitude) ?>;
    let currentRadius = <?= json_encode((int) $school_radius) ?>;

    // Inisialisasi Peta Leaflet
    const map = L.map('map').setView([currentLat, currentLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker Titik Pusat
    const marker = L.marker([currentLat, currentLng], {
        draggable: true,
        title: 'Titik Pusat SMKN 3 Yogyakarta'
    }).addTo(map);

    // Circle Perimeter Radius Skagata Emerald
    const circle = L.circle([currentLat, currentLng], {
        radius: currentRadius,
        color: '#0f5132',
        fillColor: '#10b981',
        fillOpacity: 0.22,
        weight: 2
    }).addTo(map);

    function setCoordinates(lat, lng) {
        document.getElementById('school_latitude').value = Number(lat).toFixed(6);
        document.getElementById('school_longitude').value = Number(lng).toFixed(6);
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]);
    }

    // Event Marker Dragged
    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        setCoordinates(pos.lat, pos.lng);
    });

    // Event Map Clicked
    map.on('click', function(e) {
        setCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // Event Input Latitude / Longitude Manual
    document.getElementById('school_latitude').addEventListener('change', function() {
        const lat = parseFloat(this.value);
        const lng = parseFloat(document.getElementById('school_longitude').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            setCoordinates(lat, lng);
            map.panTo([lat, lng]);
        }
    });

    document.getElementById('school_longitude').addEventListener('change', function() {
        const lat = parseFloat(document.getElementById('school_latitude').value);
        const lng = parseFloat(this.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            setCoordinates(lat, lng);
            map.panTo([lat, lng]);
        }
    });

    // Slider Radius Interaktif
    function updateRadius(val) {
        const r = parseInt(val, 10);
        document.getElementById('radiusLabel').innerText = r + ' meter';
        circle.setRadius(r);
    }

    // Deteksi Lokasi Saya Saat Ini
    function deteksiLokasiSaya() {
        if (!navigator.geolocation) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Didukung',
                text: 'Browser Anda tidak mendukung Geolocation API.',
                confirmButtonColor: '#0f5132'
            });
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                setCoordinates(lat, lng);
                map.setView([lat, lng], 17);
                Swal.fire({
                    icon: 'success',
                    title: 'Lokasi Terdeteksi',
                    text: `Koordinat berhasil disesuaikan ke posisi GPS Anda saat ini.`,
                    confirmButtonColor: '#0f5132',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            function(err) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal Mendeteksi Lokasi',
                    text: 'Pastikan izin akses lokasi telah diberikan pada browser Anda.',
                    confirmButtonColor: '#0f5132'
                });
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }
</script>
<?= $this->endSection() ?>
