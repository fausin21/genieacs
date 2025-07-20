<?php 
$pageTitle = '500 - Internal Server Error';
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center" data-aos="fade-up">
            <div class="card shadow-lg border-0">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 6rem;"></i>
                    </div>
                    
                    <h1 class="display-4 fw-bold text-danger mb-3">500</h1>
                    <h2 class="h4 mb-4">Internal Server Error</h2>
                    
                    <p class="text-muted mb-4">
                        Terjadi kesalahan pada server. Mohon maaf atas ketidaknyamanan ini. 
                        Tim teknis kami telah diberitahu dan sedang menangani masalah ini.
                    </p>
                    
                    <div class="alert alert-warning border-0 shadow-sm mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Tips:</strong> Coba refresh halaman atau kembali lagi beberapa saat.
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button onclick="window.location.reload()" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh Halaman
                        </button>
                        <a href="<?= url('dashboard') ?>" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-house-fill me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-muted">
                        <small>
                            Jika masalah berlanjut, silakan hubungi administrator sistem.<br>
                            Error ID: <?= uniqid() ?> - <?= date('Y-m-d H:i:s') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?> 