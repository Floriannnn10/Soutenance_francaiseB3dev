<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutSession extends Model
{
    use HasFactory;

    protected $table = 'statuts_session';

    protected $fillable = [
        'nom',
        'couleur',
    ];

    /**
     * Relation avec les sessions de cours
     */
    public function sessionsDeCours()
    {
        return $this->hasMany(SessionDeCours::class);
    }

    /**
     * Constantes pour les statuts
     */
    const PREVUE = 'Prévue';
    const ANNULEE = 'Annulée';
    const REPORTEE = 'Reportée';
    const TERMINEE = 'Terminée';

    /**
     * Obtenir tous les statuts disponibles
     */
    public static function getStatuts()
    {
        return [
            self::PREVUE => '#3B82F6', // Bleu
            self::ANNULEE => '#EF4444', // Rouge
            self::REPORTEE => '#F59E0B', // Orange
            self::TERMINEE => '#10B981', // Vert
        ];
    }
}
