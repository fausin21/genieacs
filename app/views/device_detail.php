<?php include 'layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Back Button -->
    <div class="row mb-3" data-aos="fade-right">
        <div class="col-12">
            <a href="<?= url('devices') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Perangkat
            </a>
        </div>
    </div>

    <?php if ($device && $info): ?>
        <!-- Page Header -->
        <div class="row mb-4" data-aos="fade-right" data-aos-delay="100">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold mb-1">
                    <i class="bi bi-router me-3"></i>Detail Perangkat
                </h1>
                <p class="text-muted">
                    <code><?= htmlspecialchars($info['id']) ?></code>
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-success" onclick="refreshDevice('<?= htmlspecialchars($info['id']) ?>')">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                    <button type="button" class="btn btn-warning" onclick="rebootDevice('<?= htmlspecialchars($info['id']) ?>')">
                        <i class="bi bi-power me-2"></i>Reboot
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#factoryResetModal">
                        <i class="bi bi-exclamation-triangle me-2"></i>Factory Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Device Info Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="card device-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-building fs-1 text-primary mb-3"></i>
                        <h6 class="card-title">Manufaktur</h6>
                        <p class="card-text fw-bold"><?= htmlspecialchars($info['manufacturer'] ?: 'Unknown') ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="card device-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cpu fs-1 text-info mb-3"></i>
                        <h6 class="card-title">Model</h6>
                        <p class="card-text fw-bold"><?= htmlspecialchars($info['model'] ?: 'Unknown') ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="card device-card h-100">
                    <div class="card-body text-center">
                        <?php
                        $statusIcon = 'question-circle';
                        $statusColor = 'secondary';
                        
                        switch ($info['status']) {
                            case 'Online':
                                $statusIcon = 'wifi';
                                $statusColor = 'success';
                                break;
                            case 'Recently Online':
                                $statusIcon = 'wifi';
                                $statusColor = 'warning';
                                break;
                            case 'Offline':
                                $statusIcon = 'wifi-off';
                                $statusColor = 'danger';
                                break;
                        }
                        ?>
                        <i class="bi bi-<?= $statusIcon ?> fs-1 text-<?= $statusColor ?> mb-3"></i>
                        <h6 class="card-title">Status</h6>
                        <span class="badge bg-<?= $statusColor ?> status-badge"><?= $info['status'] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3" data-aos="zoom-in" data-aos-delay="500">
                <div class="card device-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-globe fs-1 text-warning mb-3"></i>
                        <h6 class="card-title">IP Address</h6>
                        <p class="card-text fw-bold">
                            <?= $info['ipAddress'] !== 'Unknown' ? htmlspecialchars($info['ipAddress']) : 'Unknown' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Details -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informasi Perangkat
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Device ID:</strong></td>
                                <td><code><?= htmlspecialchars($info['id']) ?></code></td>
                            </tr>
                            <tr>
                                <td><strong>Serial Number:</strong></td>
                                <td><?= htmlspecialchars($info['serial']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Software Version:</strong></td>
                                <td><?= htmlspecialchars($info['softwareVersion'] ?: 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Hardware Version:</strong></td>
                                <td><?= htmlspecialchars($info['hardwareVersion'] ?: 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Last Inform:</strong></td>
                                <td>
                                    <?php if ($info['connectionTime']): ?>
                                        <?= date('Y-m-d H:i:s', strtotime($info['connectionTime'])) ?>
                                        <br><small class="text-muted">(<?= date('D, d M Y', strtotime($info['connectionTime'])) ?>)</small>
                                    <?php else: ?>
                                        <span class="text-muted">Unknown</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Uptime:</strong></td>
                                <td>
                                    <?php if ($info['uptime']): ?>
                                        <?php
                                        $seconds = intval($info['uptime']);
                                        $days = floor($seconds / 86400);
                                        $hours = floor(($seconds % 86400) / 3600);
                                        $minutes = floor(($seconds % 3600) / 60);
                                        echo "$days hari, $hours jam, $minutes menit";
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">Unknown</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="700">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning-charge me-2"></i>Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Quick Tasks Form -->
                        <form method="POST" action="<?= url('devices', ['action' => 'task', 'id' => $info['id']]) ?>">
                            <div class="mb-3">
                                <label for="task_type" class="form-label">Pilih Task:</label>
                                <select class="form-select" id="task_type" name="task_type" required>
                                    <option value="">Pilih task...</option>
                                    <option value="refresh">Refresh Device</option>
                                    <option value="reboot">Reboot Device</option>
                                    <option value="factory_reset">Factory Reset</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-play-circle me-2"></i>Jalankan Task
                            </button>
                        </form>

                        <hr>

                        <!-- Quick Parameter Updates -->
                        <h6 class="mb-3">Update Parameter Cepat:</h6>
                        
                        <!-- WiFi Settings -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-wifi me-2"></i>Pengaturan WiFi</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="<?= url('devices', ['action' => 'update', 'id' => $info['id']]) ?>" id="wifiUpdateForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="wifi_ssid" class="form-label">SSID Wi-Fi:</label>
                                            <input type="text" class="form-control" id="wifi_ssid" name="parameters[Device.WiFi.SSID.1.SSID]" placeholder="Masukkan SSID baru">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="wifi_password" class="form-label">Password Wi-Fi:</label>
                                            <input type="password" class="form-control" id="wifi_password" name="parameters[Device.WiFi.AccessPoint.1.Security.KeyPassphrase]" placeholder="Masukkan password baru">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-wifi me-2"></i>Update WiFi
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Virtual Parameters -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Virtual Parameters - Editable</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="<?= url('devices', ['action' => 'update', 'id' => $info['id']]) ?>" id="virtualParamsForm">
                                    <div class="row">
                                        <!-- PPPoE Settings -->
                                        <div class="col-md-6 mb-3">
                                            <label for="pppoe_username" class="form-label">PPPoE Username:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="pppoe_username" name="parameters[VirtualParameters.pppoeUsername]" 
                                                       placeholder="Username PPPoE"
                                                       value="<?= htmlspecialchars($parameters['VirtualParameters.pppoeUsername']['value'] ?? '') ?>">
                                                <button type="button" class="btn btn-outline-secondary" onclick="setPresetValue('pppoe_username', 'epon')" data-bs-toggle="tooltip" title="Set default">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="pppoe_password" class="form-label">PPPoE Password:</label>
                                            <input type="password" class="form-control" id="pppoe_password" name="parameters[VirtualParameters.pppoePassword]" 
                                                   placeholder="Password PPPoE"
                                                   value="<?= htmlspecialchars($parameters['VirtualParameters.pppoePassword']['value'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- WiFi Settings -->
                                        <div class="col-md-6 mb-3">
                                            <label for="wlan_password" class="form-label">WiFi/WLAN Password:</label>
                                            <input type="password" class="form-control" id="wlan_password" name="parameters[VirtualParameters.WlanPassword]" 
                                                   placeholder="Password WiFi"
                                                   value="<?= htmlspecialchars($parameters['VirtualParameters.WlanPassword']['value'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">WiFi Status:</label>
                                            <div class="form-control-plaintext">
                                                <?php if (!empty($parameters['VirtualParameters.WlanPassword']['value'])): ?>
                                                    <span class="badge bg-success"><i class="bi bi-shield-check me-1"></i>Password Set</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><i class="bi bi-shield-exclamation me-1"></i>No Password</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- Admin Settings -->
                                        <div class="col-md-6 mb-3">
                                            <label for="super_admin" class="form-label">Super Admin Username:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="super_admin" name="parameters[VirtualParameters.superAdmin]" 
                                                       placeholder="admin"
                                                       value="<?= htmlspecialchars($parameters['VirtualParameters.superAdmin']['value'] ?? '') ?>">
                                                <button type="button" class="btn btn-outline-secondary" onclick="setPresetValue('super_admin', 'admin')" data-bs-toggle="tooltip" title="Set default">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="super_password" class="form-label">Super Admin Password:</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="super_password" name="parameters[VirtualParameters.superPassword]" 
                                                       placeholder="Password admin"
                                                       value="<?= htmlspecialchars($parameters['VirtualParameters.superPassword']['value'] ?? '') ?>">
                                                <button type="button" class="btn btn-outline-secondary" onclick="setPresetValue('super_password', 'admin')" data-bs-toggle="tooltip" title="Set default">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- User Settings -->
                                        <div class="col-md-6 mb-3">
                                            <label for="user_admin" class="form-label">User Admin Username:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="user_admin" name="parameters[VirtualParameters.userAdmin]" 
                                                       placeholder="user1234"
                                                       value="<?= htmlspecialchars($parameters['VirtualParameters.userAdmin']['value'] ?? '') ?>">
                                                <button type="button" class="btn btn-outline-secondary" onclick="setPresetValue('user_admin', 'user1234')" data-bs-toggle="tooltip" title="Set default">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="user_password" class="form-label">User Admin Password:</label>
                                            <input type="password" class="form-control" id="user_password" name="parameters[VirtualParameters.userPassword]" 
                                                   placeholder="Password user"
                                                   value="<?= htmlspecialchars($parameters['VirtualParameters.userPassword']['value'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-gear me-2"></i>Update Virtual Parameters
                                    </button>
                                    
                                    <!-- Quick Presets -->
                                    <div class="mt-3">
                                        <h6 class="mb-2">Quick Presets:</h6>
                                        <button type="button" class="btn btn-outline-info btn-sm me-2 mb-2" onclick="setStandardAdminPreset()">
                                            <i class="bi bi-person-gear me-1"></i>Standard Admin
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm me-2 mb-2" onclick="setEponPreset()">
                                            <i class="bi bi-wifi me-1"></i>EPON Default
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm me-2 mb-2" onclick="clearAllFields()">
                                            <i class="bi bi-eraser me-1"></i>Clear All
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Device Information (Read-only) -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Device Information (Read-only)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <strong>Serial Number:</strong><br>
                                        <code><?= $parameters['VirtualParameters.getSerialNumber']['value'] ?? 'N/A' ?></code>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <strong>PON Mode:</strong><br>
                                        <span class="badge bg-info"><?= $parameters['VirtualParameters.getponmode']['value'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <strong>Temperature:</strong><br>
                                        <span class="badge bg-<?= (isset($parameters['VirtualParameters.gettemp']['value']) && intval($parameters['VirtualParameters.gettemp']['value']) > 50) ? 'danger' : 'success' ?>">
                                            <?= $parameters['VirtualParameters.gettemp']['value'] ?? 'N/A' ?>°C
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <strong>RX Power:</strong><br>
                                        <span class="badge bg-<?= (isset($parameters['VirtualParameters.RXPower']['value']) && intval($parameters['VirtualParameters.RXPower']['value']) < -25) ? 'danger' : 'info' ?>">
                                            <?= $parameters['VirtualParameters.RXPower']['value'] ?? 'N/A' ?> dBm
                                        </span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <strong>Device Uptime:</strong><br>
                                        <span class="badge bg-success"><?= $parameters['VirtualParameters.getdeviceuptime']['value'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <strong>PPP Uptime:</strong><br>
                                        <span class="badge bg-primary"><?= $parameters['VirtualParameters.getpppuptime']['value'] ?? 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Network Information -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-ethernet me-2"></i>Network Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>PPPoE IP Address:</strong><br>
                                        <code><?= $parameters['VirtualParameters.pppoeIP']['value'] ?? 'N/A' ?></code>
                                        <?php if (!empty($parameters['VirtualParameters.pppoeIP']['value'])): ?>
                                            <span class="badge bg-success ms-2">Connected</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Active Devices:</strong><br>
                                        <span class="badge bg-info fs-6"><?= $parameters['VirtualParameters.activedevices']['value'] ?? '0' ?> devices</span>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>PON MAC Address:</strong><br>
                                        <code><?= $parameters['VirtualParameters.PonMac']['value'] ?? 'N/A' ?></code>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>PPPoE MAC Address:</strong><br>
                                        <code><?= $parameters['VirtualParameters.pppoeMac']['value'] ?? 'N/A' ?></code>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Current PPPoE Username:</strong><br>
                                        <code><?= $parameters['VirtualParameters.pppoeUsername2']['value'] ?? 'N/A' ?></code>
                                        <small class="text-muted d-block">Read-only (current active)</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>TR069 IP:</strong><br>
                                        <code><?= $parameters['VirtualParameters.IPTR069']['value'] ?: 'Auto/DHCP' ?></code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parameters Table -->
        <div class="row" data-aos="fade-up" data-aos-delay="800">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i>Parameter Lengkap
                        </h5>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleWritableOnly()">
                                <i class="bi bi-funnel me-1"></i>Hanya Writable
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#parameterForm">
                                <i class="bi bi-pencil me-1"></i>Edit Parameters
                            </button>
                        </div>
                    </div>
                    
                    <!-- Parameter Edit Form -->
                    <div class="collapse" id="parameterForm">
                        <div class="card-body border-bottom">
                            <form method="POST" action="<?= url('devices', ['action' => 'update', 'id' => $info['id']]) ?>" id="parameterUpdateForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="param_name" class="form-label">Parameter Name:</label>
                                        <input type="text" class="form-control" id="param_name" placeholder="e.g., Device.WiFi.SSID.1.SSID">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="param_value" class="form-label">Value:</label>
                                        <input type="text" class="form-control" id="param_value" placeholder="Parameter value">
                                    </div>
                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary w-100" onclick="addParameterToForm()">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="dynamicParameters"></div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Update Parameters
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <?php if (!empty($parameters)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="parametersTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40%">Parameter</th>
                                            <th width="30%">Value</th>
                                            <th width="10%">Type</th>
                                            <th width="10%">Writable</th>
                                            <th width="10%">Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($parameters as $param => $data): ?>
                                            <tr class="parameter-row <?= $data['writable'] ? 'writable-param' : '' ?>">
                                                <td>
                                                    <code class="text-primary"><?= htmlspecialchars($param) ?></code>
                                                </td>
                                                <td>
                                                    <span class="text-break"><?= htmlspecialchars(strlen($data['value']) > 100 ? substr($data['value'], 0, 100) . '...' : $data['value']) ?></span>
                                                    <?php if (strlen($data['value']) > 100): ?>
                                                        <button type="button" class="btn btn-link btn-sm p-0 ms-2" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#valueModal"
                                                                onclick="showFullValue('<?= htmlspecialchars($param) ?>', '<?= htmlspecialchars($data['value']) ?>')">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark"><?= htmlspecialchars($data['type']) ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($data['writable']): ?>
                                                        <i class="bi bi-pencil-square text-success" title="Writable"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-lock text-muted" title="Read-only"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($data['timestamp']): ?>
                                                        <small class="text-muted" data-bs-toggle="tooltip" title="<?= htmlspecialchars($data['timestamp']) ?>">
                                                            <?= date('d/m H:i', strtotime($data['timestamp'])) ?>
                                                        </small>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-list-ul fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada parameter ditemukan</h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Device Not Found -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
                    <h3 class="text-danger">Perangkat Tidak Ditemukan</h3>
                    <p class="text-muted">Perangkat yang Anda cari mungkin tidak ada atau terjadi error saat mengambil data.</p>
                    <a href="<?= url('devices') ?>" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Perangkat
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Value Modal -->
<div class="modal fade" id="valueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Parameter Value</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 id="modalParamName" class="mb-3"></h6>
                <pre id="modalParamValue" class="bg-light p-3 rounded"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Factory Reset Modal -->
<div class="modal fade" id="factoryResetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Factory Reset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>PERINGATAN:</strong> Factory reset akan mengembalikan perangkat ke pengaturan pabrik dan menghapus semua konfigurasi.</p>
                <p>Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="<?= url('devices', ['action' => 'task', 'id' => $info['id'] ?? '']) ?>" class="d-inline">
                    <input type="hidden" name="task_type" value="factory_reset">
                    <button type="submit" class="btn btn-danger">Ya, Factory Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let writableOnlyFilter = false;

function toggleWritableOnly() {
    writableOnlyFilter = !writableOnlyFilter;
    const rows = document.querySelectorAll('.parameter-row');
    
    rows.forEach(row => {
        if (writableOnlyFilter) {
            row.style.display = row.classList.contains('writable-param') ? '' : 'none';
        } else {
            row.style.display = '';
        }
    });
    
    const btn = document.querySelector('[onclick="toggleWritableOnly()"]');
    btn.innerHTML = writableOnlyFilter 
        ? '<i class="bi bi-funnel-fill me-1"></i>Semua Parameter'
        : '<i class="bi bi-funnel me-1"></i>Hanya Writable';
}

function showFullValue(paramName, paramValue) {
    document.getElementById('modalParamName').textContent = paramName;
    document.getElementById('modalParamValue').textContent = paramValue;
}

function addParameterToForm() {
    const paramName = document.getElementById('param_name').value;
    const paramValue = document.getElementById('param_value').value;
    
    if (!paramName || !paramValue) {
        alert('Mohon isi nama parameter dan value');
        return;
    }
    
    const container = document.getElementById('dynamicParameters');
    const row = document.createElement('div');
    row.className = 'row mb-2';
    row.innerHTML = `
        <div class="col-md-6">
            <input type="text" class="form-control" name="parameters[${paramName}]" value="${paramValue}" readonly>
        </div>
        <div class="col-md-4">
            <code class="form-control-plaintext">${paramName}</code>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.parentElement.parentElement.remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(row);
    
    // Clear inputs
    document.getElementById('param_name').value = '';
    document.getElementById('param_value').value = '';
}

function setPresetValue(inputId, presetValue) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = presetValue;
        input.focus();
        
        // Add visual feedback
        input.classList.add('bg-warning-subtle');
        setTimeout(() => {
            input.classList.remove('bg-warning-subtle');
        }, 1000);
    }
}

// Add confirmation for virtual parameters form
document.addEventListener('DOMContentLoaded', function() {
    const virtualParamsForm = document.getElementById('virtualParamsForm');
    if (virtualParamsForm) {
        virtualParamsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if any field is filled
            const inputs = this.querySelectorAll('input[type="text"], input[type="password"]');
            let hasValues = false;
            
            inputs.forEach(input => {
                if (input.value.trim()) {
                    hasValues = true;
                }
            });
            
            if (!hasValues) {
                showAlert('warning', 'Mohon isi minimal satu parameter yang ingin diubah.');
                return;
            }
            
            // Show confirmation
            const confirmed = confirm(
                'Apakah Anda yakin ingin mengubah Virtual Parameters?\n\n' +
                'Perubahan ini akan mempengaruhi:\n' +
                '- Pengaturan login admin\n' +
                '- Konfigurasi PPP/PPPoE\n' +
                '- Akses ke perangkat\n\n' +
                'Pastikan Anda mencatat perubahan yang dilakukan.'
            );
            
            if (confirmed) {
                this.submit();
            }
        });
    }
});

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of form
    const form = document.getElementById('virtualParamsForm');
    if (form) {
        form.insertAdjacentHTML('afterbegin', alertHtml);
    }
}

function setStandardAdminPreset() {
    setPresetValue('super_admin', 'admin');
    setPresetValue('super_password', 'admin');
    setPresetValue('user_admin', 'user1234');
    setPresetValue('user_password', 'password1234');
    showAlert('info', 'Preset "Standard Admin" telah diterapkan.');
}

function setEponPreset() {
    setPresetValue('pppoe_username', 'epon');
    setPresetValue('pppoe_password', 'epon');
    setPresetValue('wlan_password', 'epon');
    showAlert('info', 'Preset "EPON Default" telah diterapkan.');
}

function clearAllFields() {
    document.getElementById('super_admin').value = '';
    document.getElementById('super_password').value = '';
    document.getElementById('user_admin').value = '';
    document.getElementById('user_password').value = '';
    document.getElementById('pppoe_username').value = '';
    document.getElementById('pppoe_password').value = '';
    document.getElementById('wlan_password').value = '';
    showAlert('warning', 'Semua parameter telah dikosongkan.');
}
</script>

<?php include 'layouts/footer.php'; ?> 