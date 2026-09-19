# Core Four Roofing platform

Laravel 11 app: public StoryBrand site plus `/admin` for agency, owner, and office staff.

## Seed logins

Password for all three: `CoreFour2026!`

| Role | Email |
|---|---|
| Agency | agency@corefourroofing.com |
| Owner | owner@corefourroofing.com |
| Staff | staff@corefourroofing.com |

Staff sees leads + chat only. Owner adds reports, email, guides. Agency adds users and settings.

Static look-and-feel preview (no PHP): serve `public/` and open `/preview/`.

## Local / VPS

See [DEPLOY.md](DEPLOY.md). After `composer install` and MySQL:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```
