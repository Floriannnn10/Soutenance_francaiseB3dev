<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Presence extends Model
{
    use HasFactory;

    protected $table = 'presences';

    protected $fillable = [
        'etudiant_id',
        'course_session_id',
        'presence_status_id',
        'enregistre_le',
        'enregistre_par_user_id',
        'academic_year_id',
        'semester_id',
    ];

    protected $casts = [
        'enregistre_le' => 'datetime',
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function sessionDeCours(): BelongsTo
    {
        return $this->belongsTo(SessionDeCours::class, 'course_session_id');
    }

    public function statutPresence(): BelongsTo
    {
        return $this->belongsTo(StatutPresence::class, 'presence_status_id');
    }

    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par_user_id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'academic_year_id');
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class, 'semester_id');
    }

    public function justification(): HasOne
    {
        return $this->hasOne(JustificationAbsence::class);
    }
}
