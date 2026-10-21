<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrimonio extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_patrimonio',
        'nome',
        'descricao',
        'user_id',
        'data_atribuicao',
    ];

    protected $casts = [
        'data_atribuicao' => 'date',
    ];

    /**
     * Get the user who holds this asset.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
