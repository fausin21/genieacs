<?php 
$pageTitle = '404 - Halaman Tidak Ditemukan';
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center" data-aos="fade-up">
            <div class="card shadow-lg border-0">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 6rem;"></i>
                    </div>
                    
                    <h1 class="display-4 fw-bold text-primary mb-3">404</h1>
                    <h2 class="h4 mb-4">Halaman Tidak Ditemukan</h2>
                    
                    <p class="text-muted mb-4">
                        Maaf, halaman yang Anda cari tidak dapat ditemukan. 
                        Mungkin halaman telah dipindahkan atau URL yang Anda masukkan salah.
                    </p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-house-fill me-2"></i>Kembali ke Dashboard
                        </a>
                        <a href="<?= url('devices') ?>" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-router-fill me-2"></i>Lihat Perangkat
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <i class="bi bi-speedometer2 fs-3 text-primary mb-2"></i>
                            <h6>Dashboard</h6>
                            <small class="text-muted">Lihat overview sistem</small>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-router-fill fs-3 text-success mb-2"></i>
                            <h6>Perangkat</h6>
                            <small class="text-muted">Kelola perangkat TR-069</small>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-gear-fill fs-3 text-info mb-2"></i>
                            <h6>Pengaturan</h6>
                            <small class="text-muted">Konfigurasi sistem</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?> 