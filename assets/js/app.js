/**
 * GenieACS Portal - Application JavaScript
 * Professional interactive features and AJAX functionality
 */

// Global variables
let isLoading = false;
let autoRefreshInterval = null;
let notificationTimeout = null;

// Application initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    initializeTooltips();
    initializeModals();
    initializeFormValidation();
    initializeLiveSearch();
    initializeTheme();
    
    // Start auto-refresh if on devices page
    if (window.location.search.includes('halaman=devices') || window.location.search.includes('halaman=dashboard') || !window.location.search.includes('halaman=')) {
        startAutoRefresh();
    }
});

// Initialize application
function initializeApp() {
    console.log('GenieACS Portal initialized');
    
    // Add loading states to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            }
        });
    });
    
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();
}

// Initialize tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
    });
}

// Initialize modals
function initializeModals() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            const firstInput = modal.querySelector('input, select, textarea');
            if (firstInput) {
                firstInput.focus();
            }
        });
    });
}

// Initialize form validation
function initializeFormValidation() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                e.stopPropagation();
                showNotification('danger', 'Mohon lengkapi semua field yang wajib diisi');
            }
        });
    });
}

// Initialize live search
function initializeLiveSearch() {
    const searchInputs = document.querySelectorAll('input[name="search"]');
    
    searchInputs.forEach(input => {
        let searchTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    performLiveSearch(this.value);
                }
            }, 500);
        });
    });
}

// Initialize theme system
function initializeTheme() {
    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    
    // Update theme toggle text
    updateThemeToggleText(savedTheme);
    
    // Listen for system theme changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('theme')) {
                const newTheme = e.matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', newTheme);
                updateThemeToggleText(newTheme);
            }
        });
    }
}

// Initialize keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl+K for search
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Ctrl+R for refresh
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            refreshCurrentPage();
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modalInstance = bootstrap.Modal.getInstance(openModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        }
    });
}

// Form validation
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    requiredFields.forEach(field => {
        field.classList.remove('is-invalid');
        
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
            
            // Create or update error message
            let errorMsg = field.parentNode.querySelector('.invalid-feedback');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'invalid-feedback';
                field.parentNode.appendChild(errorMsg);
            }
            errorMsg.textContent = 'Field ini wajib diisi';
        }
    });
    
    return isValid;
}

// Live search functionality
function performLiveSearch(query) {
    if (isLoading) return;
    
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('search', query);
    currentUrl.searchParams.delete('page');
    
    showLoading();
    
    fetch(currentUrl.toString())
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            
            // Update results table
            const currentTable = document.querySelector('.table-responsive');
            const newTable = newDoc.querySelector('.table-responsive');
            
            if (currentTable && newTable) {
                currentTable.innerHTML = newTable.innerHTML;
                
                // Re-initialize tooltips for new content
                initializeTooltips();
                
                // Update results info
                const currentInfo = document.querySelector('.row .text-muted');
                const newInfo = newDoc.querySelector('.row .text-muted');
                if (currentInfo && newInfo) {
                    currentInfo.textContent = newInfo.textContent;
                }
            }
        })
        .catch(error => {
            console.error('Live search error:', error);
            showNotification('danger', 'Error saat melakukan pencarian');
        })
        .finally(() => {
            hideLoading();
        });
}

// Theme toggle functionality
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    updateThemeToggleText(newTheme);
    
    // Add a nice transition effect
    document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
    setTimeout(() => {
        document.body.style.transition = '';
    }, 300);
}

// Update theme toggle text
function updateThemeToggleText(theme) {
    const themeToggle = document.querySelector('[onclick="toggleTheme()"]');
    if (themeToggle) {
        themeToggle.innerHTML = theme === 'dark' 
            ? '<i class="bi bi-sun-fill me-2"></i>Mode Terang'
            : '<i class="bi bi-moon-fill me-2"></i>Mode Gelap';
    }
}

// Loading overlay functions
function showLoading() {
    isLoading = true;
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.remove('d-none');
    }
    
    // Add loading class to body
    document.body.classList.add('loading');
}

function hideLoading() {
    isLoading = false;
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.add('d-none');
    }
    
    // Remove loading class from body
    document.body.classList.remove('loading');
}

// Notification system
function showNotification(type, message, duration = 5000) {
    // Clear existing notification timeout
    if (notificationTimeout) {
        clearTimeout(notificationTimeout);
    }
    
    // Remove existing notifications
    document.querySelectorAll('.notification-alert').forEach(alert => {
        alert.remove();
    });
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show notification-alert position-fixed" 
             style="top: 80px; right: 20px; z-index: 9999; min-width: 300px;" 
             role="alert">
            <i class="bi bi-${getIconForType(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto remove after duration
    notificationTimeout = setTimeout(() => {
        const notification = document.querySelector('.notification-alert');
        if (notification) {
            notification.remove();
        }
    }, duration);
}

// Get icon for notification type
function getIconForType(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// API call helper with better error handling
async function apiCall(url, options = {}) {
    if (isLoading) {
        throw new Error('Another request is in progress');
    }
    
    showLoading();
    
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, finalOptions);
        
        if (!response.ok) {
            let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
            
            try {
                const errorData = await response.json();
                errorMessage = errorData.error || errorData.message || errorMessage;
            } catch (e) {
                // Use default error message if JSON parsing fails
            }
            
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        return data;
        
    } catch (error) {
        console.error('API Error:', error);
        showNotification('danger', error.message);
        throw error;
    } finally {
        hideLoading();
    }
}

// Device management functions
async function refreshDevice(deviceId) {
    try {
        const data = await apiCall(`?halaman=api&action=refresh_device&device_id=${encodeURIComponent(deviceId)}`);
        showNotification('success', data.message || 'Refresh task berhasil dibuat');
        
        // Refresh current page after 2 seconds
        setTimeout(() => {
            refreshCurrentPage();
        }, 2000);
    } catch (error) {
        // Error already handled in apiCall
    }
}

async function rebootDevice(deviceId) {
    const confirmed = await showConfirmDialog(
        'Konfirmasi Reboot',
        'Apakah Anda yakin ingin reboot perangkat ini? Perangkat akan restart dan mungkin offline sementara.',
        'warning'
    );
    
    if (!confirmed) return;
    
    try {
        const data = await apiCall(`?halaman=api&action=reboot_device&device_id=${encodeURIComponent(deviceId)}`);
        showNotification('success', data.message || 'Reboot task berhasil dibuat');
        
        // Refresh current page after 2 seconds
        setTimeout(() => {
            refreshCurrentPage();
        }, 2000);
    } catch (error) {
        // Error already handled in apiCall
    }
}

// Confirmation dialog
function showConfirmDialog(title, message, type = 'warning') {
    return new Promise((resolve) => {
        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="confirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-${type} text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-${getIconForType(type)} me-2"></i>${title}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${message}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-${type}" id="confirmBtn">Ya, Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('confirmModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to page
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        
        // Handle confirm button
        document.getElementById('confirmBtn').addEventListener('click', () => {
            modal.hide();
            resolve(true);
        });
        
        // Handle modal close
        document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
            document.getElementById('confirmModal').remove();
            resolve(false);
        });
        
        modal.show();
    });
}

// Auto-refresh functionality
function startAutoRefresh(intervalMs = 30000) {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    autoRefreshInterval = setInterval(() => {
        // Only refresh if user is active and not in a modal
        if (!document.hidden && !document.querySelector('.modal.show') && !isLoading) {
            updatePageData();
        }
    }, intervalMs);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

// Update page data without full reload
async function updatePageData() {
    try {
        const response = await fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) return;
        
        const html = await response.text();
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');
        
        // Update specific elements based on current page
        if (window.location.search.includes('halaman=dashboard') || !window.location.search.includes('halaman=')) {
            updateDashboardData(newDoc);
        } else if (window.location.search.includes('halaman=devices')) {
            updateDevicesData(newDoc);
        }
        
    } catch (error) {
        console.error('Auto-refresh error:', error);
    }
}

// Update dashboard data
function updateDashboardData(newDoc) {
    // Update stat cards
    const statCards = document.querySelectorAll('.stat-card h3');
    const newStatCards = newDoc.querySelectorAll('.stat-card h3');
    
    statCards.forEach((card, index) => {
        if (newStatCards[index]) {
            animateNumberChange(card, newStatCards[index].textContent);
        }
    });
    
    // Update recent devices table
    const currentTable = document.querySelector('.table tbody');
    const newTable = newDoc.querySelector('.table tbody');
    
    if (currentTable && newTable && currentTable.innerHTML !== newTable.innerHTML) {
        currentTable.innerHTML = newTable.innerHTML;
        initializeTooltips(); // Re-initialize tooltips
    }
}

// Update devices data
function updateDevicesData(newDoc) {
    const currentTable = document.querySelector('.table tbody');
    const newTable = newDoc.querySelector('.table tbody');
    
    if (currentTable && newTable && currentTable.innerHTML !== newTable.innerHTML) {
        currentTable.innerHTML = newTable.innerHTML;
        initializeTooltips(); // Re-initialize tooltips
        
        // Show notification for updates
        showNotification('info', 'Data perangkat telah diperbarui', 2000);
    }
}

// Animate number changes
function animateNumberChange(element, newValue) {
    const currentValue = parseInt(element.textContent.replace(/,/g, ''));
    const targetValue = parseInt(newValue.replace(/,/g, ''));
    
    if (currentValue === targetValue) return;
    
    const duration = 1000;
    const steps = 30;
    const increment = (targetValue - currentValue) / steps;
    let current = currentValue;
    let step = 0;
    
    const interval = setInterval(() => {
        step++;
        current += increment;
        
        if (step >= steps) {
            current = targetValue;
            clearInterval(interval);
        }
        
        element.textContent = Math.round(current).toLocaleString();
    }, duration / steps);
}

// Refresh current page
function refreshCurrentPage() {
    showLoading();
    window.location.reload();
}

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
            } else {
            if (window.location.search.includes('halaman=devices') || window.location.search.includes('halaman=dashboard') || !window.location.search.includes('halaman=')) {
                startAutoRefresh();
            }
        }
});

// Handle beforeunload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});

// Export functions for global use
window.GenieACS = {
    refreshDevice,
    rebootDevice,
    toggleTheme,
    showNotification,
    showConfirmDialog,
    apiCall,
    refreshCurrentPage
}; 