<?php

namespace App\CQRS\Statement\Handlers;

use App\CQRS\Statement\Commands\ImportStatementCommand;
use App\Repositories\Statement\StatementRepositoryInterface;

class ImportStatementHandler
{
    public function __construct(
        private readonly StatementRepositoryInterface $repository,
    ) {}

    public function handle(ImportStatementCommand $command): void
    {
        $statement = $this->repository->findForUser($command->userId, $command->statementId);

        $this->repository->markImported($statement);
    }
}
