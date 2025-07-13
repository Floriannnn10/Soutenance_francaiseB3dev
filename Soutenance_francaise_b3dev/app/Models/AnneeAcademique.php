<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnneeAcademique extends Model
{
    use HasFactory;

    protected $table = 'annees_academiques';

    protected $fillable = [
        'nom',
        'date_debut',
        'date_fin',
        'est_active',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_active' => 'boolean',
    ];

    /**
     * Relation avec les semestres
     */
    public function semestres()
    {
        return $this->hasMany(Semestre::class);
    }

    /**
     * Relation avec les inscriptions d'étudiants
     */
    public function inscriptions()
    {
        return $this->hasMany(ClasseEtudiant::class);
    }

    /**
     * Obtenir l'année académique active
     */
    public static function getActive()
    {
        return static::where('est_active', true)->first();
    }

    /**
     * Activer cette année académique et désactiver les autres
     */
    public function activate()
    {
        static::where('est_active', true)->update(['est_active' => false]);
        $this->update(['est_active' => true]);
    }
}
