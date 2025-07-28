<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JustificationAbsence extends Model
{
    use HasFactory;

    protected $table = 'justifications_absence';

    protected $fillable = [
        'justifie_par_user_id',
        'date_justification',
        'motif',
        'presence_id',
        'piece_jointe',
        'statut',
    ];

    protected $casts = [
        'date_justification' => 'date',
    ];

    public function justifiePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'justifie_par_user_id');
    }

    public function presence(): BelongsTo
    {
        return $this->belongsTo(Presence::class);
    }
}
