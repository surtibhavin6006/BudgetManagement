# Budget Management

A microservices-based budget management application with AI-powered transaction categorisation, RAG-driven category suggestions, and real-time PDF statement processing.

For service-specific architecture and API docs, see each service's own README:

- [Nginx Gateway →](services/nginx/README.md)
- [Laravel API →](services/laravel-api/README.md)
- [Upload Service →](services/upload-service/README.md)

---

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Service URLs](#service-urls)
- [Common Docker Commands](#common-docker-commands)
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
| Laravel API Docs (Swagger UI) | `http://api.budget.test/docs/api` | None (public) |
| Laravel API | `http://api.budget.test/api/*` | JWT `Authorization: Bearer <token>` |
| Upload API Docs (Swagger) | `http://api.budget.test/upload/docs` | None (public) |
| Upload API | `http://api.budget.test/upload/*` | JWT |

For domain configuration, Basic Auth setup, and the JWT gateway flow see the [Nginx README →](services/nginx/README.md)

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
    │   ├── README.md                    # Domain config, JWT flow, Basic Auth
    │   ├── Dockerfile
    │   ├── docker-entrypoint.sh
    │   ├── .htpasswd
    │   ├── templates/
    │   │   └── default.conf.template
    │   └── html/
    │       └── index.html.template
    │
    ├── laravel-api/            # PHP 8.3 / Laravel 13 — REST API + Auth + CQRS
    │   └── README.md           # Architecture, API reference, setup details
    │
    └── upload-service/         # Python 3.12 / FastAPI — PDF upload + event publishing
        └── README.md           # Architecture, endpoints, setup details
```
