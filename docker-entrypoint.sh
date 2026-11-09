#!/bin/sh
set -e

# Railway memberikan port dinamis lewat variabel $PORT (contoh: 8080 / 10000)
PORT="${PORT:-80}"
sed -i "s/Listen .*/Listen $PORT/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:.*/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true

# Hapus PID apache lama jika container restart
rm -f /var/run/apache2/apache2.pid 2>/dev/null || true

# BERSIHKAN SEMUA MPM LALU HANYA AKTIFKAN PREFORK SECARA PAKSA (Fix AH00534)
rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

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
