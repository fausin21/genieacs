# GenieACS Portal

Portal manajemen GenieACS yang profesional menggunakan PHP native dan Bootstrap 5. Aplikasi ini menyediakan antarmuka web yang modern dan responsif untuk mengelola perangkat TR-069 melalui API GenieACS.

## 🚀 Fitur

### Core Features
- **Dashboard Interaktif** - Overview statistik perangkat dengan charts real-time
- **Manajemen Perangkat** - Daftar, detail, dan kontrol perangkat TR-069
- **Pencarian & Filter** - Pencarian live dan filter berdasarkan status
- **Pagination** - Navigasi halaman yang smooth dan responsive
- **Task Management** - Refresh, reboot, dan factory reset perangkat

### UI/UX Features
- **Bootstrap 5** - Desain modern dan responsive
- **AOS Animations** - Animasi scroll yang smooth
- **Dark Mode** - Toggle antara tema terang dan gelap
- **Loading States** - Indikator loading untuk semua operasi
- **Notifications** - Sistem notifikasi real-time
- **Charts** - Visualisasi data dengan Chart.js

### Technical Features
- **PHP Native** - Tidak ada framework, mudah dipahami dan dimodifikasi
- **RESTful API** - Komunikasi dengan GenieACS via REST API
- **Auto-refresh** - Update data otomatis tanpa reload halaman
- **Error Handling** - Penanganan error yang comprehensive
- **Security** - Headers keamanan dan validasi input

## 📋 Requirements

### Server Requirements
- PHP 7.4 atau lebih tinggi
- Web server (Apache/Nginx)
- cURL extension
- JSON extension
- Session support

### GenieACS Requirements
- GenieACS server yang berjalan
- API endpoint yang dapat diakses
- (Opsional) Autentikasi jika dikonfigurasi

## 🛠️ Instalasi

### 1. Clone atau Download
```bash
git clone <repository-url> genieacs-portal
cd genieacs-portal
```

### 2. Konfigurasi Web Server

#### Apache
File `.htaccess` sudah disediakan di folder `public/`. Pastikan mod_rewrite aktif:
```apache
a2enmod rewrite
service apache2 restart
```

Arahkan document root ke folder `public/`:
```apache
<VirtualHost *:80>
    DocumentRoot /path/to/genieacs-portal/public
    ServerName genieacs-portal.local
    
    <Directory /path/to/genieacs-portal/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name genieacs-portal.local;
    root /path/to/genieacs-portal/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Konfigurasi Aplikasi

Edit file `app/core/Config.php`:
```php
'GENIE_BASE' => 'http://192.168.99.134:7557',  // URL GenieACS
'GENIE_USERNAME' => '',                         // Username (jika ada auth)
'GENIE_PASSWORD' => '',                         // Password (jika ada auth)
'DEBUG' => false,                               // Set false untuk production
```

### 4. Set Permissions
```bash
chmod -R 755 public/
chmod -R 644 app/
```

### 5. Testing Installation

#### PHP Built-in Server (Development)
```bash
cd public
php -S localhost:8000
```
Akses: http://localhost:8000

#### Production
Akses melalui domain/IP yang dikonfigurasi di web server.

## 📖 Struktur Aplikasi

```
genieacs-portal/
│
├── public/                     # Web root - files yang dapat diakses browser
│   ├── index.php              # Front controller
│   ├── .htaccess              # Apache rewrite rules
│   └── assets/
│       ├── css/
│       │   └── custom.css     # Custom styling
│       └── js/
│           └── app.js         # JavaScript aplikasi
│
├── app/                       # Application logic
│   ├── core/                  # Core classes
│   │   ├── Config.php         # Configuration management
│   │   ├── Router.php         # Simple routing
│   │   └── HttpClient.php     # HTTP client untuk API
│   ├── controllers/           # Controllers
│   │   ├── Dashboard.php      # Dashboard controller
│   │   └── Devices.php        # Device management controller
│   ├── models/                # Models
│   │   └── Device.php         # Device model
│   └── views/                 # Views
│       ├── layouts/
│       │   ├── header.php     # Header layout
│       │   └── footer.php     # Footer layout
│       ├── dashboard.php      # Dashboard view
│       ├── devices.php        # Devices list view
│       ├── device_detail.php  # Device detail view
│       └── errors/            # Error pages
│           ├── 404.php
│           └── 500.php
│
└── README.md                  # Documentation
```

## 🎯 Penggunaan

### Dashboard
- Lihat statistik real-time perangkat
- Monitor perangkat online/offline
- Akses cepat ke fungsi utama
- Charts distribusi manufaktur

### Manajemen Perangkat
- **Daftar Perangkat**: Lihat semua perangkat dengan pagination
- **Pencarian**: Cari berdasarkan Device ID, manufaktur, atau model
- **Filter**: Filter berdasarkan status (online/offline)
- **Detail Perangkat**: Lihat informasi lengkap dan parameter
- **Task Management**: Refresh, reboot, factory reset

### Operasi Perangkat
1. **Refresh Device**: Memperbarui data perangkat dari GenieACS
2. **Reboot Device**: Restart perangkat secara remote
3. **Update Parameters**: Mengubah parameter seperti SSID/password WiFi
4. **Factory Reset**: Reset perangkat ke pengaturan pabrik

## 🔧 Kustomisasi

### Menambah Halaman Baru
1. Buat controller baru di `app/controllers/`
2. Buat view di `app/views/`
3. Tambahkan routing di `app/core/Router.php`

### Mengubah Styling
- Edit `public/assets/css/custom.css` untuk styling tambahan
- Variabel CSS tersedia di `:root` untuk konsistensi

### Menambah Fitur API
- Extend `app/models/Device.php` untuk fungsi baru
- Tambah endpoint di `Router::handleApiRoutes()`

## 🛡️ Keamanan

### Security Headers
- X-Frame-Options: SAMEORIGIN
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Content-Security-Policy (dapat disesuaikan)

### Input Validation
- Semua input di-escape untuk mencegah XSS
- Parameter URL di-validate
- CSRF protection pada form (dapat ditambahkan)

### File Protection
- Akses ke folder `app/` diblokir via .htaccess
- File sensitif (.env, logs) tidak dapat diakses

## 🐛 Troubleshooting

### GenieACS Connection Error
1. Pastikan URL GenieACS benar di `Config.php`
2. Cek firewall dan network connectivity
3. Verifikasi GenieACS service berjalan
4. Test dengan curl: `curl http://your-genieacs:7557/devices`

### Permission Errors
```bash
# Set proper permissions
chmod -R 755 public/
chown -R www-data:www-data public/
```

### Apache Rewrite Issues
```bash
# Enable mod_rewrite
a2enmod rewrite
service apache2 restart
```

### PHP Errors
- Set `DEBUG => true` di Config.php untuk development
- Check PHP error logs
- Pastikan semua PHP extensions terinstall

## 🔄 API Endpoints

### Internal API Routes
- `?p=api&action=refresh_device&device_id={id}` - Refresh device
- `?p=api&action=reboot_device&device_id={id}` - Reboot device

### GenieACS API Calls
- `GET /devices` - List devices
- `GET /devices/{id}` - Get device details
- `POST /tasks?device={id}` - Create task

## 📱 Responsive Design

Aplikasi fully responsive dengan breakpoints:
- **Desktop**: > 992px
- **Tablet**: 768px - 991px
- **Mobile**: < 768px

## 🎨 Theming

### Dark Mode
- Toggle manual via navbar
- Preferensi tersimpan di localStorage
- Respects system preference

### Customization
Ubah variabel CSS di `custom.css`:
```css
:root {
    --primary-gradient: your-gradient;
    --border-radius: your-radius;
    --transition-normal: your-timing;
}
```

## 📈 Performance

### Optimizations
- Lazy loading untuk images
- CSS/JS minification (production)
- Browser caching headers
- Gzip compression
- Auto-refresh hanya saat tab aktif

### Monitoring
- Built-in performance monitoring (debug mode)
- Memory usage tracking
- API response time monitoring

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Support

Untuk bantuan dan pertanyaan:
- Create issue di repository
- Email: support@example.com
- Documentation: /docs

## 🚀 Roadmap

### Version 2.0
- [ ] User authentication system
- [ ] Role-based permissions
- [ ] Advanced device grouping
- [ ] Scheduled tasks
- [ ] Email notifications
- [ ] Backup/restore functionality
- [ ] Multi-language support
- [ ] Advanced reporting

### Version 2.1
- [ ] WebSocket real-time updates
- [ ] Device configuration templates
- [ ] Bulk operations
- [ ] API rate limiting
- [ ] Advanced charts and analytics 