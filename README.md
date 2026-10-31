# Kwang-Lim Chapel Ministry Portal
**Africa University Chaplaincy Office &bull; Mutare, Zimbabwe**

The complete Kwang-Lim Chapel portal: activity proposals and approvals, the master calendar, worship planning and projection, the hymnal, club records vaults, attendance, equipment, reports, leadership handovers and an installable PWA. **This repository is the whole application and deploys as one service.**

---

## Architecture: one service

```text
                 ┌──────────────────────── one container ────────────────────────┐
Browser / PWA ──▶│ nginx :80 ──▶ PHP-FPM (Laravel 12)                            │
                 │   • Blade pages, JSON endpoints, Vite assets, PWA (sw.js)     │
                 │ scheduler   (php artisan schedule:work: reminders, backups)   │
                 │ queue worker (php artisan queue:work: email delivery)         │
                 └──────────────┬───────────────────────────────┬────────────────┘
                                │                               │
                   PostgreSQL / MySQL / SQLite        storage volume (uploads, backups,
                                                      logs, sessions, SQLite file)
```

Pages are rendered on the server, so there is no separate frontend to deploy. The former `chapel-frontend` repository was only an nginx proxy in front of this service and has been retired.

---

## Deploy on Dokploy

1. **Create Application** → source: GitHub `Xiggyelias/chapel-backend`, branch `main`.
2. **Build type:** Dockerfile (`./Dockerfile`, context `.`). **Port:** `80`. **Health check:** `/up`.
3. **Volume:** mount a named volume at **`/var/www/html/storage`** only.
   Do **not** mount anything at `/var/www/html/database`: that would hide new migrations shipped in each release.
4. **Environment** (see `.env.production.example`):

   | Variable | Value |
   |---|---|
   | `APP_KEY` | output of `php artisan key:generate --show` (keep secret, keep stable) |
   | `APP_ENV` / `APP_DEBUG` | `production` / `false` |
   | `APP_URL` | your public https URL |
   | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | PostgreSQL service details |
   | `SESSION_SECURE_COOKIE` | `true` (HTTPS) |
   | `QUEUE_CONNECTION` | `database` |
   | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | SMTP (e.g. Gmail + App Password) |
   | `SEED_ON_DEPLOY` | `false` (an empty database is seeded automatically on first boot) |

5. **Domain:** attach your domain to this application with HTTPS.
6. Deploy. On every start the container waits for the database, runs migrations, moves any legacy public uploads into private storage, caches config/routes/views, then starts nginx, PHP-FPM, the scheduler and the queue worker.

After the first deploy, verify email with:
```bash
php artisan chapel:mail-test you@example.com
```

### Migrating from the old two-application setup
If Dokploy still runs a `chapel-frontend` application in front of this one:
1. Move the public domain from the frontend application to this application.
2. Confirm the site works, then stop and delete the frontend application in Dokploy.
3. Archive or delete the `chapel-frontend` repository on GitHub.

---

## Local development

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm run build            # or: npm run dev
php artisan serve        # http://127.0.0.1:8000
php artisan queue:work   # optional: deliver queued emails locally
```
Demo accounts use `Chapel@2026!`. The one-click role switcher (`/demo-switch/{id}`) works only with `APP_ENV=local` and `DEMO_LOGIN_ENABLED=true`.

Run the tests (62 tests, including a walkthrough that opens every page as every role):
```bash
php artisan test
```

Or run the production image locally:
```bash
APP_KEY=base64:... docker compose up -d --build   # http://localhost:8000
```

---

## Operations

| Command | Purpose |
|---|---|
| `php artisan chapel:backup` | Database (SQLite / `pg_dump` / `mysqldump`) + vault files → `storage/app/backups`, keeps `CHAPEL_BACKUP_KEEP` (default 14). Scheduled daily 02:00. |
| `php artisan chapel:restore <zip> --force` | Restore a backup into the configured database and storage. |
| `php artisan chapel:reminders` | Worship deadlines, 14-day decisions, report prompts; emails the Office a digest. Scheduled daily 06:00. |
| `php artisan chapel:secure-uploads` | Move uploads left on the public disk into private storage (runs at every start). |
| `php artisan chapel:mail-test <address>` | Send a test email immediately. |

Copy backups off the server regularly (`CHAPEL_BACKUP_SECONDARY_DIR` or your provider's volume snapshots).

---

## Feature overview

- **Role dashboards:** each role opens on an action inbox built from live data (Chaplain, Assistant Chaplain, Chapel Secretary, Club President/Executive, Campus Life & SRC).
- **Notifications:** in-portal inbox plus email for proposal decisions, new and resubmitted proposals, equipment updates, report reviews, charter applications, handovers, sign-in details and the daily digest.
- **Proposals:** 14-day rule, VC go-ahead, venue clash detection with override, revise & resubmit, signed clearance passes with scannable QR codes and public gatekeeper verification.
- **Records vault:** private, club-isolated document storage with health scoring.
- **Hymnal:** UMH, MHB and Shona catalogues. Hymns without verified texts show "Lyrics not yet entered"; the Office enters texts from licensed printed hymnals (source required).
- **Security:** one-time passwords with forced change, login throttling, suspension takes effect immediately, private file downloads, security headers, no caching of signed-in pages.
