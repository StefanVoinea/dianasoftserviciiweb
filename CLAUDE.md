# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

DianaSoft EasyIFN is a full-stack financial/credit management SPA for institutions. It manages loan applications (solicitari), contracts (contracte), loan approvals (aprobarifinanciare), payment collections (incasari), and related document workflows. Built with **Laravel 8 backend** + **Vue.js 2 frontend**.

## Commands

### Frontend

```bash
npm run dev          # Development build (Webpack via Laravel Mix)
npm run watch        # Watch mode for development
npm run hot          # Hot module replacement
npm run prod         # Production build
```

### Backend

```bash
php artisan serve           # Start development server
php artisan migrate         # Run database migrations
php artisan queue:work      # Process background jobs
php artisan passport:install # Set up OAuth2 keys (first time)
vendor/bin/phpunit          # Run PHP tests
```

### Linting

```bash
npx eslint resources/js/src --ext .js,.vue   # Lint frontend
```

## Architecture

### Backend (`app/`)

- **`Http/Controllers/Api/`** — 200+ controllers, one per entity. Each maps to a route file in `routes/api_routes/`.
- **`Models/`** — Eloquent models, one per database table.
- **`Exports/` / `Imports/`** — Excel export/import via Maatwebsite/Excel.
- **`Events/`** — Laravel broadcasting events (Redis driver). Key events: `ChatEvents`, `DianaSoftMenuOptionUpdated`.
- **`Helpers/helpers.php`** — Global helper functions auto-loaded.
- **`Policies/`** — Authorization policies. Middleware `CheckRoutePermission` enforces per-route access; `IpMiddleware` enforces IP allowlists.

Routes entry point: `routes/api.php` includes all per-entity files from `routes/api_routes/`. Add new entity routes there.

### Frontend (`resources/js/src/`)

- **`router/routes/dianasoft.js`** — All application routes. Route guards call `canNavigate()` (CASL-based); unauthenticated requests redirect to login. Components are lazy-loaded.
- **`store/`** — Vuex modules: `app` (window/theme state), `app-config` (layout config), `vertical-menu` (nav state).
- **`views/app_pages/dianasoft/`** — Reusable DianaSoft-specific components: `Selectone` (autocomplete select), `DataCalendaristica` (date picker), `Dstable` (paginated table), `OcrCI` (OCR document reader).
- **`libs/axios.js`** — Axios instance exposed as `this.$http` on the Vue prototype. Base URL from `.env`.
- **`libs/acl/`** — CASL ability instance, initial config, and route protection helper.
- **`plugins/userpermitt.js`** — Global permission check methods mixed into Vue.
- **`auth/jwt/useJwt.js`** — Passport token management; token stored in `localStorage`.

### Path Aliases (webpack.mix.js / vue.config.js)

| Alias | Resolves to |
|-------|-------------|
| `@` | `resources/js/src/` |
| `@core` | `resources/js/src/@core/` |
| `@axios` | `resources/js/src/libs/axios.js` |
| `@validations` | `resources/js/src/@core/utils/validations/` |

## Key Integrations

- **Auth:** Laravel Passport (OAuth2). Token injected into every Axios request automatically.
- **Permissions (frontend):** CASL `@casl/ability` — abilities come from the user object in `localStorage`. Route guard calls `canNavigate(to)`.
- **Permissions (backend):** `hasPermission($permission, $company_id)` helper; company-scoped multi-tenancy.
- **Excel:** `Maatwebsite/Excel` — export classes in `app/Exports/`, imports in `app/Imports/`.
- **PDF:** `barryvdh/laravel-snappy`.
- **OCR:** `jfuentestgn/ocr-space` + Google Cloud Vision (`GOOGLE_CLOUD_VISION_KEY` in `.env`).
- **File storage:** Local + Dropbox (`DROPBOX_ACCESS_TOKEN` in `.env`).
- **SMS:** Orange API (`ORANGE_URL`, `ORANGE_USER`, `ORANGE_PASS` in `.env`).
- **Email:** Zoho SMTP (`smtppro.zoho.eu`).
- **Queue:** Database driver (`QUEUE_CONNECTION=database`). Run `php artisan queue:work` for background jobs.
- **Broadcasting:** Redis (`BROADCAST_DRIVER=redis`). Laravel Echo + Socket.io configured but not enabled by default.

## Environment

Copy `.env.example` to `.env` and fill in:
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — MySQL connection
- `GOOGLE_CLOUD_VISION_KEY` — OCR features
- `DROPBOX_ACCESS_TOKEN` — cloud file storage
- `ORANGE_*` — SMS sending
- `MAIL_*` — Zoho SMTP credentials
- Passport keys via `php artisan passport:install`

## Conventions

- **API controllers** follow resource convention: one controller class per entity, one route file per entity under `routes/api_routes/`.
- **Frontend pages** live under `resources/js/src/views/app_pages/dianasoft/` grouped by domain (e.g., `solicitari/`, `contracte/`).
- **Form validation** uses Vee-Validate with Romanian locale.
- **UI components** are Bootstrap-Vue. DianaSoft custom wrappers (`Dstable`, `Selectone`, etc.) should be preferred over raw Bootstrap-Vue components for consistency.
- **No semicolons** in JS/Vue files (ESLint Airbnb config, semicolons off). Max line length rule is disabled.
