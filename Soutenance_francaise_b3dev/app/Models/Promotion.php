<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function coordinateur()
    {
        return $this->hasOne(Coordinateur::class);
    }
}
