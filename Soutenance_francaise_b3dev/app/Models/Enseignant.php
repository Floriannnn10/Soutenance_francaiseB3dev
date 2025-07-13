<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'numero_enseignant',
        'grade',
        'specialite',
        'telephone',
        'bureau',
        'photo',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours()
    {
        return $this->hasMany(SessionDeCours::class);
    }

    /**
     * Obtenir les sessions de cours pour une période donnée
     */
    public function getSessionsForPeriod($dateDebut, $dateFin)
    {
        return $this->sessionsDeCours()
                    ->whereBetween('date', [$dateDebut, $dateFin])
                    ->with(['classe', 'matiere', 'semestre.anneeAcademique'])
                    ->orderBy('date')
                    ->orderBy('heure_debut');
    }

    /**
     * Obtenir les sessions de cours pour aujourd'hui
     */
    public function getSessionsToday()
    {
        return $this->sessionsDeCours()
                    ->where('date', today())
                    ->with(['classe', 'matiere', 'statutSession'])
                    ->orderBy('heure_debut');
    }
}
