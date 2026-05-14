<?php

namespace App\Policies;

use App\Models\Statement;
use App\Models\User;

class StatementPolicy
{
    public function modify(User $user, Statement $statement): bool
    {
        return $user->id === $statement->user_id;
    }
}
