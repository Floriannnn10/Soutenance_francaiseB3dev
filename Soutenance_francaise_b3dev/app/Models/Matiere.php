<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'description',
        'coefficient',
        'heures_cm',
        'heures_td',
        'heures_tp',
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'heures_cm' => 'integer',
        'heures_td' => 'integer',
        'heures_tp' => 'integer',
    ];

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours()
    {
        return $this->hasMany(SessionDeCours::class);
    }

    /**
     * Calculer le total des heures
     */
    public function getTotalHeuresAttribute()
    {
        return $this->heures_cm + $this->heures_td + $this->heures_tp;
    }

    /**
     * Obtenir le nom complet avec le code
     */
    public function getNomCompletAttribute()
    {
        return "{$this->code} - {$this->nom}";
    }
}
