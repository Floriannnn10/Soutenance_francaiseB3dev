<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionDeCours extends Model
{
    use HasFactory;

    protected $table = 'sessions_de_cours';

    protected $fillable = [
        'semestre_id',
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'type_cours_id',
        'statut_session_id',
        'session_originale_id',
        'date',
        'heure_debut',
        'heure_fin',
        'salle',
        'commentaire',
    ];

    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
    ];

    /**
     * Relation avec le semestre
     */
    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    /**
     * Relation avec la classe
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec la matière
     */
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Relation avec l'enseignant
     */
    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    /**
     * Relation avec le type de cours
     */
    public function typeCours()
    {
        return $this->belongsTo(TypeCours::class);
    }

    /**
     * Relation avec le statut de session
     */
    public function statutSession()
    {
        return $this->belongsTo(StatutSession::class);
    }

    /**
     * Relation avec la session originale (pour les cours reportés)
     */
    public function sessionOriginale()
    {
        return $this->belongsTo(SessionDeCours::class, 'session_originale_id');
    }

    /**
     * Relation avec les sessions reportées
     */
    public function sessionsReportees()
    {
        return $this->hasMany(SessionDeCours::class, 'session_originale_id');
    }

    /**
     * Relation avec les présences
     */
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Vérifier si la session est aujourd'hui
     */
    public function isToday()
    {
        return $this->date->isToday();
    }

    /**
     * Vérifier si la session est en cours
     */
    public function isEnCours()
    {
        $now = now();
        return $this->date->isToday() &&
               $now->between($this->heure_debut, $this->heure_fin);
    }

    /**
     * Vérifier si la session est terminée
     */
    public function isTerminee()
    {
        $now = now();
        return $this->date->isPast() ||
               ($this->date->isToday() && $now->isAfter($this->heure_fin));
    }

    /**
     * Obtenir la durée de la session
     */
    public function getDureeAttribute()
    {
        return $this->heure_debut->diffInMinutes($this->heure_fin);
    }

    /**
     * Obtenir le nom complet de la session
     */
    public function getNomCompletAttribute()
    {
        return "{$this->matiere->nom} - {$this->classe->nom} - {$this->date->format('d/m/Y')}";
    }
}
