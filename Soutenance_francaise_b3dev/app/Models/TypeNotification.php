<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeNotification extends Model
{
    use HasFactory;

    protected $table = 'types_notification';

    protected $fillable = [
        'nom',
        'icone',
    ];

    /**
     * Relation avec les notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Constantes pour les types
     */
    const DROPPE = 'Droppé';
    const ANNULATION_COURS = 'Annulation de cours';
    const INFORMATION = 'Information';

    /**
     * Obtenir tous les types disponibles
     */
    public static function getTypes()
    {
        return [
            self::DROPPE => 'warning',
            self::ANNULATION_COURS => 'error',
            self::INFORMATION => 'info',
        ];
    }
}
