<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Semestre extends Model
{
    use HasFactory;

    protected $table = 'semestres';

    protected $fillable = [
        'nom',
        'annee_academique_id',
        'date_debut',
        'date_fin',
        'actif',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'actif' => 'boolean',
    ];

    /**
     * Relation avec l'année académique
     */
    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'semester_id');
    }

    /**
     * Relation avec les présences
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'semester_id');
    }

    /**
     * Activer ce semestre et désactiver les autres de la même année académique
     */
    public function activate(): void
    {
        // Désactiver tous les autres semestres de la même année académique
        self::where('annee_academique_id', $this->annee_academique_id)
            ->where('id', '!=', $this->id)
            ->update(['actif' => false]);

        // Activer ce semestre
        $this->update(['actif' => true]);
    }

    /**
     * Obtenir le semestre actif d'une année académique
     */
    public static function getActiveForYear($anneeAcademiqueId)
    {
        return self::where('annee_academique_id', $anneeAcademiqueId)
                   ->where('actif', true)
                   ->first();
    }

    /**
     * Vérifier si le semestre est en cours
     */
    public function isEnCours(): bool
    {
        $now = Carbon::now();
        return $now->between($this->date_debut, $this->date_fin);
    }

    /**
     * Obtenir le statut du semestre
     */
    public function getStatutAttribute(): string
    {
        $now = Carbon::now();

        if ($now->lt($this->date_debut)) {
            return 'À venir';
        } elseif ($now->gt($this->date_fin)) {
            return 'Terminé';
        } else {
            return 'En cours';
        }
    }
}
