<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeAcademique extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'actif',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'actif' => 'boolean',
    ];

    public function semestres(): HasMany
    {
        return $this->hasMany(Semestre::class, 'academic_year_id');
    }

    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'academic_year_id');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'academic_year_id');
    }
}
