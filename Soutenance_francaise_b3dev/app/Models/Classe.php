<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'niveau',
        'specialite',
        'description',
    ];

    /**
     * Relation avec les étudiants (many-to-many via classe_etudiant)
     */
    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class, 'classe_etudiant')
                    ->withPivot('annee_academique_id', 'date_inscription', 'est_actif')
                    ->withTimestamps();
    }

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours()
    {
        return $this->hasMany(SessionDeCours::class);
    }

    /**
     * Relation avec les inscriptions
     */
    public function inscriptions()
    {
        return $this->hasMany(ClasseEtudiant::class);
    }

    /**
     * Relation avec les coordinateurs (many-to-many via coordinateur_classe)
     */
    public function coordinateurs(): BelongsToMany
    {
        return $this->belongsToMany(Coordinateur::class, 'coordinateur_classe', 'classe_id', 'coordinateur_id')
                    ->withPivot(['annee_academique_id', 'date_debut', 'date_fin', 'commentaire', 'est_actif'])
                    ->withTimestamps();
    }

    /**
     * Obtenir les coordinateurs actifs pour une année académique spécifique
     */
    public function getCoordinateursForAnnee($anneeAcademiqueId)
    {
        return $this->belongsToMany(Coordinateur::class, 'coordinateur_classe', 'classe_id', 'coordinateur_id')
                    ->wherePivot('annee_academique_id', $anneeAcademiqueId)
                    ->wherePivot('est_actif', true)
                    ->withPivot(['date_debut', 'date_fin', 'commentaire'])
                    ->withTimestamps();
    }

    /**
     * Obtenir les étudiants pour une année académique spécifique
     */
    public function getEtudiantsForAnnee($anneeAcademiqueId)
    {
        return $this->belongsToMany(Etudiant::class, 'classe_etudiant')
                    ->wherePivot('annee_academique_id', $anneeAcademiqueId)
                    ->wherePivot('est_actif', true)
                    ->withPivot('date_inscription')
                    ->withTimestamps();
    }

    /**
     * Obtenir le nom complet de la classe
     */
    public function getNomCompletAttribute(): string
    {
        $nom = $this->nom;
        if ($this->niveau) {
            $nom .= ' - ' . $this->niveau;
        }
        if ($this->specialite) {
            $nom .= ' (' . $this->specialite . ')';
        }
        return $nom;
    }
}
