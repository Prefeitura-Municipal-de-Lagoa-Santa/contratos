<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTicket extends Model
{
    public const CATEGORIES = [
        'novo_processo',
        'compra_direta',
        'renovacao',
    ];

    public const STATUSES = [
        'aberto',
        'em_atendimento',
        'resolvido',
        'aprovado',
        'recusado',
    ];

    public const MANAGEMENT_STATUSES = [
        'aberto',
        'em_atendimento',
        'resolvido',
    ];

    public const DECISION_STATUSES = [
        'aprovado',
        'recusado',
    ];

    protected $fillable = [
        'user_id',
        'responsible_user_id',
        'subject',
        'category',
        'description',
        'specifications',
        'justification',
        'ficha',
        'dotacao',
        'in_pca',
        'involves_other_secretariats',
        'other_fichas',
        'status',
        'management_response',
    ];

    protected function casts(): array
    {
        return [
            'in_pca' => 'boolean',
            'involves_other_secretariats' => 'boolean',
            'other_fichas' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ContractTicketAttachment::class);
    }
}
