<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AnneeAcademique;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckAcademicStatus
{
    public function handle(Request $request, Closure $next)
    {
        $anneeAcademique = AnneeAcademique::getActive();

        if (!$anneeAcademique) {
            return redirect()->back()->with('error', 'Aucune année académique active.');
        }

        // Vérifier si c'est une action de modification
        $isModificationAction = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);

        // Si c'est une action de modification et que l'année est terminée
        if ($isModificationAction && $anneeAcademique->date_fin < Carbon::now()) {
            // Exception pour les coordinateurs qui peuvent toujours créer des justifications
            if (Auth::user()->roles->first()->code === 'coordinateur' && $request->routeIs('justifications.*')) {
                return $next($request);
            }

            return redirect()->back()->with('error', 'Les modifications ne sont pas autorisées pour une année académique terminée.');
        }

        // Ajouter l'année académique à la requête pour y accéder facilement
        $request->merge(['annee_academique' => $anneeAcademique]);

        return $next($request);
    }
}
