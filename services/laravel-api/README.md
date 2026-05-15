# Budget Management — Laravel API

REST API for the Budget Management application. Handles authentication, categories, monthly budgets, bank statement imports, and transaction tracking.

---

## Tech Stack

| | |
|---|---|
| Framework | Laravel 13, PHP 8.3+ |
| Auth | JWT (`php-open-source-saver/jwt-auth ^2.9`) |
| Database | MySQL 8.0 via Eloquent ORM |
| Container | Docker |

---

## Architecture

### Request Lifecycle

```
FormRequest        validates input, then binds DTO to container via passedValidation()
    │
    ▼ DTO          typed readonly object — constructor only, no fromRequest/fromArray
    │
Controller         injects FormRequest + DTO, delegates to Service
    │
    ▼
Service            extends BaseService, builds Commands/Queries, dispatches via Bus
    │
    ▼
Bus                convention-based routing — no explicit handler map needed
    │
    ▼
Handler            single responsibility, calls Repository with scalars/models
    │
    ▼
Repository         Eloquent only — accepts PHP primitives, never DTOs
```

---

### CQRS

Commands and Queries are `final readonly` data objects with no logic. The `Bus` resolves handlers by namespace convention — replacing `\Commands\` or `\Queries\` with `\Handlers\` and the `Command`/`Query` suffix with `Handler`:

```
App\CQRS\Budget\Commands\CreateBudgetCommand
    → App\CQRS\Budget\Handlers\CreateBudgetHandler

App\CQRS\Budget\Queries\ListBudgetsQuery
    → App\CQRS\Budget\Handlers\ListBudgetsHandler
```

No registration or mapping required — the naming convention is the contract.

---

### FormRequest → DTO → Controller

`BaseFormRequest` uses the `ResolvesDTO` trait. After validation passes, `passedValidation()` calls `toDTO()` and binds the result into the Laravel container. The controller type-hints the DTO and Laravel resolves it automatically:

```php
// FormRequest
class StoreBudgetRequest extends BaseFormRequest
{
    public function rules(): array { ... }

    public function toDTO(): StoreBudgetDTO
    {
        return new StoreBudgetDTO(
            userId:      current_user_id(),
            categoryId:  $this->validated('category_id'),
            month:       $this->validated('month'),
            amountLimit: (float) $this->validated('amount_limit'),
        );
    }
}

// Controller — $request triggers binding, $dto is resolved from container
public function store(StoreBudgetRequest $request, StoreBudgetDTO $dto): JsonResponse
{
    $budget = $this->budgetService->store($dto);
    return ApiResponse::success($budget, 'Budget created', 201);
}
```

---

### Repository Contract

Repositories know nothing about DTOs or Commands. The Handler is the translation layer:

```php
// Repository interface — primitives only
public function create(int $userId, int $categoryId, string $month, float $amountLimit): Budget;

// Handler — reads from Command, calls Repository with scalars
public function handle(CreateBudgetCommand $command): Budget
{
    return $this->repository->create(
        $command->userId,
        $command->categoryId,
        $command->month,
        $command->amountLimit,
    );
}
```

---

### Base Classes

| Class | Purpose |
|---|---|
| `BaseFormRequest` | Abstract. Uses `ResolvesDTO` trait, default `authorize(): true`, enforces `abstract toDTO(): object` |
| `BaseService` | Abstract. Holds `protected readonly BusInterface $bus` — all Bus-based services extend it |

---

## Directory Structure

```
app/
├── CQRS/
│   ├── Bus/
│   │   ├── BusInterface.php
│   │   └── Bus.php                       # convention-based handler routing
│   ├── Auth/
│   │   ├── Commands/                     # RegisterCommand, LoginCommand, LogoutCommand
│   │   │                                 # SendPasswordResetLinkCommand, ResetPasswordCommand
│   │   ├── Queries/                      # GetAuthenticatedUserQuery, ValidateTokenQuery
│   │   └── Handlers/                     # one handler per command/query
│   ├── Budget/
│   │   ├── Commands/                     # CreateBudgetCommand, UpdateBudgetCommand, DeleteBudgetCommand
│   │   ├── Queries/                      # ListBudgetsQuery
│   │   └── Handlers/
│   ├── Category/
│   │   ├── Commands/                     # CreateCategoryCommand, UpdateCategoryCommand, DeleteCategoryCommand
│   │   ├── Queries/                      # ListCategoriesQuery
│   │   └── Handlers/
│   ├── Statement/
│   │   ├── Commands/                     # ImportStatementCommand
│   │   ├── Queries/                      # ListStatementsQuery, GetPendingTransactionsQuery
│   │   └── Handlers/
│   └── Transaction/
│       ├── Queries/                      # ListTransactionsQuery
│       └── Handlers/
│
├── DTOs/
│   ├── Auth/                             # RegisterDTO, LoginDTO, ForgotPasswordDTO, ResetPasswordDTO
│   ├── Budget/                           # StoreBudgetDTO, UpdateBudgetDTO
│   └── Category/                         # StoreCategoryDTO, UpdateCategoryDTO
│
├── Http/
│   ├── Controllers/                      # AuthController, CategoryController, BudgetController
│   │                                     # StatementController, TransactionController
│   ├── Requests/
│   │   ├── BaseFormRequest.php           # abstract — enforces toDTO(), default authorize()
│   │   ├── Concerns/
│   │   │   └── ResolvesDTO.php           # passedValidation() → app()->instance(DTO)
│   │   ├── Auth/                         # RegisterRequest, LoginRequest, ForgotPasswordRequest
│   │   │                                 # ResetPasswordRequest
│   │   ├── Budget/                       # StoreBudgetRequest, UpdateBudgetRequest, DestroyBudgetRequest
│   │   └── Category/                     # StoreCategoryRequest, UpdateCategoryRequest, DestroyCategoryRequest
│   └── Responses/
│       └── ApiResponse.php
│
├── Models/                               # User, Category, Budget, Statement, Transaction
│
├── Observers/
│   ├── UserObserver.php                  # cascade soft-deletes on user deletion
│   └── CategoryObserver.php             # cascade soft-deletes budgets on category deletion
│
├── Policies/
│   ├── CategoryPolicy.php               # modify: owner check
│   ├── BudgetPolicy.php                 # modify: owner check
│   └── StatementPolicy.php             # modify: owner check
│
├── Providers/
│   ├── AppServiceProvider.php           # Gate::policy() + Observer registration
│   ├── RepositoryServiceProvider.php   # binds Repository interfaces → implementations
│   └── ServiceServiceProvider.php     # binds Service + Bus interfaces → implementations
│
├── Repositories/
│   ├── Auth/                            # AuthRepositoryInterface, AuthRepository
│   ├── Budget/                          # BudgetRepositoryInterface, BudgetRepository
│   ├── Category/                        # CategoryRepositoryInterface, CategoryRepository
│   ├── Statement/                       # StatementRepositoryInterface, StatementRepository
│   └── Transaction/                     # TransactionRepositoryInterface, TransactionRepository
│
├── Services/
│   ├── BaseService.php                  # abstract — holds protected BusInterface $bus
│   ├── Auth/                            # AuthServiceInterface, AuthService
│   ├── Budget/                          # BudgetServiceInterface, BudgetService
│   ├── Category/                        # CategoryServiceInterface, CategoryService
│   ├── Statement/                       # StatementServiceInterface, StatementService
│   └── Transaction/                     # TransactionServiceInterface, TransactionService
│
└── helpers.php                          # current_user_id(), current_user()
```

---

## API Reference

All protected routes require `Authorization: Bearer <token>`. JWT is validated by Nginx before the request reaches Laravel — see the root README for the gateway flow.

### Auth

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/api/auth/register` | No | Register a new user |
| POST | `/api/auth/login` | No | Login, returns JWT token |
| POST | `/api/auth/forgot-password` | No | Send password reset email |
| POST | `/api/auth/reset-password` | No | Reset password using token |
| POST | `/api/auth/logout` | Yes | Invalidate token |
| GET | `/api/auth/me` | Yes | Get authenticated user |
| GET | `/api/auth/validate` | — | Internal — called by Nginx `auth_request` only |

**POST** `/api/auth/register`
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password",
  "monthly_income": 5000.00
}
```

**POST** `/api/auth/login`
```json
{
  "email": "john@example.com",
  "password": "password"
}
```

**POST** `/api/auth/forgot-password`
```json
{ "email": "john@example.com" }
```

**POST** `/api/auth/reset-password`
```json
{
  "token": "<token-from-email>",
  "email": "john@example.com",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

---

### Categories

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/categories` | List all categories for the authenticated user |
| POST | `/api/categories` | Create a category |
| PUT | `/api/categories/{id}` | Update a category (owner only) |
| DELETE | `/api/categories/{id}` | Soft-delete a category — cascades to its budgets |

**POST** `/api/categories`
```json
{
  "name": "Groceries",
  "color": "#22c55e",
  "icon": "shopping-cart"
}
```
`color` defaults to `#6366f1`. `icon` defaults to `tag`.

**PUT** `/api/categories/{id}`
```json
{
  "name": "Groceries",
  "color": "#22c55e",
  "icon": "shopping-cart"
}
```

---

### Budgets

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/budgets?month=YYYY-MM` | List budgets for a month (defaults to current month) |
| POST | `/api/budgets` | Create a budget for a category/month |
| PUT | `/api/budgets/{id}` | Update the amount limit (owner only) |
| DELETE | `/api/budgets/{id}` | Soft-delete a budget |

**POST** `/api/budgets`
```json
{
  "category_id": 1,
  "month": "2026-05",
  "amount_limit": 500.00
}
```

**PUT** `/api/budgets/{id}`
```json
{
  "amount_limit": 750.00
}
```

---

### Statements

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/statements` | List all statements for the authenticated user |
| GET | `/api/statements/{id}/transactions` | List pending transactions on a statement |
| POST | `/api/statements/{id}/import` | Mark a statement as imported |

---

### Transactions

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/transactions?month=YYYY-MM&category_id=1` | List transactions with optional filters |

---

## Database Schema

### users
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| email | varchar | unique |
| password | varchar | bcrypt |
| monthly_income | decimal(10,2) | |
| deleted_at | timestamp | soft delete |

### categories
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users | |
| name | varchar | unique per user |
| color | char(7) | hex, default `#6366f1` |
| icon | varchar | default `tag` |
| is_ai_suggested | boolean | |
| deleted_at | timestamp | soft delete |

### budgets
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users | |
| category_id | FK → categories | |
| month | char(7) | `YYYY-MM` |
| amount_limit | decimal(10,2) | |
| deleted_at | timestamp | soft delete |
| | | unique: (user_id, category_id, month) |

### statements
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users | |
| file_path | varchar | |
| original_filename | varchar | |
| status | enum | `uploaded` → `parsing` → `categorising` → `saving` → `reviewing` → `imported` |
| deleted_at | timestamp | soft delete |

### transactions
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users | |
| statement_id | FK → statements | nullable |
| category_id | FK → categories | nullable |
| amount | decimal(10,2) | |
| date | date | |
| description | varchar | |
| is_confirmed | boolean | |
| deleted_at | timestamp | soft delete |

---

## Setup

```bash
# From the project root
docker compose up -d

# First-time only
docker exec laravel-api composer install
docker exec laravel-api php artisan key:generate
docker exec laravel-api php artisan jwt:secret
docker exec laravel-api php artisan migrate
```

API is available at `http://localhost/api`.
