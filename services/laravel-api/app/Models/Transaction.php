<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'statement_id',
        'category_id',
        'amount',
        'date',
        'description',
        'is_confirmed',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'date'         => 'date',
            'is_confirmed' => 'boolean',
            'deleted_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statement(): BelongsTo
    {
        return $this->belongsTo(Statement::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
