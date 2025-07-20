# Cara Menggunakan GenieACS Portal

## 🚀 Akses Aplikasi

Setelah aplikasi disetup di Laragon, Anda bisa mengakses dengan URL berikut:

### URL Akses
- **Dashboard**: `http://localhost/genieacs/` atau `http://localhost/genieacs/index.php?halaman=dashboard`
- **Daftar Perangkat**: `http://localhost/genieacs/index.php?halaman=devices`
- **Detail Perangkat**: `http://localhost/genieacs/index.php?halaman=devices&action=detail&id=DEVICE_ID`

## 📖 Cara Menggunakan

### 1. Dashboard
Akses: `http://localhost/genieacs/`
- Lihat statistik perangkat (total, online, offline)
- Monitor perangkat yang baru terhubung
- Charts distribusi manufaktur
- Quick actions untuk navigasi

### 2. Daftar Perangkat
Akses: `http://localhost/genieacs/index.php?halaman=devices`
- Lihat semua perangkat TR-069
- Pencarian berdasarkan Device ID, manufaktur, model
- Filter berdasarkan status (online/offline)
- Pagination untuk navigasi halaman
- Quick actions: Detail, Refresh, Reboot

### 3. Detail Perangkat
Akses: `http://localhost/genieacs/index.php?halaman=devices&action=detail&id=DEVICE_ID`
- Informasi lengkap perangkat
- Semua parameter TR-069
- Update parameter (SSID WiFi, Password, dll)
- Task management (Refresh, Reboot, Factory Reset)
- Filter parameter (hanya yang writable)

## ⚙️ Konfigurasi GenieACS

Edit file `app/core/Config.php`:
```php
'GENIE_BASE' => 'http://192.168.99.134:7557',  // URL GenieACS server
'GENIE_USERNAME' => '',                         // Username (jika ada)
'GENIE_PASSWORD' => '',                         // Password (jika ada)
```

## 🎯 Fitur Utama

### Dashboard Features
- ✅ Real-time statistics
- ✅ Device status overview
- ✅ Manufacturer distribution charts
- ✅ Quick navigation
- ✅ Auto-refresh (30 detik)

### Device Management
- ✅ Search & Filter
- ✅ Pagination
- ✅ Status monitoring
- ✅ Parameter viewing/editing
- ✅ Remote tasks (refresh, reboot, factory reset)

### UI Features
- ✅ Bootstrap 5 responsive design
- ✅ Dark mode toggle
- ✅ Smooth animations
- ✅ Loading indicators
- ✅ Real-time notifications
- ✅ Mobile-friendly interface

## 🔧 Troubleshooting

### 1. Koneksi Error ke GenieACS
- Pastikan GenieACS server berjalan
- Cek URL di Config.php
- Test koneksi: `curl http://192.168.99.134:7557/devices`

### 2. PHP Errors
- Pastikan PHP 7.4+ terinstall
- Enable extension: cURL, JSON
- Set permissions yang benar

### 3. Asset Loading Issues
- Pastikan folder `assets/` dapat diakses
- Cek path di browser developer tools

## 📝 URL Patterns

Aplikasi menggunakan pattern URL sederhana:

### Basic Structure
```
index.php?halaman=[page]&action=[action]&id=[id]
```

### Examples
```
index.php?halaman=dashboard
index.php?halaman=devices
index.php?halaman=devices&action=detail&id=abc123
index.php?halaman=devices&search=router&status=online
index.php?halaman=api&action=refresh_device&device_id=abc123
```

## 🎨 Customization

### Mengubah Styling
Edit `assets/css/custom.css` untuk styling tambahan

### Menambah Halaman
1. Buat controller di `app/controllers/`
2. Buat view di `app/views/`
3. Tambah case di `index.php` routing

### Mengubah API Endpoints
Edit fungsi `handleApiRoutes()` di `index.php`

## 🔄 Auto-refresh

Aplikasi otomatis refresh data setiap 30 detik:
- Hanya saat tab aktif
- Tidak saat modal terbuka
- Dashboard: update statistics
- Devices: update status

## 📱 Mobile Support

Aplikasi full responsive:
- Navigation collapse pada mobile
- Table scroll horizontal
- Touch-friendly buttons
- Optimized untuk semua screen size

## 🚀 Performance Tips

1. **GenieACS Optimization**
   - Set inform interval yang reasonable
   - Limit parameter discovery
   - Use projection untuk large datasets

2. **Browser Performance**
   - Enable browser caching
   - Use latest browsers
   - Close unused tabs

3. **Server Performance**
   - Adequate PHP memory
   - Fast network to GenieACS
   - SSD for web server

## 📞 Support

Untuk bantuan:
- Cek documentasi GenieACS
- Review error logs
- Test API endpoints manually 