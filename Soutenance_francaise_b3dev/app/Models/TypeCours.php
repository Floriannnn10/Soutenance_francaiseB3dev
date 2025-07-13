<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeCours extends Model
{
    use HasFactory;

    protected $table = 'types_cours';

    protected $fillable = [
        'nom',
        'description',
    ];

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours()
    {
        return $this->hasMany(SessionDeCours::class);
    }

    /**
     * Constantes pour les types de cours
     */
    const CM = 'CM';
    const TD = 'TD';
    const TP = 'TP';
    const EXAMEN = 'Examen';
    const CONTROLE = 'Contrôle';

    /**
     * Obtenir tous les types disponibles
     */
    public static function getTypes()
    {
        return [
            self::CM => 'Cours Magistral',
            self::TD => 'Travaux Dirigés',
            self::TP => 'Travaux Pratiques',
            self::EXAMEN => 'Examen',
            self::CONTROLE => 'Contrôle',
        ];
    }
}
