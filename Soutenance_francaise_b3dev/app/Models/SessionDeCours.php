<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionDeCours extends Model
{
    use HasFactory;

    protected $table = 'course_sessions';

    protected $fillable = [
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'type_cours_id',
        'status_id',
        'start_time',
        'end_time',
        'location',
        'notes',
        'replacement_for_session_id',
        'academic_year_id',
        'semester_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function typeCours(): BelongsTo
    {
        return $this->belongsTo(TypeCours::class, 'type_cours_id');
    }

    public function statutSession(): BelongsTo
    {
        return $this->belongsTo(StatutSession::class, 'status_id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'academic_year_id');
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class, 'semester_id');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'course_session_id');
    }

    public function sessionRemplacee(): BelongsTo
    {
        return $this->belongsTo(SessionDeCours::class, 'replacement_for_session_id');
    }

    public function sessionsRemplacees(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'replacement_for_session_id');
    }
}
