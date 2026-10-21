<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Oficio extends Model
{
    use HasFactory;

    protected $table = 'oficios';

    protected $fillable = [
        'code',
        'year',
        'date',
        'sender_name',
        'sender_role',
        'department',
        'recipient_name',
        'recipient_role',
        'recipient_institution',
        'subject',
        'content',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $appends = [
        'formatted_date',
        'formatted_content',
    ];

    /**
     * Get the user that created the oficio.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate the next sequential code for the current year.
     */
    public static function generateNextCode(): string
    {
        $year = date('Y');

        $lastOficio = static::where('year', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOficio) {
            // Extract number from code like "001/2025"
            $lastNumber = (int) explode('/', $lastOficio->code)[0];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/' . $year;
    }

    /**
     * Get the formatted date in Brazilian format with month name capitalized.
     */
    public function getFormattedDateAttribute(): string
    {
        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        $day = $this->date->format('d');
        $month = $months[(int) $this->date->format('m')];
        $year = $this->date->format('Y');

        return "{$day} de {$month} de {$year}";
    }

    /**
     * Get the formatted content with proper paragraphs for PDF.
     */
    public function getFormattedContentAttribute(): string
    {
        // Split content by double line breaks to get paragraphs
        $paragraphs = preg_split('/\n\s*\n/', trim($this->content));

        $formattedParagraphs = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                // Handle single line breaks within the paragraph
                $paragraph = str_replace("\n", "<br>", $paragraph);
                $formattedParagraphs[] = '<p class="paragraph">' . e($paragraph) . '</p>';
            }
        }

        return implode("\n", $formattedParagraphs);
    }
}
