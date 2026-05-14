<?php

namespace Database\Seeders;

use App\Repositories\Statement\StatementRepositoryInterface;
use Illuminate\Database\Seeder;

class StatementSeeder extends Seeder
{
    public function run(StatementRepositoryInterface $statementRepository): void
    {
        $statements = [
            ['user_id' => 1, 'original_filename' => 'april_statement.pdf', 'status' => 'imported'],
            ['user_id' => 1, 'original_filename' => 'may_statement.pdf',   'status' => 'reviewing'],
            ['user_id' => 2, 'original_filename' => 'bank_april.pdf',      'status' => 'imported'],
            ['user_id' => 3, 'original_filename' => 'march_statement.pdf', 'status' => 'imported'],
            ['user_id' => 3, 'original_filename' => 'april_statement.pdf', 'status' => 'categorising'],
            ['user_id' => 4, 'original_filename' => 'hsbc_april.pdf',      'status' => 'imported'],
            ['user_id' => 4, 'original_filename' => 'hsbc_may.pdf',        'status' => 'reviewing'],
            ['user_id' => 5, 'original_filename' => 'savings_april.pdf',   'status' => 'imported'],
        ];

        foreach ($statements as $row) {
            $statementRepository->create([
                'user_id'           => $row['user_id'],
                'file_path'         => 'statements/user-' . $row['user_id'] . '/' . $row['original_filename'],
                'original_filename' => $row['original_filename'],
                'status'            => $row['status'],
            ]);
        }
    }
}
