<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Date;

class Transaction extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'direction',
        'amount_cents',
        'currency',
        'source_type',
        'source_id',
        'occurred_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForAccount(Builder $query, int $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeOccurredAtDesc(Builder $query): Builder
    {
        return $query->orderByDesc('occurred_at');
    }

    public function scopeInMonth(Builder $query, string $month): Builder
    {
        $start = Date::parse($month)->startOfMonth();
        $end = Date::parse($month)->endOfMonth();

        return $query->whereBetween('occurred_at', [$start, $end]);
    }
}
