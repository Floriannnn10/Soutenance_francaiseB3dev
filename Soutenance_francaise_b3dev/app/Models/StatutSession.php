<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatutSession extends Model
{
    use HasFactory;

    protected $table = 'session_statuses';

    protected $fillable = [
        'name',
        'display_name',
    ];

    public function sessionsDeCours(): HasMany
    {
        return $this->hasMany(SessionDeCours::class, 'status_id');
    }
}
