<?php include 'layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4" data-aos="fade-right">
        <div class="col-12">
            <h1 class="display-6 fw-bold mb-1">
                <i class="bi bi-speedometer2 me-3"></i>Dashboard
            </h1>
            <p class="text-muted">Overview dan monitoring perangkat GenieACS</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="card stat-card info text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0 fw-bold"><?= number_format($stats['total']) ?></h3>
                        <p class="mb-0 opacity-75">Total Perangkat</p>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-router-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="card stat-card success text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0 fw-bold"><?= number_format($stats['online']) ?></h3>
                        <p class="mb-0 opacity-75">Perangkat Online</p>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-wifi fs-1 pulse"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="300">
            <div class="card stat-card warning text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0 fw-bold"><?= number_format($stats['offline']) ?></h3>
                        <p class="mb-0 opacity-75">Perangkat Offline</p>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-wifi-off fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="400">
            <div class="card stat-card text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0 fw-bold"><?= $stats['percentage_online'] ?>%</h3>
                        <p class="mb-0 opacity-75">Tingkat Online</p>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-graph-up fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <?php if (!empty($alerts)): ?>
    <div class="row mb-4" data-aos="fade-up" data-aos-delay="450">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>System Alerts
                        <span class="badge bg-danger ms-2"><?= count($alerts) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach (array_slice($alerts, 0, 6) as $alert): // Limit to 6 alerts ?>
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-<?= $alert['type'] ?> alert-sm mb-0 d-flex align-items-center">
                                    <i class="bi bi-<?= $alert['type'] === 'danger' ? 'exclamation-triangle-fill' : ($alert['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                                    <div class="flex-grow-1">
                                        <small><?= htmlspecialchars($alert['message']) ?></small>
                                    </div>
                                    <a href="<?= url('devices', ['action' => 'detail', 'id' => $alert['device_id']]) ?>" class="btn btn-sm btn-outline-<?= $alert['type'] ?> ms-2">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($alerts) > 6): ?>
                        <div class="text-center mt-3">
                            <button class="btn btn-outline-warning btn-sm" onclick="toggleAllAlerts()">
                                <i class="bi bi-eye me-1"></i>Lihat Semua Alert (<?= count($alerts) ?>)
                            </button>
                        </div>
                        
                        <div id="allAlerts" class="collapse mt-3">
                            <div class="row">
                                <?php foreach (array_slice($alerts, 6) as $alert): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="alert alert-<?= $alert['type'] ?> alert-sm mb-0 d-flex align-items-center">
                                            <i class="bi bi-<?= $alert['type'] === 'danger' ? 'exclamation-triangle-fill' : ($alert['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                                            <div class="flex-grow-1">
                                                <small><?= htmlspecialchars($alert['message']) ?></small>
                                            </div>
                                            <a href="<?= url('devices', ['action' => 'detail', 'id' => $alert['device_id']]) ?>" class="btn btn-sm btn-outline-<?= $alert['type'] ?> ms-2">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Device Status Chart -->
        <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="500">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Status Perangkat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Virtual Parameters Overview -->
        <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="600">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Device Monitoring
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentDevices)): ?>
                        <div class="row">
                            <?php 
                            $deviceCount = 0;
                            foreach ($recentDevices as $device): 
                                if ($deviceCount >= 3) break; // Limit to 3 devices for overview
                                $deviceCount++;
                                
                                // Extract Virtual Parameters if available
                                $virtualParams = [];
                                if (isset($device['VirtualParameters'])) {
                                    $virtualParams = $device['VirtualParameters'];
                                }
                            ?>
                                <div class="col-12 mb-3">
                                    <div class="card bg-light border-start border-primary border-3">
                                        <div class="card-body py-2">
                                            <h6 class="mb-1 text-truncate">
                                                <?= htmlspecialchars($device['summary']['manufacturer'] ?? 'Unknown') ?>
                                                <?= htmlspecialchars($device['summary']['modelName'] ?? 'Unknown') ?>
                                            </h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small><strong>Temperature:</strong></small><br>
                                                    <span class="badge bg-<?= (isset($virtualParams['gettemp']['_value']) && intval($virtualParams['gettemp']['_value']) > 50) ? 'danger' : 'success' ?> badge-sm">
                                                        <?= $virtualParams['gettemp']['_value'] ?? 'N/A' ?>°C
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>RX Power:</strong></small><br>
                                                    <span class="badge bg-<?= (isset($virtualParams['RXPower']['_value']) && intval($virtualParams['RXPower']['_value']) < -25) ? 'danger' : 'info' ?> badge-sm">
                                                        <?= $virtualParams['RXPower']['_value'] ?? 'N/A' ?> dBm
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <small><strong>Active Devices:</strong></small><br>
                                                    <span class="badge bg-info badge-sm">
                                                        <?= $virtualParams['activedevices']['_value'] ?? '0' ?>
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <small><strong>PPPoE IP:</strong></small><br>
                                                    <?php if (!empty($virtualParams['pppoeIP']['_value'])): ?>
                                                        <span class="badge bg-success badge-sm">Connected</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning badge-sm">No IP</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($recentDevices) > 3): ?>
                            <div class="text-center">
                                <a href="<?= url('devices') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Lihat Semua (<?= count($recentDevices) ?> devices)
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-gear fs-2 mb-3"></i>
                            <p class="mb-0">Tidak ada data monitoring</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Devices -->
        <div class="col-lg-8 mb-4" data-aos="fade-up" data-aos-delay="700">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Perangkat Terbaru
                    </h5>
                    <a href="<?= url('devices') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentDevices)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Device ID</th>
                                        <th>Manufaktur</th>
                                        <th>Model</th>
                                        <th>Software Version</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recentDevices, 0, 5) as $device): ?>
                                        <tr>
                                            <td>
                                                <code class="text-primary"><?= htmlspecialchars(substr($device['_id'] ?? '', 0, 20)) ?>...</code>
                                            </td>
                                            <td><?= htmlspecialchars($device['summary']['manufacturer'] ?? 'Unknown') ?></td>
                                            <td><?= htmlspecialchars($device['summary']['modelName'] ?? 'Unknown') ?></td>
                                            <td><?= htmlspecialchars($device['summary']['softwareVersion'] ?? 'Unknown') ?></td>
                                            <td>
                                                <?php
                                                $lastInform = $device['_lastInform'] ?? '';
                                                if ($lastInform) {
                                                    $time = new DateTime($lastInform);
                                                    $now = new DateTime();
                                                    $diff = $now->getTimestamp() - $time->getTimestamp();
                                                    
                                                    if ($diff < 300) {
                                                        echo '<span class="badge bg-success status-badge"><i class="bi bi-wifi me-1"></i>Online</span>';
                                                    } elseif ($diff < 3600) {
                                                        echo '<span class="badge bg-warning status-badge"><i class="bi bi-wifi me-1"></i>Recent</span>';
                                                    } else {
                                                        echo '<span class="badge bg-danger status-badge"><i class="bi bi-wifi-off me-1"></i>Offline</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge bg-secondary status-badge">Unknown</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?= url('devices', ['action' => 'detail', 'id' => $device['_id']]) ?>" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-router fs-1 mb-3"></i>
                            <p>Tidak ada perangkat ditemukan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Online Devices -->
        <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="800">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-wifi me-2"></i>Perangkat Online
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($onlineDevices)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($onlineDevices as $device): ?>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-truncate">
                                                <?= htmlspecialchars($device['summary']['manufacturer'] ?? 'Unknown') ?>
                                                <?= htmlspecialchars($device['summary']['modelName'] ?? 'Unknown') ?>
                                            </h6>
                                            <small class="text-muted">
                                                <code><?= htmlspecialchars(substr($device['_id'] ?? '', 0, 15)) ?>...</code>
                                            </small>
                                        </div>
                                        <span class="badge bg-success status-badge ms-2">
                                            <i class="bi bi-wifi"></i>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($onlineDevices) >= 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?= Router::url('devices', ['status' => 'online']) ?>" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Lihat Semua Online
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-wifi-off fs-2 mb-3"></i>
                            <p class="mb-0">Tidak ada perangkat online</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4" data-aos="fade-up" data-aos-delay="900">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= url('devices') ?>" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-router-fill fs-4 d-block mb-2"></i>
                                Kelola Perangkat
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= url('devices', ['status' => 'online']) ?>" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-wifi fs-4 d-block mb-2"></i>
                                Perangkat Online
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= url('devices', ['status' => 'offline']) ?>" class="btn btn-outline-danger w-100 py-3">
                                <i class="bi bi-wifi-off fs-4 d-block mb-2"></i>
                                Perangkat Offline
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-outline-info w-100 py-3" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise fs-4 d-block mb-2"></i>
                                Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Chart (Doughnut)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($statusData['labels']) ?>,
            datasets: [{
                data: <?= json_encode($statusData['data']) ?>,
                backgroundColor: <?= json_encode($statusData['colors']) ?>,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});

// Toggle alerts function
function toggleAllAlerts() {
    const alertsDiv = document.getElementById('allAlerts');
    const btn = document.querySelector('[onclick="toggleAllAlerts()"]');
    
    if (alertsDiv.classList.contains('show')) {
        alertsDiv.classList.remove('show');
        btn.innerHTML = '<i class="bi bi-eye me-1"></i>Lihat Semua Alert (<?= count($alerts ?? []) ?>)';
    } else {
        alertsDiv.classList.add('show');
        btn.innerHTML = '<i class="bi bi-eye-slash me-1"></i>Sembunyikan Alert';
    }
}

// Auto-refresh alerts every 30 seconds
setInterval(function() {
    if (!document.hidden) {
        // Only refresh if no modals are open
        if (!document.querySelector('.modal.show')) {
            location.reload();
        }
    }
}, 30000);
</script>

<?php include 'layouts/footer.php'; ?> 