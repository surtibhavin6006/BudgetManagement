# Budget Management

A microservices-based budget management application with AI-powered transaction categorisation, RAG-driven category suggestions, and real-time PDF statement processing.

For service-specific architecture and API docs, see each service's own README:

- [Laravel API →](services/laravel-api/README.md)
- [Upload Service →](services/upload-service/README.md)

---

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Domain Configuration](#domain-configuration)
- [Dev Panel & Basic Auth](#dev-panel--basic-auth)
- [Changing the Basic Auth Password](#changing-the-basic-auth-password)
- [Service URLs](#service-urls)
- [Common Docker Commands](#common-docker-commands)
- [Environment Variables](#environment-variables)
- [Directory Structure](#directory-structure)

---

## Prerequisites

| Tool | Notes |
|---|---|
| Docker Desktop | https://www.docker.com/products/docker-desktop |
| Git | https://git-scm.com |

No PHP, Python, or Node.js required — everything runs inside Docker.

---

## Quick Start

### 1. Clone the repo

```bash
git clone <repo-url>
cd BudgetManagement
```

### 2. Add hosts entries (one-time setup)

The project uses local domains instead of `localhost:PORT`. You need to tell your OS to resolve them to `127.0.0.1`.

**Windows** — open Notepad **as Administrator** and add these lines to `C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1 budget.test
127.0.0.1 api.budget.test
127.0.0.1 minio.budget.test
127.0.0.1 redis.budget.test
127.0.0.1 uptime.budget.test
```

**macOS / Linux**:

```bash
sudo tee -a /etc/hosts <<EOF
127.0.0.1 budget.test
127.0.0.1 api.budget.test
127.0.0.1 minio.budget.test
127.0.0.1 redis.budget.test
127.0.0.1 uptime.budget.test
EOF
```

Verify:

```powershell
# Windows
Get-Content "C:\Windows\System32\drivers\etc\hosts" | Select-String "\.test"
```

```bash
# macOS / Linux
grep '\.test' /etc/hosts
```

### 3. Start all services

```bash
docker compose up -d --build
```

First run takes a few minutes. When done, open **`http://budget.test/monitor`** in your browser.

### 4. Run Laravel migrations (first time only)

```bash
docker exec laravel-api php artisan migrate
```

---

## Domain Configuration

All domain names are controlled from one file — **`.env`** in the project root:

```env
FRONTEND_DOMAIN=budget.test
API_DOMAIN=api.budget.test
MINIO_DOMAIN=minio.budget.test
REDIS_DOMAIN=redis.budget.test
UPTIME_DOMAIN=uptime.budget.test
```

When nginx starts, these values are automatically injected into the nginx server config and the dev panel HTML. Changing a domain requires only three steps:

**Step 1** — Edit `.env` with the new domain names.

**Step 2** — Rebuild nginx:

```bash
docker compose up -d --build nginx
```

**Step 3** — Update your hosts file to add the new entries (and remove the old ones).

> **Do not use `.dev` as a local TLD.** All `.dev` domains are on the HSTS preload list built into every major browser — they will be permanently forced to HTTPS with no way to override it. Use `.test` instead.

---

## Dev Panel & Basic Auth

The developer dashboard is at:

```
http://budget.test/monitor
```

It requires HTTP Basic Auth. Default credentials:

| Field | Value |
|---|---|
| Username | `dev` |
| Password | set when `.htpasswd` was generated |

The panel shows quick links to all service UIs (API docs, Adminer, RedisInsight, MinIO Console, Uptime Kuma).

---

## Changing the Basic Auth Password

### Step 1 — Generate a new hashed password

No extra tools needed — use Docker:

```bash
docker run --rm httpd:alpine htpasswd -nb <username> <newpassword>
```

Example:

```bash
docker run --rm httpd:alpine htpasswd -nb dev mysecretpassword
```

Output:

```
dev:$apr1$Rf4nX5kq$kIHKAB8EWDz8GUJT3Fewl0
```

### Step 2 — Update the file

Replace the entire contents of `services/nginx/.htpasswd` with the output from Step 1. One line per user:

```
dev:$apr1$Rf4nX5kq$kIHKAB8EWDz8GUJT3Fewl0
```

To add a second user, run `htpasswd` again with a different username and append that line.

### Step 3 — Reload nginx

```bash
docker compose restart nginx
```

---

## Service URLs

### Routed through Nginx (port 80)

| Service | URL | Auth |
|---|---|---|
| Dev Panel | `http://budget.test/monitor` | Basic Auth |
| Adminer (DB UI) | `http://budget.test/adminer/` | Basic Auth |
| MinIO Console | `http://minio.budget.test` | Basic Auth |
| RedisInsight | `http://redis.budget.test` | Basic Auth |
| Uptime Kuma | `http://uptime.budget.test` | Basic Auth |
| Laravel Auth endpoints | `http://api.budget.test/auth/*` | None (public) |
| Laravel API Docs (Scramble) | `http://api.budget.test/docs/api` | None (public) |
| Laravel API | `http://api.budget.test/api/*` | JWT `Authorization: Bearer <token>` |
| Upload API Docs (Swagger) | `http://api.budget.test/upload/docs` | None (public) |
| Upload API | `http://api.budget.test/upload/*` | JWT |

### Direct port access (bypasses Nginx — no auth, useful as fallback)

| Service | URL | Credentials |
|---|---|---|
| Laravel API (raw) | `http://localhost:8000` | — |
| Upload Service (raw) | `http://localhost:8002` | — |
| Adminer | `http://localhost:8080` | server: `mysql` · user: `app` · pass: `secret` |
| RedisInsight | `http://localhost:5540` | connect to `redis:6379` on first launch |
| MinIO Console | `http://localhost:9001` | user: `minioadmin` · pass: `minioadmin` |
| Uptime Kuma | `http://localhost:3001` | create account on first launch |

---

## Common Docker Commands

```bash
# Start all services
docker compose up -d

# Start with rebuild (after any code or config change)
docker compose up -d --build

# Rebuild and restart only nginx (after .env domain change)
docker compose up -d --build nginx

# Stop all services (data volumes preserved)
docker compose down

# Stop all services and delete all data (full reset)
docker compose down -v

# View logs
docker compose logs -f
docker compose logs -f nginx laravel-api

# Check container status
docker compose ps

# Enter a container shell
docker exec -it laravel-api bash
docker exec -it upload-service sh
docker exec -it mysql mysql -u app -psecret budget_management
docker exec -it redis redis-cli

# Laravel Artisan
docker exec laravel-api php artisan migrate
docker exec laravel-api php artisan migrate:fresh --seed
docker exec laravel-api php artisan route:list
docker exec laravel-api php artisan tinker
```

---

## Environment Variables

### `.env` (root) — domain names

Read by Docker Compose and injected into nginx at container startup.

```env
FRONTEND_DOMAIN=budget.test
API_DOMAIN=api.budget.test
MINIO_DOMAIN=minio.budget.test
REDIS_DOMAIN=redis.budget.test
UPTIME_DOMAIN=uptime.budget.test
```

### Service-level `.env` files

Each service has its own `.env` inside its directory:

- `services/laravel-api/.env` — see [Laravel API README](services/laravel-api/README.md)
- `services/upload-service/.env` — see [Upload Service README](services/upload-service/README.md)

---

## Directory Structure

```
BudgetManagement/
│
├── .env                        # Domain names — single source of truth
├── .gitignore
├── docker-compose.yml
├── README.md                   # ← you are here
│
└── services/
    │
    ├── nginx/
    │   ├── Dockerfile                   # Custom image with envsubst entrypoint
    │   ├── docker-entrypoint.sh         # Runs envsubst on conf + HTML, starts nginx
    │   ├── .htpasswd                    # Basic auth credentials for /monitor
    │   ├── templates/
    │   │   └── default.conf.template    # Nginx config with ${DOMAIN} placeholders
    │   └── html/
    │       └── index.html.template      # Dev panel HTML with ${DOMAIN} placeholders
    │
    ├── laravel-api/            # PHP 8.3 / Laravel 13 — REST API + Auth + CQRS
    │   └── README.md           # Architecture, API reference, setup details
    │
    └── upload-service/         # Python 3.12 / FastAPI — PDF upload + event publishing
        └── README.md           # Architecture, endpoints, setup details
```
