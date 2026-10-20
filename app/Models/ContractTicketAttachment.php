<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTicketAttachment extends Model
{
    protected $fillable = [
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ContractTicket::class, 'contract_ticket_id');
    }
}
