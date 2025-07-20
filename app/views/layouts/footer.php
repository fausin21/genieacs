            </main>
        </div>
    </div>

    <!-- System Info Modal -->
    <div class="modal fade" id="systemInfoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>Informasi Sistem
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php 
                    $dashboardController = new Dashboard();
                    $systemInfo = $dashboardController->getSystemInfo();
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Server Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td><strong>PHP Version:</strong></td><td><?= $systemInfo['php_version'] ?></td></tr>
                                <tr><td><strong>Server Time:</strong></td><td><?= $systemInfo['server_time'] ?></td></tr>
                                <tr><td><strong>Timezone:</strong></td><td><?= $systemInfo['timezone'] ?></td></tr>
                                <tr><td><strong>Memory Usage:</strong></td><td><?= $systemInfo['memory_usage'] ?></td></tr>
                                <tr><td><strong>Memory Peak:</strong></td><td><?= $systemInfo['memory_peak'] ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Application Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td><strong>App Version:</strong></td><td><?= $systemInfo['app_version'] ?></td></tr>
                                <tr><td><strong>GenieACS URL:</strong></td><td><?= $systemInfo['genieacs_url'] ?></td></tr>
                                <tr><td><strong>App Name:</strong></td><td><?= Config::get('APP_NAME') ?></td></tr>
                                <tr><td><strong>Debug Mode:</strong></td><td><?= Config::get('DEBUG') ? 'Enabled' : 'Disabled' ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });

        // Theme toggle functionality
        function toggleTheme() {
            const html = document.querySelector('html');
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update theme toggle text
            const themeToggle = document.querySelector('[onclick="toggleTheme()"]');
            if (themeToggle) {
                themeToggle.innerHTML = newTheme === 'dark' 
                    ? '<i class="bi bi-sun-fill me-2"></i>Mode Terang'
                    : '<i class="bi bi-moon-fill me-2"></i>Mode Gelap';
            }
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.querySelector('html').setAttribute('data-bs-theme', savedTheme);
            
            // Update theme toggle text
            const themeToggle = document.querySelector('[onclick="toggleTheme()"]');
            if (themeToggle) {
                themeToggle.innerHTML = savedTheme === 'dark' 
                    ? '<i class="bi bi-sun-fill me-2"></i>Mode Terang'
                    : '<i class="bi bi-moon-fill me-2"></i>Mode Gelap';
            }
        });

        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('d-none');
        }

        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('d-none');
        }

        // API call helper
        async function apiCall(url, options = {}) {
            showLoading();
            try {
                const response = await fetch(url, {
                    headers: {
                        'Content-Type': 'application/json',
                        ...options.headers
                    },
                    ...options
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            } catch (error) {
                console.error('API Error:', error);
                showAlert('danger', 'Error: ' + error.message);
                throw error;
            } finally {
                hideLoading();
            }
        }

        // Show alert helper
        function showAlert(type, message) {
            const alertContainer = document.createElement('div');
            alertContainer.className = `alert alert-${type} alert-dismissible fade show m-3`;
            alertContainer.setAttribute('role', 'alert');
            alertContainer.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-triangle' : 'info-circle')} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.insertBefore(alertContainer, document.body.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertContainer.parentNode) {
                    alertContainer.remove();
                }
            }, 5000);
        }

        // Refresh device function
        async function refreshDevice(deviceId) {
            try {
                const data = await apiCall(`?p=api&action=refresh_device&device_id=${encodeURIComponent(deviceId)}`);
                showAlert('success', data.message);
                
                // Refresh page after 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } catch (error) {
                // Error already handled in apiCall
            }
        }

        // Reboot device function
        async function rebootDevice(deviceId) {
            if (!confirm('Apakah Anda yakin ingin reboot perangkat ini?')) {
                return;
            }
            
            try {
                const data = await apiCall(`?p=api&action=reboot_device&device_id=${encodeURIComponent(deviceId)}`);
                showAlert('success', data.message);
                
                // Refresh page after 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } catch (error) {
                // Error already handled in apiCall
            }
        }

        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }

        // Auto-refresh functionality for device status
        let autoRefreshInterval;
        
        function startAutoRefresh(intervalMs = 30000) {
            autoRefreshInterval = setInterval(() => {
                // Only refresh if user is active (not in background tab)
                if (!document.hidden) {
                    updateDeviceStatuses();
                }
            }, intervalMs);
        }
        
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }
        
        async function updateDeviceStatuses() {
            // This would update device statuses without full page reload
            // Implementation depends on specific requirements
            console.log('Updating device statuses...');
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Handle responsive tables
        document.addEventListener('DOMContentLoaded', function() {
            const tables = document.querySelectorAll('.table-responsive table');
            tables.forEach(table => {
                if (table.offsetWidth > table.parentElement.offsetWidth) {
                    table.parentElement.style.overflowX = 'auto';
                }
            });
        });
    </script>

    <script src="assets/js/app.js"></script>
</body>
</html> 