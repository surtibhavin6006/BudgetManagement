# Budget Management with AI

A microservices-based budget management application with AI-powered transaction categorisation, RAG-driven category suggestions, and real-time PDF statement processing via a multi-agent pipeline.

---

## What This App Does

- **Auth** — Signup (with monthly income), Login, Logout, Forgot Password via email, JWT tokens
- **Categories** — AI suggests categories based on spending patterns (RAG), user can add/edit their own
- **Budgets** — Set monthly spend limits per category
- **Statement Upload** — Upload a bank PDF, AI parses and categorises transactions, user reviews and confirms before import
- **Dashboard** — Spending graphs, category breakdowns, filters by month/category

---

## Architecture

### Principles

- **Event-driven** — services communicate only via Redis Pub/Sub; no direct HTTP calls between backend services
- **Loosely coupled** — each service owns a single responsibility and can be scaled or replaced independently
- **Laravel = HTTP only** — Laravel serves the REST API and auth; it has zero worker or queue code
- **Python = all async/event work** — upload, AI processing, event consumption, SSE streaming
- **Centralised auth at Nginx** — JWT is validated once at the gateway; downstream services never parse tokens

### Service Map

```
                         ┌──────────────────────────────┐
                         │            NGINX             │
                         │  /          → frontend:3000  │
                         │  /api/*     → laravel:8000   │
                         │  /upload/*  → upload:8002    │
                         │  /ai/*      → ai-svc:8001    │
                         └──────────────┬───────────────┘
                                        │
          ┌─────────────┬───────────────┼───────────────┐
          ↓             ↓               ↓               ↓
     React:3000   Laravel:8000   Upload Svc:8002   AI Svc:8001
     (frontend)   (REST API)     (Python)          (Python)
                       │               │               │
                   MySQL:3306      MinIO:9000      ChromaDB
                       │               │               │
                       └───────────────┴───────────────┘
                                       │
                               Redis Event Bus
                                       │
                               Python Worker
                               (Redis sub → MySQL)
```

---

## Auth & Gateway Pattern

JWT validation happens **once at Nginx** using the `auth_request` module. No downstream service parses or validates a token — they only read trusted headers injected by Nginx.

### Flow

```
Client (Bearer token in Authorization header)
    │
    ▼
  NGINX
    │
    ├── auth_request → Laravel /api/auth/validate  (internal subrequest)
    │                         │
    │                    Decode JWT
    │                    Valid? ── No ──→ 401 returned to client immediately
    │                         │
    │                        Yes
    │                    return 200
    │                    + X-User-Id: 42
    │                    + X-User-Email: user@example.com
    │
    ├── Nginx injects into original request:
    │     X-User-Id: 42
    │     X-User-Email: user@example.com
    │
    ▼
Target service (laravel-api / upload-service / ai-service)
Reads X-User-Id from header — no JWT library needed
```

### Public vs Protected Routes

| Route pattern | auth_request | Notes |
|---|---|---|
| `POST /api/auth/login` | No | Public |
| `POST /api/auth/register` | No | Public |
| `POST /api/auth/forgot-password` | No | Public |
| `POST /api/auth/reset-password` | No | Public |
| `GET /api/auth/validate` | No | Internal only — called by Nginx, not exposed to clients |
| `/api/*` (all others) | Yes | Protected |
| `/upload/*` | Yes | Protected |
| `/ai/*` | Yes | Protected |

### Laravel — validate endpoint

```php
// GET /api/auth/validate  (Nginx internal subrequest only)
public function validate(Request $request)
{
    $user = JWTAuth::parseToken()->authenticate();

    return response('', 200)
        ->header('X-User-Id',    $user->id)
        ->header('X-User-Email', $user->email);
    // Any JWT exception (expired, invalid) bubbles to 401 automatically
}
```

### How downstream services consume the user identity

```php
// laravel-api controllers — trust the header, never re-validate JWT
$userId = $request->header('X-User-Id');
```

```python
# upload-service / ai-service / python-worker — no JWT library needed
user_id = request.headers.get("X-User-Id")
```

---

### Service Responsibilities

| Service | Language | Port | Responsibility |
|---|---|---|---|
| `frontend` | React + Vite | 3000 | UI — all pages, SSE client, charts |
| `nginx` | Nginx | 80 | Reverse proxy, routing |
| `laravel-api` | PHP / Laravel | 8000 | REST API, Auth, JWT, CRUD reads |
| `upload-service` | Python / FastAPI | 8002 | Receive PDF, store in MinIO, publish event |
| `ai-service` | Python / FastAPI | 8001 | LangGraph pipeline, SSE stream, RAG category suggestions |
| `python-worker` | Python / asyncio | — | Subscribe Redis progress events, write MySQL |
| `mysql` | MySQL 8 | 3306 | Primary relational database |
| `redis` | Redis 7 | 6379 | Event bus (Pub/Sub) + event history |
| `minio` | MinIO | 9000 | Object storage for uploaded PDFs |
| `chromadb` | ChromaDB | 8003 | Vector store for RAG embeddings |

---

## Event-Driven Flow

### Redis Channels

| Channel | Publisher | Subscribers | Purpose |
|---|---|---|---|
| `statements:events` | upload-service | ai-service | Triggers AI processing pipeline |
| `statement:{id}:progress` | ai-service | python-worker, ai-service (SSE) | Real-time processing progress |
| `statement:{id}:history` | ai-service | — | Redis List, durable event log, TTL 24hr |
| `statement:{id}:status` | ai-service | — | Redis String, latest state for reconnect |

### Statement Upload Flow

```
1.  React          → POST /upload/statement (PDF)
2.  upload-service → stores PDF in MinIO
3.  upload-service → INSERT statement row in MySQL (status: uploaded)
4.  upload-service → PUBLISH statements:events { event: statement.uploaded, id: 123 }
5.  upload-service → returns { statement_id: 123 }

6.  React          → GET /ai/stream/123  (SSE connection)

7.  ai-service     (subscribed to statements:events) picks up event, starts LangGraph pipeline:

    parser_agent      → extracts rows from PDF text
                      → PUBLISH + RPUSH { status: parsing, percent: 30 }

    categorizer_agent → tags each row via Groq + RAG lookup
                      → PUBLISH + RPUSH { status: categorising, percent: 60 }

    save_node         → PUBLISH + RPUSH { status: saving, percent: 85, data: [...] }

    done              → PUBLISH + RPUSH { status: reviewing, percent: 100 }

8.  python-worker  (subscribed to statement:123:progress) on each event:
                   → UPDATE statements SET status = ... WHERE id = 123
                   → BULK INSERT into transactions (on saving event)

9.  React          → GET /api/statements/123/transactions  (review screen)
10. User confirms  → POST /api/statements/123/import
11. laravel-api    → marks transactions as confirmed
```

### SSE Reconnection (Page Reload Safe)

Every progress event is both published (live) and appended to a Redis List (`RPUSH statement:{id}:history`).

On SSE connect or reconnect:
1. AI service reads full history from Redis (`LRANGE statement:{id}:history`)
2. Replays all past events to the client immediately (catches up)
3. Subscribes to live channel for future events
4. Browser sends `Last-Event-ID` header — service replays only missed events

If processing is already complete when the client connects, history is replayed and the connection closes cleanly.

---

## AI Features

### RAG — Category Suggestions

When a user signs up or requests suggestions:
1. Their transaction descriptions are embedded via Groq/HuggingFace embeddings
2. Stored in ChromaDB
3. On suggestion request: similar past transactions are retrieved, Groq generates personalised category recommendations

### Multi-Agent Pipeline (LangGraph)

```
Orchestrator (Supervisor)
    │
    ├── Parser Agent
    │     Extracts raw rows from PDF text (pdfplumber)
    │     Output: list of { date, description, amount }
    │
    ├── Categoriser Agent
    │     For each transaction: RAG lookup + Groq LLM tags category
    │     Output: list of { ...row, category, confidence }
    │
    └── Save Node
          Publishes final transactions array in progress event
          python-worker bulk inserts into MySQL
```

---

## Tech Stack

### Backend

| Layer | Technology |
|---|---|
| REST API + Auth | Laravel 11 + `tymon/jwt-auth` |
| AI Framework | LangGraph (multi-agent state machine) |
| LLM Provider | Groq (`llama-3.3-70b-versatile`) — fast inference for dev/test |
| PDF Parsing | pdfplumber |
| Vector Store | ChromaDB (local, no infra overhead) |
| Embeddings | `langchain-groq` / HuggingFace |
| Event Bus | Redis 7 Pub/Sub |
| File Storage | MinIO (S3-compatible, self-hosted) |
| Database | MySQL 8 |
| Python HTTP | FastAPI + uvicorn |
| Python DB | SQLAlchemy + aiomysql |
| Python Redis | `redis-py` async |

### Frontend

| Layer | Technology |
|---|---|
| Framework | React 18 + Vite |
| Routing | React Router v6 |
| State | Zustand |
| Charts | Recharts |
| HTTP | Axios |
| Realtime | Native EventSource (SSE) |
| Styling | Tailwind CSS |

### Infrastructure

| Component | Technology |
|---|---|
| Containerisation | Docker + Docker Compose |
| Reverse Proxy / Auth Gateway | Nginx (`auth_request` module) |
| CI-ready | docker-compose.dev.yml for local, docker-compose.yml for prod |

---

## Data Model

```
users
  id, name, email, password, monthly_income, email_verified_at, created_at

categories
  id, user_id, name, color, icon, is_ai_suggested, created_at

budgets
  id, user_id, category_id, month (YYYY-MM), amount_limit, created_at

statements
  id, user_id, file_path, original_filename, status (uploaded|parsing|categorising|saving|reviewing|imported), created_at

transactions
  id, user_id, statement_id, category_id, amount, date, description, is_confirmed, created_at
```

---

## Directory Structure

```
BudgetManagement/
├── docker-compose.yml
├── docker-compose.dev.yml
├── .env.example
├── README.md
│
├── nginx/
│   └── conf.d/
│       └── default.conf             # auth_request rules + upstream routing
│
├── services/
│   │
│   ├── frontend/                        # React + Vite
│   │   ├── Dockerfile
│   │   └── src/
│   │       ├── pages/
│   │       │   ├── Login.tsx
│   │       │   ├── Signup.tsx
│   │       │   ├── Dashboard.tsx
│   │       │   ├── Budget.tsx
│   │       │   ├── StatementUpload.tsx
│   │       │   └── StatementReview.tsx
│   │       ├── components/
│   │       ├── hooks/
│   │       │   ├── useSSE.ts
│   │       │   └── useAuth.ts
│   │       └── api/
│   │
│   ├── laravel-api/                     # PHP — HTTP only (no workers, no queues)
│   │   ├── Dockerfile
│   │   └── app/
│   │       ├── Http/Controllers/
│   │       │   ├── AuthController.php
│   │       │   ├── CategoryController.php
│   │       │   ├── BudgetController.php
│   │       │   ├── StatementController.php
│   │       │   └── TransactionController.php
│   │       ├── Models/
│   │       │   ├── User.php
│   │       │   ├── Category.php
│   │       │   ├── Budget.php
│   │       │   ├── Statement.php
│   │       │   └── Transaction.php
│   │       └── database/migrations/
│   │
│   ├── upload-service/                  # Python — file intake + event publisher
│   │   ├── Dockerfile
│   │   ├── requirements.txt
│   │   └── app/
│   │       ├── main.py
│   │       ├── routers/
│   │       │   └── upload.py
│   │       └── services/
│   │           ├── minio_client.py
│   │           ├── redis_publisher.py
│   │           └── db.py
│   │
│   ├── ai-service/                      # Python — LangGraph + SSE + RAG
│   │   ├── Dockerfile
│   │   ├── requirements.txt
│   │   └── app/
│   │       ├── main.py
│   │       ├── routers/
│   │       │   ├── stream.py            # GET /ai/stream/{id} → SSE
│   │       │   ├── process.py           # internal trigger (from Redis sub)
│   │       │   └── categories.py        # POST /ai/suggest-categories
│   │       ├── agents/
│   │       │   ├── orchestrator.py      # LangGraph supervisor
│   │       │   ├── parser_agent.py      # PDF row extraction node
│   │       │   └── categorizer.py       # Groq + RAG tagging node
│   │       ├── rag/
│   │       │   ├── embeddings.py
│   │       │   ├── vector_store.py      # ChromaDB
│   │       │   └── retriever.py
│   │       └── services/
│   │           ├── minio_client.py
│   │           ├── redis_pubsub.py      # PUBLISH + RPUSH history
│   │           └── pdf_parser.py
│   │
│   └── python-worker/                   # Python — Redis subscriber → MySQL writer
│       ├── Dockerfile
│       ├── requirements.txt
│       └── app/
│           ├── main.py                  # asyncio entry, subscribes on startup
│           ├── subscribers/
│           │   └── progress.py          # handles statement:*:progress events
│           └── db/
│               ├── statements.py        # UPDATE statement status
│               └── transactions.py      # BULK INSERT transactions
│
└── infrastructure/
    ├── mysql/
    │   └── init.sql
    ├── minio/
    │   └── create-buckets.sh
    └── redis/
        └── redis.conf
```

---

## API Endpoints (Laravel)

### Auth
```
POST   /api/auth/register          body: name, email, password, monthly_income
POST   /api/auth/login             body: email, password
POST   /api/auth/logout
POST   /api/auth/forgot-password   body: email
POST   /api/auth/reset-password    body: token, email, password
GET    /api/auth/me
```

### Categories
```
GET    /api/categories
POST   /api/categories
PUT    /api/categories/{id}
DELETE /api/categories/{id}
```

### Budgets
```
GET    /api/budgets?month=2026-05
POST   /api/budgets
PUT    /api/budgets/{id}
DELETE /api/budgets/{id}
```

### Statements
```
GET    /api/statements
GET    /api/statements/{id}/transactions
POST   /api/statements/{id}/import
```

### Transactions
```
GET    /api/transactions?month=2026-05&category_id=1
```

## API Endpoints (Upload Service)

```
POST   /upload/statement           multipart: file (PDF), user_id
```

## API Endpoints (AI Service)

```
GET    /ai/stream/{statement_id}   SSE — real-time processing progress
POST   /ai/suggest-categories      body: user_id → returns AI category suggestions
```

---

## Local Development Setup

> Prerequisites: Docker Desktop, Git

```bash
cp .env.example .env
# fill in GROQ_API_KEY and mail credentials

docker compose -f docker-compose.dev.yml up --build
```

Services will be available at:
- App: http://localhost
- MinIO Console: http://localhost:9001
- MySQL: localhost:3306

---

## Environment Variables

```env
# App
APP_ENV=local

# MySQL
MYSQL_ROOT_PASSWORD=secret
MYSQL_DATABASE=budget_management
MYSQL_USER=app
MYSQL_PASSWORD=secret

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# MinIO
MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=minioadmin
MINIO_BUCKET=statements

# AI
GROQ_API_KEY=your_groq_api_key

# Laravel
JWT_SECRET=your_jwt_secret
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=

# ChromaDB
CHROMA_HOST=chromadb
CHROMA_PORT=8003
```

---

## Scalability Notes

- `python-worker` can be scaled horizontally (`docker compose up --scale python-worker=3`) — Redis Pub/Sub fan-out delivers to all instances, use consumer groups if needed
- `ai-service` stateless — scale independently for parallel PDF processing
- `laravel-api` stateless (JWT) — scale behind Nginx upstream pool
- ChromaDB can be swapped for Pinecone or Qdrant for production without changing agent code
- Groq can be swapped for OpenAI or Claude by changing the `ChatGroq` provider in `agents/`
