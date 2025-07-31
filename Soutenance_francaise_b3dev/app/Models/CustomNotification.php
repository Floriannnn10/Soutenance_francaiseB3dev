<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'type',
    ];

    /**
     * Les utilisateurs qui ont reçu cette notification
     */
    public function utilisateurs()
    {
        return $this->belongsToMany(User::class, 'custom_notification_user')
                    ->withPivot('lu_a')
                    ->withTimestamps();
    }
}
