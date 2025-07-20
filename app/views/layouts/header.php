<?php
// Ensure url function is available
if (!function_exists('url')) {
    function url($halaman, $params = []) {
        $url = "index.php?halaman=$halaman";
        foreach ($params as $key => $value) {
            $url .= "&$key=" . urlencode($value);
        }
        return $url;
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= Config::get('APP_NAME') ?></title>
    <meta name="description" content="<?= Config::get('APP_DESCRIPTION') ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- AOS Animation CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
    
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        .device-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .device-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: scale(1.05);
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .content-wrapper {
            min-height: calc(100vh - 56px);
        }
        
        .loading-spinner {
            display: none;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn-action {
            margin: 2px;
        }
        
        .parameter-row {
            transition: background-color 0.3s ease;
        }
        
        .parameter-row:hover {
            background-color: rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm" data-aos="fade-down">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('dashboard') ?>">
                <i class="bi bi-router me-2"></i><?= Config::get('APP_NAME') ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['halaman'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>" 
                           href="<?= url('dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['halaman'] ?? '') === 'devices' ? 'active' : '' ?>" 
                           href="<?= url('devices') ?>">
                            <i class="bi bi-router-fill me-1"></i>Perangkat
                        </a>
                    </li>
                    <?php if (Config::get('DEBUG')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['halaman'] ?? '') === 'debug' ? 'active' : '' ?>" 
                           href="<?= url('debug') ?>">
                            <i class="bi bi-bug me-1"></i>Debug
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear-fill me-1"></i>Pengaturan
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#systemInfoModal">
                                <i class="bi bi-info-circle me-2"></i>Info Sistem
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="toggleTheme()">
                                <i class="bi bi-moon-fill me-2"></i>Mode Gelap
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> alert-dismissible fade show m-3" role="alert" data-aos="zoom-in">
            <i class="bi bi-<?= $_SESSION['flash_message']['type'] === 'success' ? 'check-circle' : ($_SESSION['flash_message']['type'] === 'danger' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert" data-aos="shake">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <main class="col-12 content-wrapper"><?php // Content akan dimuat di sini ?> 