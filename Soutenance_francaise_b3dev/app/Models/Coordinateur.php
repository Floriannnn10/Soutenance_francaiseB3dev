<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coordinateur extends Model
{
    use HasFactory;

    protected $table = 'coordinateurs';

    protected $fillable = [
        'user_id',
        'prenom',
        'nom',
        'photo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'coordinateur_classe', 'coordinateur_id', 'classe_id');
    }
}
