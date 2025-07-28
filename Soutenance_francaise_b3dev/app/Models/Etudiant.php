<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Etudiant extends Model
{
    use HasFactory;

    protected $table = 'etudiants';

    protected $fillable = [
        'user_id',
        'classe_id',
        'prenom',
        'nom',
        'email',
        'password',
        'date_naissance',
        'photo',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Relation : un étudiant appartient à une seule classe
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    /**
     * Relation : un étudiant appartient à un seul utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
