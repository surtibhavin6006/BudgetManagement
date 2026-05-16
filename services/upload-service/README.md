# Upload Service

Handles bank statement PDF uploads. Stores the file in object storage, records the statement in the database, and publishes an event to Redis so the AI service can begin processing.

---

## Tech Stack

| | |
|---|---|
| Framework | FastAPI, Python 3.12 |
| Object Storage | MinIO (S3-compatible) |
| Database | MySQL 8.0 via SQLAlchemy (async) |
| Event Bus | Redis 7 Pub/Sub |
| Container | Docker |

---

## Responsibility

This service does one thing:

```
POST /upload/statement (PDF)
    │
    ├── 1. Validate file (PDF only, non-empty)
    ├── 2. Store PDF in MinIO  →  users/{user_id}/{uuid}.pdf
    ├── 3. INSERT statement row in MySQL  (status = uploaded)
    ├── 4. PUBLISH to Redis  →  statements:events
    └── 5. Return { statement_id }
```

JWT auth is handled upstream by Nginx (`auth_request`). By the time a request reaches this service, `X-User-Id` is already injected by Nginx — no token parsing needed here.

---

## Architecture

### Storage Abstraction

The router depends on `StorageInterface`, not MinIO directly. Switching to S3 or any other provider requires only two steps:

```python
# 1. Write a new implementation
class S3Storage(StorageInterface):
    def ensure_bucket(self) -> None: ...
    def upload(self, path, data, content_type) -> str: ...

# 2. Change one line in dependencies.py
def get_storage() -> StorageInterface:
    return S3Storage()
```

Nothing else changes.

### Directory Structure

```
app/
├── main.py                  # FastAPI app + lifespan (creates MinIO bucket on startup)
├── config.py                # Pydantic Settings — reads from env vars / .env file
├── database.py              # Async SQLAlchemy engine + get_db() dependency
├── dependencies.py          # get_storage() — single place that knows about MinIO
├── routers/
│   └── upload.py            # POST /upload/statement
└── services/
    ├── storage.py           # StorageInterface — abstract base class
    ├── minio_client.py      # MinioStorage — concrete implementation
    └── redis_publisher.py   # publish_statement_uploaded()
```

---

## API

### POST `/upload/statement`

Upload a PDF bank statement.

**Headers**

| Header | Description |
|---|---|
| `Authorization` | `Bearer <jwt>` — validated by Nginx, not this service |
| `Content-Type` | `multipart/form-data` |

**Body** (multipart/form-data)

| Field | Type | Description |
|---|---|---|
| `file` | File | PDF bank statement (max 20 MB, enforced by Nginx) |

**Response `201`**
```json
{ "statement_id": 42 }
```

**Errors**

| Code | Reason |
|---|---|
| 400 | File is not a PDF or is empty |
| 401 | Invalid / missing JWT (rejected by Nginx before reaching service) |

---

## Redis Event

On successful upload, publishes to the `statements:events` channel:

```json
{
  "event":        "statement.uploaded",
  "statement_id": 42,
  "user_id":      7
}
```

The AI service subscribes to `statements:events` and picks this up to start the processing pipeline.

---

## Environment Variables

Copy `.env.example` to `.env` and fill in values for local development. In Docker, these are provided by `docker-compose.yml`.

| Variable | Default | Description |
|---|---|---|
| `DB_HOST` | `mysql` | MySQL hostname |
| `DB_PORT` | `3306` | MySQL port |
| `DB_DATABASE` | `budget_management` | Database name |
| `DB_USERNAME` | `app` | Database user |
| `DB_PASSWORD` | `secret` | Database password |
| `REDIS_HOST` | `redis` | Redis hostname |
| `REDIS_PORT` | `6379` | Redis port |
| `MINIO_HOST` | `minio` | MinIO hostname |
| `MINIO_PORT` | `9000` | MinIO API port |
| `MINIO_ACCESS_KEY` | `minioadmin` | MinIO access key |
| `MINIO_SECRET_KEY` | `minioadmin` | MinIO secret key |
| `MINIO_BUCKET` | `statements` | Bucket name for PDFs |

---

## Setup

```bash
# From the project root
docker compose up -d

# The service starts automatically — no migrations needed (uses Laravel's schema)
```

Service is available at `http://localhost/upload/` (via Nginx) or directly at `http://localhost:8002`.

MinIO console is available at `http://localhost:9001` (user: `minioadmin`, password: `minioadmin`).
