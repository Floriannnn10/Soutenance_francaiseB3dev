<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee_academique_id',
        'nom',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Relation avec l'année académique
     */
    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours()
    {
        return $this->hasMany(SessionDeCours::class);
    }

    /**
     * Vérifier si le semestre est actif
     */
    public function isActive()
    {
        $now = now();
        return $now->between($this->date_debut, $this->date_fin);
    }
}
