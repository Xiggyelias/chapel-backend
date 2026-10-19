# Kwang-Lim Chapel - Backend Service
**Africa University Chaplaincy Office &bull; Mutare, Zimbabwe**

Standalone backend API and management service for the Kwang-Lim Chapel Management Portal. Dokploy ready with multi-stage Docker build, PHP 8.3 FPM, Nginx, SQLite / MySQL support, and automated entrypoint migrations.

---

## 🚀 Features

- **Multi-Role Access Control**: Built-in authorization boundaries for Chaplain, Assistant, Secretary, Club Executives, and Campus Life.
- **Complete Chapel Hymnody (2,029 Hymns)**:
  - UMH: #1 through #734 (100% complete)
  - MHB: #1 through #984 (100% complete)
  - Shona: #1 through #311 (100% complete)
- **14-Day Advance Notice Rule (§4.1)**: Automated enforcement and Chaplaincy policy waiver routing.
- **Smart Clash Radar**: Automated venue clash detection and time overlap resolver.
- **Records Vault & Sentinel Compliance**: Immutable audit trails and document storage.
- **Dokploy Ready**: Native `dokploy.json`, `Dockerfile`, `docker-compose.yml`, and `/up` healthcheck.

---

## 📁 Directory Structure

```text
backend/
├── app/
│   ├── Console/Commands/    # Scheduled backup & notification tasks
│   ├── Http/Controllers/    # HTTP & REST API controllers
│   ├── Models/              # Eloquent models (User, Request, Event, ServiceSheet, Hymn)
│   ├── Providers/           # Service providers & authorization policies
│   └── Services/            # Decoupled business logic (Clash detection, Hymns, Workflow)
├── bootstrap/               # Application bootstrap & provider registration
├── config/                  # App, database, auth, session configuration
├── database/
│   ├── database.sqlite      # Pre-seeded SQLite database with 2,029 hymns & users
│   ├── migrations/          # Complete database migrations
│   └── seeders/             # DatabaseSeeder, HymnSeeder, and JSON datasets
├── docker/                  # Nginx, PHP-FPM, Supervisor, and entrypoint scripts
├── public/                  # Public web root and entrypoint
├── resources/               # Blade views, layouts, and components
├── routes/                  # API, Web, and Console route definitions
├── storage/                 # Framework cache, sessions, views, logs
├── tests/                   # Automated feature & unit test suite (33 tests, 237 assertions)
├── Dockerfile               # Production multi-stage Dockerfile
├── docker-compose.yml       # Dokploy compose deployment specification
├── dokploy.json             # Dokploy application configuration
├── composer.json            # PHP dependencies & PSR-4 mapping
└── composer.lock            # Exact locked PHP dependencies
```

---

## 🐳 Dokploy Deployment Guide

### Option 1: Deploy via Dokploy GUI (GitHub Repository)
1. In Dokploy, click **Create Application**.
2. Select **GitHub** and connect your backend repository (`kwanglim-chapel-backend`).
3. Set **Build Type**: `Dockerfile`.
4. Set **Port**: `80`.
5. Set **Health Check Path**: `/up`.
6. Add Persistent Volumes in **Mounts**:
   - Volume `chapel_backend_storage` -> `/var/www/html/storage`
   - Volume `chapel_backend_database` -> `/var/www/html/database`
7. Add Environment Variables:
   ```env
   APP_NAME="Kwang-Lim Chapel"
   APP_ENV=production
   APP_KEY=base64:X8p7F3gL1wK9mQ2vR5tY8uI1oP4sD7fG0hJ3kL6zX9c=
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   DB_CONNECTION=sqlite
   DB_DATABASE=/var/www/html/database/database.sqlite
   ```
8. Click **Deploy**.

### Option 2: Deploy via Docker Compose
1. In Dokploy, click **Create Compose**.
2. Paste the contents of `docker-compose.yml`.
3. Set your environment variables and click **Deploy**.

---

## 🛠️ Local Development & Testing

```bash
# Run automated test suite (33 tests, 237 assertions)
php artisan test

# Run database migrations and hymn seeders
php artisan migrate --force
php artisan db:seed --class=HymnSeeder

# Start local server
php artisan serve --port=8000
```

---

## 📦 GitHub Repository Setup

To push this backend service to GitHub:

```bash
cd backend
git init -b main
git add .
git commit -m "feat: complete Dokploy-ready backend service for Kwang-Lim Chapel"
git remote add origin https://github.com/<YOUR-GITHUB-USERNAME>/kwanglim-chapel-backend.git
git push -u origin main
```
