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
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            /* Background default */
        }
    </style>

    <?= $this->renderSection('styles'); ?>

</head>

<body>

    <?= $this->renderSection('content'); ?>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: '<?= session()->getFlashdata('error') ?>',
                    confirmButtonColor: '#003366',
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
                    text: '<?= session()->getFlashdata('pesan') ?>',
                    confirmButtonColor: '#003366',
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