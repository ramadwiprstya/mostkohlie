#!/bin/sh
set -e

# Railway/Render memberikan port dinamis lewat variabel $PORT (contoh: 8080 / 10000)
PORT="${PORT:-80}"
sed -i "s/Listen .*/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Hapus pid apache lama jika container restart
rm -f /var/run/apache2/apache2.pid 2>/dev/null || true

# Pastikan hanya 1 MPM aktif
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Buat storage link jika belum ada
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link || true
fi

# Jalankan cache konfigurasi Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan Apache di foreground
exec apache2-foreground
