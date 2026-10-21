<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'contract_type_id',
        'code',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'total',
        'manager',
        'manager_email',
        'deputy_manager',
        'deputy_manager_email',
        'inspector',
        'inspector_email',
        'deputy_inspector',
        'deputy_inspector_email',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total' => 'decimal:2',
    ];

    /**
     * Generate next contract code
     */
    public static function generateCode(): string
    {
        $lastContract = self::orderBy('id', 'desc')->first();

        if (!$lastContract) {
            return 'CONT-0001';
        }

        // Extract number from last code (e.g., CONT-0001 -> 1)
        $lastNumber = (int) substr($lastContract->code, 5);
        $nextNumber = $lastNumber + 1;

        return 'CONT-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship with ContractType
     */
    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class);
    }

    /**
     * Relationship with ContractFile
     */
    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContractFile::class);
    }
}
