<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutPresence extends Model
{
    use HasFactory;

    protected $table = 'statuts_presence';

    protected $fillable = [
        'nom',
        'couleur',
    ];

    /**
     * Relation avec les présences
     */
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Constantes pour les statuts
     */
    const PRESENT = 'Présent';
    const EN_RETARD = 'En retard';
    const ABSENT = 'Absent';

    /**
     * Obtenir tous les statuts disponibles
     */
    public static function getStatuts()
    {
        return [
            self::PRESENT => '#10B981', // Vert
            self::EN_RETARD => '#F59E0B', // Orange
            self::ABSENT => '#EF4444', // Rouge
        ];
    }
}
