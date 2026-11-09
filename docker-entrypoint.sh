#!/bin/sh
set -e

# Render menggunakan environment variable $PORT dinamis (contoh: 10000)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf

# Buat storage link jika belum ada
php artisan storage:link || true

# Jalankan cache konfigurasi Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan Apache di foreground
exec apache2-foreground

