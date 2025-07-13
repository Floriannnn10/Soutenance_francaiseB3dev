<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Etudiant extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'numero_etudiant',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'telephone',
        'nationalite',
        'sexe',
        'photo',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Relation avec les classes (many-to-many via classe_etudiant)
     */
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'classe_etudiant')
                    ->withPivot('annee_academique_id', 'date_inscription', 'est_actif')
                    ->withTimestamps();
    }

    /**
     * Relation avec les présences
     */
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Relation avec les inscriptions
     */
    public function inscriptions()
    {
        return $this->hasMany(ClasseEtudiant::class);
    }

    /**
     * Relation avec les parents (many-to-many via parent_etudiant)
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentEtudiant::class, 'parent_etudiant', 'etudiant_id', 'parent_id')
                    ->withPivot(['type_relation', 'est_responsable_legal', 'peut_recevoir_notifications'])
                    ->withTimestamps();
    }

    /**
     * Obtenir les parents responsables légaux de cet étudiant.
     */
    public function parentsResponsablesLegaux()
    {
        return $this->belongsToMany(ParentEtudiant::class, 'parent_etudiant', 'etudiant_id', 'parent_id')
                    ->wherePivot('est_responsable_legal', true)
                    ->withPivot(['type_relation', 'est_responsable_legal', 'peut_recevoir_notifications'])
                    ->withTimestamps();
    }

    /**
     * Obtenir les parents qui peuvent recevoir des notifications pour cet étudiant.
     */
    public function parentsAvecNotifications()
    {
        return $this->belongsToMany(ParentEtudiant::class, 'parent_etudiant', 'etudiant_id', 'parent_id')
                    ->wherePivot('peut_recevoir_notifications', true)
                    ->withPivot(['type_relation', 'est_responsable_legal', 'peut_recevoir_notifications'])
                    ->withTimestamps();
    }

    /**
     * Obtenir les classes pour une année académique spécifique
     */
    public function getClassesForAnnee($anneeAcademiqueId)
    {
        return $this->belongsToMany(Classe::class, 'classe_etudiant')
                    ->wherePivot('annee_academique_id', $anneeAcademiqueId)
                    ->wherePivot('est_actif', true)
                    ->withPivot('date_inscription')
                    ->withTimestamps();
    }

    /**
     * Calculer le taux de présence pour une matière et un semestre
     */
    public function getTauxPresence($matiereId, $semestreId)
    {
        $totalSessions = SessionDeCours::where('matiere_id', $matiereId)
                                      ->where('semestre_id', $semestreId)
                                      ->count();

        if ($totalSessions === 0) {
            return 0;
        }

        $presences = $this->presences()
                          ->whereHas('sessionDeCours', function ($query) use ($matiereId, $semestreId) {
                              $query->where('matiere_id', $matiereId)
                                    ->where('semestre_id', $semestreId);
                          })
                          ->where('statut_presence_id', StatutPresence::where('nom', StatutPresence::PRESENT)->first()->id)
                          ->count();

        return round(($presences / $totalSessions) * 100, 2);
    }

    /**
     * Vérifier si l'étudiant est "droppé" pour une matière et un semestre
     */
    public function isDropped($matiereId, $semestreId)
    {
        $taux = $this->getTauxPresence($matiereId, $semestreId);
        return $taux <= 30;
    }

    /**
     * Obtenir le nom complet de l'étudiant
     */
    public function getNomCompletAttribute(): string
    {
        return $this->utilisateur ? $this->utilisateur->name : 'Étudiant #' . $this->numero_etudiant;
    }
}
