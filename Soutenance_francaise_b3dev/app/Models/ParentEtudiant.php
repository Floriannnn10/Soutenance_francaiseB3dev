<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentEtudiant extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'profession',
        'numero_parent',
        'type_parent',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    /**
     * Les étudiants liés à ce parent.
     */
    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(Etudiant::class, 'parent_etudiant', 'parent_id', 'etudiant_id')
            ->withPivot(['type_relation', 'est_responsable_legal', 'peut_recevoir_notifications'])
            ->withTimestamps();
    }

    /**
     * Les notifications reçues par ce parent.
     */
    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'notification_utilisateur', 'utilisateur_id', 'notification_id')
            ->withPivot(['est_lue', 'lu_a'])
            ->withTimestamps();
    }

    /**
     * Obtenir le nom complet du parent.
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Scope pour les parents actifs.
     */
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope pour les parents par type.
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type_parent', $type);
    }

    /**
     * Obtenir les étudiants dont ce parent est responsable légal.
     */
    public function etudiantsResponsableLegal()
    {
        return $this->belongsToMany(Etudiant::class, 'parent_etudiant', 'parent_id', 'etudiant_id')
            ->wherePivot('est_responsable_legal', true)
            ->withPivot(['type_relation', 'est_responsable_legal', 'peut_recevoir_notifications'])
            ->withTimestamps();
    }

    /**
     * Obtenir les étudiants pour lesquels ce parent peut recevoir des notifications.
     */
    public function etudiantsAvecNotifications()
    {
        return $this->belongsToMany(Etudiant::class, 'parent_etudiant', 'parent_id', 'etudiant_id')
            ->wherePivot('peut_recevoir_notifications', true)
            ->withPivot(['type_relation', 'est_responsable_legal', 'peut_recevoir_notifications'])
            ->withTimestamps();
    }
}
