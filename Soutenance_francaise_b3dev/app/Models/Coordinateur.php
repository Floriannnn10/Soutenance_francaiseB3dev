<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coordinateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'specialite',
        'grade',
        'numero_coordinateur',
        'responsabilites',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    /**
     * Les classes coordonnées par ce coordinateur.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'coordinateur_classe', 'coordinateur_id', 'classe_id')
            ->withPivot(['annee_academique_id', 'date_debut', 'date_fin', 'commentaire', 'est_actif'])
            ->withTimestamps();
    }

    /**
     * Les années académiques pour lesquelles ce coordinateur est actif.
     */
    public function anneesAcademiques(): BelongsToMany
    {
        return $this->belongsToMany(AnneeAcademique::class, 'coordinateur_classe', 'coordinateur_id', 'annee_academique_id')
            ->withPivot(['classe_id', 'date_debut', 'date_fin', 'commentaire', 'est_actif'])
            ->withTimestamps();
    }

    /**
     * Les notifications reçues par ce coordinateur.
     */
    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'notification_utilisateur', 'utilisateur_id', 'notification_id')
            ->withPivot(['est_lue', 'lu_a'])
            ->withTimestamps();
    }

    /**
     * Obtenir le nom complet du coordinateur.
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Scope pour les coordinateurs actifs.
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope pour les coordinateurs par spécialité.
     */
    public function scopeParSpecialite($query, $specialite)
    {
        return $query->where('specialite', $specialite);
    }

    /**
     * Obtenir les classes coordonnées pour une année académique spécifique.
     */
    public function classesPourAnnee($anneeAcademiqueId)
    {
        return $this->belongsToMany(Classe::class, 'coordinateur_classe', 'coordinateur_id', 'classe_id')
            ->wherePivot('annee_academique_id', $anneeAcademiqueId)
            ->wherePivot('est_actif', true)
            ->withPivot(['date_debut', 'date_fin', 'commentaire'])
            ->withTimestamps();
    }

    /**
     * Obtenir les étudiants des classes coordonnées par ce coordinateur.
     */
    public function etudiantsCoordonnes()
    {
        return $this->belongsToMany(Etudiant::class, 'coordinateur_classe', 'coordinateur_id', 'classe_id', 'coordinateur_id', 'classe_id')
            ->join('classe_etudiant', 'classes.id', '=', 'classe_etudiant.classe_id')
            ->join('etudiants', 'classe_etudiant.etudiant_id', '=', 'etudiants.id')
            ->where('coordinateur_classe.est_actif', true);
    }
}
