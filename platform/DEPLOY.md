# Hostinger VPS cutover

Replace the existing WordPress stack on `corefourroofing.com` with this Laravel app. Keep the same domain. Do not change city slugs.

## Server

Ubuntu 22 or 24 on Hostinger VPS.

```bash
sudo apt update
sudo apt install -y nginx mysql-server redis-server unzip git \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-xml php8.3-mbstring \
  php8.3-curl php8.3-zip php8.3-bcmath php8.3-redis
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Node 20 is only needed to rebuild CSS/JS. The repo already ships `/public/css` and `/public/js`.

## App

```bash
sudo mkdir -p /var/www/corefour
sudo chown $USER:$USER /var/www/corefour
# copy this platform/ folder to /var/www/corefour
cd /var/www/corefour
cp .env.example .env
# set APP_URL, DB_*, MAIL_*, OFFICE_*
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

Create MySQL database `core_four` and a user with a strong password. Put both in `.env`.

Mail: Amazon SES or Mailgun SMTP. Hostinger mailbox SMTP will choke a 12-month list.

## Nginx

Use `deploy/nginx.conf`. Point `root` at `/var/www/corefour/public`.

```bash
sudo ln -s /var/www/corefour/deploy/nginx.conf /etc/nginx/sites-available/corefour
sudo ln -s /etc/nginx/sites-available/corefour /etc/nginx/sites-enabled/corefour
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d corefourroofing.com -d www.corefourroofing.com
```

`www` 301s to apex. Admin is noindex in the HTML.

## Supervisor + cron

```bash
sudo cp deploy/supervisor-queue.conf /etc/supervisor/conf.d/corefour-queue.conf
sudo cp deploy/supervisor-schedule.conf /etc/supervisor/conf.d/corefour-schedule.conf
sudo supervisorctl reread
sudo supervisorctl update
```

Or crontab: `* * * * * cd /var/www/corefour && php artisan schedule:run >> /dev/null 2>&1`

Queue worker sends nurture mail (`email:send-due` every five minutes).

Reverb is optional. Chat ships with a 3-second long-poll so a blocked websocket port does not kill inbox.

## Deploy loop

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
# if you edit CSS/JS sources:
# npm ci && npm run build
sudo supervisorctl restart corefour-queue
```

## DNS / SSL cutover

1. Lower WordPress TTL a day ahead.
2. Confirm this app responds on the VPS IP (`curl -H "Host: corefourroofing.com" http://VPS_IP/`).
3. Point A record for `@` and `www` to the VPS.
4. Certbot SSL.
5. Hit `/sitemap.xml` and submit in Search Console.
6. Spot-check 301s below and 10 city URLs from `data/landing-pages.json`.
7. Keep the WordPress install as a files-only archive for two weeks. Delete it after 301s and rankings look stable.

## Backups

Daily:

```bash
mysqldump core_four | gzip > /var/backups/corefour-$(date +%F).sql.gz
tar czf /var/backups/corefour-storage-$(date +%F).tgz /var/www/corefour/storage
```

Keep 14 days. Test a restore once before cutover.

## Security

- CSRF on all forms and chat POST
- Throttle on login, leads, chat, guide download
- Hashed passwords, no shared `CoreReport2026!` HTML gate
- `/admin` noindex
- Rate-limit Nginx on `/login` if you see brute force

## WordPress 301 map

Same path on the new app (no redirect needed if Nginx serves Laravel):

- `/`
- `/residential-roofing/`
- `/commercial-roofing/`
- `/insurance-claims/`
- `/contact-core-four-roofing/`
- `/about/` (also 301 from `/about-us/` and `/about-core-four-roofing/`)
- `/service-areas/` (also 301 from `/areas-we-serve/`)
- `/emergency-roofing/` (also 301 from `/emergency-roof-repair/`)
- `/asphalt-shingles/`, `/metal-roofing/`, `/stone-coated-steel/`, `/synthetic-roofing/`
- `/tpo-roofing/`, `/epdm-roofing/`, `/modified-bitumen/`, `/built-up-roofing/`
- Every city URL in `database/data/landing-pages.json`

If a live WordPress slug differs, add `Route::permanentRedirect` in `routes/web.php` — do not invent a new city slug.

## Seed users (change these passwords after first login)

- agency@corefourroofing.com / `CoreFour2026!`
- owner@corefourroofing.com / `CoreFour2026!`
- staff@corefourroofing.com / `CoreFour2026!`
