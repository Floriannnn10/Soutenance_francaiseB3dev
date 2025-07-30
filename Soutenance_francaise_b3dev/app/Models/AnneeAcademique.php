<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AnneeAcademique extends Model
{
    use HasFactory;

    protected $table = 'annees_academiques';

    protected $fillable = [
        'nom',
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
     * Relation avec les semestres
     */
    public function semestres(): HasMany
    {
        return $this->hasMany(Semestre::class, 'annee_academique_id');
    }

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'annee_academique_id');
    }

    /**
     * Relation avec les promotions
     */
    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'annee_academique_id');
    }


    /**
     * Activer cette année académique et désactiver les autres
     */
    public function activate(): void
    {
        // Désactiver toutes les autres années académiques
        self::where('id', '!=', $this->id)->update(['actif' => false]);

        // Activer cette année académique
        $this->update(['actif' => true]);
    }

    /**
     * Obtenir l'année académique active
     */
    public static function getActive()
    {
        // D'abord essayer de trouver une année marquée comme active
        $active = self::where('actif', true)->first();
        if ($active) {
            return $active;
        }

        // Sinon, chercher une année en cours (basé sur les dates)
        $now = Carbon::now();
        $enCours = self::where('date_debut', '<=', $now)
            ->where('date_fin', '>=', $now)
            ->first();
        if ($enCours) {
            return $enCours;
        }

        // Sinon, retourner la plus récente
        return self::orderBy('date_debut', 'desc')->first();
    }

    /**
     * Vérifier si l'année académique est en cours
     */
    public function isEnCours(): bool
    {
        $now = Carbon::now();
        return $now->between($this->date_debut, $this->date_fin);
    }

    /**
     * Obtenir le statut de l'année académique
     */
    public function getStatutAttribute(): string
    {
        $now = Carbon::now();

        if ($now->lt($this->date_debut)) {
            return 'À venir';
        } elseif ($now->gt($this->date_fin)) {
            return 'Terminée';
        } else {
            return 'En cours';
        }
    }
}
