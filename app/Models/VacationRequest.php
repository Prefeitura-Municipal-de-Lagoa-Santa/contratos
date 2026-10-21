<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VacationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supervisor_id',
        'start_date',
        'end_date',
        'period_days',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'observations',
        'year',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Status possíveis
     */
    const STATUS_PENDING = 'pendente';
    const STATUS_APPROVED = 'aprovado';
    const STATUS_REJECTED = 'rejeitado';
    const STATUS_CANCELLED = 'cancelado';

    /**
     * Períodos permitidos (em dias)
     */
    const ALLOWED_PERIODS = [15, 30];

    /**
     * Antecedência mínima em dias
     */
    const MINIMUM_ADVANCE_DAYS = 30;

    /**
     * Limite de funcionários de férias simultâneas
     */
    const MAX_CONCURRENT_VACATIONS = 2;

    /**
     * Get the user that requested vacation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that approved the vacation.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the supervisor for the request.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Get formatted start date in Brazilian format
     */
    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date->format('d/m/Y');
    }

    /**
     * Get formatted end date in Brazilian format
     */
    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date->format('d/m/Y');
    }

    /**
     * Get status label in Portuguese
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_CANCELLED => 'Cancelado',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_CANCELLED => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if vacation period overlaps with another period
     */
    public static function hasOverlap(Carbon $startDate, Carbon $endDate, ?int $excludeId = null): int
    {
        $query = static::approved()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count();
    }

    /**
     * Validate minimum advance days
     */
    public static function validateMinimumAdvance(Carbon $startDate): bool
    {
        return $startDate->diffInDays(Carbon::today()) >= self::MINIMUM_ADVANCE_DAYS;
    }
}
