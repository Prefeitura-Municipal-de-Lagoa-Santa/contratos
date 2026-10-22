<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
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
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
    ];

    protected $appends = [
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
        ];
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdministrator();
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
