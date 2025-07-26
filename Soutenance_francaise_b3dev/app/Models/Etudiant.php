<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Etudiant extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'etudiants';

    protected $fillable = [
        'classe_id',
        'prenom',
        'nom',
        'email',
        'password',
        'date_naissance',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relation : un étudiant appartient à une seule classe
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentEtudiant::class, 'parent_etudiant', 'etudiant_id', 'parent_id');
    }
}
