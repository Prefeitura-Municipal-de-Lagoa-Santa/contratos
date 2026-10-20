<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTicket extends Model
{
    public const CATEGORIES = [
        'contrato',
        'novo_contrato',
        'aditivo',
        'documentacao',
        'fiscalizacao',
        'pagamento',
        'outro',
    ];

    public const STATUSES = [
        'aberto',
        'em_atendimento',
        'resolvido',
    ];

    protected $fillable = [
        'user_id',
        'responsible_user_id',
        'subject',
        'category',
        'description',
        'specifications',
        'justification',
        'status',
        'management_response',
    ];

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
