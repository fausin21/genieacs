<?php include 'layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4" data-aos="fade-right">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold mb-1">
                <i class="bi bi-router-fill me-3"></i>Perangkat
            </h1>
            <p class="text-muted">Kelola dan monitor semua perangkat TR-069</p>
        </div>
        <div class="col-md-4 text-md-end">
            <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="halaman" value="devices">
                        
                        <div class="col-md-4">
                            <label for="search" class="form-label">Pencarian</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Cari device ID, manufaktur, atau model..."
                                       value="<?= htmlspecialchars($filters['search']) ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="online" <?= $filters['status'] === 'online' ? 'selected' : '' ?>>Online</option>
                                <option value="offline" <?= $filters['status'] === 'offline' ? 'selected' : '' ?>>Offline</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="page" class="form-label">Halaman</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="page" 
                                   name="page" 
                                   min="1" 
                                   max="<?= $pagination['total_pages'] ?>"
                                   value="<?= $pagination['current_page'] ?>">
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-filter me-1"></i>Filter
                            </button>
                            <a href="<?= url('devices') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    <div class="row mb-3" data-aos="fade-up" data-aos-delay="100">
        <div class="col-md-6">
            <p class="text-muted mb-0">
                Menampilkan <?= count($devices) ?> dari <?= number_format($pagination['total_devices']) ?> perangkat
                <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                    (filtered)
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-6 text-md-end">
            <small class="text-muted">
                Halaman <?= $pagination['current_page'] ?> dari <?= $pagination['total_pages'] ?>
            </small>
        </div>
    </div>

    <!-- Devices Table -->
    <div class="row" data-aos="fade-up" data-aos-delay="200">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <?php if (!empty($devices)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="bi bi-router me-1"></i>Device ID</th>
                                        <th><i class="bi bi-building me-1"></i>Manufacturer</th>
                                        <th><i class="bi bi-cpu me-1"></i>Model</th>
                                        <th><i class="bi bi-clock me-1"></i>Last Inform</th>
                                        <th><i class="bi bi-activity me-1"></i>Status</th>
                                        <th><i class="bi bi-thermometer me-1"></i>Temp/Signal</th>
                                        <th><i class="bi bi-gear-fill me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($devices as $device): ?>
                                        <tr class="device-row" data-aos="fade-up">
                                            <td>
                                                <strong class="text-primary"><?= htmlspecialchars($device['id']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($device['serialNumber']) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($device['manufacturer']) ?></td>
                                            <td><?= htmlspecialchars($device['model']) ?></td>
                                            <td>
                                                <?php if (!empty($device['lastInform'])): ?>
                                                    <small class="text-muted" 
                                                           data-bs-toggle="tooltip" 
                                                           title="<?= date('Y-m-d H:i:s', strtotime($device['lastInform'])) ?>">
                                                        <?= date('d/m H:i', strtotime($device['lastInform'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($device['status'] === 'online'): ?>
                                                    <span class="badge bg-success pulse">
                                                        <i class="bi bi-wifi me-1"></i>Online
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-wifi-off me-1"></i>Offline
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                // Extract Virtual Parameters for quick view
                                                $temp = 'N/A';
                                                $rxPower = 'N/A';
                                                $tempClass = 'secondary';
                                                $signalClass = 'secondary';
                                                
                                                if (isset($device['virtualParams'])) {
                                                    if (isset($device['virtualParams']['gettemp']['_value'])) {
                                                        $temp = $device['virtualParams']['gettemp']['_value'] . '°C';
                                                        $tempClass = intval($device['virtualParams']['gettemp']['_value']) > 50 ? 'danger' : 'success';
                                                    }
                                                    if (isset($device['virtualParams']['RXPower']['_value'])) {
                                                        $rxPower = $device['virtualParams']['RXPower']['_value'] . ' dBm';
                                                        $signalClass = intval($device['virtualParams']['RXPower']['_value']) < -25 ? 'warning' : 'info';
                                                    }
                                                }
                                                ?>
                                                <div class="d-flex flex-column">
                                                    <span class="badge bg-<?= $tempClass ?> mb-1" style="font-size: 0.7em;">
                                                        <i class="bi bi-thermometer me-1"></i><?= $temp ?>
                                                    </span>
                                                    <span class="badge bg-<?= $signalClass ?>" style="font-size: 0.7em;">
                                                        <i class="bi bi-reception-4 me-1"></i><?= $rxPower ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= url('devices', ['action' => 'detail', 'id' => $device['id']]) ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       data-bs-toggle="tooltip" 
                                                       title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= url('devices', ['action' => 'edit', 'id' => $device['id']]) ?>" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       data-bs-toggle="tooltip" 
                                                       title="Edit Virtual Parameters">
                                                        <i class="bi bi-gear"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="refreshDevice('<?= $device['id'] ?>')" 
                                                            data-bs-toggle="tooltip" 
                                                            title="Refresh Device">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-router fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada perangkat ditemukan</h5>
                            <p class="text-muted">
                                <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                                    Coba ubah filter pencarian Anda
                                <?php else: ?>
                                    Belum ada perangkat yang terdaftar di GenieACS
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                                <a href="<?= Router::url('devices') ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Lihat Semua Perangkat
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="row mt-4" data-aos="fade-up" data-aos-delay="300">
            <div class="col-12">
                <nav aria-label="Device pagination">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page -->
                        <?php if ($pagination['has_prev']): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= Router::url('devices', array_merge($filters, ['page' => $pagination['prev_page']])) ?>">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link"><i class="bi bi-chevron-left"></i> Previous</span>
                            </li>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <?php
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        if ($start > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= Router::url('devices', array_merge($filters, ['page' => 1])) ?>">1</a>
                            </li>
                            <?php if ($start > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <?php if ($i === $pagination['current_page']): ?>
                                    <span class="page-link"><?= $i ?></span>
                                <?php else: ?>
                                    <a class="page-link" href="<?= Router::url('devices', array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end < $pagination['total_pages']): ?>
                            <?php if ($end < $pagination['total_pages'] - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= Router::url('devices', array_merge($filters, ['page' => $pagination['total_pages']])) ?>"><?= $pagination['total_pages'] ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next Page -->
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= Router::url('devices', array_merge($filters, ['page' => $pagination['next_page']])) ?>">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">Next <i class="bi bi-chevron-right"></i></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.device-row {
    transition: all 0.3s ease;
}

.device-row:hover {
    background-color: rgba(0, 123, 255, 0.05) !important;
}

.btn-action {
    transition: all 0.2s ease;
}

.btn-action:hover {
    transform: scale(1.1);
}

.pagination .page-link {
    border-radius: 0.375rem;
    margin: 0 2px;
    border: 1px solid #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>

<?php include 'layouts/footer.php'; ?> 