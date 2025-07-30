<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Matiere extends Model
{
    use HasFactory;

    protected $table = 'matieres';

    protected $fillable = [
        'nom',
        'code',
        'coefficient',
        'volume_horaire',
    ];

    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'matiere_id');
    }

    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere', 'matiere_id', 'enseignant_id');
    }

    /**
     * Relation : une matière peut être abandonnée par plusieurs étudiants
     */
    public function etudiantsDropped(): HasMany
    {
        return $this->hasMany(EtudiantMatiereDropped::class);
    }

    /**
     * Obtenir le nombre d'étudiants qui ont abandonné cette matière
     */
    public function getDroppedStudentsCount($anneeAcademiqueId = null, $semestreId = null): int
    {
        $query = $this->etudiantsDropped();

        if ($anneeAcademiqueId) {
            $query->where('annee_academique_id', $anneeAcademiqueId);
        }

        if ($semestreId) {
            $query->where('semestre_id', $semestreId);
        }

        return $query->count();
    }
}
