<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_notification_id',
        'message',
        'donnees_supplementaires',
    ];

    protected $casts = [
        'donnees_supplementaires' => 'array',
    ];

    /**
     * Relation avec le type de notification
     */
    public function typeNotification()
    {
        return $this->belongsTo(TypeNotification::class);
    }

    /**
     * Relation avec les utilisateurs (many-to-many via notification_utilisateur)
     */
    public function utilisateurs()
    {
        return $this->belongsToMany(User::class, 'notification_utilisateur', 'notification_id', 'utilisateur_id')
                    ->withTimestamps()
                    ->withPivot('lu_a');
    }

    /**
     * Marquer la notification comme lue pour un utilisateur
     */
    public function marquerCommeLue($utilisateurId)
    {
        $this->utilisateurs()->updateExistingPivot($utilisateurId, [
            'lu_a' => now(),
        ]);
    }

    /**
     * Vérifier si la notification est lue par un utilisateur
     */
    public function isLue($utilisateurId)
    {
        return $this->utilisateurs()
                    ->wherePivot('utilisateur_id', $utilisateurId)
                    ->wherePivot('lu_a', '!=', null)
                    ->exists();
    }

    /**
     * Obtenir le nombre d'utilisateurs qui ont lu la notification
     */
    public function getNombreLecteursAttribute()
    {
        return $this->utilisateurs()
                    ->wherePivot('lu_a', '!=', null)
                    ->count();
    }

    /**
     * Obtenir le nombre total d'utilisateurs destinataires
     */
    public function getNombreDestinatairesAttribute()
    {
        return $this->utilisateurs()->count();
    }

    /**
     * Obtenir le pourcentage de lecture
     */
    public function getPourcentageLectureAttribute()
    {
        $total = $this->nombre_destinataires;
        if ($total === 0) {
            return 0;
        }
        return round(($this->nombre_lecteurs / $total) * 100, 2);
    }
}
