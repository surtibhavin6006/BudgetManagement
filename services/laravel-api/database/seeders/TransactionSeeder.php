<?php

namespace Database\Seeders;

use App\Models\Statement;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    private array $transactionTemplates = [
        'Food & Dining'  => [
            ['description' => 'Tesco Groceries',       'amount' => 87.50],
            ['description' => 'Deliveroo Order',        'amount' => 24.80],
        ],
        'Transport'      => [
            ['description' => 'TfL Oyster Top-up',     'amount' => 30.00],
            ['description' => 'BP Fuel Station',        'amount' => 65.00],
        ],
        'Housing'        => [
            ['description' => 'Monthly Rent',           'amount' => 1200.00],
            ['description' => 'Council Tax',            'amount' => 145.00],
        ],
        'Utilities'      => [
            ['description' => 'British Gas Bill',       'amount' => 95.00],
            ['description' => 'BT Broadband',           'amount' => 39.99],
        ],
        'Entertainment'  => [
            ['description' => 'Netflix Subscription',   'amount' => 15.99],
            ['description' => 'Spotify Premium',        'amount' => 9.99],
        ],
        'Shopping'       => [
            ['description' => 'Amazon Purchase',        'amount' => 49.99],
            ['description' => 'ASOS Order',             'amount' => 67.50],
        ],
        'Healthcare'     => [
            ['description' => 'GP Prescription',        'amount' => 9.90],
            ['description' => 'Dental Check-up',        'amount' => 55.00],
        ],
        'Education'      => [
            ['description' => 'Udemy Course',           'amount' => 14.99],
            ['description' => 'Book Purchase',          'amount' => 22.00],
        ],
    ];

    private array $dates = [
        '2026-04-03', '2026-04-08', '2026-04-12', '2026-04-17',
        '2026-04-21', '2026-04-25', '2026-05-02', '2026-05-06',
    ];

    public function run(TransactionRepositoryInterface $transactionRepository): void
    {
        $now = Carbon::now()->toDateTimeString();

        User::with('categories')->get()->each(function (User $user) use ($transactionRepository, $now) {
            $statement   = Statement::where('user_id', $user->id)->where('status', 'imported')->first();
            $categoryMap = $user->categories->keyBy('name');
            $dateIndex   = 0;
            $rows        = [];

            foreach ($this->transactionTemplates as $categoryName => $items) {
                $category = $categoryMap->get($categoryName);

                if (!$category) {
                    continue;
                }

                foreach ($items as $item) {
                    $rows[] = [
                        'user_id'      => $user->id,
                        'statement_id' => $statement?->id,
                        'category_id'  => $category->id,
                        'amount'       => $item['amount'],
                        'date'         => $this->dates[$dateIndex % count($this->dates)],
                        'description'  => $item['description'],
                        'is_confirmed' => true,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                    $dateIndex++;
                }
            }

            $transactionRepository->bulkCreate($rows);
        });
    }
}
