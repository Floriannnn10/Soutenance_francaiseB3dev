<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasseEtudiant extends Model
{
    use HasFactory;

    protected $table = 'classe_etudiant';

    protected $fillable = [
        'etudiant_id',
        'classe_id',
        'annee_academique_id',
        'date_inscription',
        'est_actif',
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'est_actif' => 'boolean',
    ];

    /**
     * Relation avec l'étudiant
     */
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Relation avec la classe
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec l'année académique
     */
    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }
}
