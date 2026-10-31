# Odik Shop — cPanel Addon Domain Deploy Guide

Ei zip ta **upload-ready**. `vendor/` ar `public/build/` (compiled CSS/JS) already vitore ache —
tai server e **composer** ba **npm** chalanor dorkar nei.

**Requirement:** PHP **8.2+** (Laravel 12 + Livewire 4), MySQL 5.7+/MariaDB 10.3+

---

## Step 1 — Addon domain toiri korun

cPanel → **Domains → Create A Domain**

| Field | Value |
|---|---|
| Domain | `newshop.com` (apnar notun domain) |
| Document Root | `/home/sameerth/newshop.com/public`  ← **`/public` ta obosshoi likhben** |

> "Share document root" checkbox tick **korben na**.
> Jodi cPanel `/public` shoho doc root nite na dey, niche **Step 7 (Plan B)** dekhun.

---

## Step 2 — Zip upload + extract

1. File Manager → `/home/sameerth/newshop.com/` folder e jan
   (public folder ta already toiri ache — problem nei, extract holei overwrite hobe)
2. **Upload** → `odikshop-laravel-deploy.zip`
3. File select kore **Extract** click korun
4. Extract sesh hole zip file ta **delete** kore din

Ekhon folder ta erokom dekhabe:

```
/home/sameerth/newshop.com/
├── app/  bootstrap/  config/  database/  lang/
├── public/          <-- domain er document root
├── resources/  routes/  storage/  vendor/
├── .env             <-- edit korte hobe (Step 4)
└── artisan
```

---

## Step 3 — Database toiri korun

cPanel → **MySQL Databases**

1. New database: `sameerth_newshop`
2. New user + strong password: `sameerth_shopuser`
3. **Add User To Database** → **ALL PRIVILEGES** din

Ei 3 ta value tine rakhun — porer step e lagbe.

---

## Step 4 — `.env` file edit korun

File Manager e `.env` file ta **Edit** korun (hidden file — na dekhle
Settings → "Show Hidden Files" on korun). Ei line gulo change korun:

```env
APP_URL=https://newshop.com

DB_DATABASE=sameerth_newshop
DB_USERNAME=sameerth_shopuser
DB_PASSWORD=apnar_password
```

`APP_ENV=production` ar `APP_DEBUG=false` already set kora ache — thik ache.

> **APP_KEY** already deya ache, tai `key:generate` na chalaleo cholbe.

---

## Step 5 — PHP version + migration

**cPanel → MultiPHP Manager**: ei domain er PHP version **8.2 / 8.3** set korun.

Er por **cPanel → Terminal** e:

```bash
cd ~/newshop.com
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Terminal na thakle:** cPanel → **Cron Jobs** e ekta "Once Per Minute" cron banie
> `cd ~/newshop.com && /usr/local/bin/php artisan migrate --force` chaliye,
> kaj hoye gele cron ta delete kore din.
>
> **storage:link** er jonno easy way: browser e ekbar
> `https://newshop.com/storage-link.php` open korun → tarpor file ta **delete** kore din.

---

## Step 6 — Login kore check korun

| | |
|---|---|
| Site | `https://newshop.com` |
| Admin panel | `https://newshop.com/admin` |
| Admin email | `admin@odikshop.com` |
| Admin password | `admin1234` |

**Login korar sathe sathe password change korun.**

Sesh e cPanel → **SSL/TLS Status** → domain select → **Run AutoSSL** (free HTTPS).

---

## Step 7 — Plan B (doc root e `/public` dite na parle)

Jodi addon domain er doc root `/home/sameerth/newshop.com` (public chara) hoy:

1. `public/` folder er **shob file** (`.htaccess` shoho) `newshop.com/` er root e move korun
2. `newshop.com/index.php` edit kore ei duita line thik korun:

```php
require __DIR__.'/laravel/vendor/autoload.php';
$app = require_once __DIR__.'/laravel/bootstrap/app.php';
```

3. baki shob folder (`app`, `bootstrap`, `config`, ... `vendor`) ekta `laravel/` folder e rakhun

> Ekhane `/public` shoho doc root deyai onek beshi valo — parle Step 1 er poth e jan.

---

## Troubleshooting

| Problem | Fix |
|---|---|
| 500 error | `.env` e `APP_DEBUG=true` kore refresh, ba `storage/logs/laravel.log` porun |
| Blank page / no CSS | `.env` e `APP_URL` https shoho thik ache kina dekhun, tarpor `php artisan config:cache` |
| Product image ashe na | `php artisan storage:link` cholenai — Step 5 er note dekhun |
| `Permission denied` | `storage/` ar `bootstrap/cache/` folder er permission **755** korun |
| `.env` change kore kaj hocche na | `php artisan config:clear && php artisan config:cache` |

---

## Notun code push korar somoy (future update)

Local e `npm run build` chaliye notun zip banan, tarpor server e:

```bash
cd ~/newshop.com
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
