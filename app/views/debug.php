<?php include 'layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4" data-aos="fade-right">
        <div class="col-12">
            <h1 class="display-6 fw-bold mb-1">
                <i class="bi bi-bug me-3"></i>Debug & Test Koneksi
            </h1>
            <p class="text-muted">Test koneksi dan debugging untuk GenieACS API</p>
        </div>
    </div>

    <!-- Test Connection -->
    <div class="row mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-wifi me-2"></i>Test Koneksi GenieACS
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    $device = new Device();
                    $testResult = $device->testConnection();
                    ?>
                    
                    <?php if ($testResult['success']): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>✅ Koneksi Berhasil!</strong><br>
                            <?= htmlspecialchars($testResult['message']) ?>
                        </div>
                        
                        <h6>Response Data:</h6>
                        <pre class="bg-light p-3 rounded"><code><?= htmlspecialchars(json_encode($testResult['data'], JSON_PRETTY_PRINT)) ?></code></pre>
                        
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>❌ Koneksi Gagal!</strong><br>
                            <?= htmlspecialchars($testResult['message']) ?>
                        </div>
                        
                        <h6>Detail Error:</h6>
                        <pre class="bg-light p-3 rounded"><code><?= htmlspecialchars($testResult['error']) ?></code></pre>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Info -->
    <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Konfigurasi Saat Ini
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>GenieACS URL:</strong></td>
                            <td><code><?= htmlspecialchars(Config::get('GENIE_BASE')) ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Username:</strong></td>
                            <td><code><?= Config::get('GENIE_USERNAME') ? htmlspecialchars(Config::get('GENIE_USERNAME')) : '(tidak diset)' ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Password:</strong></td>
                            <td><code><?= Config::get('GENIE_PASSWORD') ? '***' : '(tidak diset)' ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Debug Mode:</strong></td>
                            <td><span class="badge bg-<?= Config::get('DEBUG') ? 'warning' : 'success' ?>"><?= Config::get('DEBUG') ? 'ON' : 'OFF' ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Troubleshooting Tips
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Jika koneksi gagal:</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Pastikan GenieACS server berjalan</li>
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Cek URL di Config.php</li>
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Test dengan curl/browser</li>
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Periksa firewall/network</li>
                        <li><i class="bi bi-arrow-right text-primary me-2"></i>Verifikasi credentials jika ada</li>
                    </ul>
                    
                    <h6 class="mt-3">Test Manual:</h6>
                    <code>curl <?= htmlspecialchars(Config::get('GENIE_BASE')) ?>/devices</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Task Creation -->
    <div class="row mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-play-circle me-2"></i>Test Task API
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= url('debug') ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="test_device_id" class="form-label">Device ID untuk Test:</label>
                                <input type="text" class="form-control" id="test_device_id" name="test_device_id" placeholder="Masukkan Device ID">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="test_task" class="form-label">Task Type:</label>
                                <select class="form-select" id="test_task" name="test_task">
                                    <option value="refresh">Refresh</option>
                                    <option value="reboot">Reboot</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="test_task_submit">
                            <i class="bi bi-play me-2"></i>Test Task API
                        </button>
                    </form>

                    <?php if (isset($_POST['test_task_submit']) && !empty($_POST['test_device_id'])): ?>
                        <hr class="my-4">
                        <h6>Hasil Test Task:</h6>
                        <?php
                        $deviceId = $_POST['test_device_id'];
                        $taskType = $_POST['test_task'];
                        
                        $task = [];
                        switch ($taskType) {
                            case 'refresh':
                                $task = ['name' => 'refreshObject', 'objectName' => ''];
                                break;
                            case 'reboot':
                                $task = ['name' => 'reboot'];
                                break;
                        }
                        
                        $device = new Device();
                        $result = $device->createTaskAlternative($deviceId, $task);
                        ?>
                        
                        <?php if ($result['success']): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>✅ Task berhasil dibuat!</strong>
                            </div>
                            <pre class="bg-light p-3 rounded"><code><?= htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) ?></code></pre>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>❌ Task gagal dibuat!</strong><br>
                                <?= htmlspecialchars($result['message']) ?>
                            </div>
                            
                            <?php if (isset($result['errors'])): ?>
                                <h6>Detail Errors:</h6>
                                <?php foreach ($result['errors'] as $format => $error): ?>
                                    <div class="mb-2">
                                        <strong><?= htmlspecialchars($format) ?>:</strong><br>
                                        <code><?= htmlspecialchars($error) ?></code>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="row" data-aos="fade-up" data-aos-delay="300">
        <div class="col-12 text-center">
            <a href="<?= url('dashboard') ?>" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?> 