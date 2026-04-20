# Windows VPS deployment

This project is a Laravel 12 application that should be deployed on Windows VPS with IIS, PHP 8.2, Composer, Node.js, and MySQL or MariaDB.

## 1. Recommended stack

- Windows Server with IIS
- PHP 8.2 x64 NTS configured through FastCGI
- Composer 2
- Node.js 20 or 22 LTS
- MySQL 8 or MariaDB 10.6+
- IIS URL Rewrite module

## 2. Server prerequisites

Open PowerShell as Administrator and install the IIS features required for PHP:

```powershell
Install-WindowsFeature Web-Server,Web-Common-Http,Web-Default-Doc,Web-Static-Content,Web-Http-Errors,Web-Http-Logging,Web-Filtering,Web-Performance,Web-Stat-Compression,Web-CGI,Web-Mgmt-Console
```

Install the following software on the VPS:

- PHP 8.2 NTS x64
- Composer
- Node.js LTS
- MySQL or MariaDB
- IIS URL Rewrite

For PHP, enable at least these extensions in `php.ini`:

- `extension=curl`
- `extension=fileinfo`
- `extension=gd`
- `extension=intl`
- `extension=mbstring`
- `extension=mysqli`
- `extension=openssl`
- `extension=pdo_mysql`
- `extension=zip`
- `zend_extension=opcache`

Then restart IIS:

```powershell
iisreset
```

## 3. Clone the project

Example target path:

```powershell
cd C:\
git clone <your-repository-url> inetpub\be_graduation
cd C:\inetpub\be_graduation
```

## 4. Configure the environment

Create `.env` from `.env.example` if it does not exist:

```powershell
Copy-Item .env.example .env
```

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.example.com
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_API_PREFIX=api
FRONTEND_URL=https://app.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=be_graduation
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

If you want a simpler single-server setup, you can switch these values instead:

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Use the simpler setup only if you do not need database-backed sessions, cache, or queued jobs.

## 5. Bootstrap the application

Run the deployment helper from the project root:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\windows\deploy-production.ps1 -PhpBin "C:\PHP\php.exe" -ComposerBin "composer" -NpmBin "npm" -SeedBaselineData
```

What the script does:

- creates `.env` if missing
- runs `composer install --no-dev`
- generates `APP_KEY` only when it is empty
- generates `JWT_SECRET` only when it is empty
- runs `php artisan migrate --force`
- optionally seeds baseline lookup tables
- creates `public/storage`
- builds Vite assets
- caches config and views

`route:cache` is intentionally not used because this project contains closure routes.

## 6. Seeder note for production

`DatabaseSeeder` includes demo seeders:

- `DemoUserSeeder`
- `DemoRoomSeeder`
- `DemoCmsSeeder`

Do not run `php artisan db:seed` on production unless you explicitly want demo data.

For production, use only the baseline seeders:

```powershell
php artisan db:seed --class=Database\Seeders\LocationSeeder --force
php artisan db:seed --class=Database\Seeders\CategorySeeder --force
php artisan db:seed --class=Database\Seeders\PostTypeSeeder --force
php artisan db:seed --class=Database\Seeders\PermissionSeeder --force
php artisan db:seed --class=Database\Seeders\RolePermissionSeeder --force
```

## 7. Configure IIS

Point the IIS site to the `public` directory, not the repository root.

Example:

- Project root: `C:\inetpub\be_graduation`
- IIS physical path: `C:\inetpub\be_graduation\public`

This repository now includes `public\web.config` for Laravel URL rewriting.

Create a dedicated application pool:

- .NET CLR version: `No Managed Code`
- Pipeline mode: `Integrated`

Grant write permissions to the IIS application pool identity for:

- `storage`
- `bootstrap\cache`
- `public\uploads` if the application writes image files there

Example permission commands:

```powershell
icacls C:\inetpub\be_graduation\storage /grant "IIS AppPool\be_graduation_pool:(OI)(CI)M" /T
icacls C:\inetpub\be_graduation\bootstrap\cache /grant "IIS AppPool\be_graduation_pool:(OI)(CI)M" /T
icacls C:\inetpub\be_graduation\public\uploads /grant "IIS AppPool\be_graduation_pool:(OI)(CI)M" /T
```

If `public\uploads` does not exist yet, create it first:

```powershell
New-Item -ItemType Directory -Force -Path C:\inetpub\be_graduation\public\uploads
```

## 8. FastCGI and PHP

Configure IIS FastCGI to use `php-cgi.exe`, then add a handler mapping for `*.php`.

Typical values:

- Executable: `C:\PHP\php-cgi.exe`
- Request path: `*.php`
- Module: `FastCgiModule`

Make sure `index.php` is included in the default document list.

## 9. Scheduler and queue worker

This project has a scheduled command in `routes/console.php`:

- `php artisan schedule:run`

Create a Windows Scheduled Task that runs every minute.

If you keep `QUEUE_CONNECTION=database`, also run a long-lived worker. A practical option on Windows is NSSM:

```powershell
php artisan queue:work --tries=3 --timeout=120
```

If you do not want to manage a worker yet, set:

```env
QUEUE_CONNECTION=sync
```

At the moment, this codebase does not show custom queued jobs, so `sync` is acceptable for a first production deployment.

## 10. Final verification

Run these commands after deployment:

```powershell
php artisan about
php artisan migrate:status
php artisan storage:link
```

Then verify:

- the site opens through IIS
- `https://your-domain/up` returns the Laravel health page
- API routes work under `/api/...`
- image uploads can write to `storage` or `public\uploads`
- login works and JWT tokens are issued correctly
- email configuration is valid if contact mail is used
