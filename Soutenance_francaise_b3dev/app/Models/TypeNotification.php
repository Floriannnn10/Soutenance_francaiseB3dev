<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeNotification extends Model
{
    use HasFactory;

    protected $table = 'notification_types';

    protected $fillable = [
        'name',
        'display_name',
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'type_notification_id');
    }
}
