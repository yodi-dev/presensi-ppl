<?= $this->extend('layout/template') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .settings-card {
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

    #map {
        height: 380px;
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid #dee2e6;
        z-index: 1;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #003366;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1.25rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4 mb-5">

    <!-- Top Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-0">Pengaturan Lokasi &amp; Jam Presensi</h3>
            <p class="text-muted mt-1 mb-0">Kalibrasi koordinat GPS radius sekolah dan ketentuan waktu presensi</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('admin') ?>" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                Kembali ke Manajemen Pengguna
            </a>
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger rounded-pill px-3">
                Logout
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
                            <h5 class="mb-0 fw-bold text-dark">Peta Geofencing Perimeter</h5>
                            <small class="text-muted">Geser pin marker atau klik pada peta untuk menentukan titik pusat</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" onclick="deteksiLokasiSaya()">
                            Lokasi Saya Saat Ini
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div id="map"></div>
                        <div class="mt-3 p-3 bg-light rounded text-muted small">
                            Lingkaran biru menunjukkan <strong>radius perimeter absensi</strong>. Mahasiswa yang berada di luar batas lingkaran ini akan otomatis ditolak saat melakukan presensi datang.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Form Konfigurasi -->
            <div class="col-12 col-lg-5">
                <div class="card settings-card">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold text-dark">Parameter Konfigurasi</h5>
                    </div>
                    <div class="card-body p-4">

                        <!-- Section Institusi -->
                        <div class="form-section-title">Informasi Sekolah</div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Institusi / Sekolah</label>
                            <input type="text" name="school_name" class="form-control" value="<?= esc($school_name) ?>" required>
                        </div>

                        <!-- Section Koordinat & Radius -->
                        <div class="form-section-title mt-4">Geofencing &amp; Titik Lokasi</div>
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
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Radius Presensi (Meter)</span>
                                <span class="badge bg-primary text-white" id="radiusLabel"><?= esc($school_radius) ?> meter</span>
                            </label>
                            <input type="range" name="school_radius" id="school_radius" class="form-range" min="10" max="1000" step="5" value="<?= esc($school_radius) ?>" oninput="updateRadius(this.value)">
                            <div class="d-flex justify-content-between text-muted small">
                                <span>10m</span>
                                <span>100m (Default)</span>
                                <span>500m</span>
                                <span>1000m</span>
                            </div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded border">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="geofence_active" id="geofence_active" value="1" <?= $geofence_active ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold text-dark ms-2" for="geofence_active">
                                    Aktifkan Penegakan Radius Ketat
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Jika dinonaktifkan, mahasiswa dapat melakukan presensi tanpa batasan jarak perimeter (berguna untuk mode darurat / pengujian).</small>
                        </div>

                        <!-- Section Jam Presensi -->
                        <div class="form-section-title mt-4">Ketentuan Waktu Presensi</div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Jam Masuk Maksimal</label>
                                <input type="time" name="jam_masuk_max" class="form-control" value="<?= esc(substr($jam_masuk_max, 0, 5)) ?>" required>
                                <small class="text-muted">Lewat jam ini = Terlambat</small>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Jam Pulang Minimal</label>
                                <input type="time" name="jam_pulang_min" class="form-control" value="<?= esc(substr($jam_pulang_min, 0, 5)) ?>" required>
                                <small class="text-muted">Sebelum jam ini = Dilarang pulang</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                            Simpan Perubahan Pengaturan
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
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Marker Draggable
    const marker = L.marker([currentLat, currentLng], {
        draggable: true,
        title: 'Titik Pusat Sekolah'
    }).addTo(map);

    // Circle Perimeter Radius
    const circle = L.circle([currentLat, currentLng], {
        radius: currentRadius,
        color: '#003366',
        fillColor: '#0d6efd',
        fillOpacity: 0.18,
        weight: 2
    }).addTo(map);

    function setCoordinates(lat, lng) {
        document.getElementById('school_latitude').value = Number(lat).toFixed(6);
        document.getElementById('school_longitude').value = Number(lng).toFixed(6);
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]);
    }

    // Event Marker Digeser
    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        setCoordinates(pos.lat, pos.lng);
    });

    // Event Klik pada Peta
    map.on('click', function(e) {
        setCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // Update Radius Dinamis dari Slider
    function updateRadius(val) {
        const radiusNum = parseInt(val, 10);
        document.getElementById('radiusLabel').innerText = `${radiusNum} meter`;
        circle.setRadius(radiusNum);
    }

    // Input text koordinat manual listener
    document.getElementById('school_latitude').addEventListener('change', function() {
        const lat = parseFloat(this.value);
        const lng = parseFloat(document.getElementById('school_longitude').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        }
    });

    document.getElementById('school_longitude').addEventListener('change', function() {
        const lat = parseFloat(document.getElementById('school_latitude').value);
        const lng = parseFloat(this.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        }
    });

    // Deteksi Lokasi Browser GPS
    function deteksiLokasiSaya() {
        if (!navigator.geolocation) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Didukung',
                text: 'Perangkat atau browser Anda tidak mendukung akses Geolocation.',
                confirmButtonColor: '#003366'
            });
            return;
        }

        Swal.fire({
            title: 'Mendeteksi Lokasi...',
            text: 'Mohon tunggu, mengambil titik koordinat GPS terkini...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        navigator.geolocation.getCurrentPosition(
            function(position) {
                Swal.close();
                const myLat = position.coords.latitude;
                const myLng = position.coords.longitude;
                setCoordinates(myLat, myLng);
                map.flyTo([myLat, myLng], 17);
            },
            function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mendeteksi Lokasi',
                    text: 'Pastikan izin akses lokasi diizinkan di browser Anda.',
                    confirmButtonColor: '#003366'
                });
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }
</script>
<?= $this->endSection() ?>
