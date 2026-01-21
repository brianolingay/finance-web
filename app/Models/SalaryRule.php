<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SalaryRule extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'name',
        'type',
        'fixed_cents',
        'commission_bps',
        'currency',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $salaryRule): void {
            $validator = Validator::make($salaryRule->only([
                'type',
                'fixed_cents',
                'commission_bps',
            ]), [
                'type' => ['required', 'string', 'in:fixed,commission,hybrid'],
                'fixed_cents' => ['nullable', 'integer', 'min:0', 'required_if:type,fixed,hybrid'],
                'commission_bps' => ['nullable', 'integer', 'min:0', 'required_if:type,commission,hybrid'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function cashierSalaries(): HasMany
    {
        return $this->hasMany(CashierSalary::class);
    }
}
