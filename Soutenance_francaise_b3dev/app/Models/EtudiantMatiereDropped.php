<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtudiantMatiereDropped extends Model
{
    use HasFactory;

    protected $table = 'etudiant_matiere_dropped';

    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'annee_academique_id',
        'semestre_id',
        'raison_drop',
        'date_drop',
        'dropped_by',
    ];

    protected $casts = [
        'date_drop' => 'date',
    ];

    /**
     * Relation : un drop appartient à un étudiant
     */
    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Relation : un drop appartient à une matière
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Relation : un drop appartient à une année académique
     */
    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    /**
     * Relation : un drop appartient à un semestre
     */
    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    /**
     * Relation : un drop est effectué par un utilisateur
     */
    public function droppedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dropped_by');
    }
}
