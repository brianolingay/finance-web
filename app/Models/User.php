<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function accountMembers(): HasMany
    {
        return $this->hasMany(AccountMember::class);
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function isAccountOwner(Account $account): bool
    {
        return $this->id === $account->owner_user_id;
    }

    public function isAccountMember(Account $account): bool
    {
        return $this->isAccountOwner($account)
            || $this->accountMembers()
                ->where('account_id', $account->id)
                ->exists();
    }

    /**
     * @param  array<int, string>  $roles
     */
    public function hasAccountRole(Account $account, array $roles): bool
    {
        if ($this->isAccountOwner($account)) {
            return true;
        }

        return $this->accountMembers()
            ->where('account_id', $account->id)
            ->whereIn('role', $roles)
            ->exists();
    }
}
