<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeCours extends Model
{
    use HasFactory;

    protected $table = 'types_cours';

    protected $fillable = [
        'nom',
    ];

    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'type_cours_id');
    }
}
