<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Relation avec l'admin (si l'utilisateur est un admin)
     */
    public function admin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Relation avec les notifications
     */
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_utilisateur')
                    ->withTimestamps()
                    ->withPivot('lu_a');
    }

    /**
     * Relation avec l'étudiant (si l'utilisateur est un étudiant)
     */
    public function etudiant()
    {
        // Adapter la clé étrangère si besoin (par défaut par email)
        return $this->hasOne(\App\Models\Etudiant::class, 'email', 'email');
    }

    /**
     * Relation avec le parent (si l'utilisateur est un parent)
     */
    public function parent()
    {
        return $this->hasOne(\App\Models\ParentEtudiant::class, 'user_id', 'id');
    }

    /**
     * Relation avec le coordinateur (si l'utilisateur est un coordinateur)
     */
    public function coordinateur()
    {
        return $this->hasOne(\App\Models\Coordinateur::class, 'user_id', 'id');
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function hasRole($role)
    {
        return $this->roles()->where('nom', $role)->exists();
    }

    /**
     * Vérifier si l'utilisateur a l'un des rôles spécifiés
     */
    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('nom', (array) $roles)->exists();
    }
}
