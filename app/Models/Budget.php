<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Budget extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (self $budget): void {
            if (DB::getDriverName() === 'pgsql') {
                return;
            }

            $query = self::query()
                ->where('account_id', $budget->account_id)
                ->where('category_id', $budget->category_id)
                ->where('month', $budget->month)
                ->whereNull('deleted_at');

            if ($budget->exists) {
                $query->whereKeyNot($budget->getKey());
            }

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    'month' => 'A budget for this account, category, and month already exists.',
                ]);
            }
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'category_id',
        'month',
        'amount_cents',
        'currency',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
