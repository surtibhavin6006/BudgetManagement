# Budget Management — Nginx Gateway

Nginx acts as the single entry point for all services. It handles domain routing, HTTP Basic Auth for dev tools, and JWT validation for protected API routes via `auth_request`.

---

## Table of Contents

- [Domain Configuration](#domain-configuration)
- [Server Blocks](#server-blocks)
- [JWT Auth Flow](#jwt-auth-flow)
- [Basic Auth](#basic-auth)
- [Changing the Basic Auth Password](#changing-the-basic-auth-password)
- [Dev Panel](#dev-panel)
- [Directory Structure](#directory-structure)

---

## Domain Configuration

All domain names come from a single source — the **`.env`** file in the project root:

```env
FRONTEND_DOMAIN=budget.test
API_DOMAIN=api.budget.test
MINIO_DOMAIN=minio.budget.test
REDIS_DOMAIN=redis.budget.test
UPTIME_DOMAIN=uptime.budget.test
```

At container startup, `docker-entrypoint.sh` runs `envsubst` on two templates and writes the rendered files into place:

```
templates/default.conf.template  →  /etc/nginx/conf.d/default.conf
html/index.html.template          →  /usr/share/nginx/html/index.html
```

Only the five variables listed above are substituted — all other `$` signs in the nginx config are left untouched.

To change a domain:

1. Edit `.env` in the project root
2. Rebuild nginx: `docker compose up -d --build nginx`
3. Update your OS hosts file with the new names

> **Do not use `.dev` as a local TLD** — all `.dev` domains are on the HSTS preload list and browsers force them to HTTPS permanently. Use `.test`.

---

## Server Blocks

| Server name | Purpose |
|---|---|
| `${FRONTEND_DOMAIN}` | Dev panel (`/monitor`) + Adminer (`/adminer/`) + frontend placeholder |
| `${API_DOMAIN}` | Laravel API + Upload Service — JWT-gated, see [JWT Auth Flow](#jwt-auth-flow) |
| `${MINIO_DOMAIN}` | MinIO Console — Basic Auth only |
| `${REDIS_DOMAIN}` | RedisInsight — Basic Auth only |
| `${UPTIME_DOMAIN}` | Uptime Kuma — Basic Auth only |

### `${API_DOMAIN}` routing detail

| Path | Auth | Upstream |
|---|---|---|
| `/auth/*` | None | `laravel-api:8000/api/auth/*` |
| `/docs` / `/docs/*` | None | `laravel-api:8000` |
| `/api/auth/*` | None | `laravel-api:8000` (Swagger UI "Try it" path) |
| `/api/*` | JWT (`auth_request`) | `laravel-api:8000` |
| `/upload/docs`, `/upload/redoc` | None | `upload-service:8002` |
| `/upload/*` | JWT (`auth_request`) | `upload-service:8002` (prefix stripped) |

MinIO, RedisInsight, and Uptime Kuma use dedicated subdomains (root-path proxy) because their SPAs rely on absolute asset paths and break when proxied at a subpath.

---

## JWT Auth Flow

Protected routes use nginx's `auth_request` module:

```
Client request  →  nginx
                      │
                      ├─ POST /_auth (internal)
                      │       → laravel-api /api/auth/validate
                      │       ← 200 (valid) + X-User-Id, X-User-Email headers
                      │       ← 401 (invalid / missing token)
                      │
                      ├─ 200 → proxy request to upstream, inject X-User-Id / X-User-Email
                      └─ 401 → return JSON {"message":"Unauthenticated."}
```

The `/_auth` location is `internal` — it cannot be called directly by clients.

On a 401, nginx returns JSON rather than its default HTML error page:

```nginx
error_page 401 = @error_401;
location @error_401 {
    default_type application/json;
    return 401 '{"message":"Unauthenticated."}';
}
```

---

## Basic Auth

Dev tools (dev panel, Adminer, MinIO, RedisInsight, Uptime Kuma) are protected by HTTP Basic Auth using `/etc/nginx/.htpasswd`.

Default credentials:

| Field | Value |
|---|---|
| Username | `dev` |
| Password | set when `.htpasswd` was generated |

---

## Changing the Basic Auth Password

### Step 1 — Generate a new hashed password

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

Replace the entire contents of `services/nginx/.htpasswd` with the output. One line per user:

```
dev:$apr1$Rf4nX5kq$kIHKAB8EWDz8GUJT3Fewl0
```

To add a second user, run `htpasswd` again with a different username and append that line.

### Step 3 — Reload nginx

```bash
docker compose restart nginx
```

---

## Dev Panel

The developer dashboard is at `http://budget.test/monitor` (Basic Auth required).

It shows quick links to all service UIs: API docs, Adminer, RedisInsight, MinIO Console, and Uptime Kuma.

---

## Directory Structure

```
services/nginx/
│
├── Dockerfile                   # nginx:alpine + envsubst entrypoint
├── docker-entrypoint.sh         # runs envsubst on templates, then starts nginx
├── .htpasswd                    # Basic Auth credentials (one line per user)
│
├── templates/
│   └── default.conf.template    # nginx config with ${DOMAIN} placeholders
│
└── html/
    └── index.html.template      # dev panel HTML with ${DOMAIN} placeholders
```
