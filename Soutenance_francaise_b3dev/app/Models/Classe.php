<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classe extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'nom',
        "niveau",
    ];

    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class, 'classe_id');
    }

    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'classe_id');
    }

    public function coordinateurs(): BelongsToMany
    {
        return $this->belongsToMany(Coordinateur::class, 'coordinateur_classe', 'classe_id', 'coordinateur_id');
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }
}
