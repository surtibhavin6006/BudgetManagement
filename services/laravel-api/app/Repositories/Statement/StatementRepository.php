<?php

namespace App\Repositories\Statement;

use App\Models\Statement;
use Illuminate\Database\Eloquent\Collection;

class StatementRepository implements StatementRepositoryInterface
{
    public function allForUser(int $userId): Collection
    {
        return Statement::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function findForUser(int $userId, int $statementId): Statement
    {
        return Statement::where('user_id', $userId)
            ->findOrFail($statementId);
    }

    public function pendingTransactions(Statement $statement): Collection
    {
        return $statement->transactions()
            ->with('category')
            ->where('is_confirmed', false)
            ->get();
    }

    public function markImported(Statement $statement): void
    {
        $statement->transactions()->update(['is_confirmed' => true]);
        $statement->update(['status' => 'imported']);
    }

    public function create(array $data): Statement
    {
        return Statement::create($data);
    }

    public function deleteAllForUser(int $userId): void
    {
        Statement::where('user_id', $userId)->delete();
    }

    public function restoreAllForUser(int $userId): void
    {
        Statement::onlyTrashed()->where('user_id', $userId)->restore();
    }
}
