<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semestre extends Model
{
    use HasFactory;

    protected $table = 'semesters';

    protected $fillable = [
        'libelle',
        'academic_year_id',
        'date_debut',
        'date_fin',
        'actif',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'actif' => 'boolean',
    ];

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'academic_year_id');
    }

    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'semester_id');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'semester_id');
    }
}
