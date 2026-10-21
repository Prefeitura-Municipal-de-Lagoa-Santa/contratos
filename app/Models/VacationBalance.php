<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacationBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'total_days',
        'used_days',
        'remaining_days',
    ];

    /**
     * Get the user that owns this balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create balance for a user in a specific year
     */
    public static function getOrCreateForUser(int $userId, ?string $year = null): self
    {
        $year = $year ?? date('Y');

        return static::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['total_days' => 30, 'used_days' => 0, 'remaining_days' => 30]
        );
    }

    /**
     * Deduct days from the balance
     */
    public function deductDays(int $days): bool
    {
        if ($days > $this->remaining_days) {
            return false;
        }

        $this->used_days += $days;
        $this->remaining_days -= $days;
        return $this->save();
    }

    /**
     * Restore days to the balance (when vacation is cancelled)
     */
    public function restoreDays(int $days): bool
    {
        $this->used_days = max(0, $this->used_days - $days);
        $this->remaining_days = min($this->total_days, $this->remaining_days + $days);
        return $this->save();
    }

    /**
     * Check if user has enough balance for the requested days
     */
    public function hasEnoughBalance(int $days): bool
    {
        return $this->remaining_days >= $days;
    }
}
