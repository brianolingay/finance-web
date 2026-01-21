<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'cashier_id',
        'status',
        'total_cents',
        'currency',
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

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(Cashier::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function salePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'source');
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
