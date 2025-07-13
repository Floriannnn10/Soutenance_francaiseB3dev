<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'session_de_cours_id',
        'statut_presence_id',
        'enregistre_par_utilisateur_id',
        'est_justifiee',
        'motif_justification',
        'enregistre_a',
    ];

    protected $casts = [
        'est_justifiee' => 'boolean',
        'enregistre_a' => 'datetime',
    ];

    /**
     * Relation avec l'étudiant
     */
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Relation avec la session de cours
     */
    public function sessionDeCours()
    {
        return $this->belongsTo(SessionDeCours::class);
    }

    /**
     * Relation avec le statut de présence
     */
    public function statutPresence()
    {
        return $this->belongsTo(StatutPresence::class);
    }

    /**
     * Relation avec l'utilisateur qui a enregistré la présence
     */
    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par_utilisateur_id');
    }

    /**
     * Vérifier si l'étudiant est présent
     */
    public function isPresent()
    {
        return $this->statutPresence->nom === StatutPresence::PRESENT;
    }

    /**
     * Vérifier si l'étudiant est en retard
     */
    public function isEnRetard()
    {
        return $this->statutPresence->nom === StatutPresence::EN_RETARD;
    }

    /**
     * Vérifier si l'étudiant est absent
     */
    public function isAbsent()
    {
        return $this->statutPresence->nom === StatutPresence::ABSENT;
    }

    /**
     * Obtenir le statut avec couleur
     */
    public function getStatutAvecCouleurAttribute()
    {
        return [
            'nom' => $this->statutPresence->nom,
            'couleur' => $this->statutPresence->couleur,
        ];
    }
}
