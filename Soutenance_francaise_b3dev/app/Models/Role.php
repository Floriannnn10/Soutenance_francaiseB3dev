<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
    ];

    /**
     * Relation avec les utilisateurs
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Constantes pour les rôles
     */
    const ADMIN = 'Admin';
    const COORDINATEUR = 'Coordinateur';
    const ENSEIGNANT = 'Enseignant';
    const ETUDIANT = 'Étudiant';
    const PARENT = 'Parent';

    /**
     * Obtenir tous les rôles disponibles
     */
    public static function getRoles()
    {
        return [
            self::ADMIN => 'Administrateur',
            self::COORDINATEUR => 'Coordinateur',
            self::ENSEIGNANT => 'Enseignant',
            self::ETUDIANT => 'Étudiant',
            self::PARENT => 'Parent',
        ];
    }
}
