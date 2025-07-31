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
        'email',
        'prenom',
        'nom',
        'photo',
        'promotion_id',
        'est_actif',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function classes()
    {
        if (!$this->promotion) {
            return collect();
        }
        return $this->promotion->classes();
    }

    public function anneesAcademiques()
    {
        return $this->classes()->with('anneeAcademique')->get()->pluck('anneeAcademique')->unique('id');
    }
}
