<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Models\SessionDeCours;
use App\Models\TypeCours;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatistiquesController extends Controller
{
    public function index(Request $request)
    {
        $anneeAcademiques = AnneeAcademique::orderBy('date_debut', 'desc')->get();
        $anneeSelectionnee = $request->annee_id ?
            AnneeAcademique::findOrFail($request->annee_id) :
            AnneeAcademique::getActive();

        $data = [
            'tauxPresenceEtudiants' => $this->getTauxPresenceEtudiants($anneeSelectionnee),
            'tauxPresenceClasses' => $this->getTauxPresenceClasses($anneeSelectionnee),
            'volumeCoursParType' => $this->getVolumeCoursParType($anneeSelectionnee),
            'volumeCoursCumule' => $this->getVolumeCoursCumule($anneeSelectionnee),
            'anneeAcademiques' => $anneeAcademiques,
            'anneeSelectionnee' => $anneeSelectionnee
        ];

        return view('statistiques.index', $data);
    }

    private function getTauxPresenceEtudiants($anneeAcademique)
    {
        $etudiants = Etudiant::with(['presences' => function($query) use ($anneeAcademique) {
            $query->whereHas('sessionDeCours', function($q) use ($anneeAcademique) {
                $q->where('annee_academique_id', $anneeAcademique->id);
            });
        }])->get();

        $data = [];
        foreach ($etudiants as $etudiant) {
            $totalSessions = $etudiant->presences->count();
            if ($totalSessions > 0) {
                $presences = $etudiant->presences->filter(function($presence) {
                    return $presence->statutPresence->code === 'present';
                })->count();

                $taux = ($presences / $totalSessions) * 100;
                $couleur = $this->getCouleurTauxPresence($taux);

                $data[] = [
                    'nom' => $etudiant->nom . ' ' . $etudiant->prenom,
                    'taux' => round($taux, 2),
                    'couleur' => $couleur
                ];
            }
        }

        // Trier par taux décroissant
        usort($data, function($a, $b) {
            return $b['taux'] <=> $a['taux'];
        });

        return $data;
    }

    private function getCouleurTauxPresence($taux)
    {
        if ($taux >= 70) return '#006400'; // Vert foncé
        if ($taux >= 50.1) return '#90EE90'; // Vert clair
        if ($taux >= 30.1) return '#FFA500'; // Orange
        return '#FF0000'; // Rouge
    }

    private function getTauxPresenceClasses($anneeAcademique)
    {
        $classes = Classe::with(['etudiants.presences' => function($query) use ($anneeAcademique) {
            $query->whereHas('sessionDeCours', function($q) use ($anneeAcademique) {
                $q->where('annee_academique_id', $anneeAcademique->id);
            });
        }])->get();

        $data = [];
        foreach ($classes as $classe) {
            $totalPresencesPossibles = 0;
            $totalPresencesEffectives = 0;

            foreach ($classe->etudiants as $etudiant) {
                $totalPresencesPossibles += $etudiant->presences->count();
                $totalPresencesEffectives += $etudiant->presences->filter(function($presence) {
                    return $presence->statutPresence->code === 'present';
                })->count();
            }

            if ($totalPresencesPossibles > 0) {
                $taux = ($totalPresencesEffectives / $totalPresencesPossibles) * 100;
                $data[] = [
                    'classe' => $classe->nom,
                    'taux' => round($taux, 2)
                ];
            }
        }

        return $data;
    }

    private function getVolumeCoursParType($anneeAcademique)
    {
        $typesCours = TypeCours::all();
        $data = [];

        foreach ($typesCours as $type) {
            $volumeHoraire = SessionDeCours::where('annee_academique_id', $anneeAcademique->id)
                ->where('type_cours_id', $type->id)
                ->sum('duree');

            $data[] = [
                'type' => $type->nom,
                'volume' => $volumeHoraire
            ];
        }

        return $data;
    }

    private function getVolumeCoursCumule($anneeAcademique)
    {
        $annees = AnneeAcademique::where('date_fin', '<=', $anneeAcademique->date_fin)
            ->orderBy('date_debut')
            ->get();

        $data = [];
        $cumule = 0;

        foreach ($annees as $annee) {
            $volume = SessionDeCours::where('annee_academique_id', $annee->id)->sum('duree');
            $cumule += $volume;

            $data[] = [
                'annee' => $annee->nom,
                'volume' => $volume,
                'cumule' => $cumule
            ];
        }

        return $data;
    }
}
