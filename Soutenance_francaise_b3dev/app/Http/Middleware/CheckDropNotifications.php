<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomNotification;
use Symfony\Component\HttpFoundation\Response;

class CheckDropNotifications
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Récupérer les notifications non lues de type warning (drops)
            $notifications = CustomNotification::whereHas('utilisateurs', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('lu_a', false);
            })->where('type', 'warning')->get();

            // Partager les notifications avec toutes les vues
            view()->share('dropNotifications', $notifications);
        }

        return $next($request);
    }
}
