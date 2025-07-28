<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentEtudiant extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'telephone',
        'adresse',
        'profession',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Etudiant::class, 'parent_id');
    }
}
