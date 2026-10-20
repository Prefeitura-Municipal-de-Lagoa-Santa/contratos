<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    public const ROLE_ADMINISTRATOR = 'administrator';

    public const ROLE_MANAGER = 'manager';

    public const ROLE_USER = 'user';

    public const ROLES = [
        self::ROLE_ADMINISTRATOR,
        self::ROLE_MANAGER,
        self::ROLE_USER,
    ];

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
        'is_admin',
        'role',
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
            'is_admin' => 'boolean',
        ];
    }

    public function setIsAdminAttribute(bool $value): void
    {
        $this->attributes['is_admin'] = $value;
        $this->attributes['role'] = $value ? self::ROLE_ADMINISTRATOR : self::ROLE_USER;
    }

    public function setRoleAttribute(string $value): void
    {
        $this->attributes['role'] = $value;
        $this->attributes['is_admin'] = $value === self::ROLE_ADMINISTRATOR;
    }

    public function isAdministrator(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    public function canManageTickets(): bool
    {
        return in_array($this->role, [self::ROLE_ADMINISTRATOR, self::ROLE_MANAGER], true);
    }

    /**
     * Get the vacation requests for the user.
     */
    public function vacationRequests()
    {
        return $this->hasMany(VacationRequest::class);
    }

    /**
     * Get the vacation balance for current year.
     */
    public function vacationBalance()
    {
        return $this->hasOne(VacationBalance::class)->where('year', date('Y'));
    }

    /**
     * Get or create vacation balance for a specific year.
     */
    public function getVacationBalance(?string $year = null)
    {
        return VacationBalance::getOrCreateForUser($this->id, $year);
    }
}
