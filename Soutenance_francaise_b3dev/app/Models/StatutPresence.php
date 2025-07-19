<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatutPresence extends Model
{
    use HasFactory;

    protected $table = 'statuts_presence';

    protected $fillable = [
        'nom',
        'description',
        'couleur',
    ];

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'status_id');
    }
}
