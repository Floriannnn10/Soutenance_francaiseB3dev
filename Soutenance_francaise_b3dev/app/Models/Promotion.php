<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'annee_academique_id'];

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function coordinateur()
    {
        return $this->hasOne(Coordinateur::class);
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }
}
